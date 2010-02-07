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


namespace Sirprize\Basecamp\Milestone\Collection\Observer;


/**
 * Abstract class to observe and print state changes of the observed milestone
 *
 * @category  Sirprize
 * @package   Basecamp
 */
abstract class Abstrakt
{
	
	
	abstract public function onStartSuccess(\Sirprize\Basecamp\Milestone\Collection $collection);
	abstract public function onCreateSuccess(\Sirprize\Basecamp\Milestone\Collection $collection);
	abstract public function onStartError(\Sirprize\Basecamp\Milestone\Collection $collection);
	abstract public function onCreateError(\Sirprize\Basecamp\Milestone\Collection $collection);
	
	
	
	protected function _getOnStartSuccessMessage(\Sirprize\Basecamp\Milestone\Collection $collection)
	{
		return "started milestone collection. found ".$collection->count()." milestones";
	}
	
	
	protected function _getOnCreateSuccessMessage(\Sirprize\Basecamp\Milestone\Collection $collection)
	{
		return "milestones have been created for this collection";
	}
	
	
	protected function _getOnStartErrorMessage(\Sirprize\Basecamp\Milestone\Collection $collection)
	{
		return "milestone collection could not be started";
	}
	
	
	protected function _getOnCreateErrorMessage(\Sirprize\Basecamp\Milestone\Collection $collection)
	{
		return "milestone collection could not be created";
	}
	
}