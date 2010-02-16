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


class TodoItem
{
	
	
	protected $_content = null;
	protected $_responsibleParty = null;
	protected $_isPrivate = true;
	protected $_note = null;
	
	
	public function setContent($content)
	{
		$this->_content = $content;
		return $this;
	}
	
	
	public function getContent()
	{
		return $this->_content;
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
	
}