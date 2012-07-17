<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

use Sirprize\Basecamp\Exception;

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
            $this->fail('Expected Exception not thrown');
        }
        catch (Exception $e)
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
            $this->fail('Expected Exception not thrown');
        }
        catch (Exception $e)
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
            $this->fail('Expected Exception not thrown');
        }
        catch (Exception $e)
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