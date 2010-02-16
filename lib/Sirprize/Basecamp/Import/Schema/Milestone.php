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


class Milestone
{
	
	
	
	protected $_title = null;
	protected $_deadline = null;
	protected $_responsibleParty = null;
	protected $_isPrivate = true;
	protected $_note = null;
	protected $_todoLists = array();
	
	
	
	public function setTitle($title)
	{
		$this->_title = $title;
		return $this;
	}
	
	
	public function getTitle()
	{
		return $this->_title;
	}
	
	
	public function setDeadline($deadline)
	{
		$this->_deadline = $deadline;
		return $this;
	}
	
	
	public function getDeadline()
	{
		return $this->_deadline;
	}
	
	
	public function setResponsibleParty($responsibleParty)
	{
		$this->_responsibleParty = $responsibleParty;
		return $this;
	}
	
	
	public function getResponsibleParty()
	{
		return $this->_responsibleParty;
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
	
	
	public function getTodoList($key)
	{
		if(!isset($this->_todoLists[$key]))
		{
			require_once 'Sirprize/Basecamp/Import/Schema/Exception.php';
			throw new \Sirprize\Basecamp\Import\Schema\Exception("there is no todo-list '$key'");
		}
		
		return $this->_todoLists[$key];
	}
	
	
	public function addTodoList($key, \Sirprize\Basecamp\Import\Schema\TodoList $todoList)
	{
		$this->_todoLists[$key] = $todoList;
		return $this;
	}
	
	
	public function getTodoLists()
	{
		return $this->_todoLists;
	}
	
}