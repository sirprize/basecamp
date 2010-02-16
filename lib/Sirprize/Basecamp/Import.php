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


namespace Sirprize\Basecamp;


class Import
{
	
	protected $_basecamp = null;
	protected $_project = null;
	protected $_schema = null;
	protected $_responsiblePartyIds = array();
	
	
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
	
	
	public function setProject(\Sirprize\Basecamp\Project\Entity $project)
	{
		$this->_project = $project;
		return $this;
	}
	
	
	
	protected function _getProject()
	{
		if($this->_project === null)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call setProject() before '.__METHOD__);
		}
		
		return $this->_project;
	}
	
	
	
	public function setSchema(\Sirprize\Basecamp\Import\Schema $schema)
	{
		$this->_schema = $schema;
		return $this;
	}
	
	
	
	protected function _getSchema()
	{
		if($this->_schema === null)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call setSchema() before '.__METHOD__);
		}
		
		return $this->_schema;
	}
	
	
	
	public function setResponsiblePartyIds(array $responsiblePartyIds)
	{
		$this->_responsiblePartyIds = $responsiblePartyIds;
		return $this;
	}
	
	
	
	public function getResponsiblePartyId($key)
	{
		return (isset($this->_responsiblePartyIds[$key])) ? $this->_responsiblePartyIds[$key] : null;
	}
	
	
	
	public function populate()
	{
		$this->_getProject()->startSubElements();
		$milestones = $this->_assembleMilestones();
		$this->_getProject()->addMilestones($milestones);
		$todoLists = $this->_assembleTodoListsForExistingMilestones();
		$this->_getProject()->addTodoLists($todoLists);
		$todoItems = $this->_assembleTodoItemsForExistingTodoListsAndMilestones();
		$this->_getProject()->addTodoItems($todoItems);
	}
	
	
	
	public function run(array $data)
	{
		if($data['something_done'])
		{
			$this->_completeMilestone('milestoneKey');
			$this->_completeTodoItem('milestoneKey', 'todoListKey', 'todoItemKey');
		}
	}
	
	
	
	protected function _assembleMilestones()
	{
		$milestones = $this->_getBasecamp()->getMilestonesInstance();
		
		foreach($this->_getSchema()->getMilestones() as $schemaMilestone)
		{
			$milestone = $this->_getBasecamp()->getMilestonesInstance()->getMilestoneInstance();
			$milestone
				->setProjectId($this->_getProject()->getId())
				->setTitle($schemaMilestone->getTitle())
				->setDeadline(new \Sirprize\Basecamp\Date($schemaMilestone->getDeadline()))
			;
			
			$responsibleParty = $schemaMilestone->getResponsibleParty();
			
			if($this->getResponsiblePartyId($responsibleParty) !== null)
			{
				$milestone->setResponsiblePartyId($this->getResponsiblePartyId($responsibleParty));
			}
			
			$milestones->attach($milestone);
		}
		
		return $milestones;
	}
	
	
	
	protected function _assembleTodoListsForExistingMilestones()
	{
		$todoLists = $this->_getBasecamp()->getTodoListsInstance();
		
		foreach($this->_getSchema()->getMilestones() as $schemaMilestone)
		{
			$title = $schemaMilestone->getTitle();
			$milestone = $this->_getProject()->findMilestoneByTitle($title);
			$schemaTodoLists = $schemaMilestone->getTodoLists();
		
			if($milestone === null)
			{
				continue;
			}
		
			foreach($schemaTodoLists as $schemaTodoList)
			{
				$todoList = $this->_getBasecamp()->getTodoListsInstance()->getTodoListInstance();
				$todoList
					->setProjectId($this->_getProject()->getId())
					->setMilestoneId($milestone->getId())
					->setName($schemaTodoList->getName())
				;
				
				$todoLists->attach($todoList);
			}
		}
		
		return $todoLists;
	}
	
	
	
	protected function _assembleTodoItemsForExistingTodoListsAndMilestones()
	{
		$todoItems = $this->_getBasecamp()->getTodoItemsInstance();
		
		foreach($this->_getSchema()->getMilestones() as $schemaMilestone)
		{
			$title = $schemaMilestone->getTitle();
			$milestone = $this->_getProject()->findMilestoneByTitle($title);
			$schemaTodoLists = $schemaMilestone->getTodoLists();
		
			if($milestone === null)
			{
				continue;
			}
		
			foreach($schemaTodoLists as $schemaTodoList)
			{
				$name = $schemaTodoList->getName();
				$todoList = $this->_getProject()->findTodoListByName($name);
				$schemaTodoItems = $schemaTodoList->getTodoItems();
				
				if($todoList === null)
				{
					continue;
				}
				
				foreach($schemaTodoItems as $schemaTodoItem)
				{
					$responsibleParty = $schemaTodoItem->getResponsibleParty();
					$responsiblePartyId = $this->getResponsiblePartyId($responsibleParty);
					
					$todoItem = $todoItems->getTodoItemInstance();
					$todoItem->setTodoListId($todoList->getId());
					$todoItem->setContent($schemaTodoItem->getContent());
				
					if($responsiblePartyId !== null)
					{
						$todoItem->setResponsiblePartyId($responsiblePartyId);
					}
				
					$todoItems->attach($todoItem);
				}
			}
		}
		
		return $todoItems;
	}
	
	
	
	protected function _completeMilestone($milestoneKey)
	{
		$title = $this->_getSchema()->getMilestone($milestoneKey)->getTitle();
		$milestone = $this->_getProject()->findMilestoneByTitle($title);
		if($milestone !== null) { $milestone->complete(); }
	}
	
	
	
	protected function _completeTodoItem($milestoneKey, $todoListKey, $todoItemKey)
	{
		$name = $this->_getSchema()->getMilestone($milestoneKey)->getTodoList($todoListKey)->getName();
		$todoList = $this->_getProject()->findTodoListByName($name);
		if($todoList === null) { return; }
		
		$content = $this->_getSchema()->getMilestone($milestoneKey)->getTodoList($todoListKey)->getTodoItem($todoItemKey)->getContent();
		$todoItem = $todoList->findTodoItemByContent($content);
		if($todoItem !== null) { $todoItem->complete(); }
	}
	
}