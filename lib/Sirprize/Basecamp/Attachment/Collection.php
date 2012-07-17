<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Attachment;

use Sirprize\Basecamp\Service;
use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\Attachment\Entity;
use Sirprize\Basecamp\Attachment\Collection\Observer\Abstrakt;

/**
 * Encapsulate a set of persisted attachment objects and the operations performed over them
 */
class Collection extends \SplObjectStorage
{
    const _ATTACHMENT = 'attachment';

    protected $_service = null;
    protected $_httpClient = null;
    protected $_started = false;
    protected $_loaded = false;
    protected $_response = null;
    protected $_observers = array();

    public function setService(Service $service)
    {
        $this->_service = $service;
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
     * @return \Sirprize\Basecamp\Attachment\Collection
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

    /**
     * Instantiate a new attachment entity
     *
     * @return Entity
     */
    public function getAttachmentInstance()
    {
        $attachment = new Entity();
        $attachment
            ->setHttpClient($this->_getHttpClient())
            ->setService($this->_getService())
        ;

        return $attachment;
    }

    /**
     * Defined by \SplObjectStorage
     *
     * Add attachment entity to batch-persist later by create()
     *
     * @param Entity $attachment
     * @throws \Sirprize\Basecamp\Exception
     * @return \Sirprize\Basecamp\Attachment\Collection
     */
    public function attach($attachment, $data = null)
    {
        if(!$attachment instanceof Entity)
        {
            throw new Exception('expecting an instance of Entity');
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
            throw new Exception('collection has already been loaded');
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
