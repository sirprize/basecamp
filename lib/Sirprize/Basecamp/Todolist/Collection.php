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


namespace Sirprize\Basecamp\Todolist;


/**
 * Class to find and modify todolists
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Collection extends \SplObjectStorage
{
	
	
	const FILTER_ALL = 'all';
	const FILTER_PENDING = 'pending';
	const FILTER_FINISHED = 'finished';
	
	const _TODOLIST = 'todo-list';
	
	protected $_basecamp = null;
	protected $_httpClient = null;
	protected $_started = false;
	protected $_loaded = false;
	protected $_response = null;
	protected $_observers = array();
	
	
	
	
	public function setBasecamp(\Sirprize\Basecamp $basecamp)
	{
		$this->_basecamp = $basecamp;
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
	 * @return \Sirprize\Basecamp\Todolist\Collection
	 */
	public function attachObserver(\Sirprize\Basecamp\Todolist\Collection\Observer\Abstrakt $observer)
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
	 * @return \Sirprize\Basecamp\Todolist\Collection
	 */
	public function detachObserver(\Sirprize\Basecamp\Todolist\Collection\Observer\Abstrakt $observer)
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
	 * Instantiate a new todolist entity
	 *
	 * @return \Sirprize\Basecamp\Todolist\Entity
	 */
	public function getTodolistInstance()
	{
		require_once 'Sirprize/Basecamp/Todolist/Entity.php';
		$todolist = new \Sirprize\Basecamp\Todolist\Entity();
		$todolist
			->setHttpClient($this->_getHttpClient())
			->setBasecamp($this->_getBasecamp())
		;
		
		return $todolist;
	}
	
	
	
	/**
	 * Defined by \SplObjectStorage
	 *
	 * Add todolist entity
	 *
	 * @param \Sirprize\Basecamp\Todolist\Entity $todolist
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Todolist\Collection
	 */
	public function attach($todolist, $data = null)
	{
		if(!$todolist instanceof \Sirprize\Basecamp\Todolist\Entity)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('expecting an instance of \Sirprize\Basecamp\Todolist\Entity');
		}
		
		parent::attach($todolist);
		return $this;
	}
	
	
	
	
	/**
	 * Fetch todolists across projects (response includes list-items)
	 *
	 * @param string $responsibleParty resonsible-party-id|''(empty string, unassigned lists)|null(lists of current user)
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Todolist\Collection
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
				->setUri($this->_getBasecamp()->getBaseUri()."/todo_lists.xml$query")
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
				->request('GET')
			;
		}
		catch(\Exception $exception)
		{
			// connection error
			$this->_onStartError();
			
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception($exception->getMessage());
		}
		
		require_once 'Sirprize/Basecamp/Response.php';
		$this->_response = new \Sirprize\Basecamp\Response($response);
		
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
	 * Fetch all todolists in specified project (response doesn't include list-items)
	 *
	 * @param string $filter all|pending|finished
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Todolist\Collection
	 */
	public function startAllByProjectId(\Sirprize\Basecamp\Id $projectId, $filter = null)
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
				->setUri($this->_getBasecamp()->getBaseUri()."/projects/$projectId/todo_lists.xml")
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
				->request('GET')
			;
		}
		catch(\Exception $exception)
		{
			// connection error
			$this->_onStartError();
			
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception($exception->getMessage());
		}
		
		require_once 'Sirprize/Basecamp/Response.php';
		$this->_response = new \Sirprize\Basecamp\Response($response);
		
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
	 * Fetch one todolist by id (response includes list-items)
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return null|\Sirprize\Basecamp\Todolist\Entity
	 */
	public function startById(\Sirprize\Basecamp\Id $id)
	{
		if($this->_started)
		{
			return $this;
		}
		
		$this->_started = true;
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/todo_lists/$id.xml")
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
				->request('GET')
			;
		}
		catch(\Exception $exception)
		{
			// connection error
			$this->_onStartError();
			
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception($exception->getMessage());
		}
		
		require_once 'Sirprize/Basecamp/Response.php';
		$this->_response = new \Sirprize\Basecamp\Response($response);
		
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
	 * Instantiate todolist objects with api response data
	 *
	 * @return \Sirprize\Basecamp\Todolist\Collection
	 */
	public function load(\SimpleXMLElement $xml)
	{
		if($this->_loaded)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('collection has already been loaded');
		}
		
		$this->_loaded = true;
		
		if(isset($xml->id))
		{
			// request for a single entity
			$todolist = $this->getTodolistInstance();
			$todolist->load($xml);
			$this->attach($todolist);
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
			$todolist = $this->getTodolistInstance();
			$todolist->load($array[self::_TODOLIST]);
			$this->attach($todolist);
			return $this;
		}
		
		foreach($array[self::_TODOLIST] as $row)
		{
			// list request - 2 or more items in response
			$todolist = $this->getTodolistInstance();
			$todolist->load($row);
			$this->attach($todolist);
		}
		
		return $this;
	}
	
	
	
	protected function _getBasecamp()
	{
		if($this->_basecamp === null)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call setBasecamp() before '.__METHOD__);
		}
		
		return $this->_basecamp;
	}
	
	
	protected function _getHttpClient()
	{
		if($this->_httpClient === null)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call setHttpClient() before '.__METHOD__);
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