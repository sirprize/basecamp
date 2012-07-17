<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Person;

use Sirprize\Basecamp\Service;
use Sirprize\Basecamp\Response;
use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\Id;
use Sirprize\Basecamp\Person\Entity;
use Sirprize\Basecamp\Person\Collection\Observer\Abstrakt;

/**
 * Encapsulate a set of persisted person objects and the operations performed over them
 */
class Collection extends \SplObjectStorage
{

    const _PERSON = 'person';

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

    public function setHttpClient(\Zend_Http_Client $httpClient)
    {
        $this->_httpClient = $httpClient;
        return $this;
    }

    /**
     * Get response object
     *
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Attach observer object
     *
     * @return \Sirprize\Basecamp\Person\Collection
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
     * @return \Sirprize\Basecamp\Person\Collection
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
     * Instantiate a new person entity
     *
     * @return \Sirprize\Basecamp\Person\Entity
     */
    public function getPersonInstance()
    {
        $person = new Entity();
        $person
            ->setHttpClient($this->_getHttpClient())
            ->setService($this->_getService())
        ;

        return $person;
    }

    /**
     * Defined by \SplObjectStorage
     *
     * Add person entity
     *
     * @param \Sirprize\Basecamp\Person\Entity $person
     * @throws Exception
     * @return \Sirprize\Basecamp\Person\Collection
     */
    public function attach($person, $data = null)
    {
        if(!$person instanceof Entity)
        {
            throw new Exception('expecting an instance of \Sirprize\Basecamp\Person\Entity');
        }

        parent::attach($person);
        return $this;
    }

    /**
     * Fetch currently logged in user
     *
     * @throws Exception
     * @return null|\Sirprize\Basecamp\Person\Entity
     */
    public function startMe()
    {
        if($this->_started)
        {
            return $this;
        }

        $this->_started = true;

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/me.xml")
                ->setAuth($this->_getService()->getUsername(), $this->_getService()->getPassword())
                ->setHeaders('Content-Type', 'application/xml')
                ->setHeaders('Accept', 'application/xml')
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
     * Fetch person by id
     *
     * @throws Exception
     * @return null|\Sirprize\Basecamp\Person\Entity
     */
    public function startById(Id $id)
    {
        if($this->_started)
        {
            return $this;
        }

        $this->_started = true;

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/people/$id.xml")
                ->setAuth($this->_getService()->getUsername(), $this->_getService()->getPassword())
                ->setHeaders('Content-Type', 'application/xml')
                ->setHeaders('Accept', 'application/xml')
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
     * Fetch people within project
     *
     * @throws Exception
     * @return \Sirprize\Basecamp\Person\Entity
     */
    public function startAllByProjectId(Id $projectId)
    {
        if($this->_started)
        {
            return $this;
        }

        $this->_started = true;

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/projects/$projectId/people.xml")
                ->setAuth($this->_getService()->getUsername(), $this->_getService()->getPassword())
                ->request('GET')
            ;
        }
        catch(\Exception $exception)
        {
            // connection error
            $this->_onStartError();

            throw new Exception($exception->getMessage());
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
     * Fetch people across projects (everbody)
     *
     * @throws Exception
     * @return \Sirprize\Basecamp\Person\Collection
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
                ->setUri($this->_getService()->getBaseUri()."/people.xml")
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
     * Instantiate person objects with api response data
     *
     * @return \Sirprize\Basecamp\Person\Collection
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
            $person = $this->getPersonInstance();
            $person->load($xml);
            $this->attach($person);
            return $this;
        }

        $array = (array) $xml;

        if(!isset($array[self::_PERSON]))
        {
            // list request - 0 items in response
            return $this;
        }

        if(isset($array[self::_PERSON]->id))
        {
            // list request - 1 item in response
            $person = $this->getPersonInstance();
            $person->load($array[self::_PERSON]);
            $this->attach($person);
            return $this;
        }

        foreach($array[self::_PERSON] as $row)
        {
            // list request - 2 or more items in response
            $person = $this->getPersonInstance();
            $person->load($row);
            $this->attach($person);
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