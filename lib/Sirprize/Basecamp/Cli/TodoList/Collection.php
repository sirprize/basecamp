<?php


namespace Sirprize\Basecamp\Cli\TodoList;


require_once 'Sirprize/Basecamp/TodoList/Collection.php';


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
			require_once 'Sirprize/Basecamp/Exception.php';
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
		require_once 'Sirprize/Basecamp/TodoList/Entity/Observer/Stout.php';
		$todoListObserverStout = new \Sirprize\Basecamp\TodoList\Entity\Observer\Stout();
		
		require_once 'Sirprize/Basecamp/TodoList/Entity/Observer/Log.php';
		$todoListObserverLog = new \Sirprize\Basecamp\TodoList\Entity\Observer\Log();
		$todoListObserverLog->setLog($this->_getLog());
		
		require_once 'Sirprize/Basecamp/Cli/TodoList/Entity.php';
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