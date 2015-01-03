<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp;

use Sirprize\Basecamp\Exception;

class Date
{

    const FORMAT = 'yyyy-MM-dd';
    const REGEX = '/^\d{4,4}-\d{2,2}-\d{2,2}$/';

    protected $_date = null;

    public function __construct($date)
    {
        if(!preg_match(self::REGEX, $date))
        {
            throw new Exception("invalid date format '$date'");
        }

        $this->_date = new \DateTime($date);
    }

    public function __toString()
    {
        return $this->_date->format(self::FORMAT);
    }

    public function get()
    {
        return $this->_date->format(self::FORMAT);
    }

    public static function isValid($date)
    {
        return preg_match(self::REGEX, $date);
    }

    public function addDays($numDays)
    {
        $date = new \DateTime;
        $dateOffset = new \DateInterval('P0');
        $dateOffset->d = $numDays;
        $this->_date = $date->add($dateOffset);
        return $this;
    }

    public function subDays($numDays)
    {
        $date = new \DateTime;
        $dateOffset = new \DateInterval('P0');
        $dateOffset->d = $numDays;
        $this->_date = $date->add($dateOffset);
        return $this;
    }
}