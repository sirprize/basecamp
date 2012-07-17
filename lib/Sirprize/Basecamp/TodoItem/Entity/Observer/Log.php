<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\TodoItem\Entity\Observer;

use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\TodoItem\Entity;
use Sirprize\Basecamp\TodoItem\Entity\Observer\Abstrakt;

/**
 * Class to observe and log state changes of an observed todoItem
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

    public function onCompleteSuccess(Entity $todoItem)
    {
        $this->_getLog()->info($this->_getOnCompleteSuccessMessage($todoItem));
    }

    public function onUncompleteSuccess(Entity $todoItem)
    {
        $this->_getLog()->info($this->_getOnUncompleteSuccessMessage($todoItem));
    }

    public function onCreateSuccess(Entity $todoItem)
    {
        $this->_getLog()->info($this->_getOnCreateSuccessMessage($todoItem));
    }

    public function onUpdateSuccess(Entity $todoItem)
    {
        $this->_getLog()->info($this->_getOnUpdateSuccessMessage($todoItem));
    }

    public function onDeleteSuccess(Entity $todoItem)
    {
        $this->_getLog()->info($this->_getOnDeleteSuccessMessage($todoItem));
    }

    public function onCompleteError(Entity $todoItem)
    {
        $this->_getLog()->err($this->_getOnCompleteErrorMessage($todoItem));
    }

    public function onUncompleteError(Entity $todoItem)
    {
        $this->_getLog()->err($this->_getOnUncompleteErrorMessage($todoItem));
    }

    public function onCreateError(Entity $todoItem)
    {
        $this->_getLog()->err($this->_getOnCreateErrorMessage($todoItem));
    }

    public function onUpdateError(Entity $todoItem)
    {
        $this->_getLog()->err($this->_getOnUpdateErrorMessage($todoItem));
    }

    public function onDeleteError(Entity $todoItem)
    {
        $this->_getLog()->err($this->_getOnDeleteErrorMessage($todoItem));
    }

}