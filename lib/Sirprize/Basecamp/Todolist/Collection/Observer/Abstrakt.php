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


/**
 * Abstract class to observe and print state changes of the observed todolist
 *
 * @category  Sirprize
 * @package   Basecamp
 */
abstract class Abstrakt
{
	
	
	abstract public function onStartSuccess(\Sirprize\Basecamp\Todolist\Collection $collection);
	abstract public function onStartError(\Sirprize\Basecamp\Todolist\Collection $collection);
	
	
	
	protected function _getOnStartSuccessMessage(\Sirprize\Basecamp\Todolist\Collection $collection)
	{
		return "started todolist collection. found ".$collection->count()." todolists";
	}
	
	
	protected function _getOnStartErrorMessage(\Sirprize\Basecamp\Todolist\Collection $collection)
	{
		return "todolist collection could not be started";
	}
	
}