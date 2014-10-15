<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Schema;

use Sirprize\Basecamp\Date;
use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\Milestone\Collection;

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
            throw new Exception("milestone '$referenceMilestone' does not exist");
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

    protected function _getReferenceDate(Collection $milestones, $referenceMilestone)
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
        $referenceDate = new \DateTime($referenceDate);
        $effectiveDate = new \DateTime($effectiveDate);

        $offset = $referenceDate->diff($effectiveDate);

        if($isMilestone)
        {
            return $offset->days;
        }

        return // quick fix for todo-item dates which seem to be 1 day off (?)
              ($referenceMilestone == self::REFERENCE_EXTREMITY_LAST)
            ? $offset->days + 1
            : $offset->days - 1
        ;
    }
}