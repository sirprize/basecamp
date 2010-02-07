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


namespace Sirprize\Basecamp\Todolist\Entity\Observer;


require_once 'Sirprize/Basecamp/Todolist/Entity/Observer/Abstrakt.php';


/**
 * Class to observe and print state changes of the observed todolist
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Stout extends \Sirprize\Basecamp\Todolist\Entity\Observer\Abstrakt
{
	
	
	public function onCreateSuccess(\Sirprize\Basecamp\Todolist\Entity $todolist)
	{
		print $this->_getOnCreateSuccessMessage($todolist)."\n";
	}
	
	
	public function onUpdateSuccess(\Sirprize\Basecamp\Todolist\Entity $todolist)
	{
		print $this->_getOnUpdateSuccessMessage($todolist)."\n";
	}
	
	
	public function onDeleteSuccess(\Sirprize\Basecamp\Todolist\Entity $todolist)
	{
		print $this->_getOnDeleteSuccessMessage($todolist)."\n";
	}
	
	
	
	
	
	
	public function onCreateError(\Sirprize\Basecamp\Todolist\Entity $todolist)
	{
		print $this->_getOnCreateErrorMessage($todolist)."\n";
	}
	
	
	public function onUpdateError(\Sirprize\Basecamp\Todolist\Entity $todolist)
	{
		print $this->_getOnUpdateErrorMessage($todolist)."\n";
	}
	
	
	public function onDeleteError(\Sirprize\Basecamp\Todolist\Entity $todolist)
	{
		print $this->_getOnDeleteErrorMessage($todolist)."\n";
	}
	
}