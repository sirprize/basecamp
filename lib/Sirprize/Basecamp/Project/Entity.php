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
 * Represent and modify a project
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Entity
{
	
	
	const STATUS_ACTIVE = 'active';
	const STATUS_ON_HOLD = 'on_hold';
	const STATUS_ARCHIVED = 'archived';
	
	const _ANNOUNCEMENT = 'announcement'; // SimpleXMLElement
	const _CREATED_ON = 'created-on';
	const _ID = 'id';
	const _LAST_CHANGED_ON = 'last-changed-on';
	const _NAME = 'name';
	const _SHOW_ANNOUNCEMENT = 'show-announcement';
	const _SHOW_WRITEBOARDS = 'show-writeboards';
	const _START_PAGE = 'start-page';
	const _STATUS = 'status';
	const _COMPANY = 'company'; // SimpleXMLElement
	
	
	protected $_basecamp = null;
	protected $_httpClient = null;
	protected $_data = array();
	protected $_loaded = false;
	protected $_response = null;
	
	// sub-elements
	protected $_milestones = null;
	protected $_todoLists = null;
	#protected $_hasError = false;
	protected $_subElementsStarted = false;
	
	
	
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
	
	
	
	public function getAnnouncement()
	{
		return $this->_getVal(self::_ANNOUNCEMENT);
	}
	
	
	public function getCreatedOn()
	{
		return $this->_getVal(self::_CREATED_ON);
	}
	
	
	/**
	 * @return \Sirprize\Basecamp\Id
	 */
	public function getId()
	{
		return $this->_getVal(self::_ID);
	}
	
	
	public function getLastChangedOn()
	{
		return $this->_getVal(self::_LAST_CHANGED_ON);
	}
	
	
	public function getName()
	{
		return $this->_getVal(self::_NAME);
	}
	
	
	public function getShowAnnouncement()
	{
		return $this->_getVal(self::_SHOW_ANNOUNCEMENT);
	}
	
	
	public function getShowWriteboards()
	{
		return $this->_getVal(self::_SHOW_WRITEBOARDS);
	}
	
	
	public function getStartPage()
	{
		return $this->_getVal(self::_START_PAGE);
	}
	
	
	public function getStatus()
	{
		return $this->_getVal(self::_STATUS);
	}
	
	
	public function getCompany()
	{
		return $this->_getVal(self::_COMPANY);
	}
	
	
	public function isActive()
	{
		return ($this->getStatus() == self::STATUS_ACTIVE);
	}
	
	
	public function isArchived()
	{
		return ($this->getStatus() == self::STATUS_ARCHIVED);
	}
	
	
	public function isOnHold()
	{
		return ($this->getStatus() == self::STATUS_ON_HOLD);
	}
	
	
	
	
	/**
	 * Load data returned from an api request
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Project
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
		
		$showAnnouncement = ($array[self::_SHOW_ANNOUNCEMENT] == 'true');
		$showWriteboards = ($array[self::_SHOW_WRITEBOARDS] == 'true');
		
		$this->_data = array(
			self::_ANNOUNCEMENT => $array[self::_ANNOUNCEMENT],
			self::_CREATED_ON => $array[self::_CREATED_ON],
			self::_ID => $id,
			self::_LAST_CHANGED_ON => $array[self::_LAST_CHANGED_ON],
			self::_NAME => $array[self::_NAME],
			self::_SHOW_ANNOUNCEMENT => $showAnnouncement,
			self::_SHOW_WRITEBOARDS => $showWriteboards,
			self::_START_PAGE => $array[self::_START_PAGE],
			self::_STATUS => $array[self::_STATUS],
			self::_COMPANY => $array[self::_COMPANY]
		);
		
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
	
	
	protected function _getVal($name)
	{
		return (isset($this->_data[$name])) ? $this->_data[$name] : null;
	}
	
	
	





	
	
	
	
	public function startSubElements()
	{
		if(!$this->_loaded)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call load() before '.__METHOD__);
		}
		
		if($this->_subElementsStarted === true)
		{
			return $this;
		}
		
		$this->_milestones = $this->_getBasecamp()->getMilestonesInstance()->startAllByProjectId($this->getId());
		$this->_todoLists = $this->_getBasecamp()->getTodoListsInstance()->startAllByProjectId($this->getId());
		
		foreach($this->_todoLists as $todoList)
		{
			$todoList->startTodoItems();
		}
		
		$this->_subElementsStarted = true;
		return $this;
	}
	
	
	
	public function getMilestones()
	{
		if($this->_milestones === null)
		{
			$this->_milestones = $this->_getBasecamp()->getMilestonesInstance();
		}
		
		return $this->_milestones;
	}
	
	
	
	public function getTodoLists()
	{
		if($this->_todoLists === null)
		{
			$this->_todoLists = $this->_getBasecamp()->getTodoListsInstance();
		}
		
		return $this->_todoLists;
	}
	
	
	
	public function findMilestoneByTitle($title)
	{
		foreach($this->getMilestones() as $milestone)
		{
			if($title == $milestone->getTitle())
			{
				return $milestone;
			}
		}
		
		return null;
	}
	
	
	
	public function findTodoListByName($name)
	{
		foreach($this->getTodoLists() as $todoList)
		{
			if($name == $todoList->getName())
			{
				return $todoList;
			}
		}
		
		return null;
	}
	
	
	
	public function findTodoListById(\Sirprize\Basecamp\Id $id)
	{
		foreach($this->getTodoLists() as $todoList)
		{
			if((string)$id == (string)$todoList->getId())
			{
				return $todoList;
			}
		}
		
		return null;
	}
	
	
	
	public function findTodoListsByMilestoneId(\Sirprize\Basecamp\Id $milestoneId)
	{
		$todoLists = $this->_getBasecamp()->getTodoListsInstance();
		
		foreach($this->getTodoLists() as $todoList)
		{
			if((string)$milestoneId == (string)$todoList->getMilestoneId())
			{
				$todoLists->attach($todoList);
			}
		}
		
		return $todoLists;
	}
	
	
	
	public function deleteMilestones()
	{
		foreach($this->getMilestones() as $milestone)
		{
			$milestone->delete();
		}
		
		$this->_milestones = $this->_getBasecamp()->getMilestonesInstance();
		return $this;
	}
	
	
	
	public function deleteTodoLists()
	{
		foreach($this->getTodoLists() as $todoList)
		{
			$todoList->delete();
		}
		
		$this->_todoLists = $this->_getBasecamp()->getTodoListsInstance();
		return $this;
	}
	
	
	
	public function applySchema(\Sirprize\Basecamp\Schema $schema)
	{
		$reloadMilestones = false;
		$reloadTodoLists = false;
		
		$this->_milestones = $this->_getBasecamp()->getMilestonesInstance()->startAllByProjectId($this->getId());
		
		foreach($schema->getMilestones() as $schemaMilestone)
		{
			if($this->findMilestoneByTitle($schemaMilestone->getTitle()))
			{
				continue;
			}
			
			$schemaMilestone->setProjectId($this->getId());
			$schemaMilestone->create();
			$reloadMilestones = true;
		}
		
		if($reloadMilestones)
		{
			$this->_milestones = $this->_getBasecamp()->getMilestonesInstance()->startAllByProjectId($this->getId());
		}
		
		$this->_todoLists = $this->_getBasecamp()->getTodoListsInstance()->startAllByProjectId($this->getId());
		
		foreach($schema->getMilestones() as $schemaMilestone)
		{
			$milestone = $this->findMilestoneByTitle($schemaMilestone->getTitle());
			if($milestone === null) { continue; }
			
			foreach($schemaMilestone->getTodoLists() as $schemaTodoList)
			{
				if($this->findTodoListByName($schemaTodoList->getName()))
				{
					continue;
				}
				
				$schemaTodoList
					->setProjectId($this->getId())
					->setMilestoneId($milestone->getId())
				;
				
				$schemaTodoList->create();
				$reloadTodoLists = true;
			}
		}
		
		if($reloadTodoLists)
		{
			$this->_todoLists = $this->_getBasecamp()->getTodoListsInstance()->startAllByProjectId($this->getId());
		}
		
		foreach($schema->getMilestones() as $schemaMilestone)
		{
			$milestone = $this->findMilestoneByTitle($schemaMilestone->getTitle());
			if($milestone === null) { continue; }
			
			foreach($schemaMilestone->getTodoLists() as $schemaTodoList)
			{
				$todoList = $this->findTodoListByName($schemaTodoList->getName());
				if($todoList === null) { continue; }
				
				$todoList->startTodoItems();
				
				foreach($schemaTodoList->getTodoItems() as $schemaTodoItem)
				{
					if($todoList->findTodoItemByContent($schemaTodoItem->getContent()))
					{
						continue;
					}
					
					$schemaTodoItem->setTodoListId($todoList->getId());
					$schemaTodoItem->create();
				}
			}
		}
		
		return $this;
	}
	
	
	
	public function findMilestoneBySchemaIndex(\Sirprize\Basecamp\Schema $schema, $milestoneKey)
	{
		foreach($schema->getMilestones() as $key => $schemaMilestone)
		{
			if($key != $milestoneKey) { continue; }
			return $this->findMilestoneByTitle($schemaMilestone->getTitle());
		}
		
		return null;
	}
	
	
	
	public function findTodoItemBySchemaIndex(\Sirprize\Basecamp\Schema $schema, $milestoneKey, $todoListKey, $todoItemKey)
	{
		foreach($schema->getMilestones() as $x => $schemaMilestone)
		{
			if($x != $milestoneKey) { continue; }
			$milestone = $this->findMilestoneByTitle($schemaMilestone->getTitle());
			if($milestone === null) { break; }
			
			foreach($schemaMilestone->getTodoLists() as $y => $schemaTodoList)
			{
				if($y != $todoListKey) { continue; }
				$todoList = $this->findTodoListByName($schemaTodoList->getName());
				if($todoList === null) { break; }
				
				foreach($schemaTodoList->getTodoItems() as $z => $schemaTodoItem)
				{
					if($z != $todoItemKey) { continue; }
					return $todoList->findTodoItemByContent($schemaTodoItem->getContent());
				}
			}
		}
		
		return null;
	}
	
}