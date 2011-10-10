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


namespace Sirprize\Basecamp\Comment;


/**
 * Encapsulate a set of persisted comment objects and the operations performed over them
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Collection extends \SplObjectStorage
{
	
	
	const RESOURCE_MESSAGE = 'posts';
	const RESOURCE_TODO_ITEM = 'todo_items';
	const RESOURCE_MILESTONE = 'milestones';
	const _COMMENT = 'comment';
	
	protected $_service = null;
	protected $_httpClient = null;
	protected $_started = false;
	protected $_loaded = false;
	protected $_response = null;
	protected $_observers = array();
	
	
	
	
	public function setService(\Sirprize\Basecamp\Service $service)
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
	 * @return \Sirprize\Basecamp\Comment\Collection
	 */
	public function attachObserver(\Sirprize\Basecamp\Comment\Collection\Observer\Abstrakt $observer)
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
	 * @return \Sirprize\Basecamp\Comment\Collection
	 */
	public function detachObserver(\Sirprize\Basecamp\Comment\Collection\Observer\Abstrakt $observer)
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
	 * Instantiate a new comment entity
	 *
	 * @return \Sirprize\Basecamp\Comment\Entity
	 */
	public function getCommentInstance()
	{
		$comment = new \Sirprize\Basecamp\Comment\Entity();
		$comment
			->setHttpClient($this->_getHttpClient())
			->setService($this->_getService())
		;
		
		return $comment;
	}
	
	
	
	/**
	 * Defined by \SplObjectStorage
	 *
	 * Add comment entity to batch-persist later by create()
	 *
	 * @param \Sirprize\Basecamp\Comment\Entity $comment
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Comment\Collection
	 */
	public function attach($comment, $data = null)
	{
		if(!$comment instanceof \Sirprize\Basecamp\Comment\Entity)
		{
			throw new \Sirprize\Basecamp\Exception('expecting an instance of \Sirprize\Basecamp\Comment\Entity');
		}
		
		parent::attach($comment);
		return $this;
	}
	
	
	
	/**
	 * Fetch comments for a resource
	 *
	 * @param \Sirprize\Basecamp\Id $resourceId completed|upcoming|late|all
	 * @param string $resource todo_items|posts|milestones
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Comment\Collection
	 */
	public function startAllByResourceId(\Sirprize\Basecamp\Id $resourceId, $resource = null)
	{
		if($this->_started)
		{
			return $this;
		}
		
		$this->_started = true;
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getService()->getBaseUri()."/$resource/$resourceId/comments.xml")
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
				// connection error
				$this->_onStartError();
			
				throw new \Sirprize\Basecamp\Exception($exception->getMessage());
			}
		}
		
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
	 * Fetch one todoList by id (response includes list-items)
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return null|\Sirprize\Basecamp\TodoList\Entity
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
				->setUri($this->_getService()->getBaseUri()."/comments/$id.xml")
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
			
				throw new \Sirprize\Basecamp\Exception($exception->getMessage());
			}
		}
		
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
	 * Instantiate comment objects with api response data
	 *
	 * @return \Sirprize\Basecamp\Comment\Collection
	 */
	public function load(\SimpleXMLElement $xml)
	{
		if($this->_loaded)
		{
			throw new \Sirprize\Basecamp\Exception('collection has already been loaded');
		}
		
		$this->_loaded = true;
		
		if(isset($xml->id))
		{
			// request for a single entity
			$comment = $this->getCommentInstance();
			$comment->load($xml);
			$this->attach($comment);
			return $this;
		}
		
		$array = (array) $xml;
		
		if(!isset($array[self::_COMMENT]))
		{
			// list request - 0 items in response
			return $this;
		}
		
		if(isset($array[self::_COMMENT]->id))
		{
			// list request - 1 item in response
			$comment = $this->getCommentInstance();
			$comment->load($array[self::_COMMENT]);
			$this->attach($comment);
			return $this;
		}
		
		foreach($array[self::_COMMENT] as $row)
		{
			// list request - 2 or more items in response
			$comment = $this->getCommentInstance();
			$comment->load($row);
			$this->attach($comment);
		}
		
		return $this;
	}
	
	
	
	protected function _getService()
	{
		if($this->_service === null)
		{
			throw new \Sirprize\Basecamp\Exception('call setService() before '.__METHOD__);
		}
		
		return $this->_service;
	}
	
	
	protected function _getHttpClient()
	{
		if($this->_httpClient === null)
		{
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
