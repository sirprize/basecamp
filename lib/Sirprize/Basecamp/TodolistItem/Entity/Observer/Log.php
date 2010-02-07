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


namespace Sirprize\Basecamp\TodolistItem\Entity\Observer;


require_once 'Sirprize/Basecamp/TodolistItem/Entity/Observer/Abstrakt.php';


/**
 * Class to observe and log state changes of an observed todolistItem
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Log extends \Sirprize\Basecamp\TodolistItem\Entity\Observer\Abstrakt
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
	
	
	public function onCompleteSuccess(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$this->_getLog()->info($this->_getOnCompleteSuccessMessage($todolistItem));
	}
	
	
	public function onUncompleteSuccess(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$this->_getLog()->info($this->_getOnUncompleteSuccessMessage($todolistItem));
	}
	
	
	public function onCreateSuccess(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$this->_getLog()->info($this->_getOnCreateSuccessMessage($todolistItem));
	}
	
	
	public function onUpdateSuccess(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$this->_getLog()->info($this->_getOnUpdateSuccessMessage($todolistItem));
	}
	
	
	public function onDeleteSuccess(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$this->_getLog()->info($this->_getOnDeleteSuccessMessage($todolistItem));
	}
	
	
	
	
	
	
	public function onCompleteError(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$this->_getLog()->err($this->_getOnCompleteErrorMessage($todolistItem));
	}
	
	
	public function onUncompleteError(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$this->_getLog()->err($this->_getOnUncompleteErrorMessage($todolistItem));
	}
	
	
	public function onCreateError(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$this->_getLog()->err($this->_getOnCreateErrorMessage($todolistItem));
	}
	
	
	public function onUpdateError(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$this->_getLog()->err($this->_getOnUpdateErrorMessage($todolistItem));
	}
	
	
	public function onDeleteError(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$this->_getLog()->err($this->_getOnDeleteErrorMessage($todolistItem));
	}
	
}