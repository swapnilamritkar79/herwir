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
 * Class containing data for courses view in the mycourses block.
 *
 * @package    block_mycourses
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_mycourses\output;
defined('MOODLE_INTERNAL') || die();
require_once(dirname(__FILE__) . '/../../../iomad_company_admin/lib.php');
require_once(dirname(__FILE__) . '/../../../iomad_commerce/lib.php');
use renderable;
use renderer_base;
use templatable;
use core_course\external\course_summary_exporter;

/**
 * Class containing data for courses view in the mycourses block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class all_view implements renderable, templatable {
    /** Quantity of courses per page. */
    const COURSES_PER_PAGE = 6;

    /**
     * The courses_view constructor.
     *
     * @param array $courses list of courses.
     * @param array $coursesprogress list of courses progress.
     */
    public function __construct() {
       
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $DB,$SESSION;
        require_once($CFG->dirroot.'/course/lib.php');

        // Build courses view data structure.
       $allview = ['wwwroot'=>$CFG->wwwroot];
		
       
		
		$allcourses = $DB->get_records_sql("select c.*,cp.price from {course} c inner join {course_price}  cp on c.id = cp.courseid where visible =1 ");
		$allview['allcourses'] =[];
		foreach ($allcourses as $mid => $course) {
				
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
            $exportedcourse->url = new \moodle_url('/course/view.php', array('id' => $course->id));
            $exportedcourse->carturl = new \moodle_url('/blocks/mycourses/buynow.php', array('courseid' => $course->id,'popup'=>1));
            $exportedcourse->image = $imageurl;
            $exportedcourse->summary = substr(content_to_text($course->summary, $course->summaryformat),0,80);
			$exportedcourse->cnt = count($allview['allcourses']);
			$exportedcourse->price = $course->price;
			$exportedcourse->id = $course->id;
			
			
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
			
            $allview['allcourses'][] = $exportedcourse;
			
        }
		$allview['viewcarturl'] = new \moodle_url('/blocks/mycourses/basket.php', array('popup'=>1));
			
        return $allview;
    }
}
