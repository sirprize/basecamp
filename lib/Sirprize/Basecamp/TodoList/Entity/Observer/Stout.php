<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\TodoList\Entity\Observer;

use Sirprize\Basecamp\TodoList\Entity;
use Sirprize\Basecamp\TodoList\Entity\Observer\Abstrakt;

/**
 * Class to observe and print state changes of the observed todoList
 */
class Stout extends Abstrakt
{

    public function onCreateSuccess(Entity $todoList)
    {
        print $this->_getOnCreateSuccessMessage($todoList)."\n";
    }

    public function onUpdateSuccess(Entity $todoList)
    {
        print $this->_getOnUpdateSuccessMessage($todoList)."\n";
    }

    public function onDeleteSuccess(Entity $todoList)
    {
        print $this->_getOnDeleteSuccessMessage($todoList)."\n";
    }

    public function onCreateError(Entity $todoList)
    {
        print $this->_getOnCreateErrorMessage($todoList)."\n";
    }

    public function onUpdateError(Entity $todoList)
    {
        print $this->_getOnUpdateErrorMessage($todoList)."\n";
    }

    public function onDeleteError(Entity $todoList)
    {
        print $this->_getOnDeleteErrorMessage($todoList)."\n";
    }

}