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
 * Class to represent and modify a todolistItem
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Entity
{
	
	
	const _COMMENTS_COUNT = 'comments-count';
	const _COMPLETED = 'completed';
	const _COMPLEETED_AT = 'completed-at';
	const _COMPLETER_ID = 'completer-id';
	const _CONTENT = 'content';
	const _CREATED_AT = 'created-at';
	const _CREATOR_ID = 'creator-id';
	const _DUE_AT = 'due-at';
	const _ID = 'id';
	const _POSITION = 'position';
	const _TODO_LIST_ID = 'todo-list-id';
	const _COMPLETED_ON = 'completed-on';
	const _CREATED_ON = 'created-on';
	
	
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
	 * @return \Sirprize\Basecamp\TodolistItem
	 */
	public function attachObserver(\Sirprize\Basecamp\TodolistItem\Entity\Observer\Abstrakt $observer)
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
	 * @return \Sirprize\Basecamp\TodolistItem
	 */
	public function detachObserver(\Sirprize\Basecamp\TodolistItem\Entity\Observer\Abstrakt $observer)
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
	
	
	public function setTitle($title)
	{
		$this->_data[self::_TITLE] = $title;
		return $this;
	}
	
	
	public function setDeadline(\Sirprize\Basecamp\Date $deadline)
	{
		$this->_data[self::_DEADLINE] = $deadline;
		return $this;
	}
	
	
	public function setProjectId(\Sirprize\Basecamp\Id $projectId)
	{
		$this->_data[self::_PROJECT_ID] = $projectId;
		return $this;
	}
	
	
	public function setResponsiblePartyId(\Sirprize\Basecamp\Id $responsiblePartyId)
	{
		$this->_data[self::_RESPONSIBLE_PARTY_ID] = $responsiblePartyId;
		return $this;
	}
	
	
	public function setWantsNotification($wantsNotification)
	{
		$this->_data[self::_WANTS_NOTIFICATION] = $wantsNotification;
		return $this;
	}
	
	
	

	
	public function getCommentsCount()
	{
		return $this->_getVal(self::_COMMENTS_COUNT);
	}
	
	public function getIsCompleted()
	{
		return $this->_getVal(self::_COMPLETED);
	}
	
	
	public function getCompletedAt()
	{
		return $this->_getVal(self::_COMPLEETED_AT);
	}
	
	/**
	 * @return \Sirprize\Basecamp\Id
	 */
	public function getCompleterId()
	{
		return $this->_getVal(self::_COMPLETER_ID);
	}
	
	
	public function getContent()
	{
		return $this->_getVal(self::_CONTENT);
	}
	
	
	public function getCraetedAt()
	{
		return $this->_getVal(self::_CREATED_AT);
	}
	
	/**
	 * @return \Sirprize\Basecamp\Id
	 */
	public function getCraatorId()
	{
		return $this->_getVal(self::_CREATOR_ID);
	}
	
	
	public function getDueAt()
	{
		return $this->_getVal(self::_DUE_AT);
	}
	
	/**
	 * @return \Sirprize\Basecamp\Id
	 */
	public function getId()
	{
		return $this->_getVal(self::_ID);
	}
	
	
	public function getPosition()
	{
		return $this->_getVal(self::_POSITION);
	}
	
	/**
	 * @return \Sirprize\Basecamp\Id
	 */
	public function getTodolistId()
	{
		return $this->_getVal(self::_TODO_LIST_ID);
	}
	
	
	public function getCompletedOn()
	{
		return $this->_getVal(self::_COMPLETED_ON);
	}
	
	
	public function getCreatedOn()
	{
		return $this->_getVal(self::_CREATED_ON);
	}
	
	
	
	
	
	
	
	/**
	 * Load data returned from an api request
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\TodolistItem
	 */
	public function load(\SimpleXMLElement $data, $force = false)
	{
		if($this->_loaded && !$force)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('entity has already been loaded');
		}
		
		#print_r($data); exit;
		$this->_loaded = true;
		$data = (array) $data;
		
		require_once 'Sirprize/Basecamp/Id.php';
		$id = new \Sirprize\Basecamp\Id($data[self::_ID]);
		#$completerId = new \Sirprize\Basecamp\Id($data[self::_COMPLETER_ID]);
		$creatorId = new \Sirprize\Basecamp\Id($data[self::_CREATOR_ID]);
		$todolistId = new \Sirprize\Basecamp\Id($data[self::_TODO_LIST_ID]);
		
		$completed = ($data[self::_COMPLETED] == 'true');
		
		$this->_data = array(
			self::_COMMENTS_COUNT => $data[self::_COMMENTS_COUNT],
			self::_COMPLETED => $completed,
			#self::_COMPLEETED_AT => $data[self::_COMPLEETED_AT],
			#self::_COMPLETER_ID => $completerId,
			#self::_COMPLETED_ON => $data[self::_COMPLETED_ON],
			self::_CONTENT => $data[self::_CONTENT],
			self::_CREATED_AT => $data[self::_CREATED_AT],
			self::_CREATOR_ID => $creatorId,
			self::_DUE_AT => $data[self::_DUE_AT],
			self::_ID => $id,
			self::_POSITION => $data[self::_POSITION],
			self::_TODO_LIST_ID => $todolistId,
			self::_CREATED_ON => $data[self::_CREATED_ON]
		);
		
		return $this;
	}
	
	
	
	/**
	 * Create XML to create a new todolistItem
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return string
	 * /
	public function getCreateXml()
	{
		if($this->_getVal(self::_TITLE) === null)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call setTitle() before '.__METHOD__);
		}
		
		if($this->_getVal(self::_DEADLINE) === null)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call setDeadline() before '.__METHOD__);
		}
		
		if($this->_getVal(self::_RESPONSIBLE_PARTY_ID) === null)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call setResponsiblePartyId() before  '.__METHOD__);
		}
		
  		$xml  = '<todolistItem>';
		$xml .= '<title>'.$this->_getVal(self::_TITLE).'</title>';
		$xml .= '<deadline type="date">'.$this->_getVal(self::_DEADLINE).'</deadline>';
		$xml .= '<responsible-party>'.$this->_getVal(self::_RESPONSIBLE_PARTY_ID).'</responsible-party>';
		$xml .= '<notify>'.(($this->_getVal(self::_WANTS_NOTIFICATION)) ? 'true' : 'false').'</notify>';
		$xml .= '</todolistItem>';
		return $xml;
	}
	
	
	
	/**
	 * Persist this todolistItem in storage
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return boolean
	 * /
	public function create()
	{
		if($this->getProjectId() === null)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('set project-id before  '.__METHOD__);
		}
		
		$projectId = $this->getProjectId();
		
		$xml  = '<request>';
		$xml .= $this->getCreateXml();
		$xml .= '</request>';
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/projects/$projectId/todolistItems/create")
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
			$this->onCreateError();
			
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception($exception->getMessage());
		}
		
		require_once 'Sirprize/Basecamp/Response.php';
		$this->_response = new \Sirprize\Basecamp\Response($response);
		
		if($this->_response->isError())
		{
			// service error
			$this->onCreateError();
			return false;
		}
		
		$this->onCreateLoad($this->_response->getData()->todolistItem);
		return true;
	}
	
	
	
	/**
	 * Load data from a \Sirprize\Basecamp\TodolistItem\Collection::create() opteration
	 *
	 * @return void
	 * /
	public function onCreateLoad(\SimpleXMLElement $data)
	{
		$this->load($data);
		$this->_onCreateSuccess();
	}
	
	
	
	/**
	 * Get notified of an error in a \Sirprize\Basecamp\TodolistItem\Collection::create() opteration
	 *
	 * @return void
	 * /
	public function onCreateError()
	{
		$this->_onCreateError();
	}
	
	
	
	/**
	 * Update this todolistItem in storage
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return boolean
	 * /
	public function update($moveUpcomingTodolistItems = false, $moveUpcomingTodolistItemsOffWeekends = false)
	{
		$xml  = '<request>';
		$xml .= $this->getCreateXml();
		$xml .= '<move-upcoming-todolistItems>'.(($moveUpcomingTodolistItems) ? 'true' : 'false').'</move-upcoming-todolistItems>';
		$xml .= '<move-upcoming-todolistItems-off-weekends>'.(($moveUpcomingTodolistItemsOffWeekends) ? 'true' : 'false').'</move-upcoming-todolistItems-off-weekends>';
		$xml .= '</request>';
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/todolistItems/update/".$this->getId())
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
		
		$this->load($this->_response->getData(), true);
		$this->_onUpdateSuccess();
		return true;
	}
	
	
	
	/**
	 * Delete this todolistItem from storage
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return boolean
	 * /
	public function delete()
	{
		$id = $this->getId();
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/todolistItems/delete/$id")
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
	
	
	
	/**
	 * Complete this todolistItem
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return boolean
	 * /
	public function complete()
	{
		if(!$this->_loaded)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call load() before '.__METHOD__);
		}
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/todolistItems/complete/".$this->getId())
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
				->setHeaders('Content-type', 'application/xml')
				->setHeaders('Accept', 'application/xml')
				->request('GET')
			;
		}
		catch(\Exception $exception)
		{
			// connection error
			$this->_onCompleteError();
			
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception($exception->getMessage());
		}
		
		require_once 'Sirprize/Basecamp/Response.php';
		$this->_response = new \Sirprize\Basecamp\Response($response);
		
		if($this->_response->isError())
		{
			// service error
			$this->_onCompleteError();
			return false;
		}
		
		$this->load($this->_response->getData(), true);
		$this->_onCompleteSuccess();
		return true;
	}
	
	
	
	/**
	 * Uncomplete this todolistItem
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return boolean
	 * /
	public function uncomplete()
	{
		if(!$this->_loaded)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call load() before '.__METHOD__);
		}
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/todolistItems/uncomplete/".$this->getId())
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
				->setHeaders('Content-type', 'application/xml')
				->setHeaders('Accept', 'application/xml')
				->request('GET')
			;
		}
		catch(\Exception $exception)
		{
			// connection error
			$this->_onCompleteError();
			
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception($exception->getMessage());
		}
		
		require_once 'Sirprize/Basecamp/Response.php';
		$this->_response = new \Sirprize\Basecamp\Response($response);
		
		if($this->_response->isError())
		{
			// service error
			$this->_onUncompleteError();
			return false;
		}
		
		$this->load($this->_response->getData(), true);
		$this->_onUncompleteSuccess();
		return true;
	}
	*/
	
	
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
	
	
	protected function _onCompleteSuccess()
	{
		foreach($this->_observers as $observer)
		{
			$observer->onCompleteSuccess($this);
		}
	}
	
	
	protected function _onUncompleteSuccess()
	{
		foreach($this->_observers as $observer)
		{
			$observer->onUncompleteSuccess($this);
		}
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
	
	
	protected function _onCompleteError()
	{
		foreach($this->_observers as $observer)
		{
			$observer->onCompleteError($this);
		}
	}
	
	
	protected function _onUncompleteError()
	{
		foreach($this->_observers as $observer)
		{
			$observer->onUncmpleteError($this);
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