<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\TimeEntry;

use Sirprize\Basecamp\Id;
use Sirprize\Basecamp\Service;
use Sirprize\Basecamp\Response;
use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\TimeEntry\Entity;
use Sirprize\Basecamp\TimeEntry\Collection\Observer\Abstrakt;

/**
 * Encapsulate a set of persisted TimeEntry objects and the operations performed over them
 */
class Collection extends \SplObjectStorage
{

    const _TIME_ENTRY = 'time-entry';

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
     * @return \Sirprize\Basecamp\TimeEntry\Collection
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
     * @return \Sirprize\Basecamp\TimeEntry\Collection
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
     * Instantiate a new TimeEntry entity
     *
     * @return \Sirprize\Basecamp\TimeEntry\Entity
     */
    public function getTimeEntryInstance()
    {
        $entry = new Entity();
        $entry
            ->setHttpClient($this->_getHttpClient())
            ->setService($this->_getService())
        ;

        return $entry;
    }

    /**
     * Defined by \SplObjectStorage
     *
     * Add TimeEntry entity
     *
     * @param \Sirprize\Basecamp\TimeEntry\Entity $entry
     * @throws \Sirprize\Basecamp\Exception
     * @return \Sirprize\Basecamp\TimeEntry\Collection
     */
    public function attach($entry, $data = null)
    {
        if(!$entry instanceof Entity)
        {
            throw new Exception('expecting an instance of Entity');
        }

        parent::attach($entry);
        return $this;
    }

    /**
     * Fetch timeentries by project id
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return null|\Sirprize\Basecamp\TimeEntry\Collection
     */
    public function startAllByProjectId(Id $id, $page = 1)
    {
        if($this->_started)
        {
            return $this;
        }

        $this->_started = true;

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/projects/$id/time_entries.xml")
                ->setParameterGet('page', (int) $page)
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
     * Fetch timeentry by id
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
                ->setUri($this->_getService()->getBaseUri()."/time_entries/$id.xml")
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
     * Instantiate entry objects with api response data
     *
     * @return \Sirprize\Basecamp\TimeEntry\Collection
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
            $entry = $this->getTimeEntryInstance();
            $entry->load($xml);
            $this->attach($entry);
            return $this;
        }

        $array = (array) $xml;

        if(!isset($array[self::_TIME_ENTRY]))
        {
            // list request - 0 items in response
            return $this;
        }

        if(isset($array[self::_TIME_ENTRY]->id))
        {
            // list request - 1 item in response
            $entry = $this->getTimeEntryInstance();
            $entry->load($array[self::_TIME_ENTRY]);
            $this->attach($entry);
            return $this;
        }

        foreach($array[self::_TIME_ENTRY] as $row)
        {
            // list request - 2 or more items in response
            $entry = $this->getTimeEntryInstance();
            $entry->load($row);
            $this->attach($entry);
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