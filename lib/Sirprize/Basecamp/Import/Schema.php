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


namespace Sirprize\Basecamp\Import;


class Schema
{
	
	const DATE_FORMAT = 'yyyy-MM-dd';
	
	protected $_milestones = array();
	
	
	public function getMilestone($key)
	{
		if(!isset($this->_milestones[$key]))
		{
			require_once 'Sirprize/Basecamp/Import/Schema/Exception.php';
			throw new \Sirprize\ReleaseSchedule\Exception("there is no milestone '$key'");
		}
		
		return $this->_milestones[$key];
	}
	
	
	public function getMilestones()
	{
		return $this->_milestones;
	}
	
	
	public function addMilestone($name, \Sirprize\Basecamp\Import\Schema\Milestone $milestone)
	{
		$this->_milestones[$name] = $milestone;
		return $this;
	}
	
}