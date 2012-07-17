<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Person\Collection\Observer;

use Sirprize\Basecamp\Person\Collection;

/**
 * Abstract class to observe and print state changes of the observed person
 */
abstract class Abstrakt
{

    abstract public function onStartSuccess(Collection $collection);
    abstract public function onStartError(Collection $collection);

    protected function _getOnStartSuccessMessage(Collection $collection)
    {
        return "started person collection. found ".$collection->count()." persons";
    }

    protected function _getOnStartErrorMessage(Collection $collection)
    {
        return "person collection could not be started";
    }

}