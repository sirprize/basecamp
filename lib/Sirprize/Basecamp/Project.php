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


class Project
{
	
	
	const STATUS_ACTIVE = 'active';
	const STATUS_ON_HOLD = 'on_hold';
	const STATUS_ARCHIVED = 'archived';
	
	
	protected $_basecamp = null;
	protected $_httpClient = null;
	protected $_data = array();
	protected $_loaded = false;
	
	
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
		
		#print_r($data);
		$this->_loaded = true;
		$data = (array) $data;
		
		$this->_data = array(
			'id' => $data['id'],
			'name' => $data['name'],
			'status' => $data['status']
		);
		
		return $this;
	}
	
	
	public function getId()
	{
		return $this->_getVal('id');
	}
	
	
	public function getName()
	{
		return $this->_getVal('name');
	}
	
	
	public function getStatus()
	{
		return $this->_getVal('status');
	}
	
	
	public function isActive()
	{
		return ($this->getStatus() == self::STATUS_ACTIVE);
	}
	
	
	public function isArchived()
	{
		return ($this->getStatus() == self::STATUS_ARCHIVED);
	}
	
	
	public function isOnHold()
	{
		return ($this->getStatus() == self::STATUS_ON_HOLD);
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