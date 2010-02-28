<?php


namespace Sirprize\Basecamp\Cli\TodoItem;


require_once 'Sirprize/Basecamp/TodoItem/Collection.php';


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
			require_once 'Sirprize/Basecamp/Exception.php';
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
		require_once 'Sirprize/Basecamp/TodoItem/Entity/Observer/Stout.php';
		$todoItemObserverStout = new \Sirprize\Basecamp\TodoItem\Entity\Observer\Stout();
		
		require_once 'Sirprize/Basecamp/TodoItem/Entity/Observer/Log.php';
		$todoItemObserverLog = new \Sirprize\Basecamp\TodoItem\Entity\Observer\Log();
		$todoItemObserverLog->setLog($this->_getLog());
		
		require_once 'Sirprize/Basecamp/Cli/TodoItem/Entity.php';
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