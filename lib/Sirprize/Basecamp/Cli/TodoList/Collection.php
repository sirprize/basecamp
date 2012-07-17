<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */
 
namespace Sirprize\Basecamp\Cli\TodoList;

use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\TodoList\Collection as TodoListCollection;
use Sirprize\Basecamp\TodoList\Entity\Observer\Stout;
use Sirprize\Basecamp\TodoList\Entity\Observer\Log;
use Sirprize\Basecamp\Cli\TodoList\Entity;

class Collection extends TodoListCollection
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
     * Instantiate a new todoList entity
     *
     * @return \Sirprize\Basecamp\Cli\TodoList\Entity
     */
    public function getTodoListInstance()
    {
        $todoListObserverStout = new Stout();

        $todoListObserverLog = new Log();
        $todoListObserverLog->setLog($this->_getLog());

        $todoList = new Entity();
        $todoList
            ->setHttpClient($this->_getHttpClient())
            ->setService($this->_getService())
            ->attachObserver($todoListObserverStout)
            ->attachObserver($todoListObserverLog)
        ;

        return $todoList;
    }

}