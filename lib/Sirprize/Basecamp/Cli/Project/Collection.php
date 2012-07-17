<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */
 
namespace Sirprize\Basecamp\Cli\Project;

use Sirprize\Basecamp\Cli\Project\Entity;
use Sirprize\Basecamp\Project\Collection as ProjectCollection;

class Collection extends ProjectCollection
{

    /**
     * Instantiate a new project entity
     *
     * @return \Sirprize\Basecamp\Cli\Project\Entity
     */
    public function getProjectInstance()
    {
        $project = new Entity();
        $project
            ->setHttpClient($this->_getHttpClient())
            ->setService($this->_getService())
        ;

        return $project;
    }

}