<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */
 
namespace Sirprize\Basecamp\Cli\TodoItem;

use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\TodoItem\Collection as TodoItemCollection;
use Sirprize\Basecamp\TodoItem\Entity\Observer\Stout;
use Sirprize\Basecamp\TodoItem\Entity\Observer\Log;
use Sirprize\Basecamp\Cli\TodoItem\Entity;

class Collection extends TodoItemCollection
{

    protected $_log = null;

    public function setLog(\Zend\Log\Logger $log)
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
     * Instantiate a new todoItem entity
     *
     * @return \Sirprize\Basecamp\Cli\TodoItem\Entity
     */
    public function getTodoItemInstance()
    {
        $todoItemObserverStout = new Stout();

        $todoItemObserverLog = new Log();
        $todoItemObserverLog->setLog($this->_getLog());

        $todoItem = new Entity();
        $todoItem
            ->setHttpClient($this->_getHttpClient())
            ->setService($this->_getService())
            ->attachObserver($todoItemObserverStout)
            ->attachObserver($todoItemObserverLog)
        ;

        return $todoItem;
    }

}