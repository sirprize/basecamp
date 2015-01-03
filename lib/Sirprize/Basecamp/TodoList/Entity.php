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
use Sirprize\Basecamp\TodoItem\Collection as TodoItemCollection;
use Sirprize\Basecamp\TodoList\Entity\Observer\Abstrakt;

/**
 * Represent and modify a todo-list
 */
class Entity
{

    const _COMPLETED_COUNT = 'completed-count';
    const _DESCRIPTION = 'description';
    const _ID = 'id';
    const _MILESTONE_ID = 'milestone-id';
    const _NAME = 'name';
    const _POSITION = 'position';
    const _PRIVATE = 'private';
    const _PROJECT_ID = 'project-id';
    const _TRACKED = 'tracked';
    const _UNCOMPLETED_COUNT = 'uncompleted-count';
    const _TODO_ITEMS = 'todo-items';
    const _COMPLETE = 'complete';

    protected $_service = null;
    protected $_httpClient = null;
    protected $_data = array();
    protected $_loaded = false;
    protected $_response = null;
    protected $_observers = array();
    protected $_templateId = null;
    protected $_todoItems = null;
    protected $_todoItemsLoaded = false;

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
     * @return \Sirprize\Basecamp\TodoList
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
     * @return \Sirprize\Basecamp\TodoList
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
     * Set todo-list template id to use when later calling create()
     *
     * @param \Sirprize\Basecamp\Id $templateId If this id is set then setName() is optional on create()
     */
    public function setTemplateId(Id $templateId)
    {
        $this->_templateId = $templateId;
        return $this;
    }

    public function getTemplateId()
    {
        return $this->_templateId;
    }

    public function setName($name)
    {
        $this->_data[self::_NAME] = $name;
        return $this;
    }

    public function setProjectId(Id $projectId)
    {
        $this->_data[self::_PROJECT_ID] = $projectId;
        return $this;
    }

    public function setDescription($description)
    {
        $this->_data[self::_DESCRIPTION] = $description;
        return $this;
    }

    public function setMilestoneId(Id $milestoneId)
    {
        $this->_data[self::_MILESTONE_ID] = $milestoneId;
        return $this;
    }

    public function setIsPrivate($private)
    {
        $this->_data[self::_PRIVATE] = $private;
        return $this;
    }

    public function setIsTracked($tracked)
    {
        $this->_data[self::_TRACKED] = $tracked;
        return $this;
    }

    public function setTodoItems(TodoItemCollection $todoItems)
    {
        $this->_todoItems = $todoItems;
        return $this;
    }

    public function getCompletedCount()
    {
        return $this->_getVal(self::_COMPLETED_COUNT);
    }

    public function getDescription()
    {
        return $this->_getVal(self::_DESCRIPTION);
    }

    /**
     * @return \Sirprize\Basecamp\Id
     */
    public function getId()
    {
        return $this->_getVal(self::_ID);
    }

    /**
     * @return \Sirprize\Basecamp\Id|null (if this list is not assigned to a milestone)
     */
    public function getMilestoneId()
    {
        return $this->_getVal(self::_MILESTONE_ID);
    }

    public function getName()
    {
        return $this->_getVal(self::_NAME);
    }

    public function getPosition()
    {
        return $this->_getVal(self::_POSITION);
    }

    public function getIsPrivate()
    {
        return $this->_getVal(self::_PRIVATE);
    }

    /**
     * @return \Sirprize\Basecamp\Id
     */
    public function getProjectId()
    {
        return $this->_getVal(self::_PROJECT_ID);
    }

    public function getIsTracked()
    {
        return $this->_getVal(self::_TRACKED);
    }

    public function getUncompletedCount()
    {
        return $this->_getVal(self::_UNCOMPLETED_COUNT);
    }

    /**
     * @return \Sirprize\Basecamp\TodoItems\Collection
     */
    public function getTodoItems()
    {
        if($this->_todoItems === null)
        {
            $this->_todoItems = $this->_getService()->getTodoItemsInstance();
        }

        return $this->_todoItems;
    }

    public function getIsComplete()
    {
        return $this->_getVal(self::_COMPLETE);
    }

    /**
     * Load data returned from an api request
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return \Sirprize\Basecamp\TodoList
     */
    public function load(\SimpleXMLElement $xml, $force = false)
    {
        if($this->_loaded && !$force)
        {
            throw new Exception('entity has already been loaded');
        }

        $this->_loaded = true;
        $array = (array) $xml;

        if(isset($array[self::_TODO_ITEMS]))
        {
            $this->getTodoItems()->load($array[self::_TODO_ITEMS]);
            $this->_todoItemsLoaded = true;
        }

        $id = new Id($array[self::_ID]);

        $projectId = new Id($array[self::_PROJECT_ID]);

        $milestoneId
            = ($array[self::_MILESTONE_ID] != '')
            ? new Id($array[self::_MILESTONE_ID])
            : null
        ;

        $private = ($array[self::_PRIVATE] == 'true');
        $tracked = ($array[self::_TRACKED] == 'true');
        $complete = ($array[self::_COMPLETE] == 'true');

        $this->_data = array(
            self::_COMPLETED_COUNT => $array[self::_COMPLETED_COUNT],
            self::_DESCRIPTION => $array[self::_DESCRIPTION],
            self::_ID => $id,
            self::_MILESTONE_ID => $milestoneId,
            self::_NAME => $array[self::_NAME],
            self::_POSITION => $array[self::_POSITION],
            self::_PRIVATE => $private,
            self::_PROJECT_ID => $projectId,
            self::_TRACKED => $tracked,
            self::_UNCOMPLETED_COUNT => $array[self::_UNCOMPLETED_COUNT],
            #self::_TODO_ITEMS => $todoItems,
            self::_COMPLETE => $complete
        );

        return $this;
    }

    public function startTodoItems($force = false)
    {
        if(!$this->_loaded)
        {
            throw new Exception('call load() before '.__METHOD__);
        }

        if($this->_todoItemsLoaded && !$force)
        {
            return $this;
        }

        $this->getTodoItems()->startAllByTodoListId($this->getId(), $force);
        $this->_todoItemsLoaded = true;
        return $this;
    }

    public function findTodoItemByContent($content)
    {
        foreach($this->getTodoItems() as $todoItem)
        {
            if($content == $todoItem->getContent())
            {
                return $todoItem;
            }
        }

        return null;
    }

    /**
     * Create XML to create a new todoList
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return string
     */
    public function getXml()
    {
        if($this->getName() === null && $this->getTemplateId() == null)
        {
            throw new Exception('call setName() before '.__METHOD__);
        }

          $xml  = '<todo-list>';
        $xml .= '<name>'.htmlspecialchars($this->getName(), ENT_NOQUOTES).'</name>';
        $xml .= '<description>'.htmlspecialchars($this->getDescription(), ENT_NOQUOTES).'</description>';
        $xml .= '<private type="boolean">'.(($this->getIsPrivate()) ? 'true' : 'false').'</private>';

        if($this->getMilestoneId() !== null)
        {
            $xml .= '<milestone-id>'.$this->getMilestoneId().'</milestone-id>';
        }

        if($this->getTemplateId() !== null)
        {
            $xml .= '<todo-list-template-id>'.$this->getTemplateId().'</todo-list-template-id>';
        }
        $xml .= '</todo-list>';
        return $xml;
    }

    /**
     * Persist this todoList in storage
     *
     * Note: complete data (id etc) is not automatically loaded upon creation
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return boolean
     */
    public function create()
    {
        if($this->getProjectId() === null)
        {
            throw new Exception('set project-id before  '.__METHOD__);
        }

        $projectId = $this->getProjectId();
        $xml = $this->getXml();

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/projects/$projectId/todo_lists.xml")
                ->setAuth($this->_getService()->getUsername(), $this->_getService()->getPassword())
                ->setHeaders('Content-type', 'application/xml')
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
                $this->_onCreateError();

                throw new Exception($exception->getMessage());
            }
        }

        $this->_response = new Response($response);

        if($this->_response->isError())
        {
            // service error
            $this->_onCreateError();
            return false;
        }

        $this->_loaded = true;
        $this->_onCreateSuccess();
        return true;
    }

    /**
     * Update this todoList in storage
     *
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return boolean
     */
    public function update()
    {
        if(!$this->_loaded)
        {
            throw new Exception('call load() before '.__METHOD__);
        }

        $xml = $this->getXml();
        $id = $this->getId();

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/todo_lists/$id.xml")
                ->setAuth($this->_getService()->getUsername(), $this->_getService()->getPassword())
                ->setHeaders('Content-type', 'application/xml')
                ->setHeaders('Accept', 'application/xml')
                ->setRawData($xml)
                ->request('PUT')
            ;
        }
        catch(\Exception $exception)
        {
            try {
                // connection error - try again
                $response = $this->_getHttpClient()->request('PUT');
            }
            catch(\Exception $exception)
            {
                $this->_onUpdateError();

                throw new Exception($exception->getMessage());
            }
        }

        $this->_response = new Response($response);

        if($this->_response->isError())
        {
            // service error
            $this->_onUpdateError();
            return false;
        }

        $this->_onUpdateSuccess();
        return true;
    }

    /**
     * Delete this todoList from storage
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return boolean
     */
    public function delete()
    {
        $id = $this->getId();

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/todo_lists/$id.xml")
                ->setAuth($this->_getService()->getUsername(), $this->_getService()->getPassword())
                ->setHeaders('Content-type', 'application/xml')
                ->setHeaders('Accept', 'application/xml')
                ->request('DELETE')
            ;
        }
        catch(\Exception $exception)
        {
            try {
                // connection error - try again
                $response = $this->_getHttpClient()->request('DELETE');
            }
            catch(\Exception $exception)
            {
                $this->_onDeleteError();

                throw new Exception($exception->getMessage());
            }
        }

        $this->_response = new Response($response);

        if($this->_response->isError())
        {
            // service error
            $this->_onDeleteError();
            return false;
        }

        $this->_onDeleteSuccess();
        $this->_data = array();
        $this->_loaded = false;
        return true;
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

    protected function _getVal($name)
    {
        return (isset($this->_data[$name])) ? $this->_data[$name] : null;
    }

    protected function _onCreateSuccess()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onCreateSuccess($this);
        }
    }

    protected function _onUpdateSuccess()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onUpdateSuccess($this);
        }
    }

    protected function _onDeleteSuccess()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onDeleteSuccess($this);
        }
    }

    protected function _onCreateError()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onCreateError($this);
        }
    }

    protected function _onUpdateError()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onUpdateError($this);
        }
    }

    protected function _onDeleteError()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onDeleteError($this);
        }
    }

}