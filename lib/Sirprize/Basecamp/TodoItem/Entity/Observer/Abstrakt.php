<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\TodoItem\Entity\Observer;

use Sirprize\Basecamp\TodoItem\Entity;

/**
 * Abstract class to observe and print state changes of the observed todo-item
 */
abstract class Abstrakt
{

    abstract public function onCompleteSuccess(Entity $todoItem);
    abstract public function onUncompleteSuccess(Entity $todoItem);
    abstract public function onCreateSuccess(Entity $todoItem);
    abstract public function onUpdateSuccess(Entity $todoItem);
    abstract public function onDeleteSuccess(Entity $todoItem);

    abstract public function onCompleteError(Entity $todoItem);
    abstract public function onUncompleteError(Entity $todoItem);
    abstract public function onCreateError(Entity $todoItem);
    abstract public function onUpdateError(Entity $todoItem);
    abstract public function onDeleteError(Entity $todoItem);

    protected function _getOnCompleteSuccessMessage(Entity $todoItem)
    {
        $message  = "todo-item '".$todoItem->getContent()."'";
        $message .= " completed in todo-list '".$todoItem->getTodoListId()."'";
        return $message;
    }

    protected function _getOnUncompleteSuccessMessage(Entity $todoItem)
    {
        $message  = "todo-item '".$todoItem->getContent()."'";
        $message .= " uncompleted in todo-list '".$todoItem->getTodoListId()."'";
        return $message;
    }

    protected function _getOnCreateSuccessMessage(Entity $todoItem)
    {
        $message  = "todo-item '".$todoItem->getContent()."'";
        $message .= " created in todo-list '".$todoItem->getTodoListId()."'";
        return $message;
    }

    protected function _getOnUpdateSuccessMessage(Entity $todoItem)
    {
        $message  = "todo-item '".$todoItem->getContent()."'";
        $message .= " updated in todo-list '".$todoItem->getTodoListId()."'";
        return $message;
    }

    protected function _getOnDeleteSuccessMessage(Entity $todoItem)
    {
        $message  = "todo-item '".$todoItem->getContent()."'";
        $message .= " deleted from todo-list '".$todoItem->getTodoListId()."'";
        return $message;
    }

    protected function _getOnCompleteErrorMessage(Entity $todoItem)
    {
        $message  = "todo-item '".$todoItem->getContent()."'";
        $message .= " could not be completed in todo-list '".$todoItem->getTodoListId()."'";
        return $message;
    }

    protected function _getOnUncompleteErrorMessage(Entity $todoItem)
    {
        $message  = "todo-item '".$todoItem->getContent()."'";
        $message .= " could not be uncompleted in todo-list '".$todoItem->getTodoListId()."'";
        return $message;
    }

    protected function _getOnCreateErrorMessage(Entity $todoItem)
    {
        $message  = "todo-item '".$todoItem->getContent()."'";
        $message .= " could not be created in todo-list '".$todoItem->getTodoListId()."'";
        return $message;
    }

    protected function _getOnUpdateErrorMessage(Entity $todoItem)
    {
        $message  = "todo-item '".$todoItem->getContent()."'";
        $message .= " could not be updated in todo-list '".$todoItem->getTodoListId()."'";
        return $message;
    }

    protected function _getOnDeleteErrorMessage(Entity $todoItem)
    {
        $message  = "todo-item '".$todoItem->getContent()."'";
        $message .= " could not be deleted from todo-list '".$todoItem->getTodoListId()."'";
        return $message;
    }

}