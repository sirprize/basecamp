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

namespace Sirprize\Basecamp;

class Date
{
	
	const FORMAT = 'yyyy-MM-dd';
	
	protected $_date = null;
	
	
	public function __construct($date)
	{
		if(!preg_match('/^\d{4,4}-\d{2,2}-\d{2,2}$/', $date))
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception("invalid date format '$date'");
		}
		
		$this->_date = $date;
	}
	
	
	public function __toString()
	{
		return $this->_date;
	}
	
	
	public function get()
	{
		return $this->_date;
	}
}