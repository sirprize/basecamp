Basecamp API Wrapper
====================

### Setup
	require_once 'Sirprize/Basecamp.php';
	$config = array('baseUri' => 'https://xxx.basecamphq.com', 'username' => 'xxx', 'password' => 'xxx');
	$basecamp = new \Sirprize\Basecamp($config);
	
### Fetching a list a projects
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
+ recent version of zend framework (tested with 1.10)


Supported Features
------------------

+ milestone: fully implemented
+ person:
+ project:
+ todolist:
+ todolistitems:


Getting Started
---------------

Please find plenty of examples in the example/basecamp directory

+ adjust example/basecamp/_config.php with your own settings
+ make the files in example/basecamp/* executable
+ example/basecamp/get-projects to get a list of all projects
+ example/basecamp/get-persons to get a list of all persons
+ ...