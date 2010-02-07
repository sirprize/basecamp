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


namespace Sirprize\Basecamp\Project;


/**
 * Class to find and modify projects
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Collection extends \SplObjectStorage
{
	
	
	const FIND_COMPLETED = 'completed';
	const FIND_UPCOMING = 'upcoming';
	const FIND_LATE = 'late';
	const FIND_ALL = 'all';
	const _PROJECT = 'project';
	
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
	 * @return \Sirprize\Basecamp\Project\Collection
	 */
	public function attachObserver(\Sirprize\Basecamp\Project\Collection\Observer\Abstrakt $observer)
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
	 * @return \Sirprize\Basecamp\Project\Collection
	 */
	public function detachObserver(\Sirprize\Basecamp\Project\Collection\Observer\Abstrakt $observer)
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
	 * Instantiate a new project entity
	 *
	 * @return \Sirprize\Basecamp\Project\Entity
	 */
	public function getProjectInstance()
	{
		require_once 'Sirprize/Basecamp/Project/Entity.php';
		$project = new \Sirprize\Basecamp\Project\Entity();
		$project
			->setHttpClient($this->_getHttpClient())
			->setBasecamp($this->_getBasecamp())
		;
		
		return $project;
	}
	
	
	
	/**
	 * Defined by \SplObjectStorage
	 *
	 * Add project entity
	 *
	 * @param \Sirprize\Basecamp\Project\Entity $project
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Project\Collection
	 */
	public function attach($project, $data = null)
	{
		if(!$project instanceof \Sirprize\Basecamp\Project\Entity)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('expecting an instance of \Sirprize\Basecamp\Project\Entity');
		}
		
		parent::attach($project);
		return $this;
	}
	
	
	
	
	/**
	 * Fetch project by id
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Project\Collection
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
				->setUri($this->_getBasecamp()->getBaseUri()."/projects/$id.xml")
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
	 * Fetch all projects
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Project\Collection
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
				->setUri($this->_getBasecamp()->getBaseUri()."/projects.xml")
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
	 * Instantiate project objects from api response and populate this collection
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
			$project = $this->getProjectInstance();
			$project->load($response->getData());
			$this->attach($project);
			return $response;
		}
		
		$data = (array) $response->getData();
		
		if(!isset($data[self::_PROJECT]))
		{
			// list request - 0 items in response
			return $response;
		}
		
		if(isset($data[self::_PROJECT]->id))
		{
			// list request - 1 item in response
			$project = $this->getProjectInstance();
			$project->load($data[self::_PROJECT]);
			$this->attach($project);
			return $response;
		}
		
		foreach($data[self::_PROJECT] as $row)
		{
			// list request - 2 or more items in response
			$project = $this->getProjectInstance();
			$project->load($row);
			$this->attach($project);
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