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
 * Class to observe and print state changes of the observed todolistItem
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Stout extends \Sirprize\Basecamp\TodolistItem\Entity\Observer\Abstrakt
{
	
	
	public function onCompleteSuccess(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		print $this->_getOnCompleteSuccessMessage($todolistItem)."\n";
	}
	
	
	public function onUncompleteSuccess(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		print $this->_getOnUncompleteSuccessMessage($todolistItem)."\n";
	}
	
	
	public function onCreateSuccess(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		print $this->_getOnCreateSuccessMessage($todolistItem)."\n";
	}
	
	
	public function onUpdateSuccess(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		print $this->_getOnUpdateSuccessMessage($todolistItem)."\n";
	}
	
	
	public function onDeleteSuccess(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		print $this->_getOnDeleteSuccessMessage($todolistItem)."\n";
	}
	
	
	
	
	
	
	
	
	public function onCompleteError(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		print $this->_getOnCompleteErrorMessage($todolistItem)."\n";
	}
	
	
	public function onUncompleteError(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		print $this->_getOnUncompleteErrorMessage($todolistItem)."\n";
	}
	
	
	public function onCreateError(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		print $this->_getOnCreateErrorMessage($todolistItem)."\n";
	}
	
	
	public function onUpdateError(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		print $this->_getOnUpdateErrorMessage($todolistItem)."\n";
	}
	
	
	public function onDeleteError(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		print $this->_getOnDeleteErrorMessage($todolistItem)."\n";
	}
	
}