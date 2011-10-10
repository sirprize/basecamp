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


namespace Sirprize\Basecamp\Comment;


/**
 * Represent and modify a comment
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Entity
{
	
	
	const _ID = 'id';
	const _AUTHOR_ID = 'author-id';
	const _AUTHOR_NAME = 'author-name';
	const _COMMENTABLE_ID = 'commentable-id';
	const _COMMENTABLE_TYPE = 'commentable-type';
	const _BODY = 'body';
	const _EMAILED_FROM = 'emailed-from';
	const _CREATED_AT = 'created-at';
	const _ATTACHMENTS_COUNT = 'attachments-count';	
	const _ATTACHMENTS = 'attachments';
	
	protected $_basecamp = null;
	protected $_httpClient = null;
	protected $_data = array();
	protected $_loaded = false;
	protected $_response = null;
	protected $_observers = array();
	protected $_attachments = null;
	
	
	
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
	 * @return \Sirprize\Basecamp\Comment
	 */
	public function attachObserver(\Sirprize\Basecamp\Comment\Entity\Observer\Abstrakt $observer)
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
	 * @return \Sirprize\Basecamp\Comment
	 */
	public function detachObserver(\Sirprize\Basecamp\Comment\Entity\Observer\Abstrakt $observer)
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
	
	
	
	public function getBody()
	{
		return $this->_getVal(self::_BODY);
	}
	
	
	
	public function getAttachments()
	{
		if($this->_attachments === null)
		{
			$this->_attachments = $this->_getBasecamp()->getAttachmentsInstance();
		}
		
		return $this->_attachments;
	}
	
	
	/**
	 * Load data returned from an api request
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Comment
	 */
	public function load(\SimpleXMLElement $xml, $force = false)
	{
		if($this->_loaded && !$force)
		{
			throw new \Sirprize\Basecamp\Exception('entity has already been loaded');
		}
		
		$this->_loaded = true;
		$array = (array) $xml;
		//print_r($array); exit;
		
		if(isset($array[self::_ATTACHMENTS]))
		{
			$this->getAttachments()->load($array[self::_ATTACHMENTS]);
			$this->_attachmentsLoaded = true;
		}
		
		$id = new \Sirprize\Basecamp\Id($array[self::_ID]);
		$authorId = new \Sirprize\Basecamp\Id($array[self::_AUTHOR_ID]);
		$commentableId = new \Sirprize\Basecamp\Id($array[self::_COMMENTABLE_ID]);
		$emailedFrom
			= ($array[self::_EMAILED_FROM] != '')
			? new \Sirprize\Basecamp\Id($array[self::_EMAILED_FROM])
			: null
		;
		
		$this->_data = array(
			self::_ID => $id,
			self::_AUTHOR_ID => $authorId,
			self::_AUTHOR_NAME => $array[self::_AUTHOR_NAME],
			self::_COMMENTABLE_ID => $commentableId,
			self::_COMMENTABLE_TYPE => $array[self::_COMMENTABLE_TYPE],
			self::_BODY => $array[self::_BODY],
			self::_EMAILED_FROM => $emailedFrom,
			self::_CREATED_AT => $array[self::_CREATED_AT],
			self::_ATTACHMENTS_COUNT => $array[self::_ATTACHMENTS_COUNT],
		);
		
		return $this;
	}

	protected function _getBasecamp()
	{
		if($this->_basecamp === null)
		{
			throw new \Sirprize\Basecamp\Exception('call setBasecamp() before '.__METHOD__);
		}
		
		return $this->_basecamp;
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
	
}