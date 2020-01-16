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
 * Class containing data for the Recently accessed courses block.
 *
 * @package    block_recentlyaccessedcourses
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_recommendedcourses\output;
defined('MOODLE_INTERNAL') || die();
   
use renderable;
use renderer_base;
use templatable;
use core_course\external\course_summary_exporter;
use stdClass; 
//use core_completion\progress;
use  core_course_renderer;

require_once($CFG->dirroot . '/blocks/recommendedcourses/locallib.php');
//require_once($CFG->libdir . '/completionlib.php'); 
/**
 * Class containing data for Recently accessed courses block.
 *
 * @package    block_recentlyaccessedcourses
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recommend_view implements renderable, templatable {
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return \stdClass|array
     */
	                                                                                      
 
     public function __construct($myrecommended) {                                                                                        
        $this->myrecommended = $myrecommended;                                                                                               
    } 
	 
    public function export_for_template(renderer_base $output) {
        global $CFG, $DB, $USER;
		require_once($CFG->dirroot.'/course/lib.php');
        // Build courses view data structure.
		$courseCounter = 0;
		
		$recommendurl = ['wwwroot'=>$CFG->wwwroot];
		
        $recommendview = [];

	        foreach ($this->myrecommended->myrecommend as $mid => $recommend) {
			
            $context = \context_course::instance($recommend->id);
            $course = $DB->get_record("course", array("id"=>$recommend->id));
			//print_r($course);die;
            $courseobj = new \core_course_list_element($course);

            $exporter = new course_summary_exporter($course, ['context' => $context]);
            $exportedcourse = $exporter->export($output);
            if ($CFG->mycourses_showsummary) {
                // Convert summary to plain text.
                $coursesummary = substr(content_to_text($exportedcourse->summary, $exportedcourse->summaryformat),0,80);
            } else {
                $coursesummary = '';
            }
            // display course overview files
            $imageurl = '';
            foreach ($courseobj->get_course_overviewfiles() as $file) {
                $isimage = $file->is_valid_image();
                if (!$isimage) {
                    $imageurl = null;
                } else {
                    $imageurl = file_encode_url("$CFG->wwwroot/pluginfile.php",
                                '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                                $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                }
            }
            if (empty($imageurl)) {
                $imageurl = $output->image_url('i/course');
            }

            $exportedcourse = $exporter->export($output);						
            $exportedcourse->url = new \moodle_url('/course/view.php', array('id' => $recommend->id));
            $exportedcourse->image = $imageurl;
            $exportedcourse->summary = $coursesummary;
            $recommendview['courses'][] = $exportedcourse;
			
			if(empty($recommendview['viewall']) && $courseCounter > 1 ){
				$recommendview['viewall'] = $CFG->wwwroot.'/blocks/mycourses/viewall.php?flag=recommended';
				$recommendview['viewallstring']='View All';
			}
			$courseCounter++;
        }
//var_dump($exportedcourse->flagurl);die;
        return $recommendview;
    }
    
}
