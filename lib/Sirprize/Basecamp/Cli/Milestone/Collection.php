<?php


namespace Sirprize\Basecamp\Cli\Milestone;




class Collection extends \Sirprize\Basecamp\Milestone\Collection
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
	 * Instantiate a new milestone entity
	 *
	 * @return \Sirprize\Basecamp\Cli\Milestone\Entity
	 */
	public function getMilestoneInstance()
	{
		$milestoneObserverStout = new \Sirprize\Basecamp\Milestone\Entity\Observer\Stout();
		
		$milestoneObserverLog = new \Sirprize\Basecamp\Milestone\Entity\Observer\Log();
		$milestoneObserverLog->setLog($this->_getLog());
		
		$milestone = new \Sirprize\Basecamp\Cli\Milestone\Entity();
		$milestone
			->setHttpClient($this->_getHttpClient())
			->setBasecamp($this->_getBasecamp())
			->attachObserver($milestoneObserverStout)
			->attachObserver($milestoneObserverLog)
		;
		
		return $milestone;
	}
	
}