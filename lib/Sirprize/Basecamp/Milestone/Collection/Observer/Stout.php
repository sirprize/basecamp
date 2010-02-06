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


require_once 'Sirprize/Basecamp/Milestone/Collection/Observer/Interfaze.php';


/**
 * Class to observe and print state changes of the observed milestone
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Stout implements \Sirprize\Basecamp\Milestone\Collection\Observer\Interfaze
{
	
	
	public function onStartSuccess(\Sirprize\Basecamp\Milestone\Collection $collection)
	{
		$message = "started milestone collection\n";
		print $message;
	}
	
	
	public function onCreateSuccess(\Sirprize\Basecamp\Milestone\Collection $collection)
	{
		$message = "milestones have been created for this collection\n";
		print $message;
	}
	
	
	public function onStartError(\Sirprize\Basecamp\Milestone\Collection $collection)
	{
		$message = "milestone collection could not be started\n";
		print $message;
	}
	
	
	public function onCreateError(\Sirprize\Basecamp\Milestone\Collection $collection)
	{
		$message = "milestone collection could not be created\n";
		print $message;
	}
	
}