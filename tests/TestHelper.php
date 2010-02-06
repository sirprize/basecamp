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


/*
 * Start output buffering
 */
#ob_start();

/*
 * Determine the root, library, and tests directories of the framework
 * distribution.
 */
$pear = "/usr/local/pear/PEAR"; # path to phpunit
$zend = realpath(dirname(__FILE__) . '/../../../vendor/zend/library'); # path to zend framework
$lib = realpath(dirname(__FILE__) . '/../lib'); # path to lib
$tests = dirname(__FILE__); # path to tests


/*
 * Omit from code coverage reports the contents of the tests directory
 */
foreach (array('php', 'phtml', 'csv') as $suffix)
{
    #PHPUnit_Util_Filter::addDirectoryToFilter($tests, ".$suffix");
}

/*
 * Prepend the Zend Framework library/ and tests/ directories to the
 * include_path. This allows the tests to run out of the box and helps prevent
 * loading other copies of the framework code and tests that would supersede
 * this copy.
 */
$path = array(
	$pear,
	$zend,
    $tests,
	$lib,
    get_include_path()
);


set_include_path(implode(PATH_SEPARATOR, $path));


/*
 * Include PHPUnit dependencies
 */
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';

/*
 * Set error reporting to the level to which Zend Framework code must comply.
 */
error_reporting( E_ALL | E_STRICT );

/*
 * Load the user-defined test configuration file, if it exists; otherwise, load
 * the default configuration.
 */
if(is_readable($tests . DIRECTORY_SEPARATOR . 'TestConfiguration.php'))
{
    require_once $tests . DIRECTORY_SEPARATOR . 'TestConfiguration.php';
} else {
    require_once $tests . DIRECTORY_SEPARATOR . 'TestConfiguration.php.dist';
}

/*
 * Add Zend Framework library/ directory to the PHPUnit code coverage
 * whitelist. This has the effect that only production code source files appear
 * in the code coverage report and that all production code source files, even
 * those that are not covered by a test yet, are processed.
 */
if (defined('TESTS_GENERATE_REPORT') && TESTS_GENERATE_REPORT === true &&
    version_compare(PHPUnit_Runner_Version::id(), '3.1.6', '>=')) {
    PHPUnit_Util_Filter::addDirectoryToWhitelist($lib);
}

/*
 * Unset global variables that are no longer needed.
 */
unset($tests, $path);
