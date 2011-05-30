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


namespace Sirprize\Basecamp\TimeEntry;


/**
 * Encapsulate a set of persisted TimeEntry objects and the operations performed over them
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Collection extends \SplObjectStorage
{
	
	const _TIME_ENTRY = 'time-entry';
	
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
	 * @return \Sirprize\Basecamp\TimeEntry\Collection
	 */
	public function attachObserver(\Sirprize\Basecamp\TimeEntry\Collection\Observer\Abstrakt $observer)
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
	 * @return \Sirprize\Basecamp\TimeEntry\Collection
	 */
	public function detachObserver(\Sirprize\Basecamp\TimeEntry\Collection\Observer\Abstrakt $observer)
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
	 * Instantiate a new TimeEntry entity
	 *
	 * @return \Sirprize\Basecamp\TimeEntry\Entity
	 */
	public function getTimeEntryInstance()
	{
		require_once 'Sirprize/Basecamp/TimeEntry/Entity.php';
		$entry = new \Sirprize\Basecamp\TimeEntry\Entity();
		$entry
			->setHttpClient($this->_getHttpClient())
			->setBasecamp($this->_getBasecamp())
		;
		
		return $entry;
	}
	
	
	
	/**
	 * Defined by \SplObjectStorage
	 *
	 * Add TimeEntry entity
	 *
	 * @param \Sirprize\Basecamp\TimeEntry\Entity $entry
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\TimeEntry\Collection
	 */
	public function attach($entry, $data = null)
	{
		if(!$entry instanceof \Sirprize\Basecamp\TimeEntry\Entity)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('expecting an instance of \Sirprize\Basecamp\TimeEntry\Entity');
		}
		
		parent::attach($entry);
		return $this;
	}
	
	
	/**
	 * Fetch timeentries by project id
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return null|\Sirprize\Basecamp\TimeEntry\Collection
	 */
	public function startAllByProjectId(\Sirprize\Basecamp\Id $id)
	{
		if($this->_started)
		{
			return $this;
		}
		
		$this->_started = true;
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/projects/$id/time_entries.xml")
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
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
			
				require_once 'Sirprize/Basecamp/Exception.php';
				throw new \Sirprize\Basecamp\Exception($exception->getMessage());
			}
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
	 * Fetch timeentry by id
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return null|\Sirprize\Basecamp\TimeEntry\Entity
	 */
	public function startById(\Sirprize\Basecamp\Id $id)
	{
		if($this->_started)
		{
			return $this;
		}
		
		$this->_started = true;
		$response = null;
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/time_entries/$id.xml")
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
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
			
				require_once 'Sirprize/Basecamp/Exception.php';
				throw new \Sirprize\Basecamp\Exception($exception->getMessage());
			}
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
	 * Instantiate entry objects with api response data
	 *
	 * @return \Sirprize\Basecamp\TimeEntry\Collection
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
			$entry = $this->getTimeEntryInstance();
			$entry->load($xml);
			$this->attach($entry);
			return $this;
		}
		
		$array = (array) $xml;
		
		if(!isset($array[self::_TIME_ENTRY]))
		{
			// list request - 0 items in response
			return $this;
		}
		
		if(isset($array[self::_TIME_ENTRY]->id))
		{
			// list request - 1 item in response
			$entry = $this->getTimeEntryInstance();
			$entry->load($array[self::_TIME_ENTRY]);
			$this->attach($entry);
			return $this;
		}
		
		foreach($array[self::_TIME_ENTRY] as $row)
		{
			// list request - 2 or more items in response
			$entry = $this->getTimeEntryInstance();
			$entry->load($row);
			$this->attach($entry);
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