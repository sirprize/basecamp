<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp;

use Sirprize\Basecamp\Id;
use Sirprize\Basecamp\Date;
use Sirprize\Basecamp\Service;
use Sirprize\Basecamp\Exception;

class Schema
{

    protected $_service = null;
    protected $_milestones = null;

    public function setService(Service $service)
    {
        $this->_service = $service;
        return $this;
    }

    protected function _getService()
    {
        if($this->_service === null)
        {
            throw new Exception('call setService() before '.__METHOD__);
        }

        return $this->_service;
    }

    public function getMilestones()
    {
        if($this->_milestones === null)
        {
            throw new Exception('call loadFromFile() before '.__METHOD__);
        }

        return $this->_milestones;
    }

    /**
     * @return \Sirprize\Basecamp\Milestone\Collection
     */
    public function loadFromFile($file, Date $referenceDate = null)
    {
        if(!is_readable($file))
        {
            throw new Exception("'$file' must be readable");
        }

        $xml = new \DOMDocument();
        $xml->load($file);
        return $this->_load($xml, $referenceDate);
    }

    /**
     * @return \Sirprize\Basecamp\Milestone\Collection
     */
    public function loadFromString($string, Date $referenceDate = null)
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
    protected function _load(\DOMDocument $xml, Date $referenceDate = null)
    {
        $this->_milestones = $this->_getService()->getMilestonesInstance();

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

            $milestone = $this->_getService()->getMilestonesInstance()->getMilestoneInstance();
            $milestone->setTitle($this->_getMilestoneTitle($title));

            if(Date::isValid($deadline))
            {
                $milestone->setDeadline(new Date($deadline));
            }
            else if($referenceDate !== null && $referenceDateOffset !== null)
            {
                $deadline = $this->_calculateDateFromOffsetDays($referenceDate, $referenceDateOffset);
                $milestone->setDeadline(new Date($deadline));
            }
            else {
                throw new Exception("invalid reference date and invalid reference date offset");
            }

            if($responsiblePartyId !== null)
            {
                $responsiblePartyId = new Id($responsiblePartyId);
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

                    if(Date::isValid($dueAt))
                    {
                        $todoItem->setDueAt(new Date($dueAt));
                    }
                    else if($referenceDate !== null && $referenceDateOffset !== null)
                    {
                        $dueAt = $this->_calculateDateFromOffsetDays($referenceDate, $referenceDateOffset);
                        $todoItem->setDueAt(new Date($dueAt));
                    }

                    if($responsiblePartyId !== null)
                    {
                        $responsiblePartyId = new Id($responsiblePartyId);
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

    protected function _calculateDateFromOffsetDays(Date $referenceDate, $referenceDateOffset)
    {
        $referenceDate = new \Zend_Date((string)$referenceDate, Date::FORMAT);
        $referenceDateOffset = (int)$referenceDateOffset;

        if($referenceDateOffset >= 0)
        {
            return $referenceDate->addSecond(60 * 60 * 24 * $referenceDateOffset)->toString(Date::FORMAT);
        }

        return $referenceDate->subSecond(60 * 60 * 24 * $referenceDateOffset * -1)->toString(Date::FORMAT);
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