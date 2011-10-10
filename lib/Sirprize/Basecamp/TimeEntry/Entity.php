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


namespace Sirprize\Basecamp\TimeEntry;


/**
 * Represent and modify a time entry
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Entity
{
	const _ID = 'id';
	const _DATE = 'date';
	const _DESCRIPTION = 'description';
	const _HOURS = 'hours';
	const _PERSON_ID = 'person-id';
	const _PROJECT_ID = 'project-id';
	const _TODO_ITEM_ID = 'todo-item-id';
	
	protected $_service = null;
	protected $_httpClient = null;
	protected $_data = array();
	protected $_loaded = false;
	protected $_response = null;
	
	
	public function setService(\Sirprize\Basecamp\Service $service)
	{
		$this->_service = $service;
		return $this;
	}
	
	
	public function setHttpClient(\Zend_Http_Client $httpClient)
	{
		$this->_httpClient = $httpClient;
		return $this;
	}
	
	
	/**
	 * Get response object
	 *
	 * @return \Sirprize\Basecamp\Response|null
	 */
	public function getResponse()
	{
		return $this->_response;
	}
	
	
	public function getDate()
	{
		return $this->_getVal(self::_DATE);
	}
	
	
	public function getDescription()
	{
		return $this->_getVal(self::_DESCRIPTION);
	}
	
	
	/**
	 * @return \Sirprize\Basecamp\Id
	 */
	public function getId()
	{
		return $this->_getVal(self::_ID);
	}
	
	
	public function getHours()
	{
		return $this->_getVal(self::_HOURS);
	}
	
	
	public function getPersonId()
	{
		return $this->_getVal(self::_PERSON_ID);
	}
	
	
	public function getProjectId()
	{
		return $this->_getVal(self::_PROJECT_ID);
	}
	
	
	public function getTodoItemID()
	{
		return $this->_getVal(self::_TODO_ITEM_ID);
	}
	
	
	/**
	 * Load data returned from an api request
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\TimeEntry
	 */
	public function load(\SimpleXMLElement $xml, $force = false)
	{
		if($this->_loaded && !$force)
		{
			throw new \Sirprize\Basecamp\Exception('entity has already been loaded');
		}
		
		#print_r($xml); exit;
		$this->_loaded = true;
		$array = (array) $xml;
		
		$id = new \Sirprize\Basecamp\Id($array[self::_ID]);
		$personId = new \Sirprize\Basecamp\Id($array[self::_PERSON_ID]);
		$projectId = new \Sirprize\Basecamp\Id($array[self::_PROJECT_ID]);
		
    if ($array[self::_TODO_ITEM_ID]['nil'] != 'true')
    {
		$todoItemId = $array[self::_TODO_ITEM_ID];
    }
    else
    {
      $todoItemId = null;
    }
		
		$this->_data = array(
			self::_ID => $id,
			self::_DATE => $array[self::_DATE],
			self::_DESCRIPTION => $array[self::_DESCRIPTION],
			self::_HOURS => $array[self::_HOURS],
			self::_PERSON_ID => $personId,
			self::_PROJECT_ID => $projectId,
			self::_TODO_ITEM_ID => $todoItemId
		);
		
		return $this;
	}
	
	
	protected function _getService()
	{
		if($this->_service === null)
		{
			throw new \Sirprize\Basecamp\Exception('call setService() before '.__METHOD__);
		}
		
		return $this->_service;
	}
	
	
	
	protected function _getHttpClient()
	{
		if($this->_httpClient === null)
		{
			throw new \Sirprize\Basecamp\Exception('call setHttpClient() before '.__METHOD__);
		}
		
		return $this->_httpClient;
	}
	
	
	protected function _getVal($name)
	{
		return (isset($this->_data[$name])) ? $this->_data[$name] : null;
	}



	protected function _checkIsLoaded()
	{
		if(!$this->_loaded)
		{
			throw new \Sirprize\Basecamp\Exception('call load() before '.__METHOD__);
		}
	}
}