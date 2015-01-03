<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Project;

use Sirprize\Basecamp\Id;
use Sirprize\Basecamp\Service;
use Sirprize\Basecamp\Response;
use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\Project\Entity;
use Sirprize\Basecamp\Project\Collection\Observer\Abstrakt;

/**
 * Encapsulate a set of persisted project objects and the operations performed over them
 */
class Collection extends \SplObjectStorage
{

    const FIND_COMPLETED = 'completed';
    const FIND_UPCOMING = 'upcoming';
    const FIND_LATE = 'late';
    const FIND_ALL = 'all';
    const _PROJECT = 'project';

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
     * @return \Sirprize\Basecamp\Project\Collection
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
     * @return \Sirprize\Basecamp\Project\Collection
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
     * Instantiate a new project entity
     *
     * @return \Sirprize\Basecamp\Project\Entity
     */
    public function getProjectInstance()
    {
        $project = new Entity();
        $project
            ->setHttpClient($this->_getHttpClient())
            ->setService($this->_getService())
        ;

        return $project;
    }

    /**
     * Defined by \SplObjectStorage
     *
     * Add project entity
     *
     * @param \Sirprize\Basecamp\Project\Entity $project
     * @throws \Sirprize\Basecamp\Exception
     * @return \Sirprize\Basecamp\Project\Collection
     */
    public function attach($project, $data = null)
    {
        if(!$project instanceof Entity)
        {
            throw new Exception('expecting an instance of Entity');
        }

        parent::attach($project);
        return $this;
    }

    /**
     * Fetch project by id
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return null|Entity
     */
    public function startById(Id $id)
    {
        if($this->_started)
        {
            return $this;
        }

        $this->_started = true;
        $response = null;

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/projects/$id.xml")
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
                $this->_onStartError();

                throw new Exception($exception->getMessage());
            }
        }

        $this->_response = new Response($response);

        if($this->_response->isError())
        {
            // service error
            $this->_onStartError();
            return null;
        }

        $this->load($this->_response->getData());
        $this->_onStartSuccess();
        $this->rewind();
        return $this->current();
    }

    /**
     * Fetch all projects
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return \Sirprize\Basecamp\Project\Collection
     */
    public function startAll()
    {
        if($this->_started)
        {
            return $this;
        }

        $this->_started = true;

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/projects.xml")
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
     * Instantiate project objects with api response data
     *
     * @return \Sirprize\Basecamp\Project\Collection
     */
    public function load(\SimpleXMLElement $xml)
    {
        if($this->_loaded)
        {
            throw new Exception('collection has already been loaded');
        }

        $this->_loaded = true;

        if(isset($xml->id))
        {
            // request for a single entity
            $project = $this->getProjectInstance();
            $project->load($xml);
            $this->attach($project);
            return $this;
        }

        $array = (array) $xml;

        if(!isset($array[self::_PROJECT]))
        {
            // list request - 0 items in response
            return $this;
        }

        if(isset($array[self::_PROJECT]->id))
        {
            // list request - 1 item in response
            $project = $this->getProjectInstance();
            $project->load($array[self::_PROJECT]);
            $this->attach($project);
            return $this;
        }

        foreach($array[self::_PROJECT] as $row)
        {
            // list request - 2 or more items in response
            $project = $this->getProjectInstance();
            $project->load($row);
            $this->attach($project);
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

    protected function _onStartSuccess()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onStartSuccess($this);
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