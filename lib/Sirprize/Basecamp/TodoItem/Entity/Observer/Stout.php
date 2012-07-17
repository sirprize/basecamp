<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\TodoItem\Entity\Observer;

use Sirprize\Basecamp\TodoItem\Entity;
use Sirprize\Basecamp\TodoItem\Entity\Observer\Abstrakt;

/**
 * Class to observe and print state changes of the observed todoItem
 */
class Stout extends Abstrakt
{

    public function onCompleteSuccess(Entity $todoItem)
    {
        print $this->_getOnCompleteSuccessMessage($todoItem)."\n";
    }

    public function onUncompleteSuccess(Entity $todoItem)
    {
        print $this->_getOnUncompleteSuccessMessage($todoItem)."\n";
    }

    public function onCreateSuccess(Entity $todoItem)
    {
        print $this->_getOnCreateSuccessMessage($todoItem)."\n";
    }

    public function onUpdateSuccess(Entity $todoItem)
    {
        print $this->_getOnUpdateSuccessMessage($todoItem)."\n";
    }

    public function onDeleteSuccess(Entity $todoItem)
    {
        print $this->_getOnDeleteSuccessMessage($todoItem)."\n";
    }

    public function onCompleteError(Entity $todoItem)
    {
        print $this->_getOnCompleteErrorMessage($todoItem)."\n";
    }

    public function onUncompleteError(Entity $todoItem)
    {
        print $this->_getOnUncompleteErrorMessage($todoItem)."\n";
    }

    public function onCreateError(Entity $todoItem)
    {
        print $this->_getOnCreateErrorMessage($todoItem)."\n";
    }

    public function onUpdateError(Entity $todoItem)
    {
        print $this->_getOnUpdateErrorMessage($todoItem)."\n";
    }

    public function onDeleteError(Entity $todoItem)
    {
        print $this->_getOnDeleteErrorMessage($todoItem)."\n";
    }

}