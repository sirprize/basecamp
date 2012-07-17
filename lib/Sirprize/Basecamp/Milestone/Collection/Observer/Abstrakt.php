<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Milestone\Collection\Observer;

use Sirprize\Basecamp\Milestone\Collection;

/**
 * Abstract class to observe and print state changes of the observed milestone
 */
abstract class Abstrakt
{

    abstract public function onStartSuccess(Collection $collection);
    abstract public function onCreateSuccess(Collection $collection);
    abstract public function onStartError(Collection $collection);
    abstract public function onCreateError(Collection $collection);

    protected function _getOnStartSuccessMessage(Collection $collection)
    {
        return "started milestone collection. found ".$collection->count()." milestones";
    }

    protected function _getOnCreateSuccessMessage(Collection $collection)
    {
        return "milestones have been created for this collection";
    }

    protected function _getOnStartErrorMessage(Collection $collection)
    {
        return "milestone collection could not be started";
    }

    protected function _getOnCreateErrorMessage(Collection $collection)
    {
        return "milestone collection could not be created";
    }

}