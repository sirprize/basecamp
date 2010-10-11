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
 * Encapsulate a set of persisted attachment objects and the operations performed over them
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Collection extends \SplObjectStorage
{
	const _ATTACHMENT = 'attachment';
	
	protected $_basecamp = null;
	protected $_httpClient = null;
	protected $_started = false;
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
	 * @return \Sirprize\Basecamp\Attachment\Collection
	 */
	public function attachObserver(\Sirprize\Basecamp\Attachment\Collection\Observer\Abstrakt $observer)
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
	 * @return \Sirprize\Basecamp\Attachment\Collection
	 */
	public function detachObserver(\Sirprize\Basecamp\Attachment\Collection\Observer\Abstrakt $observer)
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
	
	
	
	/**
	 * Instantiate a new attachment entity
	 *
	 * @return \Sirprize\Basecamp\Attachment\Entity
	 */
	public function getAttachmentInstance()
	{
		require_once 'Sirprize/Basecamp/Attachment/Entity.php';
		$attachment = new \Sirprize\Basecamp\Attachment\Entity();
		$attachment
			->setHttpClient($this->_getHttpClient())
			->setBasecamp($this->_getBasecamp())
		;
		
		return $attachment;
	}
	
	
	
	/**
	 * Defined by \SplObjectStorage
	 *
	 * Add attachment entity to batch-persist later by create()
	 *
	 * @param \Sirprize\Basecamp\Attachment\Entity $attachment
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Attachment\Collection
	 */
	public function attach($attachment, $data = null)
	{
		if(!$attachment instanceof \Sirprize\Basecamp\Attachment\Entity)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('expecting an instance of \Sirprize\Basecamp\Attachment\Entity');
		}
		
		parent::attach($attachment);
		return $this;
	}
	
	
	/**
	 * Instantiate attachment objects with api response data
	 *
	 * @return \Sirprize\Basecamp\Attachment\Collection
	 */
	public function load(\SimpleXMLElement $xml)
	{
		if($this->_loaded)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('collection has already been loaded');
		}
		
		$this->_loaded = true;
		
		if(isset($xml->id))
		{
			// request for a single entity
			$attachment = $this->getAttachmentInstance();
			$attachment->load($xml);
			$this->attach($attachment);
			return $this;
		}
		
		$array = (array) $xml;
		
		if(!isset($array[self::_ATTACHMENT]))
		{
			// list request - 0 items in response
			return $this;
		}
		
		if(isset($array[self::_ATTACHMENT]->id))
		{
			// list request - 1 item in response
			$attachment = $this->getAttachmentInstance();
			$attachment->load($array[self::_ATTACHMENT]);
			$this->attach($attachment);
			return $this;
		}
		
		foreach($array[self::_ATTACHMENT] as $row)
		{
			// list request - 2 or more items in response
			$attachment = $this->getAttachmentInstance();
			$attachment->load($row);
			$this->attach($attachment);
		}
		
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
	
	protected function _onStartSuccess()
	{
		foreach($this->_observers as $observer)
		{
			$observer->onStartSuccess($this);
		}
	}
	
	
	protected function _onStartError()
	{
		foreach($this->_observers as $observer)
		{
			$observer->onStartError($this);
		}
	}
	
}
