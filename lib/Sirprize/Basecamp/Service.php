<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp;

use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\Schema;
use Sirprize\Basecamp\Project\Collection as ProjectCollection;
use Sirprize\Basecamp\TimeEntry\Collection as TimeEntryCollection;
use Sirprize\Basecamp\Person\Collection as PersonCollection;
use Sirprize\Basecamp\Milestone\Collection as MilestoneCollection;
use Sirprize\Basecamp\Comment\Collection as CommentCollection;
use Sirprize\Basecamp\Attachment\Collection as AttachmentCollection;
use Sirprize\Basecamp\TodoList\Collection as TodoListCollection;
use Sirprize\Basecamp\TodoItem\Collection as TodoItemCollection;

class Service
{

    protected $_baseUri = null;
    protected $_username = null;
    protected $_password = null;
    protected $_httpClient = null;

    public function __construct(array $config = array())
    {
        if(isset($config['baseUri']))
        {
            $this->_baseUri = $config['baseUri'];
        }
        else
        {
          throw new Exception("you must set 'baseUri' in constructor config array");
        }

        if(isset($config['username']))
        {
            $this->_username = $config['username'];
        }
        else
        {
          throw new Exception("you must set 'username' in constructor config array");
        }

        if(isset($config['password']))
        {
            $this->_password = $config['password'];
        }
        else
        {
          throw new Exception("you must set 'password' in constructor config array");
        }
    }

    public function setBaseUri($baseUri)
    {
        $this->_baseUri = $baseUri;
        return $this;
    }

    public function getBaseUri()
    {
        return $this->_baseUri;
    }

    public function setUsername($username)
    {
        $this->_username = $username;
        return $this;
    }

    public function getUsername()
    {
        return $this->_username;
    }

    public function setPassword($password)
    {
        $this->_password = $password;
        return $this;
    }

    public function getPassword()
    {
        return $this->_password;
    }

    public function setHttpClient(\Zend\Http\Client $httpClient)
    {
        $this->_httpClient = $httpClient;
        return $this;
    }

    protected function _getHttpClient()
    {
        if($this->_httpClient === null)
        {
            $this->_httpClient = new \Zend\Http\Client();
        }

        return $this->_httpClient;
    }

    public function getProjectsInstance()
    {
        $projects = new ProjectCollection();
        $projects
            ->setService($this)
            ->setHttpClient($this->_getHttpClient())
        ;
        return $projects;
    }

    public function getTimeEntriesInstance()
    {
        $entries = new TimeEntryCollection();
        $entries
            ->setService($this)
            ->setHttpClient($this->_getHttpClient())
        ;
        return $entries;
    }

    public function getPersonsInstance()
    {
        $persons = new PersonCollection();
        $persons
            ->setService($this)
            ->setHttpClient($this->_getHttpClient())
        ;
        return $persons;
    }

    public function getMilestonesInstance()
    {
        $milestones = new MilestoneCollection();
        $milestones
            ->setService($this)
            ->setHttpClient($this->_getHttpClient())
        ;
        return $milestones;
    }

    public function getCommentsInstance()
    {
        $comments = new CommentCollection();
        $comments
            ->setService($this)
            ->setHttpClient($this->_getHttpClient())
        ;
        return $comments;
    }

    public function getAttachmentsInstance()
    {
        $attachments = new AttachmentCollection();
        $attachments
            ->setService($this)
            ->setHttpClient($this->_getHttpClient())
        ;
        return $attachments;
    }

    public function getTodoListsInstance()
    {
        $todoLists = new TodoListCollection();
        $todoLists
            ->setService($this)
            ->setHttpClient($this->_getHttpClient())
        ;
        return $todoLists;
    }

    public function getTodoItemsInstance()
    {
        $todoListitems = new TodoItemCollection();
        $todoListitems
            ->setService($this)
            ->setHttpClient($this->_getHttpClient())
        ;
        return $todoListitems;
    }

    public function getSchemaInstance()
    {
        $schema = new Schema();
        $schema->setService($this);
        return $schema;
    }

}