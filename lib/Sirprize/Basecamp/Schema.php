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


class Schema
{
	
	
	protected $_basecamp = null;
	protected $_milestones = null;
	
	
	
	
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
	
	
	
	public function getMilestones()
	{
		if($this->_milestones === null)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call loadFromFile() before '.__METHOD__);
		}
		
		return $this->_milestones;
	}
	
	
	
	/**
	 * @return \Sirprize\Basecamp\Milestone\Collection
	 */
	public function loadFromFile($file, \Sirprize\Basecamp\Date $referenceDate = null)
	{
		if(!is_readable($file))
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception("'$file' must be readable");
		}
		
		$xml = new \DOMDocument();
		$xml->load($file);
		return $this->_load($xml, $referenceDate);
	}
	
	
	
	
	/**
	 * @return \Sirprize\Basecamp\Milestone\Collection
	 */
	public function loadFromString($string, \Sirprize\Basecamp\Date $referenceDate = null)
	{
		$xml = new \DOMDocument();
		$xml->loadXml($string);
		return $this->_load($xml, $referenceDate);
	}
	
	
	
	
	/**
	 * Create unpersisted tree-structure of milestones, todo-lists and todo-items from an Xml document
	 *
	 * Deadlines can be set explicitly or they can be calculated based on offset-days-to-reference-date
	 * if offset-days-to-reference-date is not present, then deadline or the current date will be used
	 *
	 * @return \Sirprize\Basecamp\Milestone\Collection
	 */
	protected function _load(\DOMDocument $xml, \Sirprize\Basecamp\Date $referenceDate = null)
	{
		$this->_milestones = $this->_getBasecamp()->getMilestonesInstance();
		
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
			
			if(!$this->_checkBeforeCreatingMilestone($title))
			{
				continue;
			}
			
			$milestone = $this->_getBasecamp()->getMilestonesInstance()->getMilestoneInstance();
			$milestone->setTitle($this->_getMilestoneTitle($title));
			
			require_once 'Sirprize/Basecamp/Date.php';
			
			if(\Sirprize\Basecamp\Date::isValid($deadline))
			{
				$milestone->setDeadline(new \Sirprize\Basecamp\Date($deadline));
			}
			else if($referenceDate !== null && $referenceDateOffset !== null)
			{
				$deadline = $this->_calculateDateFromOffsetDays($referenceDate, $referenceDateOffset);
				$milestone->setDeadline(new \Sirprize\Basecamp\Date($deadline));
			}
			else {
				require_once 'Sirprize/Basecamp/Exception.php';
				throw new \Sirprize\Basecamp\Exception("invalid reference date and invalid reference date offset");
			}
			
			if($responsiblePartyId !== null)
			{
				require_once 'Sirprize/Basecamp/Id.php';
				$responsiblePartyId = new \Sirprize\Basecamp\Id($responsiblePartyId);
				$milestone->setResponsiblePartyId($responsiblePartyId);
			}
			
			$this->_milestones->attach($milestone);
			
	
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
					
					require_once 'Sirprize/Basecamp/Date.php';
					
					if(\Sirprize\Basecamp\Date::isValid($dueAt))
					{
						$todoItem->setDueAt(new \Sirprize\Basecamp\Date($dueAt));
					}
					else if($referenceDate !== null && $referenceDateOffset !== null)
					{
						$dueAt = $this->_calculateDateFromOffsetDays($referenceDate, $referenceDateOffset);
						$todoItem->setDueAt(new \Sirprize\Basecamp\Date($dueAt));
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
		
		return $this;
	}
	
	
	
	protected function _checkBeforeCreatingMilestone($title)
	{
		return true;
	}
	
	
	
	protected function _getMilestoneTitle($title)
	{
		return $title;
	}
	
	
	
	protected function _calculateDateFromOffsetDays(\Sirprize\Basecamp\Date $referenceDate, $referenceDateOffset)
	{
		require_once 'Zend/Date.php';
		require_once 'Sirprize/Basecamp/Date.php';
		$referenceDate = new \Zend_Date((string)$referenceDate, \Sirprize\Basecamp\Date::FORMAT);
		$referenceDateOffset = (int)$referenceDateOffset;
	
		if($referenceDateOffset >= 0)
		{
			return $referenceDate->addSecond(60 * 60 * 24 * $referenceDateOffset)->toString(\Sirprize\Basecamp\Date::FORMAT);
		}
	
		return $referenceDate->subSecond(60 * 60 * 24 * $referenceDateOffset * -1)->toString(\Sirprize\Basecamp\Date::FORMAT);
	}
	
	
	
	
	public function dumpIndices()
	{
		foreach($this->getMilestones() as $m => $milestone)
		{
			print "($m) ".$milestone->getTitle()."\n";
			
			foreach($milestone->getTodoLists() as $l => $todoList)
			{
				print "    ($m, $l) ".$todoList->getName()."\n";
				
				foreach($todoList->getTodoItems() as $i => $todoItem)
				{
					print "        ($m, $l, $i) ".$todoItem->getContent()."\n";
				}
			}
			
			print "------------------------------\n";
		}
	}
}