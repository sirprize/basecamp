<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\TodoItem;

use Sirprize\Basecamp\Id;
use Sirprize\Basecamp\Service;
use Sirprize\Basecamp\Response;
use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\TodoItem\Entity;
use Sirprize\Basecamp\TodoItem\Collection\Observer\Abstrakt;

/**
 * Encapsulate a set of persisted todo-item objects and the operations performed over them
 */
class Collection extends \SplObjectStorage
{

    const _TODO_ITEM = 'todo-item';

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
     * @return \Sirprize\Basecamp\Response|null
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Attach observer object
     *
     * @return \Sirprize\Basecamp\TodoItem\Collection
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
     * @return \Sirprize\Basecamp\TodoItem\Collection
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
     * Instantiate a new todoItem entity
     *
     * @return \Sirprize\Basecamp\TodoItem\Entity
     */
    public function getTodoItemInstance()
    {
        $todoItem = new Entity();
        $todoItem
            ->setHttpClient($this->_getHttpClient())
            ->setService($this->_getService())
        ;

        return $todoItem;
    }

    /**
     * Defined by \SplObjectStorage
     *
     * Add todoItem entity
     *
     * @param \Sirprize\Basecamp\TodoItem\Entity $todoItem
     * @throws \Sirprize\Basecamp\Exception
     * @return \Sirprize\Basecamp\TodoItem\Collection
     */
    public function attach($todoItem, $data = null)
    {
        if(!$todoItem instanceof Entity)
        {
            throw new Exception('expecting an instance of Entity');
        }

        parent::attach($todoItem);
        return $this;
    }

    /**
     * Fetch todo-items for a given project
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return \Sirprize\Basecamp\TodoItem\Collection
     */
    public function startAllByTodoListId(Id $todoListId, $force = false)
    {
        if($this->_started && !$force)
        {
            return $this;
        }

        $this->_started = true;

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/todo_lists/$todoListId/todo_items.xml")
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

        $this->load($this->_response->getData(), $force);
        $this->_onStartSuccess();
        return $this;
    }

    /**
     * Fetch todo-item by id
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return null|Entity
     */
    public function startById(Id $id, $force = false)
    {
        if($this->_started && !$force)
        {
            return $this;
        }

        $this->_started = true;

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/todo_items/$id.xml")
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

        $this->load($this->_response->getData(), $force);
        $this->_onStartSuccess();
        $this->rewind();
        return $this->current();
    }

    /**
     * Instantiate todo-item objects with api response data
     *
     * @return \Sirprize\Basecamp\TodoItem\Collection
     */
    public function load(\SimpleXMLElement $xml, $force = false)
    {
        if($this->_loaded && !$force)
        {
            throw new Exception('todo-item collection has already been loaded');
        }

        $this->_loaded = true;

        if(isset($xml->id))
        {
            // request for a single entity (not supported on todoItems)
            $todoItem = $this->getTodoItemInstance();
            $todoItem->load($xml, $force);
            $this->attach($todoItem);
            return $this;
        }

        $array = (array) $xml;

        if(!isset($array[self::_TODO_ITEM]))
        {
            // list request - 0 items in response
            return $this;
        }

        if(isset($array[self::_TODO_ITEM]->id))
        {
            // list request - 1 item in response
            $todoItem = $this->getTodoItemInstance();
            $todoItem->load($array[self::_TODO_ITEM], $force);
            $this->attach($todoItem);
            return $this;
        }

        foreach($array[self::_TODO_ITEM] as $row)
        {
            // list request - 2 or more items in response
            $todoItem = $this->getTodoItemInstance();
            $todoItem->load($row, $force);
            $this->attach($todoItem);
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