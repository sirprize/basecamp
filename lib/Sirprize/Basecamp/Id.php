<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp;

use Sirprize\Basecamp\Exception;

class Id
{

    protected $_id = null;

    public function __construct($id)
    {
        if(!preg_match('/^[a-zA-Z0-9]+$/', $id))
        {
            throw new Exception("invalid id format '$id'");
        }

        $this->_id = $id;
    }

    public function __toString()
    {
        return trim($this->_id);
    }

    public function get()
    {
        return trim($this->_id);
    }
}