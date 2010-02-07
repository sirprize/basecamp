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
	
	
	public function __construct(array $config)
	{
		if(!isset($config['baseUri']))
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception("'baseUri' must be set in config");
		}
		
		if(!isset($config['username']))
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception("'username' must be set in config");
		}
		
		if(!isset($config['password']))
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception("'password' must be set in config");
		}
		
		$this->_baseUri = $config['baseUri'];
		$this->_username = $config['username'];
		$this->_password = $config['password'];
	}
	
	
	public function getBaseUri()
	{
		return $this->_baseUri;
	}
	
	
	public function getUsername()
	{
		return $this->_username;
	}
	
	
	public function getPassword()
	{
		return $this->_password;
	}
	
	/*
	public function isValidId($id)
	{
		return preg_match('/^[a-zA-Z0-9]+$/', $id);
	}
	
	
	public function isValidDate($date)
	{
		return preg_match('/^\d{4,4}-\d{2,2}-\d{2,2}$/', $date);
	}
	*/
	
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
	
	
	
	public function getProjectCollectionInstance()
	{
		require_once 'Sirprize/Basecamp/Project/Collection.php';
		$projects = new \Sirprize\Basecamp\Project\Collection();
		$projects
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $projects;
	}
	
	
	
	public function getPersonCollectionInstance()
	{
		require_once 'Sirprize/Basecamp/Person/Collection.php';
		$persons = new \Sirprize\Basecamp\Person\Collection();
		$persons
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $persons;
	}
	
	
	
	public function getMilestoneCollectionInstance()
	{
		require_once 'Sirprize/Basecamp/Milestone/Collection.php';
		$milestones = new \Sirprize\Basecamp\Milestone\Collection();
		$milestones
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $milestones;
	}
	
	
	
	public function getTodolistCollectionInstance()
	{
		require_once 'Sirprize/Basecamp/Todolist/Collection.php';
		$todolists = new \Sirprize\Basecamp\Todolist\Collection();
		$todolists
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $todolists;
	}
	
	
	
	public function getTodolistItemCollectionInstance()
	{
		require_once 'Sirprize/Basecamp/TodolistItem/Collection.php';
		$todolistitems = new \Sirprize\Basecamp\TodolistItem\Collection();
		$todolistitems
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $todolistitems;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	public function getPersonsInstance()
	{
		require_once 'Sirprize/Basecamp/Persons.php';
		$milestones = new \Sirprize\Basecamp\Persons();
		$milestones
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $milestones;
	}
	
	
	
	public function getProjectsInstance()
	{
		require_once 'Sirprize/Basecamp/Projects.php';
		$milestones = new \Sirprize\Basecamp\Projects();
		$milestones
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $milestones;
	}
	
	
	
	public function getTodoListsInstance()
	{
		require_once 'Sirprize/Basecamp/TodoLists.php';
		$milestones = new \Sirprize\Basecamp\TodoLists();
		$milestones
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $milestones;
	}
	
	
	
	public function getTodoListItemsInstance()
	{
		require_once 'Sirprize/Basecamp/TodoListItems.php';
		$milestones = new \Sirprize\Basecamp\TodoListItems();
		$milestones
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
		;
		return $milestones;
	}
	*/
}