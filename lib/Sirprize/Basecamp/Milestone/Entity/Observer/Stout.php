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


namespace Sirprize\Basecamp\Milestone\Entity\Observer;




/**
 * Class to observe and print state changes of the observed milestone
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Stout extends \Sirprize\Basecamp\Milestone\Entity\Observer\Abstrakt
{
	
	
	public function onCompleteSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		print $this->_getOnCompleteSuccessMessage($milestone)."\n";
	}
	
	
	public function onUncompleteSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		print $this->_getOnUncompleteSuccessMessage($milestone)."\n";
	}
	
	
	public function onCreateSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		print $this->_getOnCreateSuccessMessage($milestone)."\n";
	}
	
	
	public function onUpdateSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		print $this->_getOnUpdateSuccessMessage($milestone)."\n";
	}
	
	
	public function onDeleteSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		print $this->_getOnDeleteSuccessMessage($milestone)."\n";
	}
	
	
	
	
	
	
	
	
	public function onCompleteError(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		print $this->_getOnCompleteErrorMessage($milestone)."\n";
	}
	
	
	public function onUncompleteError(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		print $this->_getOnUncompleteErrorMessage($milestone)."\n";
	}
	
	
	public function onCreateError(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		print $this->_getOnCreateErrorMessage($milestone)."\n";
	}
	
	
	public function onUpdateError(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		print $this->_getOnUpdateErrorMessage($milestone)."\n";
	}
	
	
	public function onDeleteError(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		print $this->_getOnDeleteErrorMessage($milestone)."\n";
	}
	
}