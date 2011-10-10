<?php


namespace Sirprize\Basecamp\Cli\TodoItem;




class Collection extends \Sirprize\Basecamp\TodoItem\Collection
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
	 * Instantiate a new todoItem entity
	 *
	 * @return \Sirprize\Basecamp\Cli\TodoItem\Entity
	 */
	public function getTodoItemInstance()
	{
		$todoItemObserverStout = new \Sirprize\Basecamp\TodoItem\Entity\Observer\Stout();
		
		$todoItemObserverLog = new \Sirprize\Basecamp\TodoItem\Entity\Observer\Log();
		$todoItemObserverLog->setLog($this->_getLog());
		
		$todoItem = new \Sirprize\Basecamp\Cli\TodoItem\Entity();
		$todoItem
			->setHttpClient($this->_getHttpClient())
			->setBasecamp($this->_getBasecamp())
			->attachObserver($todoItemObserverStout)
			->attachObserver($todoItemObserverLog)
		;
		
		return $todoItem;
	}
	
}