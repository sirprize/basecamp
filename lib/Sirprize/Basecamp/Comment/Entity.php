<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Comment;

use Sirprize\Basecamp\Id;
use Sirprize\Basecamp\Service;
use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\Comment\Entity\Observer\Abstrakt;

/**
 * Represent and modify a comment
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

    protected $_service = null;
    protected $_httpClient = null;
    protected $_data = array();
    protected $_loaded = false;
    protected $_response = null;
    protected $_observers = array();
    protected $_attachments = null;

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
     * @return \Sirprize\Basecamp\Comment
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
     * @return \Sirprize\Basecamp\Comment
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

    public function getBody()
    {
        return $this->_getVal(self::_BODY);
    }

    public function getAttachments()
    {
        if($this->_attachments === null)
        {
            $this->_attachments = $this->_getService()->getAttachmentsInstance();
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
            throw new Exception('entity has already been loaded');
        }

        $this->_loaded = true;
        $array = (array) $xml;
        //print_r($array); exit;

        if(isset($array[self::_ATTACHMENTS]))
        {
            $this->getAttachments()->load($array[self::_ATTACHMENTS]);
            $this->_attachmentsLoaded = true;
        }

        $id = new Id($array[self::_ID]);
        $authorId = new Id($array[self::_AUTHOR_ID]);
        $commentableId = new Id($array[self::_COMMENTABLE_ID]);
        $emailedFrom
            = ($array[self::_EMAILED_FROM] != '')
            ? new Id($array[self::_EMAILED_FROM])
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