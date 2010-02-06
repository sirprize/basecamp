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


namespace Sirprize\Basecamp\Milestone;


/**
 * Class to find and modify milestones
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
	const _MILESTONE = 'milestone';
	
	protected $_basecamp = null;
	protected $_httpClient = null;
	protected $_started = false;
	protected $_response = null;
	protected $_observers = array();
	
	
	/**
	 * Attach observer object
	 *
	 * @return \Sirprize\Basecamp\Milestone\Collection
	 */
	public function attachObserver(\Sirprize\Basecamp\Milestone\Collection\Observer\Interfaze $observer)
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
	 * @return \Sirprize\Basecamp\Milestone\Collection
	 */
	public function detachObserver(\Sirprize\Basecamp\Milestone\Collection\Observer\Interfaze $observer)
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
	
	
	protected function _onCreateSuccess()
	{
		foreach($this->_observers as $observer)
		{
			$observer->onCreateSuccess($this);
		}
	}
	
	
	protected function _onStartSuccess()
	{
		foreach($this->_observers as $observer)
		{
			$observer->onStartSuccess($this);
		}
	}
	
	
	protected function _onCreateError()
	{
		foreach($this->_observers as $observer)
		{
			$observer->onCreateError($this);
		}
	}
	
	
	protected function _onStartError()
	{
		foreach($this->_observers as $observer)
		{
			$observer->onStartError($this);
		}
	}
	
	
	
	public function setBasecamp(\Sirprize\Basecamp $basecamp)
	{
		$this->_basecamp = $basecamp;
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
	
	
	
	public function setHttpClient(\Zend_Http_Client $httpClient)
	{
		$this->_httpClient = $httpClient;
		return $this;
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
	
	
	
	public function getMilestoneInstance()
	{
		require_once 'Sirprize/Basecamp/Milestone/Entity.php';
		$milestone = new \Sirprize\Basecamp\Milestone\Entity();
		$milestone
			->setHttpClient($this->_getHttpClient())
			->setBasecamp($this->_getBasecamp())
		;
		
		return $milestone;
	}
	
	
	
	/**
	 * Add milestone to batch-persist later by create()
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Milestone\Collection
	 */
	public function attach($milestone, $data = null)
	{
		if(!$milestone instanceof \Sirprize\Basecamp\Milestone\Entity)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('expecting an instance of \Sirprize\Basecamp\Exception');
		}
		
		parent::attach($milestone);
		return $this;
	}
	
	
	
	/**
	 * Persist milestone objects that have previously been added by attach()
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return int Number of new milestones that have been created
	 */
	public function create(\Sirprize\Basecamp\Id $projectId)
	{
		if($this->_started)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('this collection is already persisted in storage');
		}
		
		$xml = '<request>';
		
		foreach($this as $milestone)
		{
			$xml .= $milestone->getCreateXml();
		}
		
		$xml .= '</request>';
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/projects/$projectId/milestones/create")
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
				->setHeaders('Content-Type', 'application/xml')
				->setHeaders('Accept', 'application/xml')
				->setRawData($xml)
				->request('POST')
			;
		}
		catch(\Exception $exception)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception($exception->getMessage());
		}
		
		require_once 'Sirprize/Basecamp/Response.php';
		$this->_response = new \Sirprize\Basecamp\Response($response);
		
		if($this->_response->isError())
		{
			foreach($this as $milestone)
			{
				$milestone->onCreateError();
			}
			
			$this->_onCreateError();
			return 0;
		}
		
		$data = (array) $this->_response->getData();
		$i = 0;
		
		foreach($this as $milestone)
		{
			// load full data into milestone
			$milestone->onCreateLoad($data[self::_MILESTONE][$i++]);
		}
		
		$this->_started = true;
		$this->_onCreateSuccess();
		return $this->count();
	}
	
	
	
	/**
	 * Fetch milestones for a given project
	 *
	 * @param status completed|upcoming|late|all
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Milestone\Collection
	 */
	public function startByProjectId(\Sirprize\Basecamp\Id $projectId, $status = null)
	{
		if($this->_started)
		{
			return $this;
		}
		
		$this->_started = true;
		
		switch($status)
		{
			case self::FIND_COMPLETED: $query = '?find='.self::FIND_COMPLETED; break;
			case self::FIND_UPCOMING: $query = '?find='.self::FIND_UPCOMING; break;
			case self::FIND_LATE: $query = '?find='.self::FIND_LATE; break;
			default: $query = '?find=all';
		}
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/projects/$projectId/milestones/list.xml$query")
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
				->request('GET')
			;
		}
		catch(\Exception $exception)
		{
			$this->_onStartError();
			
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception($exception->getMessage());
		}
		
		$this->_response = $this->_handleResponse($response);
		$this->_onStartSuccess();
		return $this;
	}
	
	
	
	/**
	 * Instantiate milestone objects from api response and populate this collection
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
			// single request
			$milestone = $this->getMilestoneInstance();
			$milestone->load($response->getData());
			$this->attach($milestone);
			return $response;
		}
		
		$data = (array) $response->getData();
		
		if(isset($data[self::_MILESTONE]->id))
		{
			// list request - only 1 item in response
			$milestone = $this->getMilestoneInstance();
			$milestone->load($data[self::_MILESTONE]);
			$this->attach($milestone);
			return $response;
		}
		
		if(!isset($data[self::_MILESTONE]))
		{
			return $response;
		}
		
		foreach($data[self::_MILESTONE] as $row)
		{
			// list request
			$milestone = $this->getMilestoneInstance();
			$milestone->load($row);
			$this->attach($milestone);
		}
		
		return $response;
	}
	
	
	public function getResponse()
	{
		return $this->_response;
	}
	
}