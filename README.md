Basecamp API Wrapper
====================

### Fetching a list a projects
	require_once 'Sirprize/Basecamp.php';
	$config = array('baseUri' => 'https://xxx.basecamphq.com', 'username' => 'xxx', 'password' => 'xxx');
	$basecamp = new \Sirprize\Basecamp($config);
	$projects = $basecamp->getProjectsInstance()->findAll();
	
	foreach($projects as $project)
	{
		print $project->getName()."\n";
	}

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