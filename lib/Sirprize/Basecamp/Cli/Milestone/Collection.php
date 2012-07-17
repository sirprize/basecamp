<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */
 
namespace Sirprize\Basecamp\Cli\Milestone;

use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\Milestone\Collection as MilestoneCollection;
use Sirprize\Basecamp\Milestone\Entity\Observer\Stout;
use Sirprize\Basecamp\Milestone\Entity\Observer\Log;
use Sirprize\Basecamp\Cli\Milestone\Entity;

class Collection extends MilestoneCollection
{

    protected $_log = null;

    public function setLog(\Zend_Log $log)
    {
        $this->_log = $log;
        return $this;
    }

    protected function _getLog()
    {
        if($this->_log === null)
        {
            throw new Exception('call setLog() before '.__METHOD__);
        }

        return $this->_log;
    }

    /**
     * Instantiate a new milestone entity
     *
     * @return \Sirprize\Basecamp\Cli\Milestone\Entity
     */
    public function getMilestoneInstance()
    {
        $milestoneObserverStout = new Stout();

        $milestoneObserverLog = new Log();
        $milestoneObserverLog->setLog($this->_getLog());

        $milestone = new Entity();
        $milestone
            ->setHttpClient($this->_getHttpClient())
            ->setService($this->_getService())
            ->attachObserver($milestoneObserverStout)
            ->attachObserver($milestoneObserverLog)
        ;

        return $milestone;
    }

}