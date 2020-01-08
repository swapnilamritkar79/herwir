<?php
require_once('../../config.php');
//require_once(dirname(__FILE__) . '/../recommendedcourses/locallib.php');

define('DEFAULT_PAGE_SIZE', 20);
define('SHOW_ALL_PAGE_SIZE', 5000);

$PAGE->set_context(context_system::instance());
$context = $PAGE->context;
$url = $CFG->wwwroot;
require_login();

$systemcontext = context_system::instance();
$PAGE->set_pagelayout('admin');
$PAGE->set_url('/blocks/mycourses/viewall.php', array());
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('viewall','block_mycourses'));

	$viewallOutput = '<select class="custom-select" id="id_course"  name="id_course" onchange="doAction(this.value);">';
	$viewallOutput .='<option value="">Please select view all</option>';
	$viewallOutput .='<option value="available">Available</option>';
	$viewallOutput .='<option value="inprogress">Inprogress</option>';
	$viewallOutput .='<option value="completed">Completed</option>';
	$viewallOutput .='<option value="recommended">Recommended</option>';
	$viewallOutput .= '</select>';
	
	echo "<div> $viewallOutput</div>";

$renderable = new \block_mycourses\output\viewallcourses();

$render = $PAGE->get_renderer('block_mycourses');

echo $render->render_viewallcourses($renderable );

echo $OUTPUT->footer();
?>

<script>
 function doAction(val){
	 var url = '<?php echo $url;?>'; 
        window.location= url+'/blocks/mycourses/viewall.php?flag=' + val;
    }
</script>