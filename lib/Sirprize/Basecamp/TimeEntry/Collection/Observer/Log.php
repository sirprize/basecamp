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


namespace Sirprize\Basecamp\TimeEntry\Collection\Observer;


require_once 'Sirprize/Basecamp/TimeEntry/Collection/Observer/Abstrakt.php';


/**
 * Class to observe and log state changes of an observed collection
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Log extends \Sirprize\Basecamp\TimeEntry\Collection\Observer\Abstrakt
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
	
	
	public function onStartSuccess(\Sirprize\Basecamp\TimeEntry\Collection $collection)
	{
		$this->_getLog()->info($this->_getOnStartSuccessMessage($collection));
	}
	
	
	public function onStartError(\Sirprize\Basecamp\TimeEntry\Collection $collection)
	{
		$this->_getLog()->err($this->_getOnStartErrorMessage($collection));
	}
	
}