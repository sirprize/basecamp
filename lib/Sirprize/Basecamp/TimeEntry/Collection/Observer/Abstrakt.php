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


namespace Sirprize\Basecamp\TimeEntry\Collection\Observer;


/**
 * Abstract class to observe and print state changes of the observed time entry
 *
 * @category  Sirprize
 * @package   Basecamp
 */
abstract class Abstrakt
{
	
	
	abstract public function onStartSuccess(\Sirprize\Basecamp\TimeEntry\Collection $collection);
	abstract public function onStartError(\Sirprize\Basecamp\TimeEntry\Collection $collection);
	
	
	
	protected function _getOnStartSuccessMessage(\Sirprize\Basecamp\TimeEntry\Collection $collection)
	{
		return "started time entry collection. found ".$collection->count()." entries";
	}
	
	
	protected function _getOnStartErrorMessage(\Sirprize\Basecamp\TimeEntry\Collection $collection)
	{
		return "time entry collection could not be started";
	}
	
}