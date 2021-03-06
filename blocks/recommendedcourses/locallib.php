<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Helper functions for mycourses block
 *
 * @package    block_mycourses
 * @copyright  2015 E-Learn Design http://www.e-learndesign.co.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function mycourses_get_my_recommended() {
    global $DB, $USER, $CFG;
	$interest=$USER->interestedin;

    $myrecommendcourse = new stdclass();
	if(empty($interest)){
		 $sql="select id, fullname,summary from mdl_course";		
	}
	else
	{
      $sql="SELECT c.id,c.fullname,c.summary FROM mdl_course c where id in (SELECT t.itemid FROM mdl_tag_instance t where t.tagid in ($interest) and t.itemtype='course')";
	}
    $myrecommend = $DB->get_records_sql($sql);		
	
	$recommendcourse = array();
	
	foreach ($myrecommend as $id => $recommend) {
    $myrecommend[$id]->fullname = format_string($recommend->fullname);		
    $recommendcourse[$recommend->id] = $recommend->id;
  }
  
  
  $myrecommendcourse->myrecommend = $myrecommend;

	return $myrecommendcourse;


}