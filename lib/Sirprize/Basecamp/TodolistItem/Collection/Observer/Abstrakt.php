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


namespace Sirprize\Basecamp\TodolistItem\Collection\Observer;


/**
 * Abstract class to observe and print state changes of the observed todo-items
 *
 * @category  Sirprize
 * @package   Basecamp
 */
abstract class Abstrakt
{
	
	
	abstract public function onStartSuccess(\Sirprize\Basecamp\TodolistItem\Collection $collection);
	abstract public function onStartError(\Sirprize\Basecamp\TodolistItem\Collection $collection);
	
	
	
	protected function _getOnStartSuccessMessage(\Sirprize\Basecamp\TodolistItem\Collection $collection)
	{
		return "started todo-item collection. found ".$collection->count()." todo-items";
	}
	
	
	protected function _getOnStartErrorMessage(\Sirprize\Basecamp\TodolistItem\Collection $collection)
	{
		return "todo-item collection could not be started";
	}
	
}