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


namespace Sirprize\Basecamp\Todolist\Collection\Observer;


require_once 'Sirprize/Basecamp/Todolist/Collection/Observer/Abstrakt.php';


/**
 * Class to observe and print state changes of the observed todolist
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Stout extends \Sirprize\Basecamp\Todolist\Collection\Observer\Abstrakt
{
	
	
	public function onStartSuccess(\Sirprize\Basecamp\Todolist\Collection $collection)
	{
		print $this->_getOnStartSuccessMessage($collection)."\n";
	}
	
	
	public function onStartError(\Sirprize\Basecamp\Todolist\Collection $collection)
	{
		print $this->_getOnStartErrorMessage($collection)."\n";
	}
	
}