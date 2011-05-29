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


namespace Sirprize;


class Basecamp
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
		
		if(isset($config['username']))
		{
			$this->_username = $config['username'];
		}
		
		if(isset($config['password']))
		{
			$this->_password = $config['password'];
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
			require_once 'Zend/Http/Client.php';
            $this->_httpClient = new \Zend_Http_Client();
        }

        return $this->_httpClient;
    }
	
	
	
	public function getProjectsInstance()
	{
		require_once 'Sirprize/Basecamp/Project/Collection.php';
		$projects = new \Sirprize\Basecamp\Project\Collection();
		$projects
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $projects;
	}

	public function getTimeEntriesInstance()
	{
		require_once 'Sirprize/Basecamp/TimeEntry/Collection.php';
		$entries = new \Sirprize\Basecamp\TimeEntry\Collection();
		$entries
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $entries;
	}

	
	
	
	public function getPersonsInstance()
	{
		require_once 'Sirprize/Basecamp/Person/Collection.php';
		$persons = new \Sirprize\Basecamp\Person\Collection();
		$persons
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $persons;
	}
	
	
	
	public function getMilestonesInstance()
	{
		require_once 'Sirprize/Basecamp/Milestone/Collection.php';
		$milestones = new \Sirprize\Basecamp\Milestone\Collection();
		$milestones
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $milestones;
	}
	
	public function getCommentsInstance()
	{
		require_once 'Sirprize/Basecamp/Comment/Collection.php';
		$comments = new \Sirprize\Basecamp\Comment\Collection();
		$comments
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $comments;
	}
	
	public function getAttachmentsInstance()
	{
		require_once 'Sirprize/Basecamp/Attachment/Collection.php';
		$attachments = new \Sirprize\Basecamp\Attachment\Collection();
		$attachments
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $attachments;
	}
	
	public function getTodoListsInstance()
	{
		require_once 'Sirprize/Basecamp/TodoList/Collection.php';
		$todoLists = new \Sirprize\Basecamp\TodoList\Collection();
		$todoLists
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $todoLists;
	}
	
	
	
	public function getTodoItemsInstance()
	{
		require_once 'Sirprize/Basecamp/TodoItem/Collection.php';
		$todoListitems = new \Sirprize\Basecamp\TodoItem\Collection();
		$todoListitems
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $todoListitems;
	}
	
	
	
	public function getSchemaInstance()
	{
		require_once 'Sirprize/Basecamp/Schema.php';
		$schema = new \Sirprize\Basecamp\Schema();
		$schema->setBasecamp($this);
		return $schema;
	}

}