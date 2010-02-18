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
	
	const DATE_FORMAT = 'yyyy-MM-dd';
	
	
	protected $_basecamp = null;
	protected $_project = null;
	#protected $_schema = null;
	#protected $_responsiblePartyIds = array();
	
	
	
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
	
	
	
	
	public function getMilestonesFromXml($file, $referenceDate)
	{
		if(!is_readable($file))
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception("'$file' must be readable");
		}
		
		require_once 'Zend/Date.php';
		$referenceDate = new \Zend_Date($referenceDate, self::DATE_FORMAT);
		
		$xml = new \DOMDocument();
		$xml->load($file);
		$milestones = $this->_getBasecamp()->getMilestonesInstance();
		
		
		foreach($xml->getElementsByTagName('milestone') as $milestoneElement)
		{
			$title = $milestoneElement->getElementsByTagName('title')->item(0);
			$title = ($title) ? $title->nodeValue : null;
	
			$completed = $milestoneElement->getElementsByTagName('completed')->item(0);
			$completed = ($completed) ? $completed->nodeValue : false;
	
			$responsiblePartyId = $milestoneElement->getElementsByTagName('responsible-party-id')->item(0);
			$responsiblePartyId = ($responsiblePartyId) ? $responsiblePartyId->nodeValue : null;
	
			$deadline = $milestoneElement->getElementsByTagName('deadline')->item(0);
			$deadline = ($deadline) ? $deadline->nodeValue : null;
	
			$referenceDateOffset = $milestoneElement->getElementsByTagName('reference-date-offset')->item(0);
			$referenceDateOffset = (int)(($referenceDateOffset) ? $referenceDateOffset->nodeValue : 0);
	
			$note = $milestoneElement->getElementsByTagName('note')->item(0);
			$note = ($note) ? $note->nodeValue : null;
	
			$visibilityLevel = $milestoneElement->getElementsByTagName('visibility-level')->item(0);
			$visibilityLevel = ($visibilityLevel) ? $visibilityLevel->nodeValue : null;
	
			$responsibleParty = $milestoneElement->getElementsByTagName('responsible-party')->item(0);
			$responsibleParty = ($responsibleParty) ? $responsibleParty->nodeValue : null;
			
			require_once 'Sirprize/Basecamp/Date.php';
			$deadline = new \Sirprize\Basecamp\Date($this->_calculateDate($deadline, $referenceDateOffset, $referenceDate));
			
			require_once 'Sirprize/Basecamp/Id.php';
			$responsiblePartyId = new \Sirprize\Basecamp\Id($responsiblePartyId);
			
			$milestone = $milestones->getMilestoneInstance();
			$milestone
				->setTitle($title)
				#->setIsCompleted($completed)
				->setResponsiblePartyId($responsiblePartyId)
				->setDeadline($deadline)
				#->setNote($note)
				#->setVisibilityLevel($visibilityLevel)
				#->setResponsibleParty($responsibleParty)
			;
			
			$milestones->attach($milestone);
			#print $milestone->getTitle()."\n";
			#print $milestone->getDeadline()."\n";
			
	
			foreach($milestoneElement->getElementsByTagName('todo-list') as $todoListElement)
			{
				$name = $todoListElement->getElementsByTagName('name')->item(0);
				$name = ($name) ? $name->nodeValue : null;
				
				$todoList = $milestone->getTodoLists()->getTodoListInstance();
				$todoList->setName($name);
				$milestone->getTodoLists()->attach($todoList);
				#print ">> ".$todoList->getName()."\n";
		
				foreach($todoListElement->getElementsByTagName('todo-item') as $todoItemElement)
				{
					$content = $todoItemElement->getElementsByTagName('content')->item(0);
					$content = ($content) ? $content->nodeValue : null;
			
					$responsiblePartyId = $todoItemElement->getElementsByTagName('responsible-party-id')->item(0);
					$responsiblePartyId = ($responsiblePartyId) ? $responsiblePartyId->nodeValue : null;
					
					$todoItem = $todoList->getTodoItems()->getTodoItemInstance();
					$todoItem
						->setContent($content);
					;
					
					$todoList->getTodoItems()->attach($todoItem);
					#print ">>>> ".$todoItem->getContent()."\n";
				}
			}
		}
		
		return $milestones;
	}
	
	
	
	
	protected function _calculateDate($deadline, $referenceDateOffset, $referenceDate)
	{
		if(preg_match('/^\d{4,4}-\d{2,2}-\d{2,2}$/', $deadline))
		{
			return $deadline;
		}
	
		require_once 'Zend/Date.php';
		$date = new \Zend_Date($referenceDate, self::DATE_FORMAT);
	
		if($referenceDateOffset >= 0)
		{
			return $date->addSecond(60 * 60 * 24 * $referenceDateOffset)->toString(self::DATE_FORMAT);
		}
	
		return $date->subSecond(60 * 60 * 24 * $referenceDateOffset * -1)->toString(self::DATE_FORMAT);
	}
	
	
	
	/**
	 * Populate a project with milestones, todo-lists and todo-items
	 *
	 * @return \Sirprize\Basecamp\Import
	 */
	public function populate(\Sirprize\Basecamp\Milestone\Collection $schemaMilestones)
	{
		$this->_getProject()->startSubElements();
		
		foreach($schemaMilestones as $schemaMilestone)
		{
			$schemaMilestone->setProjectId($this->_getProject()->getId());
		}
		
		$this->_getProject()->addMilestones($schemaMilestones);
		
		
		foreach($schemaMilestones as $schemaMilestone)
		{
			$milestone = $this->_getProject()->findMilestoneByTitle($schemaMilestone->getTitle());
		
			if($milestone === null)
			{
				continue;
			}
			
			foreach($schemaMilestone->getTodoLists() as $schemaTodoList)
			{
				$schemaTodoList
					->setProjectId($this->_getProject()->getId())
					->setMilestoneId($milestone->getId())
				;
			}
			
			$this->_getProject()->addTodoLists($schemaMilestone->getTodoLists());
		}
		
		
		foreach($schemaMilestones as $schemaMilestone)
		{
			$milestone = $this->_getProject()->findMilestoneByTitle($schemaMilestone->getTitle());
		
			if($milestone === null)
			{
				continue;
			}
			
			foreach($schemaMilestone->getTodoLists() as $schemaTodoList)
			{
				$todoList = $this->_getProject()->findTodoListByName($schemaTodoList->getName());
				
				if($todoList === null)
				{
					continue;
				}
				
				foreach($schemaTodoList->getTodoItems() as $schemaTodoItem)
				{
					$schemaTodoItem
						->setTodoListId($todoList->getId())
					;
				}
				
				$this->_getProject()->addTodoItems($schemaTodoList->getTodoItems());
			}
		}
	}
	
	
	
	protected function _completeMilestone($schemaMilestones, $milestoneKey)
	{
		foreach($schemaMilestones as $key => $schemaMilestone)
		{
			if($key != $milestoneKey) { continue; }
			$milestone = $this->_getProject()->findMilestoneByTitle($schemaMilestone->getTitle());
			if($milestone !== null) { $milestone->complete(); }
			break;
		}
	}
	
	
	
	protected function _completeTodoItem($schemaMilestones, $milestoneKey, $todoListKey, $todoItemKey)
	{
		foreach($schemaMilestones as $x => $schemaMilestone)
		{
			if($x != $milestoneKey) { continue; }
			$milestone = $this->_getProject()->findMilestoneByTitle($schemaMilestone->getTitle());
			if($milestone === null) { break; }
			
			foreach($schemaMilestone->getTodoLists() as $y => $schemaTodoList)
			{
				if($y != $todoListKey) { continue; }
				$todoList = $this->_getProject()->findTodoListByName($schemaTodoList->getName());
				if($todoList === null) { break; }
				
				foreach($schemaTodoList->getTodoItems() as $z => $schemaTodoItem)
				{
					if($z != $todoItemKey) { continue; }
					$todoItem = $todoList->findTodoItemByContent($schemaTodoItem->getContent());
					if($todoItem !== null) { $todoItem->complete(); }
				}
			}
		}
	}
	
}