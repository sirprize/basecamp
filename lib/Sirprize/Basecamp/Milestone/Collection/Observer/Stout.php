<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Milestone\Collection\Observer;

use Sirprize\Basecamp\Milestone\Collection\Observer\Abstrakt;
use Sirprize\Basecamp\Milestone\Collection;

/**
 * Class to observe and print state changes of the observed milestone
 */
class Stout extends Abstrakt
{

    public function onStartSuccess(Collection $collection)
    {
        print $this->_getOnStartSuccessMessage($collection)."\n";
    }

    public function onCreateSuccess(Collection $collection)
    {
        print $this->_getOnCreateSuccessMessage($collection)."\n";
    }

    public function onStartError(Collection $collection)
    {
        print $this->_getOnStartErrorMessage($collection)."\n";
    }

    public function onCreateError(Collection $collection)
    {
        print $this->_getOnCreateErrorMessage($collection)."\n";
    }

}