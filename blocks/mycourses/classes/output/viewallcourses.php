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
namespace block_mycourses\output;
defined('MOODLE_INTERNAL') || die();
   
use renderable;
use renderer_base;
use templatable;
use core_course\external\course_summary_exporter;
use stdClass; 



//require_once($CFG->libdir . '/completionlib.php'); 
/**
 * Class containing data for Recently accessed courses block.
 *
 * @package    block_recentlyaccessedcourses
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class viewallcourses implements renderable, templatable {
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return \stdClass|array
     */
	                                                                                      
 
     public function __construct() {                                                                                        
                                                                                                   
    } 
		public function getAvailable(){
		global $DB, $USER;
		
		// getInprogress
		$myavailable = $DB->get_records_sql("select * from mdl_course");
		return $myavailable;
	}
		public function getInprogress(){
		global $DB, $USER;
		
		$myinprogress = $DB->get_records_sql("SELECT cc.id as ccid, cc.userid, cc.courseid as courseid,c.*
                                          FROM {local_iomad_track} cc
                                          JOIN {course} c ON (c.id = cc.courseid)
                                          JOIN {user_enrolments} ue ON (ue.userid = cc.userid)
                                          JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = c.id)
                                          WHERE cc.userid = :userid
                                          AND c.visible = 1
                                          AND cc.timecompleted IS NULL
                                          AND ue.timestart != 0",
                                          array('userid' => $USER->id));
		return $myinprogress;
	}
		public function getCompleted(){	
        global $DB, $USER;		
		
		$mycompleted = $DB->get_records_sql("SELECT cc.id, cc.userid, cc.courseid as courseid, cc.finalscore as finalgrade, cc.timecompleted, c.*
                                       FROM {local_iomad_track} cc
                                       JOIN {course} c ON (c.id = cc.courseid)
                                       WHERE cc.userid = :userid
                                       AND c.visible = 1
                                       AND cc.timecompleted IS NOT NULL",
                                       array('userid' => $USER->id));
		return $mycompleted;
	}
		public function getShoppingcart(){
		global $DB, $USER;
		
		$mycart = $DB->get_records_sql("select c.*,cp.price from {course} c inner join {course_price}  cp on c.id = cp.courseid where visible =1");
		
		return $mycart;
	}
		public function getRecommended(){
		global $DB, $USER;
		
		$recommended = $USER->interestedin;
		if(empty($recommended)){
			$myrecommend=$DB->get_records_sql("select * from {course}");		
		}
		else
		{
			$myrecommend = $DB->get_records_sql("SELECT c.* FROM mdl_course c where id in (SELECT t.itemid FROM mdl_tag_instance t where t.tagid in ($recommended) and t.itemtype='course')");
		}
		
		
		return $myrecommend;
	} 
		public function export_for_template(renderer_base $output) {
        global $CFG, $DB, $USER;
		require_once($CFG->dirroot.'/course/lib.php');
        // Build courses view data structure.
        
        $allview = ['wwwroot'=>$CFG->wwwroot];
		$flag  = $_REQUEST['flag'];
		if(empty($flag)){
			$flag = 'available';
		}
	
	/* View all for Mycourses block(available,inprogress,completed) and Recommended courses, shopping cart, */
		switch ($flag) {
			case 'available':
				$header = $this->getAvailable();
				break;
			case 'inprogress':
				$header = $this->getInprogress();
				break;
			case 'completed':
				$header = $this->getCompleted();
				break;
			case 'shoppingcart':
				$header = $this->getShoppingcart();
				break;
			case 'recommended':
				$header = $this->getRecommended();
				break;
			default:
				echo "No Data to display";
		}
		
	
		$allview['allcourses'] =[];
		foreach ($header as $mid => $course) {
            // get the course display info.
            $context = \context_course::instance($course->id);
           // $course = $DB->get_record("course", array("id"=>$notstarted->courseid));
		   
            $courseobj = new \core_course_list_element($course);

            $exporter = new course_summary_exporter($course, ['context' => $context]);
            $exportedcourse = $exporter->export($output);
            if ($CFG->mycourses_showsummary) {
                // Convert summary to plain text.
                $coursesummary = content_to_text($exportedcourse->summary, $exportedcourse->summaryformat);
            } else {
                $coursesummary = '';
            }
            // display course overview files
            $imageurl = '';
            foreach ($courseobj->get_course_overviewfiles() as $file) {
				
                $isimage = $file->is_valid_image();
                if (!$isimage) {
                    $imageurl = NULL;
                } else {
                    $imageurl = file_encode_url("$CFG->wwwroot/pluginfile.php",
                                '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                                $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                }
			
            }
            if (empty($imageurl) ) {
                $imageurl = $output->image_url('i/course');
            }
            $exportedcourse = $exporter->export($output);
			$exportedcourse->flag = $flag;
            $exportedcourse->url = new \moodle_url('/course/view.php', array('id' => $course->id));
            $exportedcourse->carturl = new \moodle_url('/blocks/mycourses/buynow.php', array('courseid' => $course->id,'popup'=>1));
            $exportedcourse->image = $imageurl;
            $exportedcourse->summary = substr(content_to_text($course->summary, $course->summaryformat),0,80);
			$exportedcourse->cnt = count($allview['allcourses']);
			$exportedcourse->price = $course->price;
			$exportedcourse->id = $course->id;
			
			if($flag == 'inprogress')
			{
				if ($totalrec = $DB->get_records('course_completion_criteria', array('course' => $course->id))) {				
				 $usercount = $DB->count_records('course_completion_crit_compl', array('course' => $course->id, 'userid' => $USER->id));             
				 $exportedcourse->progress = round($usercount * 100 / count($totalrec), 0);
				// $exportedcourse->hasprogress = true;
				}
			}
			
			if (isset($SESSION->basketid) && $DB->record_exists_sql('SELECT ii.id
                                      FROM {invoiceitem} ii
                                INNER JOIN {course} c ON ii.invoiceableitemid = c.id
                                     WHERE c.id = :courseid
                                       AND
                                    EXISTS ( SELECT id
                                             FROM {invoice} i
                                             WHERE i.id = :basketid
                                             AND i.status = :status
                                             AND i.id = ii.invoiceid)
                                             ', array('basketid' => $SESSION->basketid, 'status' => INVOICESTATUS_BASKET,'courseid'=>$course->id)))
			 {
				 $exportedcourse->checked = "checked";
				 
			 }
			 else
			 {
				 $exportedcourse->checked = "";
			 }
			 if($flag != 'inprogress'){
				 $exportedcourse->hasprogress = '';
			 }
			$exportedcourse->flag = 'inprogress';
            $allview['courses'][] = $exportedcourse;
			
        }
		
	return $allview;
    }
	
    
}
