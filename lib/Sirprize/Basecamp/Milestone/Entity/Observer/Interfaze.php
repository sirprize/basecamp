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
 * Define the milestone observer interface
 *
 * @category  Sirprize
 * @package   Basecamp
 */
interface Interfaze
{
	
	public function onCompleteSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone);
	public function onUncompleteSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone);
	public function onCreateSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone);
	public function onUpdateSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone);
	public function onDeleteSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone);
	
	public function onCompleteError(\Sirprize\Basecamp\Milestone\Entity $milestone);
	public function onUncompleteError(\Sirprize\Basecamp\Milestone\Entity $milestone);
	public function onCreateError(\Sirprize\Basecamp\Milestone\Entity $milestone);
	public function onUpdateError(\Sirprize\Basecamp\Milestone\Entity $milestone);
	public function onDeleteError(\Sirprize\Basecamp\Milestone\Entity $milestone);
	
}