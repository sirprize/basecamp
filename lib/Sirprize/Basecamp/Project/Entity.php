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


namespace Sirprize\Basecamp\Project;


/**
 * Class to represent and modify a project
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Entity
{
	
	
	const STATUS_ACTIVE = 'active';
	const STATUS_ON_HOLD = 'on_hold';
	const STATUS_ARCHIVED = 'archived';
	
	const _ANNOUNCEMENT = 'announcement'; // SimpleXMLElement
	const _CREATED_ON = 'created-on';
	const _ID = 'id';
	const _LAST_CHANGED_ON = 'last-changed-on';
	const _NAME = 'name';
	const _SHOW_ANNOUNCEMENT = 'show-announcement';
	const _SHOW_WRITEBOARDS = 'show-writeboards';
	const _START_PAGE = 'start-page';
	const _STATUS = 'status';
	const _COMPANY = 'company'; // SimpleXMLElement
	
	
	protected $_basecamp = null;
	protected $_httpClient = null;
	protected $_data = array();
	protected $_loaded = false;
	protected $_response = null;
	
	
	
	public function setBasecamp(\Sirprize\Basecamp $basecamp)
	{
		$this->_basecamp = $basecamp;
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
	
	
	
	public function getAnnouncement()
	{
		return $this->_getVal(self::_ANNOUNCEMENT);
	}
	
	
	public function getCreatedOn()
	{
		return $this->_getVal(self::_CREATED_ON);
	}
	
	
	/**
	 * @return \Sirprize\Basecamp\Id
	 */
	public function getId()
	{
		return $this->_getVal(self::_ID);
	}
	
	
	public function getLastChangedOn()
	{
		return $this->_getVal(self::_LAST_CHANGED_ON);
	}
	
	
	public function getName()
	{
		return $this->_getVal(self::_NAME);
	}
	
	
	public function getShowAnnouncement()
	{
		return $this->_getVal(self::_SHOW_ANNOUNCEMENT);
	}
	
	
	public function getShowWriteboards()
	{
		return $this->_getVal(self::_SHOW_WRITEBOARDS);
	}
	
	
	public function getStartPage()
	{
		return $this->_getVal(self::_START_PAGE);
	}
	
	
	public function getStatus()
	{
		return $this->_getVal(self::_STATUS);
	}
	
	
	public function getCompany()
	{
		return $this->_getVal(self::_COMPANY);
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
	
	
	
	
	/**
	 * Load data returned from an api request
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Project
	 */
	public function load(\SimpleXMLElement $data, $force = false)
	{
		if($this->_loaded && !$force)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('entity has already been loaded');
		}
		
		#print_r($data); exit;
		$this->_loaded = true;
		$data = (array) $data;
		
		require_once 'Sirprize/Basecamp/Id.php';
		$id = new \Sirprize\Basecamp\Id($data[self::_ID]);
		
		$showAnnouncement = ($data[self::_SHOW_ANNOUNCEMENT] == 'true');
		$showWriteboards = ($data[self::_SHOW_WRITEBOARDS] == 'true');
		
		$this->_data = array(
			self::_ANNOUNCEMENT => $data[self::_ANNOUNCEMENT],
			self::_CREATED_ON => $data[self::_CREATED_ON],
			self::_ID => $id,
			self::_LAST_CHANGED_ON => $data[self::_LAST_CHANGED_ON],
			self::_NAME => $data[self::_NAME],
			self::_SHOW_ANNOUNCEMENT => $showAnnouncement,
			self::_SHOW_WRITEBOARDS => $showWriteboards,
			self::_START_PAGE => $data[self::_START_PAGE],
			self::_STATUS => $data[self::_STATUS],
			self::_COMPANY => $data[self::_COMPANY]
		);
		
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
	
	
	
	protected function _getHttpClient()
	{
		if($this->_httpClient === null)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('call setHttpClient() before '.__METHOD__);
		}
		
		return $this->_httpClient;
	}
	
	
	protected function _getVal($name)
	{
		return (isset($this->_data[$name])) ? $this->_data[$name] : null;
	}
	
}