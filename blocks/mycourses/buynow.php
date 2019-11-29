<?php
require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once(dirname(__FILE__) . '/../iomad_commerce/lib.php');

$remove = optional_param('remove', 0, PARAM_INT);

global $DB;

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

$courseid    = required_param('courseid', PARAM_INT);

// Get or create basket.
if (!empty($SESSION->basketid)) {
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
redirect(new \moodle_url('/blocks/mycourses/basket.php'));

echo $OUTPUT->footer();
?>