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


namespace Sirprize\Basecamp\Person;


/**
 * Class to represent and modify a person
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Entity
{
	
	
	const _ADMINISTRATOR = 'administrator';
	const _CLIENT_ID = 'client-id';
	const _CREATED_AT = 'created-at';
	const _DELETED = 'deleted';
	const _HAS_ACCESS_TO_NEW_PROJECTS = 'has-access-to-new-projects';
	const _ID = 'id';
	const _IM_HANDLE = 'im-handle';
	const _IM_SERVICE = 'im-service';
	const _PHONE_NUMBER_FAX = 'phone-number-fax';
	const _PHONE_NUMBER_HOME = 'phone-number-home';
	const _PHONE_NUMBER_MOBILE = 'phone-number-mobile';
	const _PHONE_NUMBER_OFFICE = 'phone-number-office';
	const _PHONE_NUMBER_OFFICE_EXT = 'phone-number-office-ext';
	const _TITLE = 'title';
	#const _TOKEN = 'token';
	const _UPDATED_AT = 'updated-at';
	const _UUID = 'uuid';
	const _FIRST_NAME = 'first-name';
	const _LAST_NAME = 'last-name';
	const _COMPANY_ID = 'company-id';
	const _USER_NAME = 'user-name';
	const _EMAIL_ADDRESS = 'email-address';
	const _AVATAR_URL = 'avatar-url';
	
	
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
	
	
	
	
	public function getIsAdministrator()
	{
		return $this->_getVal(self::_ADMINISTRATOR);
	}
	
	
	public function getClientId()
	{
		return $this->_getVal(self::_CLIENT_ID);
	}
	
	
	public function getCreatedAt()
	{
		return $this->_getVal(self::_CREATED_AT);
	}
	
	
	public function getIsDeleted()
	{
		return $this->_getVal(self::_DELETED);
	}
	
	
	public function getHasAccessToNewProjects()
	{
		return $this->_getVal(self::_HAS_ACCESS_TO_NEW_PROJECTS);
	}
	
	/**
	 * @return \Sirprize\Basecamp\Id
	 */
	public function getId()
	{
		return $this->_getVal(self::_ID);
	}
	
	
	public function getImHandle()
	{
		return $this->_getVal(self::_IM_HANDLE);
	}
	
	
	public function getImService()
	{
		return $this->_getVal(self::_IM_SERVICE);
	}
	
	
	public function getPhoneNumberFax()
	{
		return $this->_getVal(self::_PHONE_NUMBER_FAX);
	}
	
	
	public function getPhoneNumberHome()
	{
		return $this->_getVal(self::_PHONE_NUMBER_HOME);
	}
	
	
	public function getPhoneNumberMobile()
	{
		return $this->_getVal(self::_PHONE_NUMBER_MOBILE);
	}
	
	
	public function getPhoneNumberOffice()
	{
		return $this->_getVal(self::_PHONE_NUMBER_OFFICE);
	}
	
	
	public function getPhoneNumberOfficeExt()
	{
		return $this->_getVal(self::_PHONE_NUMBER_OFFICE_EXT);
	}
	
	
	public function getTitle()
	{
		return $this->_getVal(self::_TITLE);
	}
	
	/*
	public function getToken()
	{
		return $this->_getVal(self::_TOKEN);
	}
	*/
	
	public function getUpdatedAt()
	{
		return $this->_getVal(self::_UPDATED_AT);
	}
	
	
	public function getUuid()
	{
		return $this->_getVal(self::_UUID);
	}
	
	
	public function getFirstname()
	{
		return $this->_getVal(self::_FIRST_NAME);
	}
	
	
	public function getLastname()
	{
		return $this->_getVal(self::_LAST_NAME);
	}
	
	/**
	 * @return \Sirprize\Basecamp\Id
	 */
	public function getCompanyId()
	{
		return $this->_getVal(self::_COMPANY_ID);
	}
	
	
	public function getUsername()
	{
		return $this->_getVal(self::_USER_NAME);
	}
	
	
	public function getEmailAddress()
	{
		return $this->_getVal(self::_EMAIL_ADDRESS);
	}
	
	
	public function getAvatarUrl()
	{
		return $this->_getVal(self::_AVATAR_URL);
	}
	
	
	
	
	/**
	 * Load data returned from an api request
	 *
	 * @throws \Sirprize\Basecamp\Exception
	 * @return \Sirprize\Basecamp\Person
	 */
	public function load(\SimpleXMLElement $xml, $force = false)
	{
		if($this->_loaded && !$force)
		{
			require_once 'Sirprize/Basecamp/Exception.php';
			throw new \Sirprize\Basecamp\Exception('entity has already been loaded');
		}
		
		#print_r($xml); exit;
		$this->_loaded = true;
		$array = (array) $xml;
		
		require_once 'Sirprize/Basecamp/Id.php';
		$id = new \Sirprize\Basecamp\Id($array[self::_ID]);
		$companyId = new \Sirprize\Basecamp\Id($array[self::_COMPANY_ID]);
		
		$administrator = ($array[self::_ADMINISTRATOR] == 'true');
		$deleted = ($array[self::_DELETED] == 'true');
		$hasAccessToNewProjects = ($array[self::_HAS_ACCESS_TO_NEW_PROJECTS] == 'true');
		
		$this->_data = array(
			self::_ADMINISTRATOR => $administrator,
			self::_CLIENT_ID => $array[self::_CLIENT_ID],
			self::_CREATED_AT => $array[self::_CREATED_AT],
			self::_DELETED => $deleted,
			self::_HAS_ACCESS_TO_NEW_PROJECTS => $hasAccessToNewProjects,
			self::_ID => $id,
			self::_IM_HANDLE => $array[self::_IM_HANDLE],
			self::_IM_SERVICE => $array[self::_IM_SERVICE],
			self::_PHONE_NUMBER_FAX => $array[self::_PHONE_NUMBER_FAX],
			self::_PHONE_NUMBER_HOME => $array[self::_PHONE_NUMBER_HOME],
			self::_PHONE_NUMBER_MOBILE => $array[self::_PHONE_NUMBER_MOBILE],
			self::_PHONE_NUMBER_OFFICE => $array[self::_PHONE_NUMBER_OFFICE],
			self::_PHONE_NUMBER_OFFICE_EXT => $array[self::_PHONE_NUMBER_OFFICE_EXT],
			self::_TITLE => $array[self::_TITLE],
			#self::_TOKEN => $array[self::_TOKEN],
			self::_UPDATED_AT => $array[self::_UPDATED_AT],
			self::_UUID => $array[self::_UUID],
			self::_FIRST_NAME => $array[self::_FIRST_NAME],
			self::_LAST_NAME => $array[self::_LAST_NAME],
			self::_COMPANY_ID => $companyId,
			self::_USER_NAME => $array[self::_USER_NAME],
			self::_EMAIL_ADDRESS => $array[self::_EMAIL_ADDRESS],
			self::_AVATAR_URL => $array[self::_AVATAR_URL]
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