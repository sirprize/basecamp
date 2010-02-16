<?php

/**
 * Basecamp API Wrapper for PHP 5.3+ 
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt
 *
 * @category   Sirprize
 * @package    Basecamp
 * @copyright  Copyright (c) 2010, Christian Hoegl, Switzerland (http://sirprize.me)
 * @license    MIT License
 */


namespace Sirprize\Basecamp\Import\Schema;


class TodoList
{
	
	
	
	protected $_name = null;
	protected $_isPrivate = true;
	protected $_note = null;
	protected $_todoItems = array();
	
	
	
	public function setName($name)
	{
		$this->_name = $name;
		return $this;
	}
	
	
	public function getName()
	{
		return $this->_name;
	}
	
	
	public function setIsPrivate($isPrivate)
	{
		$this->_isPrivate = $isPrivate;
		return $this;
	}
	
	
	public function getIsPrivate()
	{
		return $this->_isPrivate;
	}
	
	
	public function setNote($note)
	{
		$this->_note = $note;
		return $this;
	}
	
	
	public function getNote()
	{
		return $this->_note;
	}
	
	
	public function getTodoItem($key)
	{
		if(!isset($this->_todoItems[$key]))
		{
			require_once 'Sirprize/Basecamp/Import/Schema/Exception.php';
			throw new \Sirprize\Basecamp\Import\Schema\Exception("there is no todo-item '$key'");
		}
		
		return $this->_todoItems[$key];
	}
	
	
	public function addTodoItem($key, \Sirprize\Basecamp\Import\Schema\TodoItem $todoItem)
	{
		$this->_todoItems[$key] = $todoItem;
		return $this;
	}
	
	
	public function getTodoItems()
	{
		return $this->_todoItems;
	}
	
}