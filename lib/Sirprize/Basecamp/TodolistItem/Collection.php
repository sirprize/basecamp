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


namespace Sirprize\Basecamp\TodolistItem;


/**
 * Class to find and modify todolistItems
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Collection extends \SplObjectStorage
{
	
	
	const _TODO_ITEM = 'todo-item';
	
	protected $_basecamp = null;
	protected $_httpClient = null;
	protected $_started = false;
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
	 * @return \Sirprize\Basecamp\TodolistItem\Collection
	 */
	public function attachObserver(\Sirprize\Basecamp\TodolistItem\Collection\Observer\Abstrakt $observer)
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
	 * @return \Sirprize\Basecamp\TodolistItem\Collection
	 */
	public function detachObserver(\Sirprize\Basecamp\TodolistItem\Collection\Observer\Abstrakt $observer)
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
	 * Instantiate a new todolistItem entity
	 *
	 * @return \Sirprize\Basecamp\TodolistItem\Entity
	 */
	public function getTodolistItemInstance()
	{
		require_once 'Sirprize/Basecamp/TodolistItem/Entity.php';
		$todolistItem = new \Sirprize\Basecamp\TodolistItem\Entity();
		$todolistItem
			->setHttpClient($this->_getHttpClient())
			->setBasecamp($this->_getBasecamp())
		;
		
		return $todolistItem;
	}
	
	
	
	/**
	 * Defined by \SplObjectStorage
	 *
	 * Add todolistItem entity
	 *
	 * @param \Sirprize\Basecamp\TodolistItem\Entity $todolistItem
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\TodolistItem\Collection
	 */
	public function attach($todolistItem, $data = null)
	{
		if(!$todolistItem instanceof \Sirprize\Basecamp\TodolistItem\Entity)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('expecting an instance of \Sirprize\Basecamp\TodolistItem\Entity');
		}
		
		parent::attach($todolistItem);
		return $this;
	}
	
	
	
	
	/**
	 * Fetch todolistItems for a given project
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\TodolistItem\Collection
	 */
	public function startByTodolistId(\Sirprize\Basecamp\Id $todolistId)
	{
		if($this->_started)
		{
			return $this;
		}
		
		$this->_started = true;
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/todo_lists/$todolistId/todo_items.xml")
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
				->request('GET')
			;
			#print $response->getBody(); exit;
		}
		catch(\Exception $exception)
		{
			// connection error
			$this->_onStartError();
			
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception($exception->getMessage());
		}
		
		$this->_response = $this->_handleResponse($response);
		
		if($this->_response->isError())
		{
			// service error
			$this->_onStartError();
			return $this;
		}
		
		$this->_onStartSuccess();
		return $this;
	}
	
	
	
	
	/**
	 * Instantiate todolistItem objects from api response and populate this collection
	 *
	 * @return \Sirprize\Basecamp\Response
	 */
	protected function _handleResponse(\Zend_Http_Response $response)
	{
		require_once 'Sirprize/Basecamp/Response.php';
		$response = new \Sirprize\Basecamp\Response($response);
		
		if($response->isError())
		{
			return $response;
		}
		
		if(isset($response->getData()->id))
		{
			// request for a single entity (not supported on todolistItems)
			$todolistItem = $this->getTodolistItemInstance();
			$todolistItem->load($response->getData());
			$this->attach($todolistItem);
			return $response;
		}
		
		$data = (array) $response->getData();
		
		if(!isset($data[self::_TODO_ITEM]))
		{
			// list request - 0 items in response
			return $response;
		}
		
		if(isset($data[self::_TODO_ITEM]->id))
		{
			// list request - 1 item in response
			$todolistItem = $this->getTodolistItemInstance();
			$todolistItem->load($data[self::_TODO_ITEM]);
			$this->attach($todolistItem);
			return $response;
		}
		
		foreach($data[self::_TODO_ITEM] as $row)
		{
			// list request - 2 or more items in response
			$todolistItem = $this->getTodolistItemInstance();
			$todolistItem->load($row);
			$this->attach($todolistItem);
		}
		
		return $response;
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