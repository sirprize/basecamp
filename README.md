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
	$deadline = new \Sirprize\Basecamp\Date('2010-03-01');

	require_once 'Sirprize/Basecamp/Id.php';
	$projectId = new \Sirprize\Basecamp\Id('xxx');
	$userId = new \Sirprize\Basecamp\Id('xxx');

	$milestone
		->setProjectId($projectId)
		->setResponsiblePartyId($userId)
		->setDeadline($deadline)
		->setTitle('Milestoners Everywhere')
		->setWantsNotification(true)
		->create()
	;

Requirements
------------

+ php 5.3+ (uses namespaces)
+ Recent version of zend framework (tested with 1.10) > uses Zend_Http_Client & Zend_Log

Getting Started
---------------

Please find plenty of working examples in the `basecamp/example/basecamp` directory and adjust `basecamp/example/basecamp/_config.php` with your own settings

Supported Features
------------------

+ Milestone: fully implemented
+ Person: fully implemented
+ Project: fully implemented
+ Todolist: fully implemented
+ Todolistitems: fully implemented

Todo
----

+ account
+ companies
+ categories
+ messages
+ comments
+ time tracking