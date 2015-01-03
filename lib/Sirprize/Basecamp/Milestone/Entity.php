<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Milestone;

use Sirprize\Basecamp\Id;
use Sirprize\Basecamp\Date;
use Sirprize\Basecamp\Service;
use Sirprize\Basecamp\Response;
use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\Milestone\Entity\Observer\Abstrakt;

/**
 * Represent and modify a milestone
 */
class Entity
{

    const _ID = 'id';
    const _TITLE = 'title';
    const _DEADLINE = 'deadline';
    const _COMMENTS_COUNT = 'comments-count';
    const _COMPLETED = 'completed';
    const _COMPLETED_ON = 'completed-on';
    const _CREATED_ON = 'created-on';
    const _CREATOR_ID = 'creator-id';
    const _PROJECT_ID = 'project-id';
    const _RESPONSIBLE_PARTY_ID = 'responsible-party-id';
    const _RESPONSIBLE_PARTY_TYPE = 'responsible-party-type';
    const _WANTS_NOTIFICATION = 'wants-notification';

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
     * @return \Sirprize\Basecamp\Milestone
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
     * @return \Sirprize\Basecamp\Milestone
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

    public function setTitle($title)
    {
        $this->_data[self::_TITLE] = $title;
        return $this;
    }

    public function setDeadline(Date $deadline)
    {
        $this->_data[self::_DEADLINE] = $deadline;
        return $this;
    }

    public function setProjectId(Id $projectId)
    {
        $this->_data[self::_PROJECT_ID] = $projectId;
        return $this;
    }

    public function setResponsiblePartyId(Id $responsiblePartyId)
    {
        $this->_data[self::_RESPONSIBLE_PARTY_ID] = $responsiblePartyId;
        return $this;
    }

    public function setWantsNotification($wantsNotification)
    {
        $this->_data[self::_WANTS_NOTIFICATION] = $wantsNotification;
        return $this;
    }

    public function getId()
    {
        return $this->_getVal(self::_ID);
    }

    public function getTitle()
    {
        return $this->_getVal(self::_TITLE);
    }

    /**
     * @return \Sirprize\Basecamp\Date
     */
    public function getDeadline()
    {
        return $this->_getVal(self::_DEADLINE);
    }

    public function getCommentsCount()
    {
        return $this->_getVal(self::_COMMENTS_COUNT);
    }

    public function getIsCompleted()
    {
        return $this->_getVal(self::_COMPLETED);
    }

    public function getCompletedOn()
    {
        return $this->_getVal(self::_COMPLETED_ON);
    }
    
    public function getCreatedOn()
    {
        return $this->_getVal(self::_CREATED_ON);
    }

    /**
     * @return \Sirprize\Basecamp\Id
     */
    public function getCreatorId()
    {
        return $this->_getVal(self::_CREATOR_ID);
    }

    /**
     * @return \Sirprize\Basecamp\Id
     */
    public function getProjectId()
    {
        return $this->_getVal(self::_PROJECT_ID);
    }

    /**
     * @return \Sirprize\Basecamp\Id
     */
    public function getResponsiblePartyId()
    {
        return $this->_getVal(self::_RESPONSIBLE_PARTY_ID);
    }

    public function getResponsiblePartyType()
    {
        return $this->_getVal(self::_RESPONSIBLE_PARTY_TYPE);
    }

    public function getWantsNotification()
    {
        return $this->_getVal(self::_WANTS_NOTIFICATION);
    }

    /**
     * Load data returned from an api request
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return \Sirprize\Basecamp\Milestone
     */
    public function load(\SimpleXMLElement $xml, $force = false)
    {
        if($this->_loaded && !$force)
        {
            throw new Exception('entity has already been loaded');
        }

        #print_r($xml); exit;
        $this->_loaded = true;
        $array = (array) $xml;

        $deadline = new Date($array[self::_DEADLINE]);

        $id = new Id($array[self::_ID]);
        $projectId = new Id($array[self::_PROJECT_ID]);
        $creatorId = new Id($array[self::_CREATOR_ID]);
        $responsiblePartyId = new Id($array[self::_RESPONSIBLE_PARTY_ID]);

        $completed = ($array[self::_COMPLETED] == 'true');
        $wantsNotification = ($array[self::_WANTS_NOTIFICATION] == 'true');

        $this->_data = array(
            self::_ID => $id,
            self::_TITLE => $array[self::_TITLE],
            self::_DEADLINE => $deadline,
            self::_COMMENTS_COUNT => $array[self::_COMMENTS_COUNT],
            self::_COMPLETED => $completed,
            self::_COMPLETED_ON => $array[self::_COMPLETED_ON],
            self::_CREATED_ON => $array[self::_CREATED_ON],
            self::_CREATOR_ID => $creatorId,
            self::_PROJECT_ID => $projectId,
            self::_RESPONSIBLE_PARTY_ID => $responsiblePartyId,
            self::_RESPONSIBLE_PARTY_TYPE => $array[self::_RESPONSIBLE_PARTY_TYPE],
            self::_WANTS_NOTIFICATION => $wantsNotification
        );

        return $this;
    }

    /**
     * Create XML to create a new milestone
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return string
     */
    public function getXml()
    {
        if($this->getTitle() === null)
        {
            throw new Exception('call setTitle() before '.__METHOD__);
        }

        if($this->getDeadline() === null)
        {
            throw new Exception('call setDeadline() before '.__METHOD__);
        }

        if($this->getResponsiblePartyId() === null)
        {
            throw new Exception('call setResponsiblePartyId() before  '.__METHOD__);
        }

          $xml  = '<milestone>';
        $xml .= '<title>'.htmlspecialchars($this->getTitle(), ENT_NOQUOTES).'</title>';
        $xml .= '<deadline type="date">'.$this->getDeadline().'</deadline>';
        $xml .= '<responsible-party>'.$this->getResponsiblePartyId().'</responsible-party>';
        $xml .= '<notify>'.(($this->getWantsNotification()) ? 'true' : 'false').'</notify>';
        $xml .= '</milestone>';
        return $xml;
    }

    /**
     * Persist this milestone in storage
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return boolean
     */
    public function create()
    {
        if($this->getProjectId() === null)
        {
            throw new Exception('set project-id before  '.__METHOD__);
        }

        $projectId = $this->getProjectId();

        $xml  = '<request>';
        $xml .= $this->getXml();
        $xml .= '</request>';

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/projects/$projectId/milestones/create")
                ->setAuth($this->_getService()->getUsername(), $this->_getService()->getPassword())
                ->setHeaders('Content-type', 'application/xml')
                ->setHeaders('Accept', 'application/xml')
                ->setRawData($xml)
                ->request('POST')
            ;
        }
        catch(\Exception $exception)
        {
            try {
                // connection error - try again
                $response = $this->_getHttpClient()->request('POST');
            }
            catch(\Exception $exception)
            {
                $this->onCreateError();

                throw new Exception($exception->getMessage());
            }
        }

        $this->_response = new Response($response);

        if($this->_response->isError())
        {
            // service error
            $this->onCreateError();
            return false;
        }

        $this->onCreateLoad($this->_response->getData()->milestone);
        return true;
    }

    /**
     * Load data from a \Sirprize\Basecamp\Milestone\Collection::create() opteration
     *
     * @return void
     */
    public function onCreateLoad(\SimpleXMLElement $xml)
    {
        $this->load($xml);
        $this->_onCreateSuccess();
    }

    /**
     * Get notified of an error in a \Sirprize\Basecamp\Milestone\Collection::create() opteration
     *
     * @return void
     */
    public function onCreateError()
    {
        $this->_onCreateError();
    }

    /**
     * Update this milestone in storage
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return boolean
     */
    public function update($moveUpcomingMilestones = false, $moveUpcomingMilestonesOffWeekends = false)
    {
        if(!$this->_loaded)
        {
            throw new Exception('call load() before '.__METHOD__);
        }

        $xml  = '<request>';
        $xml .= $this->getXml();
        $xml .= '<move-upcoming-milestones>'.(($moveUpcomingMilestones) ? 'true' : 'false').'</move-upcoming-milestones>';
        $xml .= '<move-upcoming-milestones-off-weekends>'.(($moveUpcomingMilestonesOffWeekends) ? 'true' : 'false').'</move-upcoming-milestones-off-weekends>';
        $xml .= '</request>';

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/milestones/update/".$this->getId())
                ->setAuth($this->_getService()->getUsername(), $this->_getService()->getPassword())
                ->setHeaders('Content-type', 'application/xml')
                ->setHeaders('Accept', 'application/xml')
                ->setRawData($xml)
                ->request('POST')
            ;
        }
        catch(\Exception $exception)
        {
            try {
                // connection error - try again
                $response = $this->_getHttpClient()->request('POST');
            }
            catch(\Exception $exception)
            {
                $this->_onUpdateError();

                throw new Exception($exception->getMessage());
            }
        }

        $this->_response = new Response($response);

        if($this->_response->isError())
        {
            // service error
            $this->_onUpdateError();
            return false;
        }

        $this->load($this->_response->getData(), true);
        $this->_onUpdateSuccess();
        return true;
    }

    /**
     * Delete this milestone from storage
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return boolean
     */
    public function delete()
    {
        $id = $this->getId();

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/milestones/delete/$id")
                ->setAuth($this->_getService()->getUsername(), $this->_getService()->getPassword())
                ->setHeaders('Content-type', 'application/xml')
                ->setHeaders('Accept', 'application/xml')
                ->request('DELETE')
            ;
        }
        catch(\Exception $exception)
        {
            try {
                // connection error - try again
                $response = $this->_getHttpClient()->request('DELETE');
            }
            catch(\Exception $exception)
            {
                $this->_onDeleteError();

                throw new Exception($exception->getMessage());
            }
        }

        $this->_response = new Response($response);

        if($this->_response->isError())
        {
            // service error
            $this->_onDeleteError();
            return false;
        }

        $this->_onDeleteSuccess();
        $this->_data = array();
        $this->_loaded = false;
        return true;
    }

    /**
     * Complete this milestone
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return boolean
     */
    public function complete()
    {
        if(!$this->_loaded)
        {
            throw new Exception('call load() before '.__METHOD__);
        }

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/milestones/complete/".$this->getId())
                ->setAuth($this->_getService()->getUsername(), $this->_getService()->getPassword())
                ->setHeaders('Content-type', 'application/xml')
                ->setHeaders('Accept', 'application/xml')
                ->request('GET')
            ;
        }
        catch(\Exception $exception)
        {
            try {
                // connection error - try again
                $response = $this->_getHttpClient()->request('GET');
            }
            catch(\Exception $exception)
            {
                $this->_onCompleteError();

                throw new Exception($exception->getMessage());
            }
        }

        $this->_response = new Response($response);

        if($this->_response->isError())
        {
            // service error
            $this->_onCompleteError();
            return false;
        }

        $this->load($this->_response->getData(), true);
        $this->_onCompleteSuccess();
        return true;
    }

    /**
     * Uncomplete this milestone
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return boolean
     */
    public function uncomplete()
    {
        if(!$this->_loaded)
        {
            throw new Exception('call load() before '.__METHOD__);
        }

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/milestones/uncomplete/".$this->getId())
                ->setAuth($this->_getService()->getUsername(), $this->_getService()->getPassword())
                ->setHeaders('Content-type', 'application/xml')
                ->setHeaders('Accept', 'application/xml')
                ->request('GET')
            ;
        }
        catch(\Exception $exception)
        {
            try {
                // connection error - try again
                $response = $this->_getHttpClient()->request('GET');
            }
            catch(\Exception $exception)
            {
                $this->_onCompleteError();

                throw new Exception($exception->getMessage());
            }
        }

        $this->_response = new Response($response);

        if($this->_response->isError())
        {
            // service error
            $this->_onUncompleteError();
            return false;
        }

        $this->load($this->_response->getData(), true);
        $this->_onUncompleteSuccess();
        return true;
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

    protected function _onCompleteSuccess()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onCompleteSuccess($this);
        }
    }

    protected function _onUncompleteSuccess()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onUncompleteSuccess($this);
        }
    }

    protected function _onCreateSuccess()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onCreateSuccess($this);
        }
    }

    protected function _onUpdateSuccess()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onUpdateSuccess($this);
        }
    }

    protected function _onDeleteSuccess()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onDeleteSuccess($this);
        }
    }

    protected function _onCompleteError()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onCompleteError($this);
        }
    }

    protected function _onUncompleteError()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onUncmpleteError($this);
        }
    }

    protected function _onCreateError()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onCreateError($this);
        }
    }

    protected function _onUpdateError()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onUpdateError($this);
        }
    }

    protected function _onDeleteError()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onDeleteError($this);
        }
    }

    protected $_todoLists = null;

    public function getTodoLists()
    {
        if($this->_todoLists === null)
        {
            $this->_todoLists = $this->_getService()->getTodoListsInstance();
        }

        return $this->_todoLists;
    }

    public function findTodoListByName($name)
    {
        foreach($this->getTodoLists() as $todoList)
        {
            if($name == $todoList->getName())
            {
                return $todoList;
            }
        }

        return null;
    }

}
