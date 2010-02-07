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
 * Class to represent and modify a todolist
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Entity
{
	
	
	const _COMPLETED_COUNT = 'completed-count';
	const _DESCRIPTION = 'description';
	const _ID = 'id';
	const _MILESTONE_ID = 'milestone-id';
	const _NAME = 'name';
	const _POSITION = 'position';
	const _PRIVATE = 'private';
	const _PROJECT_ID = 'project-id';
	const _TRACKED = 'tracked';
	const _UNCOMPLETED_COUNT = 'uncompleted-count';
	const _TODO_ITEMS = 'todo-items';
	const _COMPLETE = 'complete';
	
	
	protected $_basecamp = null;
	protected $_httpClient = null;
	protected $_data = array();
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
	 * @return \Sirprize\Basecamp\Todolist
	 */
	public function attachObserver(\Sirprize\Basecamp\Todolist\Entity\Observer\Abstrakt $observer)
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
	 * @return \Sirprize\Basecamp\Todolist
	 */
	public function detachObserver(\Sirprize\Basecamp\Todolist\Entity\Observer\Abstrakt $observer)
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
	
	
	
	
	public function setName($name)
	{
		$this->_data[self::_NAME] = $name;
		return $this;
	}
	
	
	public function setProjectId(\Sirprize\Basecamp\Id $projectId)
	{
		$this->_data[self::_PROJECT_ID] = $projectId;
		return $this;
	}
	
	
	public function setDescription($description)
	{
		$this->_data[self::_DESCRIPTION] = $description;
		return $this;
	}
	
	
	public function setMilestoneId(\Sirprize\Basecamp\Id $milestoneId)
	{
		$this->_data[self::_MILESTONE_ID] = $milestoneId;
		return $this;
	}
	
	
	public function setIsPrivate($private)
	{
		$this->_data[self::_PRIVATE] = $private;
		return $this;
	}
	
	
	public function setIsTracked($tracked)
	{
		$this->_data[self::_TRACKED] = $tracked;
		return $this;
	}
	
	
	
	
	
	public function getCompletedCount()
	{
		return $this->_getVal(self::_COMPLETED_COUNT);
	}
	
	
	public function getDescription()
	{
		return $this->_getVal(self::_DESCRIPTION);
	}
	
	/**
	 * @return \Sirprize\Basecamp\Id
	 */
	public function getId()
	{
		return $this->_getVal(self::_ID);
	}
	
	/**
	 * @return \Sirprize\Basecamp\Id|null (if this list is not assigned to a milestone)
	 */
	public function getMilestoneId()
	{
		return $this->_getVal(self::_MILESTONE_ID);
	}
	
	
	public function getName()
	{
		return $this->_getVal(self::_NAME);
	}
	
	
	public function getPosition()
	{
		return $this->_getVal(self::_POSITION);
	}
	
	
	public function getIsPrivate()
	{
		return $this->_getVal(self::_PRIVATE);
	}
	
	/**
	 * @return \Sirprize\Basecamp\Id
	 */
	public function getProjectId()
	{
		return $this->_getVal(self::_PROJECT_ID);
	}
	
	
	public function getIsTracked()
	{
		return $this->_getVal(self::_TRACKED);
	}
	
	
	public function getUncompletedCount()
	{
		return $this->_getVal(self::_UNCOMPLETED_COUNT);
	}
	
	
	public function getTodoItems()
	{
		return $this->_getVal(self::_TODO_ITEMS);
	}
	
	
	public function getIsComplete()
	{
		return $this->_getVal(self::_COMPLETE);
	}
	
	
	
	
	
	/**
	 * Load data returned from an api request
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Todolist
	 */
	public function load(\SimpleXMLElement $data, $force = false)
	{
		if($this->_loaded && !$force)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('entity has already been loaded');
		}
		
		#print_r($data);
		$this->_loaded = true;
		$data = (array) $data;
		
		require_once 'Sirprize/Basecamp/Id.php';
		$id = new \Sirprize\Basecamp\Id($data[self::_ID]);
		
		require_once 'Sirprize/Basecamp/Id.php';
		$projectId = new \Sirprize\Basecamp\Id($data[self::_PROJECT_ID]);
		
		require_once 'Sirprize/Basecamp/Id.php';
		$milestoneId
			= ($data[self::_MILESTONE_ID] != '')
			? new \Sirprize\Basecamp\Id($data[self::_MILESTONE_ID])
			: null
		;
		
		$private = ($data[self::_PRIVATE] == 'true');
		$tracked = ($data[self::_TRACKED] == 'true');
		$complete = ($data[self::_COMPLETE] == 'true');
		
		$this->_data = array(
			self::_COMPLETED_COUNT => $data[self::_COMPLETED_COUNT],
			self::_DESCRIPTION => $data[self::_DESCRIPTION],
			self::_ID => $id,
			self::_MILESTONE_ID => $milestoneId,
			self::_NAME => $data[self::_NAME],
			self::_POSITION => $data[self::_POSITION],
			self::_PRIVATE => $private,
			self::_PROJECT_ID => $projectId,
			self::_TRACKED => $tracked,
			self::_UNCOMPLETED_COUNT => $data[self::_UNCOMPLETED_COUNT],
			#self::_TODO_ITEMS => $data[self::_TODO_ITEMS],
			self::_COMPLETE => $complete
		);
		
		return $this;
	}
	
	
	
	/**
	 * Create XML to create a new todolist
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return string
	 */
	public function getCreateXml(Sirprize\Basecamp\Id $todoListTemplateId = null)
	{
		if($this->getName() === null)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call setName() before '.__METHOD__);
		}
		
  		$xml  = '<todo-list>';
		$xml .= '<name>'.$this->getName().'</name>';
		$xml .= '<description>'.$this->getDescription().'</description>';
		$xml .= '<private type="boolean">'.(($this->getIsPrivate()) ? 'true' : 'false').'</private>';
		
		if($this->getMilestoneId() !== null)
		{
			$xml .= '<milestone-id>'.$this->getMilestoneId().'</milestone-id>';
		}
		
		if($todoListTemplateId !== null)
		{
			$xml .= '<todo-list-template-id>'.$todoListTemplateId.'</todo-list-template-id>';
		}
		$xml .= '</todo-list>';
		return $xml;
	}
	
	
	
	/**
	 * Persist this todolist in storage
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return boolean
	 */
	public function create(Sirprize\Basecamp\Id $todoListTemplateId = null)
	{
		if($this->getProjectId() === null)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('set project-id before  '.__METHOD__);
		}
		
		$projectId = $this->getProjectId();
		$xml = $this->getCreateXml($todoListTemplateId);
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/projects/$projectId/todo_lists.xml")
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
				->setHeaders('Content-type', 'application/xml')
				->setHeaders('Accept', 'application/xml')
				->setRawData($xml)
				->request('POST')
			;
		}
		catch(\Exception $exception)
		{
			// connection error
			$this->_onCreateError();
			
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception($exception->getMessage());
		}
		
		require_once 'Sirprize/Basecamp/Response.php';
		$this->_response = new \Sirprize\Basecamp\Response($response);
		
		if($this->_response->isError())
		{
			// service error
			$this->_onCreateError();
			return false;
		}
		
		$this->_loaded = true;
		$this->_onCreateSuccess();
		return true;
	}
	
	
	
	
	
	/**
	 * Update this todolist in storage
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return boolean
	 */
	public function update()
	{
		$xml = $this->getCreateXml();
		$id = $this->getId();
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/todo_lists/$id.xml")
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
				->setHeaders('Content-type', 'application/xml')
				->setHeaders('Accept', 'application/xml')
				->setRawData($xml)
				->request('PUT')
			;
		}
		catch(\Exception $exception)
		{
			// connection error
			$this->_onUpdateError();
			
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception($exception->getMessage());
		}
		
		require_once 'Sirprize/Basecamp/Response.php';
		$this->_response = new \Sirprize\Basecamp\Response($response);
		
		if($this->_response->isError())
		{
			// service error
			$this->_onUpdateError();
			return false;
		}
		
		$this->_onUpdateSuccess();
		return true;
	}
	
	
	
	/**
	 * Delete this todolist from storage
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return boolean
	 */
	public function delete()
	{
		$id = $this->getId();
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/todo_lists/$id.xml")
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
				->setHeaders('Content-type', 'application/xml')
				->setHeaders('Accept', 'application/xml')
				->request('DELETE')
			;
		}
		catch(\Exception $exception)
		{
			// connection error
			$this->_onDeleteError();
			
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception($exception->getMessage());
		}
		
		require_once 'Sirprize/Basecamp/Response.php';
		$this->_response = new \Sirprize\Basecamp\Response($response);
		
		if($this->_response->isError())
		{
			// service error
			$this->_onDeleteError();
			return false;
		}
		
		$this->_onDeleteSuccess();
		$this->_data = array();
		$this->_loaded = false;
		return true;
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
	
	
	protected function _getVal($name)
	{
		return (isset($this->_data[$name])) ? $this->_data[$name] : null;
	}
	
	
	protected function _onCreateSuccess()
	{
		foreach($this->_observers as $observer)
		{
			$observer->onCreateSuccess($this);
		}
	}
	
	
	protected function _onUpdateSuccess()
	{
		foreach($this->_observers as $observer)
		{
			$observer->onUpdateSuccess($this);
		}
	}
	
	
	protected function _onDeleteSuccess()
	{
		foreach($this->_observers as $observer)
		{
			$observer->onDeleteSuccess($this);
		}
	}
	
	
	protected function _onCreateError()
	{
		foreach($this->_observers as $observer)
		{
			$observer->onCreateError($this);
		}
	}
	
	
	protected function _onUpdateError()
	{
		foreach($this->_observers as $observer)
		{
			$observer->onUpdateError($this);
		}
	}
	
	
	protected function _onDeleteError()
	{
		foreach($this->_observers as $observer)
		{
			$observer->onDeleteError($this);
		}
	}
	
}