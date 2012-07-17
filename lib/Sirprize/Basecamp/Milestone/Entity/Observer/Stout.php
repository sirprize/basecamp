<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Milestone\Entity\Observer;

use Sirprize\Basecamp\Milestone\Entity;
use Sirprize\Basecamp\Milestone\Entity\Observer\Abstrakt;

/**
 * Class to observe and print state changes of the observed milestone
 */
class Stout extends Abstrakt
{

    public function onCompleteSuccess(Entity $milestone)
    {
        print $this->_getOnCompleteSuccessMessage($milestone)."\n";
    }

    public function onUncompleteSuccess(Entity $milestone)
    {
        print $this->_getOnUncompleteSuccessMessage($milestone)."\n";
    }

    public function onCreateSuccess(Entity $milestone)
    {
        print $this->_getOnCreateSuccessMessage($milestone)."\n";
    }

    public function onUpdateSuccess(Entity $milestone)
    {
        print $this->_getOnUpdateSuccessMessage($milestone)."\n";
    }

    public function onDeleteSuccess(Entity $milestone)
    {
        print $this->_getOnDeleteSuccessMessage($milestone)."\n";
    }

    public function onCompleteError(Entity $milestone)
    {
        print $this->_getOnCompleteErrorMessage($milestone)."\n";
    }

    public function onUncompleteError(Entity $milestone)
    {
        print $this->_getOnUncompleteErrorMessage($milestone)."\n";
    }

    public function onCreateError(Entity $milestone)
    {
        print $this->_getOnCreateErrorMessage($milestone)."\n";
    }

    public function onUpdateError(Entity $milestone)
    {
        print $this->_getOnUpdateErrorMessage($milestone)."\n";
    }

    public function onDeleteError(Entity $milestone)
    {
        print $this->_getOnDeleteErrorMessage($milestone)."\n";
    }

}