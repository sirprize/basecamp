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


class Response
{
    
    protected $_httpResponse = null;
    protected $_data = null;
	protected $_error = null;
	
	
    
    public function __construct(\Zend_Http_Response $httpResponse)
    {
    	$this->_httpResponse = $httpResponse;
		
		/*
		if(!$httpResponse->isError() && !preg_match('/^\s*$/', $httpResponse->getBody()))
    	{
			$this->_data = simplexml_load_string($httpResponse->getBody());
    	}
		*/
		
		if(!preg_match('/^\s*$/', $httpResponse->getBody()))
    	{
			//print $httpResponse->getBody();
			$this->_data = simplexml_load_string($httpResponse->getBody());
			
			if($httpResponse->isError())
			{
				$data = (array)$this->_data;
				
				if(isset($data['error']))
				{
					$this->_error = $data['error'];
				}
			}
    	}
    }
    
    
    
    public function getHttpResponse()
    {
    	return $this->_httpResponse;
    }
    
    
    
    public function getData()
    {
    	return $this->_data;
    }
    
    
    
    public function isError()
    {
    	return (
    		$this->_httpResponse->isError() ||
    		$this->getCode() !== null ||
    		$this->getMessage() !== null
    	);
    }
    
    
    
    public function getCode()
    {
		return null;
    }
    
    
    
    public function getMessage()
    {
		return $this->_error;
    }
    
}