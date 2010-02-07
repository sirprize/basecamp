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


namespace Sirprize\Basecamp\Person;


/**
 * Class to find and modify persons
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Collection extends \SplObjectStorage
{
	
	
	const _PERSON = 'person';
	
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
	 * @return \Sirprize\Basecamp\Person\Collection
	 */
	public function attachObserver(\Sirprize\Basecamp\Person\Collection\Observer\Abstrakt $observer)
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
	 * @return \Sirprize\Basecamp\Person\Collection
	 */
	public function detachObserver(\Sirprize\Basecamp\Person\Collection\Observer\Abstrakt $observer)
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
	 * Instantiate a new person entity
	 *
	 * @return \Sirprize\Basecamp\Person\Entity
	 */
	public function getPersonInstance()
	{
		require_once 'Sirprize/Basecamp/Person/Entity.php';
		$person = new \Sirprize\Basecamp\Person\Entity();
		$person
			->setHttpClient($this->_getHttpClient())
			->setBasecamp($this->_getBasecamp())
		;
		
		return $person;
	}
	
	
	
	/**
	 * Defined by \SplObjectStorage
	 *
	 * Add person entity
	 *
	 * @param \Sirprize\Basecamp\Person\Entity $person
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Person\Collection
	 */
	public function attach($person, $data = null)
	{
		if(!$person instanceof \Sirprize\Basecamp\Person\Entity)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('expecting an instance of \Sirprize\Basecamp\Person\Entity');
		}
		
		parent::attach($person);
		return $this;
	}
	
	
	
	
	/**
	 * Fetch person by id
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Person\Collection
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
				->setUri($this->_getBasecamp()->getBaseUri()."/people/$id.xml")
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
				->setHeaders('Content-Type', 'application/xml')
				->setHeaders('Accept', 'application/xml')
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
	 * Fetch everbody
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Person\Collection
	 */
	public function startAll()
	{
		if($this->_started)
		{
			return $this;
		}
		
		$this->_started = true;
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/people.xml")
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
	 * Instantiate person objects from api response and populate this collection
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
			// request for a single entity
			$person = $this->getPersonInstance();
			$person->load($response->getData());
			$this->attach($person);
			return $response;
		}
		
		$data = (array) $response->getData();
		
		if(!isset($data[self::_PERSON]))
		{
			// list request - 0 items in response
			return $response;
		}
		
		if(isset($data[self::_PERSON]->id))
		{
			// list request - 1 item in response
			$person = $this->getPersonInstance();
			$person->load($data[self::_PERSON]);
			$this->attach($person);
			return $response;
		}
		
		foreach($data[self::_PERSON] as $row)
		{
			// list request - 2 or more items in response
			$person = $this->getPersonInstance();
			$person->load($row);
			$this->attach($person);
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