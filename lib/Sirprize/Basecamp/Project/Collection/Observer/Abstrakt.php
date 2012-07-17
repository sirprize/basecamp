<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Project\Collection\Observer;

use Sirprize\Basecamp\Project\Collection;

/**
 * Abstract class to observe and print state changes of the observed project
 */
abstract class Abstrakt
{

    abstract public function onStartSuccess(Collection $collection);
    abstract public function onStartError(Collection $collection);

    protected function _getOnStartSuccessMessage(Collection $collection)
    {
        return "started project collection. found ".$collection->count()." projects";
    }

    protected function _getOnStartErrorMessage(Collection $collection)
    {
        return "project collection could not be started";
    }

}