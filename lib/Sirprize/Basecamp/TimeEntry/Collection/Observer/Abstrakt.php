<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\TimeEntry\Collection\Observer;

use Sirprize\Basecamp\TimeEntry\Collection;

/**
 * Abstract class to observe and print state changes of the observed time entry
 */
abstract class Abstrakt
{

    abstract public function onStartSuccess(Collection $collection);
    abstract public function onStartError(Collection $collection);

    protected function _getOnStartSuccessMessage(Collection $collection)
    {
        return "started time entry collection. found ".$collection->count()." entries";
    }

    protected function _getOnStartErrorMessage(Collection $collection)
    {
        return "time entry collection could not be started";
    }

}