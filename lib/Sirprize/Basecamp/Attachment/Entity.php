<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Attachment;

use Sirprize\Basecamp\Id;
use Sirprize\Basecamp\Service;
use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\Attachment\Entity\Observer\Abstrakt;

/**
 * Represent and modify a attachment
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

    protected $_service = null;
    protected $_httpClient = null;
    protected $_data = array();
    protected $_loaded = false;
    protected $_response = null;
    protected $_observers = array();    

    public function setService(Service $service)
    {
        $this->_service = $service;
        return $this;
    }

    public function setHttpClient(\Zend\Http\Client $httpClient)
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
    public function attachObserver(Abstrakt $observer)
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
    public function detachObserver(Abstrakt $observer)
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
            throw new Exception('entity has already been loaded');
        }

        $this->_loaded = true;
        $array = (array) $xml;

        $id = new Id($array[self::_ID]);
        #$projectId = new Id($array[self::_PROJECT_ID]);
        $categoryId
            = ($array[self::_CATEGORY_ID] != '')
            ? new Id($array[self::_CATEGORY_ID])
            : null
        ;
        $personId = new Id($array[self::_PERSON_ID]);    
        $ownerId
            = ($array[self::_OWNER_ID] != '')
            ? new Id($array[self::_OWNER_ID])
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

    protected function _getService()
    {
        if($this->_service === null)
        {
            throw new Exception('call setService() before '.__METHOD__);
        }

        return $this->_service;
    }

    protected function _getHttpClient()
    {
        if($this->_httpClient === null)
        {
            throw new Exception('call setHttpClient() before '.__METHOD__);
        }

        return $this->_httpClient;
    }

    protected function _getVal($name)
    {
        return (isset($this->_data[$name])) ? $this->_data[$name] : null;
    }

}