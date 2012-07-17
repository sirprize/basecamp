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

        $this->_date = $date;
    }

    public function __toString()
    {
        return $this->_date;
    }

    public function get()
    {
        return $this->_date;
    }

    public static function isValid($date)
    {
        return preg_match(self::REGEX, $date);
    }

    public function addDays($numDays)
    {
        $date = new \Zend_Date($this->_date, self::FORMAT);
        $this->_date = $date->addSecond(60 * 60 * 24 * (int)$numDays)->toString(self::FORMAT);
        return $this;
    }

    public function subDays($numDays)
    {
        $date = new \Zend_Date($this->_date, self::FORMAT);
        $this->_date = $date->subSecond(60 * 60 * 24 * (int)$numDays)->toString(self::FORMAT);
        return $this;
    }
}