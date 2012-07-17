<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

use Sirprize\Basecamp\Id;
use Sirprize\Basecamp\Exception;

class Sirprize_Basecamp_MilestoneTest extends PHPUnit_Framework_TestCase
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

    public function testFluidInterface()
    {
        require_once 'Sirprize/Basecamp/Milestone/Entity/Observer/Log.php';
        $log = new \Sirprize\Basecamp\Milestone\Entity\Observer\Log();

        require_once 'Sirprize/Basecamp/Milestone/Entity.php';
        $milestone = new \Sirprize\Basecamp\Milestone\Entity();

        require_once 'Sirprize/Basecamp/Date.php';
        $deadline = new \Sirprize\Basecamp\Date('2010-12-23');

        require_once 'Sirprize/Basecamp/Id.php';
        $projectId = new Id('4311451');
        $responsiblePartyId = new Id('4793703');

        $milestone
            ->attachObserver($log)
            ->detachObserver($log)
            ->setBasecamp($this->_basecamp)
            ->setHttpClient($this->_httpClient)
            ->setTitle('some title')
            ->setDeadline($deadline)
            ->setProjectId($projectId)
            ->setResponsiblePartyId($responsiblePartyId)
        ;

        $this->assertTrue($milestone instanceof \Sirprize\Basecamp\Milestone\Entity);
    }

    public function testCreateXmlExceptionTitleMissing()
    {
        try {
            require_once 'Sirprize/Basecamp/Milestone/Entity.php';
            $milestone = new \Sirprize\Basecamp\Milestone\Entity();
            $milestone->setBasecamp($this->_basecamp);

            require_once 'Sirprize/Basecamp/Date.php';
            $deadline = new \Sirprize\Basecamp\Date('2010-12-23');

            require_once 'Sirprize/Basecamp/Id.php';
            $responsiblePartyId = new Id('4793703');

            $milestone
                #->setTitle('some title')
                ->setDeadline($deadline)
                ->setResponsiblePartyId($responsiblePartyId)
            ;

            $milestone->getXml();
            $this->fail('Expected Exception not thrown');
        }
        catch (Exception $e)
        {
            $this->assertContains("call setTitle() before ", $e->getMessage());
        }
    }

    public function testCreateXmlExceptionDeadlineMissing()
    {
        try {
            require_once 'Sirprize/Basecamp/Milestone/Entity.php';
            $milestone = new \Sirprize\Basecamp\Milestone\Entity();
            $milestone->setBasecamp($this->_basecamp);

            require_once 'Sirprize/Basecamp/Date.php';
            $deadline = new \Sirprize\Basecamp\Date('2010-12-23');

            require_once 'Sirprize/Basecamp/Id.php';
            $responsiblePartyId = new Id('4793703');

            $milestone
                ->setTitle('some title')
                #->setDeadline($deadline)
                ->setResponsiblePartyId($responsiblePartyId)
            ;

            $milestone->getXml();
            $this->fail('Expected Exception not thrown');
        }
        catch (Exception $e)
        {
            $this->assertContains("call setDeadline() before ", $e->getMessage());
        }
    }

    public function testCreateXmlExceptionResponsiblePartyIdMissing()
    {
        try {
            require_once 'Sirprize/Basecamp/Milestone/Entity.php';
            $milestone = new \Sirprize\Basecamp\Milestone\Entity();
            $milestone->setBasecamp($this->_basecamp);

            require_once 'Sirprize/Basecamp/Date.php';
            $deadline = new \Sirprize\Basecamp\Date('2010-12-23');

            require_once 'Sirprize/Basecamp/Id.php';
            $responsiblePartyId = new Id('4793703');

            $milestone
                ->setTitle('some title')
                ->setDeadline($deadline)
                #->setResponsiblePartyId($responsiblePartyId)
            ;

            $milestone->getXml();
            $this->fail('Expected Exception not thrown');
        }
        catch (Exception $e)
        {
            $this->assertContains("call setResponsiblePartyId() before ", $e->getMessage());
        }
    }

}

?>