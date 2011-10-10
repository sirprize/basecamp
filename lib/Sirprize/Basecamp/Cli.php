<?php


namespace Sirprize\Basecamp;




class Cli extends \Sirprize\Basecamp
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
			throw new \Sirprize\Basecamp\Exception('call setLog() before '.__METHOD__);
		}
		
		return $this->_log;
	}
	
	
	
	public function getProjectsInstance()
	{
		$projectsObserverStout = new \Sirprize\Basecamp\Project\Collection\Observer\Stout();
		
		$projectsObserverLog = new \Sirprize\Basecamp\Project\Collection\Observer\Log();
		$projectsObserverLog->setLog($this->_getLog());
		
		$projects = new \Sirprize\Basecamp\Cli\Project\Collection();
		$projects
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
			#->setLog($this->_getLog())
			->attachObserver($projectsObserverStout)
			->attachObserver($projectsObserverLog)
		;
		return $projects;
	}
	
	
	
	public function getMilestonesInstance()
	{
		$milestonesObserverStout = new \Sirprize\Basecamp\Milestone\Collection\Observer\Stout();
		
		$milestonesObserverLog = new \Sirprize\Basecamp\Milestone\Collection\Observer\Log();
		$milestonesObserverLog->setLog($this->_getLog());
		
		$milestones = new \Sirprize\Basecamp\Cli\Milestone\Collection();
		$milestones
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
			->setLog($this->_getLog())
			->attachObserver($milestonesObserverStout)
			->attachObserver($milestonesObserverLog)
		;
		return $milestones;
	}
	
	
	
	public function getTodoListsInstance()
	{
		$todoListsObserverStout = new \Sirprize\Basecamp\TodoList\Collection\Observer\Stout();
		
		$todoListsObserverLog = new \Sirprize\Basecamp\TodoList\Collection\Observer\Log();
		$todoListsObserverLog->setLog($this->_getLog());
		
		$todoLists = new \Sirprize\Basecamp\Cli\TodoList\Collection();
		$todoLists
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
			->setLog($this->_getLog())
			->attachObserver($todoListsObserverStout)
			->attachObserver($todoListsObserverLog)
		;
		return $todoLists;
	}
	
	
	
	public function getTodoItemsInstance()
	{
		$todoItemsObserverStout = new \Sirprize\Basecamp\TodoItem\Collection\Observer\Stout();
		
		$todoItemsObserverLog = new \Sirprize\Basecamp\TodoItem\Collection\Observer\Log();
		$todoItemsObserverLog->setLog($this->_getLog());
		
		$todoItems = new \Sirprize\Basecamp\Cli\TodoItem\Collection();
		$todoItems
			->setBasecamp($this)
			->setHttpClient($this->_getHttpClient())
			->setLog($this->_getLog())
			->attachObserver($todoItemsObserverStout)
			->attachObserver($todoItemsObserverLog)
		;
		return $todoItems;
	}
	
}