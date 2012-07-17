<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */
 
namespace Sirprize\Basecamp;

use Sirprize\Basecamp\Service;
use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\Project\Collection\Observer\Stout as ProjectStout;
use Sirprize\Basecamp\Project\Collection\Observer\Log as ProjectLog;
use Sirprize\Basecamp\Cli\Project\Collection as ProjectCollection;
use Sirprize\Basecamp\Milestone\Collection\Observer\Stout as MilestoneStout;
use Sirprize\Basecamp\Milestone\Collection\Observer\Log as MilestoneLog;
use Sirprize\Basecamp\Cli\Milestone\Collection as MilestoneCollection;
use Sirprize\Basecamp\TodoList\Collection\Observer\Stout as TodoListStout;
use Sirprize\Basecamp\TodoList\Collection\Observer\Log as TodoListLog;
use Sirprize\Basecamp\Cli\TodoList\Collection as TodoListCollection;
use Sirprize\Basecamp\TodoItem\Collection\Observer\Stout as TodoItemStout;
use Sirprize\Basecamp\TodoItem\Collection\Observer\Log as TodoItemLog;
use Sirprize\Basecamp\Cli\TodoItem\Collection as TodoItemCollection;

class Cli extends Service
{

    protected $_log = null;

    public function setLog(\Zend_Log $log)
    {
        $this->_log = $log;
        return $this;
    }

    protected function _getLog()
    {
        if($this->_log === null)
        {
            throw new Exception('call setLog() before '.__METHOD__);
        }

        return $this->_log;
    }

    public function getProjectsInstance()
    {
        $projectsObserverStout = new ProjectStout();

        $projectsObserverLog = new ProjectLog();
        $projectsObserverLog->setLog($this->_getLog());

        $projects = new ProjectCollection();
        $projects
            ->setService($this)
            ->setHttpClient($this->_getHttpClient())
            #->setLog($this->_getLog())
            ->attachObserver($projectsObserverStout)
            ->attachObserver($projectsObserverLog)
        ;
        return $projects;
    }

    public function getMilestonesInstance()
    {
        $milestonesObserverStout = new MilestoneStout();

        $milestonesObserverLog = new MilestoneLog();
        $milestonesObserverLog->setLog($this->_getLog());

        $milestones = new MilestoneCollection();
        $milestones
            ->setService($this)
            ->setHttpClient($this->_getHttpClient())
            ->setLog($this->_getLog())
            ->attachObserver($milestonesObserverStout)
            ->attachObserver($milestonesObserverLog)
        ;
        return $milestones;
    }

    public function getTodoListsInstance()
    {
        $todoListsObserverStout = new TodoListStout();

        $todoListsObserverLog = new TodoListLog();
        $todoListsObserverLog->setLog($this->_getLog());

        $todoLists = new TodoListCollection();
        $todoLists
            ->setService($this)
            ->setHttpClient($this->_getHttpClient())
            ->setLog($this->_getLog())
            ->attachObserver($todoListsObserverStout)
            ->attachObserver($todoListsObserverLog)
        ;
        return $todoLists;
    }

    public function getTodoItemsInstance()
    {
        $todoItemsObserverStout = new TodoItemStout();

        $todoItemsObserverLog = new TodoItemLog();
        $todoItemsObserverLog->setLog($this->_getLog());

        $todoItems = new TodoItemCollection();
        $todoItems
            ->setService($this)
            ->setHttpClient($this->_getHttpClient())
            ->setLog($this->_getLog())
            ->attachObserver($todoItemsObserverStout)
            ->attachObserver($todoItemsObserverLog)
        ;
        return $todoItems;
    }

}