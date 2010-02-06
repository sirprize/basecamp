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


namespace Sirprize\Basecamp;


class TodoList
{
	
	protected $_basecamp = null;
	protected $_httpClient = null;
	protected $_data = array();
	protected $_loaded = false;
	protected $_items = array();
	
	
	public function setBasecamp(\Sirprize\Basecamp $basecamp)
	{
		$this->_basecamp = $basecamp;
		return $this;
	}
	
	
	protected function _getBasecamp()
	{
		if($this->_basecamp === null)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call setBasecamp() before '.__METHOD__);
		}
		
		return $this->_basecamp;
	}
	
	
	
	public function setHttpClient(\Zend_Http_Client $httpClient)
	{
		$this->_httpClient = $httpClient;
		return $this;
	}
	
	
	
	protected function _getHttpClient()
	{
		if($this->_httpClient === null)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call setHttpClient() before '.__METHOD__);
		}
		
		return $this->_httpClient;
	}
	
	
	
	public function load(\SimpleXMLElement $data)
	{
		if($this->_loaded)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('entity has already been loaded');
		}
		#print_r($data); exit;
		$this->_loaded = true;
		$data = (array) $data;
		
		$this->_data['id'] = $data['id'];
		$this->_data['project-id'] = $data['project-id'];
		$this->_data['milestone-id'] = (!isset($data['milestone-id']) || $data['milestone-id'] instanceof \SimpleXMLElement) ? null : $data['milestone-id'];
		$this->_data['name'] = $data['name'];
		$this->_data['description'] = (!isset($data['description']) || $data['description'] instanceof \SimpleXMLElement) ? null : $data['description'];
		$this->_data['private'] = $data['private'];
		$this->_data['tracked'] = $data['tracked'];
		$this->_data['completed-count'] = $data['completed-count'];
		$this->_data['uncompleted-count'] = $data['uncompleted-count'];
		
		if(isset($this->_data['todo-items']))
		{
			foreach($this->_data['todo-items'] as $item)
			{
				require_once 'Sirprize/Basecamp/TodoListItem.php';
				$entity = new \Sirprize\Basecamp\TodoListItem();
				$entity->load($item);
				$this->_items[] = $entity;
			}
		}
		
		return $this;
	}
	
	
	public function getId()
	{
		return $this->_getVal('id');
	}
	
	
	public function getProjectId()
	{
		return $this->_getVal('project-id');
	}
	
	
	public function getMilestoneId()
	{
		return $this->_getVal('milestone-id');
	}
	
	
	public function getName()
	{
		return $this->_getVal('name');
	}
	
	
	public function getDescription()
	{
		return $this->_getVal('description');
	}
	
	
	public function getIsPrivate()
	{
		return $this->_getVal('private');
	}
	
	
	public function getIsTracked()
	{
		return $this->_getVal('tracked');
	}
	
	
	public function getCompletedCount()
	{
		return $this->_getVal('completed-count');
	}
	
	
	public function getUncompletedCount()
	{
		return $this->_getVal('uncompleted-count');
	}
	
	
	public function getItems()
	{
		return $this->_items;
	}
	
	
	protected function _getVal($name)
	{
		if(!isset($this->_data[$name]))
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('entity data is incomplete or not loaded');
		}
		
		return $this->_data[$name];
	}
	
}