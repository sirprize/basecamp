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


namespace Sirprize\Basecamp\Person\Collection\Observer;


/**
 * Abstract class to observe and print state changes of the observed person
 *
 * @category  Sirprize
 * @package   Basecamp
 */
abstract class Abstrakt
{
	
	
	abstract public function onStartSuccess(\Sirprize\Basecamp\Person\Collection $collection);
	abstract public function onStartError(\Sirprize\Basecamp\Person\Collection $collection);
	
	
	
	protected function _getOnStartSuccessMessage(\Sirprize\Basecamp\Person\Collection $collection)
	{
		return "started person collection. found ".$collection->count()." persons";
	}
	
	
	protected function _getOnStartErrorMessage(\Sirprize\Basecamp\Person\Collection $collection)
	{
		return "person collection could not be started";
	}
	
}