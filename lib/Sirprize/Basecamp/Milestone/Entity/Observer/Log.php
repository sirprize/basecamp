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


namespace Sirprize\Basecamp\Milestone\Entity\Observer;




/**
 * Class to observe and log state changes of an observed milestone
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Log extends \Sirprize\Basecamp\Milestone\Entity\Observer\Abstrakt
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
	
	
	public function onCompleteSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$this->_getLog()->info($this->_getOnCompleteSuccessMessage($milestone));
	}
	
	
	public function onUncompleteSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$this->_getLog()->info($this->_getOnUncompleteSuccessMessage($milestone));
	}
	
	
	public function onCreateSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$this->_getLog()->info($this->_getOnCreateSuccessMessage($milestone));
	}
	
	
	public function onUpdateSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$this->_getLog()->info($this->_getOnUpdateSuccessMessage($milestone));
	}
	
	
	public function onDeleteSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$this->_getLog()->info($this->_getOnDeleteSuccessMessage($milestone));
	}
	
	
	
	
	
	
	public function onCompleteError(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$this->_getLog()->err($this->_getOnCompleteErrorMessage($milestone));
	}
	
	
	public function onUncompleteError(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$this->_getLog()->err($this->_getOnUncompleteErrorMessage($milestone));
	}
	
	
	public function onCreateError(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$this->_getLog()->err($this->_getOnCreateErrorMessage($milestone));
	}
	
	
	public function onUpdateError(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$this->_getLog()->err($this->_getOnUpdateErrorMessage($milestone));
	}
	
	
	public function onDeleteError(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$this->_getLog()->err($this->_getOnDeleteErrorMessage($milestone));
	}
	
}