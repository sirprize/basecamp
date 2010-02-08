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
 * Class to observe and print state changes of the observed todoItem
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Stout extends \Sirprize\Basecamp\TodoItem\Entity\Observer\Abstrakt
{
	
	
	public function onCompleteSuccess(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		print $this->_getOnCompleteSuccessMessage($todoItem)."\n";
	}
	
	
	public function onUncompleteSuccess(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		print $this->_getOnUncompleteSuccessMessage($todoItem)."\n";
	}
	
	
	public function onCreateSuccess(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		print $this->_getOnCreateSuccessMessage($todoItem)."\n";
	}
	
	
	public function onUpdateSuccess(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		print $this->_getOnUpdateSuccessMessage($todoItem)."\n";
	}
	
	
	public function onDeleteSuccess(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		print $this->_getOnDeleteSuccessMessage($todoItem)."\n";
	}
	
	
	
	
	
	
	
	
	public function onCompleteError(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		print $this->_getOnCompleteErrorMessage($todoItem)."\n";
	}
	
	
	public function onUncompleteError(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		print $this->_getOnUncompleteErrorMessage($todoItem)."\n";
	}
	
	
	public function onCreateError(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		print $this->_getOnCreateErrorMessage($todoItem)."\n";
	}
	
	
	public function onUpdateError(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		print $this->_getOnUpdateErrorMessage($todoItem)."\n";
	}
	
	
	public function onDeleteError(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		print $this->_getOnDeleteErrorMessage($todoItem)."\n";
	}
	
}