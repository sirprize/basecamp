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


namespace Sirprize\Basecamp\TodoItem\Entity\Observer;


require_once 'Sirprize/Basecamp/TodoItem/Entity/Observer/Abstrakt.php';


/**
 * Class to observe and log state changes of an observed todoItem
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Log extends \Sirprize\Basecamp\TodoItem\Entity\Observer\Abstrakt
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
	
	
	public function onCompleteSuccess(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$this->_getLog()->info($this->_getOnCompleteSuccessMessage($todoItem));
	}
	
	
	public function onUncompleteSuccess(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$this->_getLog()->info($this->_getOnUncompleteSuccessMessage($todoItem));
	}
	
	
	public function onCreateSuccess(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$this->_getLog()->info($this->_getOnCreateSuccessMessage($todoItem));
	}
	
	
	public function onUpdateSuccess(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$this->_getLog()->info($this->_getOnUpdateSuccessMessage($todoItem));
	}
	
	
	public function onDeleteSuccess(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$this->_getLog()->info($this->_getOnDeleteSuccessMessage($todoItem));
	}
	
	
	
	
	
	
	public function onCompleteError(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$this->_getLog()->err($this->_getOnCompleteErrorMessage($todoItem));
	}
	
	
	public function onUncompleteError(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$this->_getLog()->err($this->_getOnUncompleteErrorMessage($todoItem));
	}
	
	
	public function onCreateError(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$this->_getLog()->err($this->_getOnCreateErrorMessage($todoItem));
	}
	
	
	public function onUpdateError(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$this->_getLog()->err($this->_getOnUpdateErrorMessage($todoItem));
	}
	
	
	public function onDeleteError(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$this->_getLog()->err($this->_getOnDeleteErrorMessage($todoItem));
	}
	
}