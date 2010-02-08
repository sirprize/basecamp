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


namespace Sirprize\Basecamp\TodoList\Entity\Observer;


require_once 'Sirprize/Basecamp/TodoList/Entity/Observer/Abstrakt.php';


/**
 * Class to observe and log state changes of an observed todoList
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Log extends \Sirprize\Basecamp\TodoList\Entity\Observer\Abstrakt
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
	
	
	public function onCreateSuccess(\Sirprize\Basecamp\TodoList\Entity $todoList)
	{
		$this->_getLog()->info($this->_getOnCreateSuccessMessage($todoList));
	}
	
	
	public function onUpdateSuccess(\Sirprize\Basecamp\TodoList\Entity $todoList)
	{
		$this->_getLog()->info($this->_getOnUpdateSuccessMessage($todoList));
	}
	
	
	public function onDeleteSuccess(\Sirprize\Basecamp\TodoList\Entity $todoList)
	{
		$this->_getLog()->info($this->_getOnDeleteSuccessMessage($todoList));
	}
	
	
	
	
	
	public function onCreateError(\Sirprize\Basecamp\TodoList\Entity $todoList)
	{
		$this->_getLog()->err($this->_getOnCreateErrorMessage($todoList));
	}
	
	
	public function onUpdateError(\Sirprize\Basecamp\TodoList\Entity $todoList)
	{
		$this->_getLog()->err($this->_getOnUpdateErrorMessage($todoList));
	}
	
	
	public function onDeleteError(\Sirprize\Basecamp\TodoList\Entity $todoList)
	{
		$this->_getLog()->err($this->_getOnDeleteErrorMessage($todoList));
	}
	
}