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


class Export
{
	
	
	// tokens that are unlikely to be milestone-titles
	const REFERENCE_EXTREMITY_FIRST = '________FIRST________';
	const REFERENCE_EXTREMITY_LAST = '________LAST________';
	
	
	
	public function getProjectXml(\Sirprize\Basecamp\Project\Entity $project, $useRelativeDates = true, $referenceMilestone = null)
	{
		$project->startSubElements();
		
		if($referenceMilestone === null)
		{
			$referenceMilestone = self::REFERENCE_EXTREMITY_LAST;
		}
		
		$referenceDate = $this->_getReferenceDate($project->getMilestones(), $referenceMilestone);
		
		if($useRelativeDates && $referenceDate === null)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception("milestone '$referenceMilestone' does not exist");
		}
		
		$xml  = "<?xml version=\"1.0\"?>\n";
		$xml .= "<project>\n";
		$xml .= "<id>".$project->getId()."</id>\n";
		$xml .= "<name>".htmlspecialchars($project->getName(), ENT_NOQUOTES)."</name>\n";
		$xml .= "<announcement>".htmlspecialchars($project->getAnnouncement(), ENT_NOQUOTES)."</announcement>\n";
		$xml .= "<status>".$project->getStatus()."</status>\n";
		$xml .= "<company>".htmlspecialchars(trim($project->getCompany()), ENT_NOQUOTES)."</company>\n";
		
		foreach($project->getMilestones() as $milestone)
		{
			$xml .= "<milestone>\n";
			$xml .= "<title>".htmlspecialchars($milestone->getTitle(), ENT_NOQUOTES)."</title>\n";
			$xml .= "<responsible-party-id>".htmlspecialchars($milestone->getResponsiblePartyId(), ENT_NOQUOTES)."</responsible-party-id>\n";
			
			if($useRelativeDates)
			{
				$xml .= "<offset-days-to-reference-date>";
				$xml .= $this->_calculateOffsetDays($referenceDate, $milestone->getDeadline(), $referenceMilestone, true);
				$xml .= "</offset-days-to-reference-date>\n";
			}
			else {
				$xml .= "<deadline>".$milestone->getDeadline()."</deadline>\n";
			}
			
			$todoLists = $project->findTodoListsByMilestoneId($milestone->getId());
			
			foreach($todoLists as $todoList)
			{
				$xml .= "<todo-list>\n";
				$xml .= "<name>".htmlspecialchars($todoList->getName(), ENT_NOQUOTES)."</name>\n";
				$xml .= "<description>".htmlspecialchars($todoList->getDescription(), ENT_NOQUOTES)."</description>\n";
				$xml .= "<private type=\"boolean\">".(($todoList->getIsPrivate()) ? 'true' : 'false')."</private>\n";
				
				foreach($todoList->getTodoItems() as $todoItem)
				{
					$xml .= "<todo-item>\n";
					$xml .= "<content>".htmlspecialchars($todoItem->getContent(), ENT_NOQUOTES)."</content>\n";
					
					if($todoItem->getResponsiblePartyId())
					{
						$xml .= "<responsible-party-id>".htmlspecialchars($todoItem->getResponsiblePartyId(), ENT_NOQUOTES)."</responsible-party-id>\n";
					}
					
					if($todoItem->getDueAt())
					{
						if($useRelativeDates)
						{
							$xml .= "<offset-days-to-reference-date>";
							$xml .= $this->_calculateOffsetDays($referenceDate, $todoItem->getDueAt(), $referenceMilestone, false);
							$xml .= "</offset-days-to-reference-date>\n";
						}
						else {
							$xml .= "<due-at>".htmlspecialchars($todoItem->getDueAt(), ENT_NOQUOTES)."</due-at>\n";
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
	
	
	
	protected function _getReferenceDate(\Sirprize\Basecamp\Milestone\Collection $milestones, $referenceMilestone)
	{
		if(!$milestones->count())
		{
			return null;
		}
		
		if($referenceMilestone == self::REFERENCE_EXTREMITY_FIRST)
		{
			$milestones->rewind();
			return $milestones->current()->getDeadline();
		}
		
		if($referenceMilestone == self::REFERENCE_EXTREMITY_LAST)
		{
			$last = null;
			$milestones->rewind();
		
			while($milestones->valid())
			{
				$last = $milestones->current()->getDeadline();
				$milestones->next();
			}
		
			return $last;
		}
		
		foreach($milestones as $milestone)
		{
			if($milestone->getTitle() == $referenceMilestone)
			{
				return $milestone->getDeadline();
			}
		}
		
		return null;
	}
	
	
	
	protected function _calculateOffsetDays($referenceDate, $effectiveDate, $referenceMilestone, $isMilestone)
	{
		require_once 'Zend/Date.php';
		require_once 'Sirprize/Basecamp/Date.php';
		$referenceDate = new \Zend_Date($referenceDate, \Sirprize\Basecamp\Date::FORMAT);
		$effectiveDate = new \Zend_Date($effectiveDate, \Sirprize\Basecamp\Date::FORMAT);
		
		$offset
			= ($referenceMilestone == self::REFERENCE_EXTREMITY_LAST)
			? ($referenceDate->getTimestamp() - $effectiveDate->getTimestamp()) * -1
			: $effectiveDate->getTimestamp() - $referenceDate->getTimestamp()
		;
		
		if($isMilestone)
		{
			return $offset / 60 / 60 / 24;
		}
		
		return // quick fix for todo-item dates which seem to be 1 day off (?)
			  ($referenceMilestone == self::REFERENCE_EXTREMITY_LAST)
			? ($offset / 60 / 60 / 24) + 1
			: ($offset / 60 / 60 / 24) - 1
		;
	}
}