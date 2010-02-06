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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010, Christian Hoegl, Switzerland (http://sirprize.me)
 * @license    MIT License
 */


require_once dirname(__FILE__) . '/../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Sirprize_AllTests::main');
}


class Sirprize_AllTests
{
	
	
	public static function main()
    {
		PHPUnit_TextUI_TestRunner::run(self::suite());
    }

	
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Sirprize // Sirprize');
		
		require_once 'Sirprize/BasecampTest.php';
		$suite->addTestSuite('Sirprize_BasecampTest');
		
		require_once 'Sirprize/Basecamp/AllTests.php';
		$suite->addTest(Sirprize_Basecamp_AllTests::suite());
		return $suite;
	}
	
}


if (PHPUnit_MAIN_METHOD == 'Sirprize_AllTests::main') {
    Sirprize_AllTests::main();
}

?>
