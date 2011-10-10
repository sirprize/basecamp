<?php

/**
 * Basecamp API Wrapper for PHP 5.3+ 
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt
 *
 * @category   Sirprize
 * @package    Basecamp
 * @copyright  Copyright (c) 2010, Christian Hoegl, Switzerland (http://sirprize.me)
 * @license    MIT License
 */


namespace Sirprize\Basecamp;


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
		  throw new \Sirprize\Basecamp\Exception("you must set 'baseUri' in constructor config array");
		}
		
		if(isset($config['username']))
		{
			$this->_username = $config['username'];
		}
		else
		{
		  throw new \Sirprize\Basecamp\Exception("you must set 'username' in constructor config array");
		}
		
		if(isset($config['password']))
		{
			$this->_password = $config['password'];
		}
		else
		{
		  throw new \Sirprize\Basecamp\Exception("you must set 'password' in constructor config array");
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
	
	
	public function setHttpClient(\Zend_Http_Client $httpClient)
	{
		$this->_httpClient = $httpClient;
		return $this;
	}
	
	
	protected function _getHttpClient()
    {
        if($this->_httpClient === null)
		{
            $this->_httpClient = new \Zend_Http_Client();
        }

        return $this->_httpClient;
    }
	
	
	
	public function getProjectsInstance()
	{
		$projects = new \Sirprize\Basecamp\Project\Collection();
		$projects
			->setService($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $projects;
	}

	public function getTimeEntriesInstance()
	{
		$entries = new \Sirprize\Basecamp\TimeEntry\Collection();
		$entries
			->setService($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $entries;
	}

	
	
	
	public function getPersonsInstance()
	{
		$persons = new \Sirprize\Basecamp\Person\Collection();
		$persons
			->setService($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $persons;
	}
	
	
	
	public function getMilestonesInstance()
	{
		$milestones = new \Sirprize\Basecamp\Milestone\Collection();
		$milestones
			->setService($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $milestones;
	}
	
	public function getCommentsInstance()
	{
		$comments = new \Sirprize\Basecamp\Comment\Collection();
		$comments
			->setService($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $comments;
	}
	
	public function getAttachmentsInstance()
	{
		$attachments = new \Sirprize\Basecamp\Attachment\Collection();
		$attachments
			->setService($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $attachments;
	}
	
	public function getTodoListsInstance()
	{
		$todoLists = new \Sirprize\Basecamp\TodoList\Collection();
		$todoLists
			->setService($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $todoLists;
	}
	
	
	
	public function getTodoItemsInstance()
	{
		$todoListitems = new \Sirprize\Basecamp\TodoItem\Collection();
		$todoListitems
			->setService($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $todoListitems;
	}
	
	
	
	public function getSchemaInstance()
	{
		$schema = new \Sirprize\Basecamp\Schema();
		$schema->setService($this);
		return $schema;
	}

}