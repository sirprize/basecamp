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


class Helper
{
	
	const DATE_FORMAT = 'yyyy-MM-dd';
	
	
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
	public function assembleMilestonesFromXml($file, $referenceDate = null)
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
			
			$milestone = $milestones->getMilestoneInstance();
			$milestone->setTitle($title);
			
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
		$referenceDate = new \Zend_Date($referenceDate, self::DATE_FORMAT);
		$referenceDateOffset = (int)$referenceDateOffset;
	
		if($referenceDateOffset >= 0)
		{
			return $referenceDate->addSecond(60 * 60 * 24 * $referenceDateOffset)->toString(self::DATE_FORMAT);
		}
	
		return $referenceDate->subSecond(60 * 60 * 24 * $referenceDateOffset * -1)->toString(self::DATE_FORMAT);
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
	
	
	
	
	
	const REFERENCE_EXTREMITY_FIRST = 'first';
	const REFERENCE_EXTREMITY_LAST = 'last';
	
	
	protected function _getReferenceDate(\Sirprize\Basecamp\Project\Entity $project, $referenceExtremity)
	{
		$project->getMilestones()->rewind();
		
		if(!$project->getMilestones()->count())
		{
			return null;
		}
		
		if($referenceExtremity == self::REFERENCE_EXTREMITY_FIRST)
		{
			return $project->getMilestones()->current()->getDeadline();
		}
		
		$last = null;
		
		while($project->getMilestones()->valid())
		{
			$last = $project->getMilestones()->current()->getDeadline();
			$project->getMilestones()->next();
		}
		
		return $last;
	}
	
	
	
	protected function _calculateOffsetDays($referenceDate, $effectiveDate, $referenceExtremity, $isMilestone)
	{
		require_once 'Zend/Date.php';
		$referenceDate = new \Zend_Date($referenceDate, self::DATE_FORMAT);
		$effectiveDate = new \Zend_Date($effectiveDate, self::DATE_FORMAT);
		
		$offset
			= ($referenceExtremity == self::REFERENCE_EXTREMITY_LAST)
			? ($referenceDate->getTimestamp() - $effectiveDate->getTimestamp()) * -1
			: $effectiveDate->getTimestamp() - $referenceDate->getTimestamp()
		;
		
		if($isMilestone)
		{
			return $offset / 60 / 60 / 24;
		}
		
		return // quick fix for todo-item dates which seem to be 1 day off (?)
			  ($referenceExtremity == self::REFERENCE_EXTREMITY_LAST)
			? ($offset / 60 / 60 / 24) + 1 // add 1 day
			: ($offset / 60 / 60 / 24) - 1 // sub 1 day
		;
	}
	
	
	
	public function getProjectXml(\Sirprize\Basecamp\Project\Entity $project, $useRelativeDates = true, $referenceExtremity = self::REFERENCE_EXTREMITY_LAST)
	{
		$project->startSubElements();
		$referenceDate = $this->_getReferenceDate($project, $referenceExtremity);
		
		$xml  = "<?xml version=\"1.0\"?>\n";
		$xml .= "<project>\n";
		$xml .= "<id>".$project->getId()."</id>\n";
		$xml .= "<name>".htmlentities($project->getName())."</name>\n";
		$xml .= "<announcement>".htmlentities($project->getAnnouncement())."</announcement>\n";
		$xml .= "<status>".$project->getStatus()."</status>\n";
		$xml .= "<company>".htmlentities(trim($project->getCompany()))."</company>\n";
		
		foreach($project->getMilestones() as $milestone)
		{
			$xml .= "<milestone>\n";
			$xml .= "<title>".htmlentities($milestone->getTitle())."</title>\n";
			$xml .= "<responsible-party-id>".htmlentities($milestone->getResponsiblePartyId())."</responsible-party-id>\n";
			
			if($useRelativeDates)
			{
				$xml .= "<offset-days-to-reference-date>";
				$xml .= $this->_calculateOffsetDays($referenceDate, $milestone->getDeadline(), $referenceExtremity, true);
				$xml .= "</offset-days-to-reference-date>\n";
			}
			else {
				$xml .= "<deadline>".$milestone->getDeadline()."</deadline>\n";
			}
			#print $this->_calculateOffsetDays($referenceDate, $milestone->getDeadline(), $referenceExtremity);
			#print ' - '.$milestone->getTitle()."\n";
			$todoLists = $project->findTodoListsByMilestoneId($milestone->getId());
			
			foreach($todoLists as $todoList)
			{
				$xml .= "<todo-list>\n";
				$xml .= "<name>".htmlentities($todoList->getName())."</name>\n";
				$xml .= "<description>".htmlentities($todoList->getDescription())."</description>\n";
				$xml .= "<private type=\"boolean\">".(($todoList->getIsPrivate()) ? 'true' : 'false')."</private>\n";
				
				foreach($todoList->getTodoItems() as $todoItem)
				{
					$xml .= "<todo-item>\n";
					$xml .= "<content>".htmlentities($todoItem->getContent())."</content>\n";
					
					if($todoItem->getResponsiblePartyId())
					{
						$xml .= "<responsible-party-id>".htmlentities($todoItem->getResponsiblePartyId())."</responsible-party-id>\n";
					}
					
					if($todoItem->getDueAt())
					{
						if($useRelativeDates)
						{
							$xml .= "<offset-days-to-reference-date>";
							$xml .= $this->_calculateOffsetDays($referenceDate, $todoItem->getDueAt(), $referenceExtremity, false);
							$xml .= "</offset-days-to-reference-date>\n";
						}
						else {
							$xml .= "<due-at>".htmlentities($todoItem->getDueAt())."</due-at>\n";
						}
					}
					$xml .= "</todo-item>\n";
				}
				$xml .= "</todo-list>\n";
			}
			$xml .= "</milestone>\n";
		}
		$xml .= "</project>";
		return $xml;
	}
	
}