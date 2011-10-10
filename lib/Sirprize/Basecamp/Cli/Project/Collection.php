<?php


namespace Sirprize\Basecamp\Cli\Project;




class Collection extends \Sirprize\Basecamp\Project\Collection
{
	
	/**
	 * Instantiate a new project entity
	 *
	 * @return \Sirprize\Basecamp\Cli\Project\Entity
	 */
	public function getProjectInstance()
	{
		$project = new \Sirprize\Basecamp\Cli\Project\Entity();
		$project
			->setHttpClient($this->_getHttpClient())
			->setService($this->_getService())
		;
		
		return $project;
	}
	
}