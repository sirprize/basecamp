<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\TodoItem\Collection\Observer;

use Sirprize\Basecamp\TodoItem\Collection;

/**
 * Abstract class to observe and print state changes of the observed todo-items
 */
abstract class Abstrakt
{

    abstract public function onStartSuccess(Collection $collection);
    abstract public function onStartError(Collection $collection);

    protected function _getOnStartSuccessMessage(Collection $collection)
    {
        return "started todo-item collection. found ".$collection->count()." todo-items";
    }

    protected function _getOnStartErrorMessage(Collection $collection)
    {
        return "todo-item collection could not be started";
    }

}