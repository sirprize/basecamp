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


namespace Sirprize\Basecamp\Attachment;


/**
 * Represent and modify a attachment
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Entity
{
	
	
	const _ID = 'id';
	const _NAME = 'name';
	const _DESCRIPTION = 'description';
	const _BYTE_SIZE = 'byte-size';
	const _DOWNLOAD_URL = 'download-url';
	#const _PROJECT_ID = 'project-id';
	const _CATEGORY_ID = 'category-id';
	const _PERSON_ID = 'person-id';
	const _PRIVATE = 'private';	
	const _CREATED_ON = 'created-on';
	const _OWNER_ID = 'owner-id';
	const _OWNER_TYPE = 'owner-type';
	const _COLLECTION = 'collection';
	const _VERSION = 'version';
	const _CURRENT = 'current';
	
	protected $_basecamp = null;
	protected $_httpClient = null;
	protected $_data = array();
	protected $_loaded = false;
	protected $_response = null;
	protected $_observers = array();	
	
	
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
	
	
	/**
	 * Attach observer object
	 *
	 * @return \Sirprize\Basecamp\Attachment
	 */
	public function attachObserver(\Sirprize\Basecamp\Attachment\Entity\Observer\Abstrakt $observer)
	{
		$exists = false;
		
		foreach(array_keys($this->_observers) as $key)
		{
			if($observer === $this->_observers[$key])
			{
				$exists = true;
				break;
			}
		}
		
		if(!$exists)
		{
			$this->_observers[] = $observer;
		}
		
		return $this;
	}
	
	
	/**
	 * Detach observer object
	 *
	 * @return \Sirprize\Basecamp\Attachment
	 */
	public function detachObserver(\Sirprize\Basecamp\Attachment\Entity\Observer\Abstrakt $observer)
	{
		foreach(array_keys($this->_observers) as $key)
		{
			if($observer === $this->_observers[$key])
			{
				unset($this->_observers[$key]);
				break;
			}
		}
		
		return $this;
	}
	
	
	public function getId()
	{
		return $this->_getVal(self::_ID);
	}
	
	public function getDownloadUrl()
	{
		return $this->_getVal(self::_DOWNLOAD_URL);
	}
	
	public function getName()
	{
		return $this->_getVal(self::_NAME);
	}
	
	public function getOwnerId()
	{
		return $this->_getVal(self::_OWNER_ID);
	}
	
	
	/**
	 * Load data returned from an api request
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Attachment
	 */
	public function load(\SimpleXMLElement $xml, $force = false)
	{
		if($this->_loaded && !$force)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('entity has already been loaded');
		}
		
		$this->_loaded = true;
		$array = (array) $xml;
		
		require_once 'Sirprize/Basecamp/Id.php';
		$id = new \Sirprize\Basecamp\Id($array[self::_ID]);
		#$projectId = new \Sirprize\Basecamp\Id($array[self::_PROJECT_ID]);
		$categoryId
			= ($array[self::_CATEGORY_ID] != '')
			? new \Sirprize\Basecamp\Id($array[self::_CATEGORY_ID])
			: null
		;
		$personId = new \Sirprize\Basecamp\Id($array[self::_PERSON_ID]);	
		$ownerId
			= ($array[self::_OWNER_ID] != '')
			? new \Sirprize\Basecamp\Id($array[self::_OWNER_ID])
			: null
		;
		
		$private = ($array[self::_PRIVATE] == 'true');
		$current = ($array[self::_CURRENT] == 'true');
		
		$this->_data = array(
			self::_ID => $id,
			self::_NAME => $array[self::_NAME],
			self::_DESCRIPTION => $array[self::_DESCRIPTION],
			self::_BYTE_SIZE => $array[self::_BYTE_SIZE],
			self::_DOWNLOAD_URL => $array[self::_DOWNLOAD_URL],
			#self::_PROJECT_ID => $projectId,
			self::_CATEGORY_ID => $categoryId,
			self::_PERSON_ID => $personId,
			self::_PRIVATE => $private,
			self::_CREATED_ON => $array[self::_CREATED_ON],
			self::_OWNER_ID => $ownerId,
			self::_OWNER_TYPE => $array[self::_OWNER_TYPE],
			self::_COLLECTION => $array[self::_COLLECTION],
			self::_VERSION => $array[self::_VERSION],
			self::_CURRENT => $current,
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