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


class Persons implements \Iterator, \Countable
{
	
	
	protected $_basecamp = null;
	protected $_httpClient = null;
	protected $_data = array();
	protected $_started = false;
	protected $_response = null;
	
	
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
	
	
	
	public function getPersonInstance()
	{
		require_once 'Sirprize/Basecamp/Person.php';
		$milestone = new \Sirprize\Basecamp\Person();
		$milestone
			->setHttpClient($this->_getHttpClient())
			->setBasecamp($this->_getBasecamp())
		;
		return $milestone;
	}
	
	
	
	public function findAll()
	{
		if($this->_started)
		{
			return $this;
		}
		
		$this->_started = true;
		
		$response = $this->_getHttpClient()
			->setUri($this->_getBasecamp()->getBaseUri()."/people.xml")
			->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
			->request('GET')
		;
		
		$this->_response = $this->_handleResponse($response);
		return $this;
	}
	
	
	
	
	public function findById($id)
	{
		if($this->_started)
		{
			return $this;
		}
		
		$this->_started = true;
		
		if(!preg_match('/^[a-zA-Z0-9]+$/', $id))
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('invalid id parameter');
		}
		
		$response = $this->_getHttpClient()
			->setUri($this->_getBasecamp()->getBaseUri()."/people/$id.xml")
			->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
			->request('GET')
		;
		
		$this->_response = $this->_handleResponse($response);
		return $this;
	}
	
	
	
	
	
	
	
	protected function _handleResponse(\Zend_Http_Response $response)
	{
		require_once 'Sirprize/Basecamp/Response.php';
		$response = new \Sirprize\Basecamp\Response($response);
		
		if($response->isError())
		{
			return $response;
		}
		
		if(isset($response->getData()->id))
		{
			// single request
			$person = $this->getPersonInstance();
			$person->load($response->getData());
			$this->_data[] = $person;
			return $response;
		}
		
		$data = (array) $response->getData();
		
		if(isset($data['person']->id))
		{
			// list request - only 1 item in response
			$person = $this->getPersonInstance();
			$person->load($data['person']);
			$this->_data[] = $person;
			return $response;
		}
		
		if(!isset($data['person']))
		{
			return $response;
		}
		
		foreach($data['person'] as $row)
		{
			// list request
			$person = $this->getPersonInstance();
			$person->load($row);
			$this->_data[] = $person;
		}
		
		return $response;
	}
	
	
	
	
	public function getResponse()
	{
		return $this->_response;
	}
	
	
	
	public function count()
    {
        return count($this->_data);
    }
    
    
    
    public function current()
    {
        $key = key($this->_data);
        return (isset($this->_data[$key])) ? $this->_data[$key] : false;
    }
    
    
    
    public function key()
    {
        return key($this->_data);
    }
    
    
    
    public function next()
    {
        return next($this->_data);
    }
    
    
    
    public function rewind()
    {
        return reset($this->_data);
    }
    
    
    
    public function valid()
    {
        return (bool) $this->current();
    }
	
}