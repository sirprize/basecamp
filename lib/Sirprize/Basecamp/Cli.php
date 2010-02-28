<?php


namespace Sirprize\Basecamp;


require_once 'Sirprize/Basecamp.php';


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
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call setLog() before '.__METHOD__);
		}
		
		return $this->_log;
	}
	
	
	
	public function getProjectsInstance()
	{
		require_once 'Sirprize/Basecamp/Project/Collection/Observer/Stout.php';
		$projectsObserverStout = new \Sirprize\Basecamp\Project\Collection\Observer\Stout();
		
		require_once 'Sirprize/Basecamp/Project/Collection/Observer/Log.php';
		$projectsObserverLog = new \Sirprize\Basecamp\Project\Collection\Observer\Log();
		$projectsObserverLog->setLog($this->_getLog());
		
		require_once 'Sirprize/Basecamp/Cli/Project/Collection.php';
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
		require_once 'Sirprize/Basecamp/Milestone/Collection/Observer/Stout.php';
		$milestonesObserverStout = new \Sirprize\Basecamp\Milestone\Collection\Observer\Stout();
		
		require_once 'Sirprize/Basecamp/Milestone/Collection/Observer/Log.php';
		$milestonesObserverLog = new \Sirprize\Basecamp\Milestone\Collection\Observer\Log();
		$milestonesObserverLog->setLog($this->_getLog());
		
		require_once 'Sirprize/Basecamp/Cli/Milestone/Collection.php';
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
		require_once 'Sirprize/Basecamp/TodoList/Collection/Observer/Stout.php';
		$todoListsObserverStout = new \Sirprize\Basecamp\TodoList\Collection\Observer\Stout();
		
		require_once 'Sirprize/Basecamp/TodoList/Collection/Observer/Log.php';
		$todoListsObserverLog = new \Sirprize\Basecamp\TodoList\Collection\Observer\Log();
		$todoListsObserverLog->setLog($this->_getLog());
		
		require_once 'Sirprize/Basecamp/Cli/TodoList/Collection.php';
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
		require_once 'Sirprize/Basecamp/TodoItem/Collection/Observer/Stout.php';
		$todoItemsObserverStout = new \Sirprize\Basecamp\TodoItem\Collection\Observer\Stout();
		
		require_once 'Sirprize/Basecamp/TodoItem/Collection/Observer/Log.php';
		$todoItemsObserverLog = new \Sirprize\Basecamp\TodoItem\Collection\Observer\Log();
		$todoItemsObserverLog->setLog($this->_getLog());
		
		require_once 'Sirprize/Basecamp/Cli/TodoItem/Collection.php';
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