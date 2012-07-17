<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\TodoList\Entity\Observer;

use Sirprize\Basecamp\TodoList\Entity;

/**
 * Abstract class to observe and print state changes of the observed todoList
 */
abstract class Abstrakt
{

    abstract public function onCreateSuccess(Entity $todoList);
    abstract public function onUpdateSuccess(Entity $todoList);
    abstract public function onDeleteSuccess(Entity $todoList);

    abstract public function onCreateError(Entity $todoList);
    abstract public function onUpdateError(Entity $todoList);
    abstract public function onDeleteError(Entity $todoList);

    protected function _getOnCreateSuccessMessage(Entity $todoList)
    {
        $message  = "todoList '".$todoList->getName()."'";
        $message .= " created in project '".$todoList->getProjectId()."'";
        return $message;
    }

    protected function _getOnUpdateSuccessMessage(Entity $todoList)
    {
        $message  = "todoList '".$todoList->getName()."'";
        $message .= " updated in project '".$todoList->getProjectId()."'";
        return $message;
    }

    protected function _getOnDeleteSuccessMessage(Entity $todoList)
    {
        $message  = "todoList '".$todoList->getName()."'";
        $message .= " deleted from project '".$todoList->getProjectId()."'";
        return $message;
    }

    protected function _getOnCreateErrorMessage(Entity $todoList)
    {
        $message  = "todoList '".$todoList->getName()."'";
        $message .= " could not be created in project '".$todoList->getProjectId()."'";
        return $message;
    }

    protected function _getOnUpdateErrorMessage(Entity $todoList)
    {
        $message  = "todoList '".$todoList->getName()."'";
        $message .= " could not be updated in project '".$todoList->getProjectId()."'";
        return $message;
    }

    protected function _getOnDeleteErrorMessage(Entity $todoList)
    {
        $message  = "todoList '".$todoList->getName()."'";
        $message .= " could not be deleted from project '".$todoList->getProjectId()."'";
        return $message;
    }

}