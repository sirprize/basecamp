<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Milestone;

use Sirprize\Basecamp\Id;
use Sirprize\Basecamp\Service;
use Sirprize\Basecamp\Response;
use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\Milestone\Entity;
use Sirprize\Basecamp\Milestone\Collection\Observer\Abstrakt;

/**
 * Encapsulate a set of persisted milestone objects and the operations performed over them
 */
class Collection extends \SplObjectStorage
{

    const FIND_COMPLETED = 'completed';
    const FIND_UPCOMING = 'upcoming';
    const FIND_LATE = 'late';
    const FIND_ALL = 'all';
    const _MILESTONE = 'milestone';

    protected $_service = null;
    protected $_httpClient = null;
    protected $_started = false;
    protected $_loaded = false;
    protected $_response = null;
    protected $_observers = array();

    public function setService(Service $service)
    {
        $this->_service = $service;
        return $this;
    }

    public function setHttpClient(\Zend\Http\Client $httpClient)
    {
        $this->_httpClient = $httpClient;
        return $this;
    }

    /**
     * Get response object
     *
     * @return \Sirprize\Basecamp\Response|null
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Attach observer object
     *
     * @return \Sirprize\Basecamp\Milestone\Collection
     */
    public function attachObserver(Abstrakt $observer)
    {
        $exists = false;

        foreach(array_keys($this->_observers) as $key)
        {
            if($observer === $this->_observers[$key])
            {
                $exists = true;
                break;
            }
        }

        if(!$exists)
        {
            $this->_observers[] = $observer;
        }

        return $this;
    }

    /**
     * Detach observer object
     *
     * @return \Sirprize\Basecamp\Milestone\Collection
     */
    public function detachObserver(Abstrakt $observer)
    {
        foreach(array_keys($this->_observers) as $key)
        {
            if($observer === $this->_observers[$key])
            {
                unset($this->_observers[$key]);
                break;
            }
        }

        return $this;
    }

    /**
     * Instantiate a new milestone entity
     *
     * @return \Sirprize\Basecamp\Milestone\Entity
     */
    public function getMilestoneInstance()
    {
        $milestone = new Entity();
        $milestone
            ->setHttpClient($this->_getHttpClient())
            ->setService($this->_getService())
        ;

        return $milestone;
    }

    /**
     * Defined by \SplObjectStorage
     *
     * Add milestone entity to batch-persist later by create()
     *
     * @param \Sirprize\Basecamp\Milestone\Entity $milestone
     * @throws \Sirprize\Basecamp\Exception
     * @return \Sirprize\Basecamp\Milestone\Collection
     */
    public function attach($milestone, $data = null)
    {
        if(!$milestone instanceof Entity)
        {
            throw new Exception('expecting an instance of Entity');
        }

        parent::attach($milestone);
        return $this;
    }

    /**
     * Persist milestone objects that have previously been added by attach()
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return int Number of new milestones that have been created
     */
    public function create(Id $projectId)
    {
        if($this->_started)
        {
            throw new Exception('this collection is already persisted in storage');
        }

        $xml = '<request>';

        foreach($this as $milestone)
        {
            $xml .= $milestone->getXml();
        }

        $xml .= '</request>';

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/projects/$projectId/milestones/create")
                ->setAuth($this->_getService()->getUsername(), $this->_getService()->getPassword())
                ->setHeaders('Content-Type', 'application/xml')
                ->setHeaders('Accept', 'application/xml')
                ->setRawData($xml)
                ->request('POST')
            ;
        }
        catch(\Exception $exception)
        {
            try {
                // connection error - try again
                $response = $this->_getHttpClient()->request('POST');
            }
            catch(\Exception $exception)
            {
                foreach($this as $milestone)
                {
                    $milestone->onCreateError();
                }

                $this->_onCreateError();

                throw new Exception($exception->getMessage());
            }
        }

        $this->_response = new Response($response);

        if($this->_response->isError())
        {
            // service error
            foreach($this as $milestone)
            {
                $milestone->onCreateError();
            }

            $this->_onCreateError();
            return 0;
        }

        $data = (array) $this->_response->getData();
        $i = 0;

        foreach($this as $milestone)
        {
            // load full data into milestone
            $milestone->onCreateLoad($data[self::_MILESTONE][$i++]);
        }

        $this->_loaded = true;
        $this->_started = true;
        $this->_onCreateSuccess();
        return $this->count();
    }

    /**
     * Fetch milestones for a given project
     *
     * @param string $status completed|upcoming|late|all
     * @throws \Sirprize\Basecamp\Exception
     * @return \Sirprize\Basecamp\Milestone\Collection
     */
    public function startAllByProjectId(Id $projectId, $status = null)
    {
        if($this->_started)
        {
            return $this;
        }

        $this->_started = true;

        switch($status)
        {
            case self::FIND_COMPLETED: $query = '?find='.self::FIND_COMPLETED; break;
            case self::FIND_UPCOMING: $query = '?find='.self::FIND_UPCOMING; break;
            case self::FIND_LATE: $query = '?find='.self::FIND_LATE; break;
            default: $query = '?find=all';
        }

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/projects/$projectId/milestones/list.xml$query")
                ->setAuth($this->_getService()->getUsername(), $this->_getService()->getPassword())
                ->request('GET')
            ;
        }
        catch(\Exception $exception)
        {
            try {
                // connection error - try again
                $response = $this->_getHttpClient()->request('GET');
            }
            catch(\Exception $exception)
            {
                // connection error
                $this->_onStartError();

                throw new Exception($exception->getMessage());
            }
        }

        $this->_response = new Response($response);

        if($this->_response->isError())
        {
            // service error
            $this->_onStartError();
            return $this;
        }

        $this->load($this->_response->getData());
        $this->_onStartSuccess();
        return $this;
    }

    /**
     * Instantiate milestone objects with api response data
     *
     * @return \Sirprize\Basecamp\Milestone\Collection
     */
    public function load(\SimpleXMLElement $xml)
    {
        if($this->_loaded)
        {
            throw new Exception('collection has already been loaded');
        }

        $this->_loaded = true;
        $array = (array) $xml;

        if(!isset($array[self::_MILESTONE]))
        {
            // list request - 0 items in response
            return $this;
        }

        if(isset($array[self::_MILESTONE]->id))
        {
            // list request - 1 item in response
            $milestone = $this->getMilestoneInstance();
            $milestone->load($array[self::_MILESTONE]);
            $this->attach($milestone);
            return $this;
        }

        foreach($array[self::_MILESTONE] as $row)
        {
            // list request - 2 or more items in response
            $milestone = $this->getMilestoneInstance();
            $milestone->load($row);
            $this->attach($milestone);
        }

        return $this;
    }

    protected function _getService()
    {
        if($this->_service === null)
        {
            throw new Exception('call setService() before '.__METHOD__);
        }

        return $this->_service;
    }

    protected function _getHttpClient()
    {
        if($this->_httpClient === null)
        {
            throw new Exception('call setHttpClient() before '.__METHOD__);
        }

        return $this->_httpClient;
    }

    protected function _onCreateSuccess()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onCreateSuccess($this);
        }
    }

    protected function _onStartSuccess()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onStartSuccess($this);
        }
    }

    protected function _onCreateError()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onCreateError($this);
        }
    }

    protected function _onStartError()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onStartError($this);
        }
    }

}