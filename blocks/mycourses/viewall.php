<?php
require_once('../../config.php');
//require_once(dirname(__FILE__) . '/../recommendedcourses/locallib.php');

define('DEFAULT_PAGE_SIZE', 20);
define('SHOW_ALL_PAGE_SIZE', 5000);

$PAGE->set_context(context_system::instance());
$context = $PAGE->context;
require_login();

$systemcontext = context_system::instance();
$PAGE->set_pagelayout('admin');
$PAGE->set_url('/blocks/mycourses/viewall.php', array());
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('viewall','block_mycourses'));


//$allview = new viewallcourses($myrecommended);
$renderable = new \block_mycourses\output\viewallcourses();

$render = $PAGE->get_renderer('block_mycourses');
echo $render->render_viewallcourses($renderable );



echo $OUTPUT->footer();
?>