Basecamp API Wrapper
====================

### Setup
	$config = array(
		'baseUri' => 'https://xxx.basecamphq.com',
		'username' => 'xxx',
		'password' => 'xxx'
	);
	
	require_once 'Sirprize/Basecamp.php';
	$basecamp = new \Sirprize\Basecamp($config);
	
### Fetch all projects
	$projects = $basecamp->getProjectsInstance()->findAll();
	
	foreach($projects as $project)
	{
		print $project->getName()."\n";
	}

### Create a new milestone
	$milestones = $basecamp->getMilestoneCollectionInstance();
	$milestone = $milestones->getMilestoneInstance();
	
	require_once 'Sirprize/Basecamp/Date.php';
	$date = new \Sirprize\Basecamp\Date('2010-03-01');

	require_once 'Sirprize/Basecamp/Id.php';
	$projectId = new \Sirprize\Basecamp\Id('xxx');
	$userId = new \Sirprize\Basecamp\Id('xxx');

	$milestone
		->setProjectId($projectId)
		->setResponsiblePartyId($userId)
		->setDeadline($date)
		->setTitle('Milestoners Everywhere')
		->setWantsNotification(true)
	;

Requirements
------------

+ php 5.3+ (uses namespaces)
+ Recent version of zend framework (tested with 1.10) > uses Zend_Http_Client


Supported Features
------------------

+ Milestone: fully implemented
+ Person:
+ Project:
+ Todolist:
+ Todolistitems:


Getting Started
---------------

Please find plenty of examples in the example/basecamp directory and adjust example/basecamp/_config.php with your own settings