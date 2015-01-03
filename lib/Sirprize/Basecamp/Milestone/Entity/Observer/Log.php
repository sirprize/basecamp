<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Milestone\Entity\Observer;

use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\Milestone\Entity;
use Sirprize\Basecamp\Milestone\Entity\Observer\Abstrakt;

/**
 * Class to observe and log state changes of an observed milestone
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

    public function onCompleteSuccess(Entity $milestone)
    {
        $this->_getLog()->info($this->_getOnCompleteSuccessMessage($milestone));
    }

    public function onUncompleteSuccess(Entity $milestone)
    {
        $this->_getLog()->info($this->_getOnUncompleteSuccessMessage($milestone));
    }

    public function onCreateSuccess(Entity $milestone)
    {
        $this->_getLog()->info($this->_getOnCreateSuccessMessage($milestone));
    }

    public function onUpdateSuccess(Entity $milestone)
    {
        $this->_getLog()->info($this->_getOnUpdateSuccessMessage($milestone));
    }

    public function onDeleteSuccess(Entity $milestone)
    {
        $this->_getLog()->info($this->_getOnDeleteSuccessMessage($milestone));
    }

    public function onCompleteError(Entity $milestone)
    {
        $this->_getLog()->err($this->_getOnCompleteErrorMessage($milestone));
    }

    public function onUncompleteError(Entity $milestone)
    {
        $this->_getLog()->err($this->_getOnUncompleteErrorMessage($milestone));
    }

    public function onCreateError(Entity $milestone)
    {
        $this->_getLog()->err($this->_getOnCreateErrorMessage($milestone));
    }

    public function onUpdateError(Entity $milestone)
    {
        $this->_getLog()->err($this->_getOnUpdateErrorMessage($milestone));
    }

    public function onDeleteError(Entity $milestone)
    {
        $this->_getLog()->err($this->_getOnDeleteErrorMessage($milestone));
    }

}