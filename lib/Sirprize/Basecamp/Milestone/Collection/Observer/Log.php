<?php

/**
 * Basecamp API Wrapper for PHP 5.3+ 
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt
 *
 * @category   Sirprize
 * @package    Basecamp
 * @copyright  Copyright (c) 2010, Christian Hoegl, Switzerland (http://sirprize.me)
 * @license    MIT License
 */


namespace Sirprize\Basecamp\Milestone\Collection\Observer;


require_once 'Sirprize/Basecamp/Milestone/Collection/Observer/Interfaze.php';


/**
 * Class to observe and log state changes of an observed collection
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Log implements \Sirprize\Basecamp\Milestone\Collection\Observer\Interfaze
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
	
	
	public function onStartSuccess(\Sirprize\Basecamp\Milestone\Collection $collection)
	{
		$message = "started milestone collection";
		$this->_getLog()->info($message);
	}
	
	
	public function onCreateSuccess(\Sirprize\Basecamp\Milestone\Collection $collection)
	{
		$message = "milestones have been created for this collection";
		$this->_getLog()->info($message);
	}
	
	
	public function onStartError(\Sirprize\Basecamp\Milestone\Collection $collection)
	{
		$message = "milestone collection could not be started";
		$this->_getLog()->err($message);
	}
	
	
	public function onCreateError(\Sirprize\Basecamp\Milestone\Collection $collection)
	{
		$message = "milestone collection could not be created";
		$this->_getLog()->err($message);
	}
	
}