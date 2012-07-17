<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

require_once dirname(__FILE__) . '/../../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Sirprize_Basecamp_AllTests::main');
}

class Sirprize_Basecamp_AllTests
{

    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Sirprize // Sirprize // Basecamp');

        require_once 'Sirprize/Basecamp/MilestoneTest.php';
        $suite->addTestSuite('Sirprize_Basecamp_MilestoneTest');
        return $suite;
    }

}

if (PHPUnit_MAIN_METHOD == 'Sirprize_Basecamp_AllTests::main') {
    Sirprize_Basecamp_AllTests::main();
}

?>
