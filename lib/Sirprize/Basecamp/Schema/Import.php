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


namespace Sirprize\Basecamp\Schema;


class Import
{
	
	
	protected $_basecamp = null;
	
	
	
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
	
	
	
	
	/**
	 * Create unpersisted tree-structure of milestones, todo-lists and todo-items from an Xml document
	 *
	 * Deadlines can be set explicitly or they can be calculated based on offset-days-to-reference-date
	 * if offset-days-to-reference-date is not present, then deadline or the current date will be used
	 *
	 * @return \Sirprize\Basecamp\Milestone\Collection
	 */
	public function assembleMilestonesAndListsAndItemsFromSchemaFile($file, $referenceDate = null)
	{
		if(!is_readable($file))
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception("'$file' must be readable");
		}
		
		$xml = new \DOMDocument();
		$xml->load($file);
		$milestones = $this->_getBasecamp()->getMilestonesInstance();
		
		
		foreach($xml->getElementsByTagName('milestone') as $milestoneElement)
		{
			$title = $milestoneElement->getElementsByTagName('title')->item(0);
			$title = ($title) ? $title->nodeValue : null;
			$responsiblePartyId = $milestoneElement->getElementsByTagName('responsible-party-id')->item(0);
			$responsiblePartyId = ($responsiblePartyId) ? $responsiblePartyId->nodeValue : null;
			$deadline = $milestoneElement->getElementsByTagName('deadline')->item(0);
			$deadline = ($deadline) ? $deadline->nodeValue : null;
			$referenceDateOffset = $milestoneElement->getElementsByTagName('offset-days-to-reference-date')->item(0);
			$referenceDateOffset = (($referenceDateOffset) ? $referenceDateOffset->nodeValue : null);
			
			if(!$this->_checkBeforeCreatingMilestoneFromSchemaFile($title))
			{
				continue;
			}
			
			$milestone = $milestones->getMilestoneInstance();
			$milestone->setTitle($this->_getTitleForCreatingMilestoneFromSchemaFile($title));
			
			if($this->_checkDate($deadline))
			{
				require_once 'Sirprize/Basecamp/Date.php';
				$milestone->setDeadline(new \Sirprize\Basecamp\Date($deadline));
			}
			else {
				require_once 'Sirprize/Basecamp/Date.php';
				$milestone->setDeadline(new \Sirprize\Basecamp\Date($this->_calculateEffectiveDate($referenceDate, $referenceDateOffset)));
			}
			
			if($responsiblePartyId !== null)
			{
				require_once 'Sirprize/Basecamp/Id.php';
				$responsiblePartyId = new \Sirprize\Basecamp\Id($responsiblePartyId);
				$milestone->setResponsiblePartyId($responsiblePartyId);
			}
			
			$milestones->attach($milestone);
			
	
			foreach($milestoneElement->getElementsByTagName('todo-list') as $todoListElement)
			{
				$name = $todoListElement->getElementsByTagName('name')->item(0);
				$name = ($name) ? $name->nodeValue : null;
				$private = $todoListElement->getElementsByTagName('private')->item(0);
				$private = ($private) ? $private->nodeValue : 0;
				$private = ($private == 'true') ? true : false;
				$description = $todoListElement->getElementsByTagName('description')->item(0);
				$description = ($description) ? $description->nodeValue : '';
				
				$todoList = $milestone->getTodoLists()->getTodoListInstance();
				$todoList
					->setName($name)
					->setIsPrivate($private)
					->setDescription($description)
				;
				$milestone->getTodoLists()->attach($todoList);
		
				foreach($todoListElement->getElementsByTagName('todo-item') as $todoItemElement)
				{
					$content = $todoItemElement->getElementsByTagName('content')->item(0);
					$content = ($content) ? $content->nodeValue : null;
					$responsiblePartyId = $todoItemElement->getElementsByTagName('responsible-party-id')->item(0);
					$responsiblePartyId = ($responsiblePartyId) ? $responsiblePartyId->nodeValue : null;
					$dueAt = $todoItemElement->getElementsByTagName('due-at')->item(0);
					$dueAt = ($dueAt) ? $dueAt->nodeValue : null;
					$referenceDateOffset = $todoItemElement->getElementsByTagName('offset-days-to-reference-date')->item(0);
					$referenceDateOffset = (($referenceDateOffset) ? $referenceDateOffset->nodeValue : null);
					$notify = $todoItemElement->getElementsByTagName('notify')->item(0);
					$notify = ($notify) ? $notify->nodeValue : null;
					$notify = ($notify == 'true') ? true : false;
					
					$todoItem = $todoList->getTodoItems()->getTodoItemInstance();
					$todoItem
						->setContent($content)
						->setNotify($notify)
					;
					
					if($this->_checkDate($dueAt))
					{
						require_once 'Sirprize/Basecamp/Date.php';
						$todoItem->setDueAt(new \Sirprize\Basecamp\Date($dueAt));
					}
					else if ($referenceDate !== null && $referenceDateOffset !== null)
					{
						require_once 'Sirprize/Basecamp/Date.php';
						$todoItem->setDueAt(new \Sirprize\Basecamp\Date($this->_calculateEffectiveDate($referenceDate, $referenceDateOffset)));
					}
					
					if($responsiblePartyId !== null)
					{
						require_once 'Sirprize/Basecamp/Id.php';
						$responsiblePartyId = new \Sirprize\Basecamp\Id($responsiblePartyId);
						$todoItem->setResponsiblePartyId($responsiblePartyId);
					}
					
					$todoList->getTodoItems()->attach($todoItem);
				}
			}
		}
		
		return $milestones;
	}
	
	
	
	
	/**
	 * Populate this project from a tree structure of unpersisted milestones, todo-lists and todo-items
	 *
	 * @return \Sirprize\Basecamp\Import
	 */
	public function populateProject(\Sirprize\Basecamp\Project\Entity $project, \Sirprize\Basecamp\Milestone\Collection $rawMilestones)
	{
		$project->startSubElements();
		
		foreach($rawMilestones as $rawMilestone)
		{
			$rawMilestone->setProjectId($project->getId());
		}
		
		$project->addMilestones($rawMilestones);
		
		
		foreach($rawMilestones as $rawMilestone)
		{
			$milestone = $project->findMilestoneByTitle($rawMilestone->getTitle());
			if($milestone === null) { continue; }
			
			foreach($rawMilestone->getTodoLists() as $rawTodoList)
			{
				$rawTodoList
					->setProjectId($project->getId())
					->setMilestoneId($milestone->getId())
				;
			}
			
			$project->addTodoLists($rawMilestone->getTodoLists());
		}
		
		
		foreach($rawMilestones as $rawMilestone)
		{
			$milestone = $project->findMilestoneByTitle($rawMilestone->getTitle());
			if($milestone === null) { continue; }
			
			foreach($rawMilestone->getTodoLists() as $rawTodoList)
			{
				$todoList = $project->findTodoListByName($rawTodoList->getName());
				if($todoList === null) { continue; }
				
				foreach($rawTodoList->getTodoItems() as $rawTodoItem)
				{
					$rawTodoItem->setTodoListId($todoList->getId());
				}
				
				$project->addTodoItems($rawTodoList->getTodoItems());
			}
		}
	}
	
	
	
	protected function _checkBeforeCreatingMilestoneFromSchemaFile($title)
	{
		return true;
	}
	
	
	protected function _getTitleForCreatingMilestoneFromSchemaFile($title)
	{
		return $title;
	}
	
	
	
	protected function _checkDate($date)
	{
		return preg_match('/^\d{4,4}-\d{2,2}-\d{2,2}$/', $date);
	}
	
	
	
	protected function _calculateEffectiveDate($referenceDate, $referenceDateOffset)
	{
		if(!$this->_checkDate($referenceDate))
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception("invalid reference date '$referenceDate'");
		}
		
		require_once 'Zend/Date.php';
		require_once 'Sirprize/Basecamp/Date.php';
		$referenceDate = new \Zend_Date($referenceDate, \Sirprize\Basecamp\Date::FORMAT);
		$referenceDateOffset = (int)$referenceDateOffset;
	
		if($referenceDateOffset >= 0)
		{
			return $referenceDate->addSecond(60 * 60 * 24 * $referenceDateOffset)->toString(\Sirprize\Basecamp\Date::FORMAT);
		}
	
		return $referenceDate->subSecond(60 * 60 * 24 * $referenceDateOffset * -1)->toString(\Sirprize\Basecamp\Date::FORMAT);
	}
}