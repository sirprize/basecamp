<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\TodoList;

use Sirprize\Basecamp\Id;
use Sirprize\Basecamp\Service;
use Sirprize\Basecamp\Response;
use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\TodoList\Entity;
use Sirprize\Basecamp\TodoList\Collection\Observer\Abstrakt;

/**
 * Encapsulate a set of persisted todo-list objects and the operations performed over them
 */
class Collection extends \SplObjectStorage
{

    const FILTER_ALL = 'all';
    const FILTER_PENDING = 'pending';
    const FILTER_FINISHED = 'finished';

    const _TODOLIST = 'todo-list';

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
     * @return \Sirprize\Basecamp\TodoList\Collection
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
     * @return \Sirprize\Basecamp\TodoList\Collection
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
     * Instantiate a new todoList entity
     *
     * @return \Sirprize\Basecamp\TodoList\Entity
     */
    public function getTodoListInstance()
    {
        $todoList = new Entity();
        $todoList
            ->setHttpClient($this->_getHttpClient())
            ->setService($this->_getService())
        ;

        return $todoList;
    }

    /**
     * Defined by \SplObjectStorage
     *
     * Add todoList entity
     *
     * @param \Sirprize\Basecamp\TodoList\Entity $todoList
     * @throws \Sirprize\Basecamp\Exception
     * @return \Sirprize\Basecamp\TodoList\Collection
     */
    public function attach($todoList, $data = null)
    {
        if(!$todoList instanceof Entity)
        {
            throw new Exception('expecting an instance of Entity');
        }

        parent::attach($todoList);
        return $this;
    }

    /**
     * Fetch todoLists across projects (response includes list-items)
     *
     * @param string $responsibleParty resonsible-party-id|''(empty string, unassigned lists)|null(lists of current user)
     * @throws \Sirprize\Basecamp\Exception
     * @return \Sirprize\Basecamp\TodoList\Collection
     */
    public function startAllByResponsibiltyParty($responsibleParty = null)
    {
        if($this->_started)
        {
            return $this;
        }

        $this->_started = true;

        $query = ''; // the current user is assumed

        if($responsibleParty === '')
        {
            // unassigned lists
            $query = '?responsible_party=';
        }
        else if($responsibleParty !== null)
        {
            // person id or company id (prefixed with c)
            $query = '?responsible_party='.$responsibleParty;
        }

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/todo_lists.xml$query")
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
     * Fetch all todoLists in specified project (response doesn't include list-items)
     *
     * @param string $filter all|pending|finished
     * @throws \Sirprize\Basecamp\Exception
     * @return \Sirprize\Basecamp\TodoList\Collection
     */
    public function startAllByProjectId(Id $projectId, $filter = null)
    {
        if($this->_started)
        {
            return $this;
        }

        $this->_started = true;

        $query = '';

        if($filter == self::FILTER_PENDING)
        {
            $query = '?filter='.self::FILTER_PENDING;
        }
        else if($filter == self::FILTER_FINISHED)
        {
            $query = '?filter='.self::FILTER_FINISHED;
        }

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/projects/$projectId/todo_lists.xml$query")
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
     * Fetch one todoList by id (response includes list-items)
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

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/todo_lists/$id.xml")
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
     * Instantiate todoList objects with api response data
     *
     * @return \Sirprize\Basecamp\TodoList\Collection
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
            $todoList = $this->getTodoListInstance();
            $todoList->load($xml);
            $this->attach($todoList);
            return $this;
        }

        $array = (array) $xml;

        if(!isset($array[self::_TODOLIST]))
        {
            // list request - 0 items in response
            return $this;
        }

        if(isset($array[self::_TODOLIST]->id))
        {
            // list request - 1 item in response
            $todoList = $this->getTodoListInstance();
            $todoList->load($array[self::_TODOLIST]);
            $this->attach($todoList);
            return $this;
        }

        foreach($array[self::_TODOLIST] as $row)
        {
            // list request - 2 or more items in response
            $todoList = $this->getTodoListInstance();
            $todoList->load($row);
            $this->attach($todoList);
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