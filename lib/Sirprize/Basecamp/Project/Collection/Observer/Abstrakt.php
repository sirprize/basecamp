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


namespace Sirprize\Basecamp\Project\Collection\Observer;


/**
 * Abstract class to observe and print state changes of the observed project
 *
 * @category  Sirprize
 * @package   Basecamp
 */
abstract class Abstrakt
{
	
	
	abstract public function onStartSuccess(\Sirprize\Basecamp\Project\Collection $collection);
	abstract public function onStartError(\Sirprize\Basecamp\Project\Collection $collection);
	
	
	
	protected function _getOnStartSuccessMessage(\Sirprize\Basecamp\Project\Collection $collection)
	{
		return "started project collection. found ".$collection->count()." projects";
	}
	
	
	protected function _getOnStartErrorMessage(\Sirprize\Basecamp\Project\Collection $collection)
	{
		return "project collection could not be started";
	}
	
}