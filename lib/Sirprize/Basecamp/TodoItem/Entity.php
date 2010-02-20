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


namespace Sirprize\Basecamp\TodoItem;


/**
 * Represent and modify a todo-item
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
	const _RESPONSIBLE_PARTY_ID = 'responsible-party-id';
	const _RESPONSIBLE_PARTY_TYPE = 'responsible-party-type';
	const _TODOLIST_ID = 'todo-list-id';
	const _COMPLETED_ON = 'completed-on';
	const _CREATED_ON = 'created-on';
	
	
	protected $_basecamp = null;
	protected $_httpClient = null;
	protected $_data = array();
	protected $_loaded = false;
	protected $_response = null;
	protected $_observers = array();
	protected $_responsiblePartyId = null;
	protected $_notify = false;
	
	
	
	
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
	 * @return \Sirprize\Basecamp\TodoItem
	 */
	public function attachObserver(\Sirprize\Basecamp\TodoItem\Entity\Observer\Abstrakt $observer)
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
	 * @return \Sirprize\Basecamp\TodoItem
	 */
	public function detachObserver(\Sirprize\Basecamp\TodoItem\Entity\Observer\Abstrakt $observer)
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
	
	
	public function setResponsiblePartyId(\Sirprize\Basecamp\Id $responsiblePartyId)
	{
		$this->_responsiblePartyId = $responsiblePartyId;
		return $this;
	}
	
	
	public function setDueAt(\Sirprize\Basecamp\Date $dueAt)
	{
		$this->_data[self::_DUE_AT] = $dueAt;
		return $this;
	}
	
	
	public function setNotify($notify)
	{
		$this->_notify = $notify;
		return $this;
	}
	
	
	public function setContent($content)
	{
		$this->_data[self::_CONTENT] = $content;
		return $this;
	}
	
	
	public function setTodoListId(\Sirprize\Basecamp\Id $todoListId)
	{
		$this->_data[self::_TODOLIST_ID] = $todoListId;
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
	
	
	public function getCreatedAt()
	{
		return $this->_getVal(self::_CREATED_AT);
	}
	
	/**
	 * @return \Sirprize\Basecamp\Id
	 */
	public function getCreatorId()
	{
		return $this->_getVal(self::_CREATOR_ID);
	}
	
	
	/**
	 * @return \Sirprize\Basecamp\Date
	 */
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
	 * @return null|\Sirprize\Basecamp\Id
	 */
	public function getResponsiblePartyId()
	{
		return $this->_getVal(self::_RESPONSIBLE_PARTY_ID);
	}
	
	
	public function getResponsiblePartyType()
	{
		return $this->_getVal(self::_RESPONSIBLE_PARTY_TYPE);
	}
	
	/**
	 * @return \Sirprize\Basecamp\Id
	 */
	public function getTodoListId()
	{
		return $this->_getVal(self::_TODOLIST_ID);
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
	 * @return \Sirprize\Basecamp\TodoItem
	 */
	public function load(\SimpleXMLElement $xml, $force = false)
	{
		if($this->_loaded && !$force)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('entity has already been loaded');
		}
		
		#print_r($xml); exit;
		$this->_loaded = true;
		$array = (array) $xml;
		
		require_once 'Sirprize/Basecamp/Id.php';
		$id = new \Sirprize\Basecamp\Id($array[self::_ID]);
		#$completerId = new \Sirprize\Basecamp\Id($array[self::_COMPLETER_ID]);
		$creatorId = new \Sirprize\Basecamp\Id($array[self::_CREATOR_ID]);
		$todoListId = new \Sirprize\Basecamp\Id($array[self::_TODOLIST_ID]);
		$responsiblePartyId = null;
		$responsiblePartyType = null;
		
		if(isset($array[self::_RESPONSIBLE_PARTY_ID]))
		{
			$responsiblePartyId = new \Sirprize\Basecamp\Id($array[self::_RESPONSIBLE_PARTY_ID]);
		}
		
		if(isset($array[self::_RESPONSIBLE_PARTY_TYPE]))
		{
			$responsiblePartyType = $array[self::_RESPONSIBLE_PARTY_TYPE];
		}
		
		$completed = ($array[self::_COMPLETED] == 'true');
		$dueAt = null;
		
		if($array[self::_DUE_AT])
		{
			$dueAt = preg_replace('/^(\d{4,4}-\d{2,2}-\d{2,2}).*$/', "$1", $array[self::_DUE_AT]);
			if(!$dueAt) { $dueAt = null; }
		}
		
		$this->_data = array(
			self::_COMMENTS_COUNT => $array[self::_COMMENTS_COUNT],
			self::_COMPLETED => $completed,
			#self::_COMPLEETED_AT => $array[self::_COMPLEETED_AT],
			#self::_COMPLETER_ID => $completerId,
			#self::_COMPLETED_ON => $array[self::_COMPLETED_ON],
			self::_CONTENT => $array[self::_CONTENT],
			self::_CREATED_AT => $array[self::_CREATED_AT],
			self::_CREATOR_ID => $creatorId,
			self::_DUE_AT => $dueAt,
			self::_ID => $id,
			self::_POSITION => $array[self::_POSITION],
			self::_RESPONSIBLE_PARTY_ID => $responsiblePartyId,
			self::_RESPONSIBLE_PARTY_TYPE => $responsiblePartyType,
			self::_TODOLIST_ID => $todoListId,
			self::_CREATED_ON => $array[self::_CREATED_ON]
		);
		
		return $this;
	}
	
	
	
	/**
	 * Create XML to create a new todoItem
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return string
	 */
	public function getXml()
	{
		if($this->getContent() === null)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call setContent() before '.__METHOD__);
		}
		
  		$xml  = '<todo-item>';
		$xml .= '<content>'.htmlspecialchars($this->getContent(), ENT_NOQUOTES).'</content>';
		
		if($this->_responsiblePartyId !== null)
		{
			$xml .= '<responsible-party>'.$this->_responsiblePartyId.'</responsible-party>';
			if($this->_notify) { $xml .= '<notify>true</notify>'; }
		}
		
		if($this->getDueAt() !== null)
		{
			$xml .= '<due-at>'.$this->getDueAt().'</due-at>';
		}
		
		$xml .= '</todo-item>';
		return $xml;
	}
	
	
	
	
	/**
	 * Persist this todo-item in storage
	 *
	 * Note: complete data (id etc) is not automatically loaded upon creation
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return boolean
	 */
	public function create()
	{
		if($this->getTodoListId() === null)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('set todoList-id before  '.__METHOD__);
		}
		
		$todoListId = $this->getTodoListId();
		$xml = $this->getXml();
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/todo_lists/$todoListId/todo_items.xml")
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
				->setHeaders('Content-type', 'application/xml')
				->setHeaders('Accept', 'application/xml')
				->setRawData($xml)
				->request('POST')
			;
		}
		catch(\Exception $exception)
		{
			try {
				// connection error - try again
				$response = $this->_getHttpClient()->request('POST');
			}
			catch(\Exception $exception)
			{
				$this->_onCreateError();
			
				require_once 'Sirprize/Basecamp/Exception.php';
				throw new \Sirprize\Basecamp\Exception($exception->getMessage());
			}
		}
		
		require_once 'Sirprize/Basecamp/Response.php';
		$this->_response = new \Sirprize\Basecamp\Response($response);
		
		if($this->_response->isError())
		{
			// service error
			$this->_onCreateError();
			return false;
		}
		
		$this->_onCreateSuccess();
		return true;
	}
	
	
	
	/**
	 * Update this todo-item in storage
	 *
	 * Note: complete data (id etc) is not automatically loaded upon update
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return boolean
	 */
	public function update()
	{
		if(!$this->_loaded)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call load() before '.__METHOD__);
		}
		
		$xml = $this->getXml();
		$id = $this->getId();
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/todo_items/$id.xml")
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
				->setHeaders('Content-type', 'application/xml')
				->setHeaders('Accept', 'application/xml')
				->setRawData($xml)
				->request('PUT')
			;
		}
		catch(\Exception $exception)
		{
			try {
				// connection error - try again
				$response = $this->_getHttpClient()->request('PUT');
			}
			catch(\Exception $exception)
			{
				$this->_onUpdateError();
			
				require_once 'Sirprize/Basecamp/Exception.php';
				throw new \Sirprize\Basecamp\Exception($exception->getMessage());
			}
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
	 * Delete this todo-item from storage
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return boolean
	 */
	public function delete()
	{
		$id = $this->getId();
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/todo_items/$id.xml")
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
				->setHeaders('Content-type', 'application/xml')
				->setHeaders('Accept', 'application/xml')
				->request('DELETE')
			;
		}
		catch(\Exception $exception)
		{
			try {
				// connection error - try again
				$response = $this->_getHttpClient()->request('DELETE');
			}
			catch(\Exception $exception)
			{
				$this->_onDeleteError();
			
				require_once 'Sirprize/Basecamp/Exception.php';
				throw new \Sirprize\Basecamp\Exception($exception->getMessage());
			}
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
	 * Complete this todo-item
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return boolean
	 */
	public function complete()
	{
		if(!$this->_loaded)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call load() before '.__METHOD__);
		}
		
		$id = $this->getId();
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/todo_items/$id/complete.xml")
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
				->setHeaders('Content-type', 'application/xml')
				->setHeaders('Accept', 'application/xml')
				->request('PUT')
			;
		}
		catch(\Exception $exception)
		{
			try {
				// connection error - try again
				$response = $this->_getHttpClient()->request('PUT');
			}
			catch(\Exception $exception)
			{
				$this->_onCompleteError();
			
				require_once 'Sirprize/Basecamp/Exception.php';
				throw new \Sirprize\Basecamp\Exception($exception->getMessage());
			}
		}
		
		require_once 'Sirprize/Basecamp/Response.php';
		$this->_response = new \Sirprize\Basecamp\Response($response);
		
		if($this->_response->isError())
		{
			// service error
			$this->_onCompleteError();
			return false;
		}
		
		$this->_onCompleteSuccess();
		return true;
	}
	
	
	
	/**
	 * Uncomplete this todo-item
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return boolean
	 */
	public function uncomplete()
	{
		if(!$this->_loaded)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call load() before '.__METHOD__);
		}
		
		$id = $this->getId();
		
		try {
			$response = $this->_getHttpClient()
				->setUri($this->_getBasecamp()->getBaseUri()."/todo_items/$id/uncomplete.xml")
				->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
				->setHeaders('Content-type', 'application/xml')
				->setHeaders('Accept', 'application/xml')
				->request('PUT')
			;
		}
		catch(\Exception $exception)
		{
			try {
				// connection error - try again
				$response = $this->_getHttpClient()->request('PUT');
			}
			catch(\Exception $exception)
			{
				$this->_onCompleteError();
			
				require_once 'Sirprize/Basecamp/Exception.php';
				throw new \Sirprize\Basecamp\Exception($exception->getMessage());
			}
		}
		
		require_once 'Sirprize/Basecamp/Response.php';
		$this->_response = new \Sirprize\Basecamp\Response($response);
		
		if($this->_response->isError())
		{
			// service error
			$this->_onUncompleteError();
			return false;
		}
		
		$this->_onUncompleteSuccess();
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