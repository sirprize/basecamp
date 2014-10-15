<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\TodoList\Collection\Observer;

use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\TodoList\Collection;
use Sirprize\Basecamp\TodoList\Collection\Observer\Abstrakt;

/**
 * Class to observe and log state changes of an observed collection
 */
class Log extends Abstrakt
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

    public function onStartSuccess(Collection $collection)
    {
        $this->_getLog()->info($this->_getOnStartSuccessMessage($collection));
    }

    public function onStartError(Collection $collection)
    {
        $this->_getLog()->err($this->_getOnStartErrorMessage($collection));
    }

}