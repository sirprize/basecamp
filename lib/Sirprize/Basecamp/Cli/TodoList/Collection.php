<?php


namespace Sirprize\Basecamp\Cli\TodoList;




class Collection extends \Sirprize\Basecamp\TodoList\Collection
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
	
	
	/**
	 * Instantiate a new todoList entity
	 *
	 * @return \Sirprize\Basecamp\Cli\TodoList\Entity
	 */
	public function getTodoListInstance()
	{
		$todoListObserverStout = new \Sirprize\Basecamp\TodoList\Entity\Observer\Stout();
		
		$todoListObserverLog = new \Sirprize\Basecamp\TodoList\Entity\Observer\Log();
		$todoListObserverLog->setLog($this->_getLog());
		
		$todoList = new \Sirprize\Basecamp\Cli\TodoList\Entity();
		$todoList
			->setHttpClient($this->_getHttpClient())
			->setBasecamp($this->_getBasecamp())
			->attachObserver($todoListObserverStout)
			->attachObserver($todoListObserverLog)
		;
		
		return $todoList;
	}
	
}