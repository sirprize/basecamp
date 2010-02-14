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


class Sirprize_BasecampTest extends PHPUnit_Framework_TestCase
{
	
	
	
	protected $_basecamp = null;
	protected $_httpClient = null;
	
	
	
	protected function setUp()
    {
		require_once 'Zend/Http/Client.php';
        $this->_httpClient = new \Zend_Http_Client();
		$this->_httpClient->setAdapter('Zend_Http_Client_Adapter_Test');
		$this->_httpClient->getAdapter()->setNextRequestWillFail(true);
			
		$config = array(
			'baseUri' => TESTS_SIPRIZE_BASECAMP_BASEURI,
			'username' => TESTS_SIPRIZE_BASECAMP_USERNAME,
			'password' => TESTS_SIPRIZE_BASECAMP_PASSWORD
		);

		require_once 'Sirprize/Basecamp.php';
		$this->_basecamp = new \Sirprize\Basecamp($config);
	}
	
	
	
	protected function tearDown()
    {}
	
	
	
	public function testConstructExceptionValuesBaseUriMissing()
    {
        try {
			$config = array(
				'username' => TESTS_SIPRIZE_BASECAMP_USERNAME,
				'password' => TESTS_SIPRIZE_BASECAMP_PASSWORD
			);

			require_once 'Sirprize/Basecamp.php';
			$basecamp = new \Sirprize\Basecamp($config);
            $this->fail('Expected \Sirprize\Basecamp\Exception not thrown');
        }
		catch (\Sirprize\Basecamp\Exception $e)
		{
            $this->assertContains("'baseUri'", $e->getMessage());
        }
    }

	
	
	public function testConstructExceptionValuesUsernameMissing()
    {
        try {
			$config = array(
				'baseUri' => TESTS_SIPRIZE_BASECAMP_BASEURI,
				'password' => TESTS_SIPRIZE_BASECAMP_PASSWORD
			);

			require_once 'Sirprize/Basecamp.php';
			$basecamp = new \Sirprize\Basecamp($config);
            $this->fail('Expected \Sirprize\Basecamp\Exception not thrown');
        }
		catch (\Sirprize\Basecamp\Exception $e)
		{
            $this->assertContains("'username'", $e->getMessage());
        }
    }

	
	
	public function testConstructExceptionValuesPasswordMissing()
    {
        try {
			$config = array(
				'baseUri' => TESTS_SIPRIZE_BASECAMP_BASEURI,
				'username' => TESTS_SIPRIZE_BASECAMP_USERNAME
			);

			require_once 'Sirprize/Basecamp.php';
			$basecamp = new \Sirprize\Basecamp($config);
            $this->fail('Expected \Sirprize\Basecamp\Exception not thrown');
        }
		catch (\Sirprize\Basecamp\Exception $e)
		{
            $this->assertContains("'password'", $e->getMessage());
        }
    }
	
	
	
	public function testFluidInterface()
    {
		$basecamp = $this->_basecamp
			->setHttpClient($this->_httpClient)
		;
		
        $this->assertSame($this->_basecamp, $basecamp);
    }

	
	
	public function testMilestoneBatchInstantiator()
    {
		$milestones = $this->_basecamp->getMilestonesInstance();
        $this->assertTrue($milestones instanceof \Sirprize\Basecamp\Milestone\Collection);
    }
	
}

?>