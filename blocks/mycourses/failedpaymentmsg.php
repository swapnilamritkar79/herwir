<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once(dirname(__FILE__) . '/../iomad_commerce/lib.php');


require_commerce_enabled();
echo $OUTPUT->header();
echo $OUTPUT->heading("Payment Failed");
$contenthtml = $OUTPUT->box("Payment failed", 'generalbox', 'notice');
$contenthtml .= $OUTPUT->continue_button(new moodle_url('/my/index.php'));
echo $contenthtml;
echo $OUTPUT->footer();
?>