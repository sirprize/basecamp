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


class TodoLists implements \Iterator, \Countable
{
	
	
	
	const FILTER_ALL = 'all';
	const FILTER_PENDING = 'pending';
	const FILTER_FINISHED = 'finished';
	
	
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
	
	
	
	public function getTodoListInstance()
	{
		require_once 'Sirprize/Basecamp/TodoList.php';
		$milestone = new \Sirprize\Basecamp\TodoList();
		$milestone
			->setHttpClient($this->_getHttpClient())
			->setBasecamp($this->_getBasecamp())
		;
		return $milestone;
	}
	
	
	
	// response includes list-items
	public function findAllByResponsibiltyParty($responsibleParty = null)
	{
		if($this->_started)
		{
			return $this;
		}
		
		$this->_started = true;
		$query = ''; // the current user is assumed
		
		if($responsibleParty == '')
		{
			// unassigned lists
			$query = '?responsible_party=';
		}
		else if($responsibleParty !== null)
		{
			// person id or company id (prefixed with c)
			$query = '?responsible_party='.$responsibleParty;
		}
		
		$response = $this->_getHttpClient()
			->setUri($this->_getBasecamp()->getBaseUri()."/todo_lists.xml$query")
			->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
			->request('GET')
		;
		
		$this->_response = $this->_handleResponse($response);
		return $this;
	}
	
	
	
	
	
	// response doesn't include list-items
	public function findAllByProjectId($id, $filter = null)
	{
		if($this->_started)
		{
			return $this;
		}
		
		$this->_started = true;
		$query = '';
		
		if($filter == self::FILTER_PENDING)
		{
			$query = '?filter='.self::FILTER_PENDING;
		}
		else if($filter == self::FILTER_FINISHED)
		{
			$query = '?filter='.self::FILTER_FINISHED;
		}
		/*
		if(!$this->_getBasecamp()->isValidId($id))
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('invalid id parameter');
		}
		*/
		$response = $this->_getHttpClient()
			->setUri($this->_getBasecamp()->getBaseUri()."/projects/$id/todo_lists.xml")
			->setAuth($this->_getBasecamp()->getUsername(), $this->_getBasecamp()->getPassword())
			->request('GET')
		;
		
		$this->_response = $this->_handleResponse($response);
		return $this;
	}
	
	
	
	
	
	// response includes list-items
	public function findById($id)
	{
		if($this->_started)
		{
			return $this;
		}
		
		$this->_started = true;
		/*
		if(!$this->_getBasecamp()->isValidId($id))
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('invalid id parameter');
		}
		*/
		$response = $this->_getHttpClient()
			->setUri($this->_getBasecamp()->getBaseUri()."/todo_lists/$id.xml")
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
			$todoList = $this->getTodoListInstance();
			$todoList->load($response->getData());
			$this->_data[] = $todoList;
			return $response;
		}
		
		$data = (array) $response->getData();
		
		if(isset($data['todo-list']->id))
		{
			// list request - only 1 item in response
			$todoList = $this->getTodoListInstance();
			$todoList->load($data['todo-list']);
			$this->_data[] = $todoList;
			return $response;
		}
		
		if(!isset($data['todo-list']))
		{
			return $response;
		}
		
		foreach($data['todo-list'] as $row)
		{
			// list request
			$todoList = $this->getTodoListInstance();
			$todoList->load($row);
			$this->_data[] = $todoList;
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