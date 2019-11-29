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

require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once(dirname(__FILE__) . '/../iomad_commerce/lib.php');
require_once('edit_cart.php');


require_commerce_enabled();

$remove = optional_param('remove', 0, PARAM_INT);

global $DB,$CFG;

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('course_shop_title', 'block_iomad_commerce');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_commerce/shop.php');

// Page stuff:.
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);
$PAGE->set_heading($SITE->fullname);
$PAGE->navbar->add($linktext, $linkurl);
$PAGE->navbar->add(get_string('basket', 'block_iomad_commerce'));

echo $OUTPUT->header();

flush();



if (!empty($SESSION->basketid)) {
    if ($remove) {
        // Before deleting
        // check that the record to be removed is on the current user's basket
        // (and not on an invoice or on somebody else's basket).
        if ($DB->record_exists_sql('SELECT ii.id
                                      FROM {invoiceitem} ii
                                INNER JOIN {course} c ON ii.invoiceableitemid = c.id
                                     WHERE ii.id = :toberemoved
                                       AND
                                    EXISTS ( SELECT id
                                             FROM {invoice} i
                                             WHERE i.id = :basketid
                                             AND i.status = :status
                                             AND i.id = ii.invoiceid
                                             )', array('basketid' => $SESSION->basketid, 'status' => INVOICESTATUS_BASKET, 'toberemoved' => $remove))) {
            $DB->delete_records('invoiceitem', array('id' => $remove));
        }
    }

    $baskethtml ='';
	$invoiceitems = $DB->get_records_sql("select ii.*,c.fullname,c.summary  from {invoiceitem} ii
                                INNER JOIN {course} c ON ii.invoiceableitemid = c.id where ii.invoiceid = :basketid",array('basketid' => $SESSION->basketid) );
	$args = array(
		'invoiceitems' => $invoiceitems
	);
	
	if(count($invoiceitems) >0)
	{
		$editform = new course_cart_form(null,$args);
		
		
		if (isset($_REQUEST['price']) && count($_REQUEST['price']) >0) 
		{
			
			foreach($_REQUEST['price'] as $iitemid =>$quantity )
			{
				$invoiceitem = $DB->get_record('invoiceitem', array('id' => $SESSION->basketid, 'id' => $iitemid));
				$invoiceitem->quantity = $quantity;
				$DB->update_record('invoiceitem', $invoiceitem);
			}
			redirect(new \moodle_url('/blocks/mycourses/basket.php'));
		}
		$editform->display(); 
	}
	else
	{
			echo '<p>' . get_string('emptybasket', 'block_iomad_commerce') . '</p>';
	}
} else {
    echo '<p>' . get_string('emptybasket', 'block_iomad_commerce') . '</p>';
}



echo $OUTPUT->footer();
