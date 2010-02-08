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
 * Class to observe and print state changes of the observed todoList
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Stout extends \Sirprize\Basecamp\TodoList\Entity\Observer\Abstrakt
{
	
	
	public function onCreateSuccess(\Sirprize\Basecamp\TodoList\Entity $todoList)
	{
		print $this->_getOnCreateSuccessMessage($todoList)."\n";
	}
	
	
	public function onUpdateSuccess(\Sirprize\Basecamp\TodoList\Entity $todoList)
	{
		print $this->_getOnUpdateSuccessMessage($todoList)."\n";
	}
	
	
	public function onDeleteSuccess(\Sirprize\Basecamp\TodoList\Entity $todoList)
	{
		print $this->_getOnDeleteSuccessMessage($todoList)."\n";
	}
	
	
	
	
	
	
	public function onCreateError(\Sirprize\Basecamp\TodoList\Entity $todoList)
	{
		print $this->_getOnCreateErrorMessage($todoList)."\n";
	}
	
	
	public function onUpdateError(\Sirprize\Basecamp\TodoList\Entity $todoList)
	{
		print $this->_getOnUpdateErrorMessage($todoList)."\n";
	}
	
	
	public function onDeleteError(\Sirprize\Basecamp\TodoList\Entity $todoList)
	{
		print $this->_getOnDeleteErrorMessage($todoList)."\n";
	}
	
}