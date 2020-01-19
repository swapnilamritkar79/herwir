<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once(dirname(__FILE__) . '/../iomad_commerce/lib.php');
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Payment Successfully");
$PAGE->set_heading("Payment Successfully");

echo $OUTPUT->header();
echo $OUTPUT->heading("Payment Successfully");
$contenthtml = $OUTPUT->box("Payment Successfully", 'generalbox', 'notice');
$contenthtml .= $OUTPUT->continue_button(new moodle_url('/blocks/iomad_company_admin/company_license_users_form.php'));
echo $contenthtml;
echo $OUTPUT->footer();

?>