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
 * Define the collection observer interface
 *
 * @category  Sirprize
 * @package   Basecamp
 */
interface Interfaze
{
	
	public function onStartSuccess(\Sirprize\Basecamp\Milestone\Collection $collection);
	public function onCreateSuccess(\Sirprize\Basecamp\Milestone\Collection $collection);
	public function onStartError(\Sirprize\Basecamp\Milestone\Collection $collection);
	public function onCreateError(\Sirprize\Basecamp\Milestone\Collection $collection);
	
}