<?php
require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once(dirname(__FILE__) . '/../iomad_commerce/lib.php');

$remove = optional_param('remove', 0, PARAM_INT);
$popup = optional_param('popup', 0, PARAM_INT);
$courseid    = required_param('courseid', PARAM_INT);
//echo "************".$popup;
global $DB;
if($popup != 1)
{	
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
}
else
{
		$PAGE->set_pagelayout('popup');
}
flush();



// Get or create basket.
if (!empty($SESSION->basketid)) {
	if ($remove) {
		
		$removeid = $DB->get_record_sql('SELECT ii.id
                                      FROM {invoiceitem} ii
                                INNER JOIN {course} c ON ii.invoiceableitemid = c.id
                                     WHERE c.id = :courseid
                                       AND
                                    EXISTS ( SELECT id
                                             FROM {invoice} i
                                             WHERE i.id = :basketid
                                             AND i.status = :status
                                             AND i.id = ii.invoiceid)
                                             ', array('basketid' => $SESSION->basketid, 'status' => INVOICESTATUS_BASKET,'courseid'=>$courseid));
		
		
		
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
                                             )', array('basketid' => $SESSION->basketid, 'status' => INVOICESTATUS_BASKET, 'toberemoved' => $removeid->id))) {
            $DB->delete_records('invoiceitem', array('id' => $removeid->id));
        }
    }
	else
    if (!$basket = $DB->get_record('invoice', array('id' => $SESSION->basketid, 'status' => INVOICESTATUS_BASKET), '*')) {
        $basket = new stdClass;
        $basket->userid = $USER->id;
        $basket->status = INVOICESTATUS_BASKET;
        $basket->date = time();
        $basket->id = $DB->insert_record('invoice', $basket, true);
        $SESSION->basketid = $basket->id;
    }
} else {
    $basket = new stdClass;
    $basket->userid = $USER->id;
    $basket->status = INVOICESTATUS_BASKET;
    $basket->date = time();
    $basket->id = $DB->insert_record('invoice', $basket, true);
    $SESSION->basketid = $basket->id;
}
if (!$remove) {
$invoiceitem = $DB->get_record('invoiceitem', array('invoiceid' => $SESSION->basketid, 'invoiceableitemid' => $courseid));

$courseprice = $DB->get_record('course_price', array('courseid' => $courseid), '*', MUST_EXIST) ;
global $CFG;
if (empty($invoiceitem->id))
{
	$invoiceitem = new stdClass;
	$invoiceitem->invoiceid = $basket->id;
	$invoiceitem->invoiceableitemid = $courseid;
	$invoiceitem->currency = $CFG->commerce_admin_currency;
	$invoiceitem->price = $courseprice->price;
	$invoiceitem->invoiceableitemtype = 'singlepurchase';	
	$invoiceitem->license_allocation = 1;
	$invoiceitem->quantity = 1;
    $invoiceitem->license_validlength = 999;
    $invoiceitem->license_shelflife = 0;
	$DB->insert_record('invoiceitem', $invoiceitem);
}
else
{
	$invoiceitem->invoiceid = $basket->id;
	$invoiceitem->invoiceableitemid = $courseid;
	$invoiceitem->currency = 'USD';
	$invoiceitem->price = $courseprice->price;
	$invoiceitem->quantity = $invoiceitem->quantity+1;
	$invoiceitem->invoiceableitemtype = 'singlepurchase';	
	$invoiceitem->license_allocation = 1;
    $invoiceitem->license_validlength = 999;
    $invoiceitem->license_shelflife = 0;
	$DB->update_record('invoiceitem', $invoiceitem);
}
}
if($popup != 1)
{	
	echo $OUTPUT->footer();
	redirect(new \moodle_url('/blocks/mycourses/basket.php'));

}
else
{
		echo "success";
}

?>