<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\TodoList\Collection\Observer;

use Sirprize\Basecamp\TodoList\Collection;

/**
 * Abstract class to observe and print state changes of the observed todoList
 */
abstract class Abstrakt
{

    abstract public function onStartSuccess(Collection $collection);
    abstract public function onStartError(Collection $collection);

    protected function _getOnStartSuccessMessage(Collection $collection)
    {
        return "started todoList collection. found ".$collection->count()." todoLists";
    }

    protected function _getOnStartErrorMessage(Collection $collection)
    {
        return "todoList collection could not be started";
    }

}