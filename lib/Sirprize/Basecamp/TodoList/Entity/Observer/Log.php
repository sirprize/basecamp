<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\TodoList\Entity\Observer;

use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\TodoList\Entity;
use Sirprize\Basecamp\TodoList\Entity\Observer\Abstrakt;

/**
 * Class to observe and log state changes of an observed todoList
 */
class Log extends Abstrakt
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

    public function onCreateSuccess(Entity $todoList)
    {
        $this->_getLog()->info($this->_getOnCreateSuccessMessage($todoList));
    }

    public function onUpdateSuccess(Entity $todoList)
    {
        $this->_getLog()->info($this->_getOnUpdateSuccessMessage($todoList));
    }

    public function onDeleteSuccess(Entity $todoList)
    {
        $this->_getLog()->info($this->_getOnDeleteSuccessMessage($todoList));
    }

    public function onCreateError(Entity $todoList)
    {
        $this->_getLog()->err($this->_getOnCreateErrorMessage($todoList));
    }

    public function onUpdateError(Entity $todoList)
    {
        $this->_getLog()->err($this->_getOnUpdateErrorMessage($todoList));
    }

    public function onDeleteError(Entity $todoList)
    {
        $this->_getLog()->err($this->_getOnDeleteErrorMessage($todoList));
    }

}