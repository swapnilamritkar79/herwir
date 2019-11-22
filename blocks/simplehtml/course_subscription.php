<?php
	require_once("../../config.php");
	ini_set('display_errors', 0);
	global $CFG,$PAGE,$USER;
	require_login();
	$isadmin = is_siteadmin($USER);
	$PAGE->set_context(context_system::instance());
	$PAGE->set_url($CFG->wwwroot.'/blocks/simplehtml/course_subscription.php');
	
	$sitecontext =$PAGE->set_context(context_system::instance());
    $site = get_site();
	$PAGE->set_pagelayout('base');
	
	$PAGE->set_title('Course Price Setting');
	echo $OUTPUT->header();
	$action = '';
	$id = '';
	if(isset($_REQUEST['a']) && isset($_REQUEST['id'])){
	$action = $_REQUEST['a'];
	$id = $_REQUEST['id'];
	$updatesql="select cp.id,c.fullname,price from mdl_course_price cp
	JOIN mdl_course c on c.id = cp.courseid
	
	where cp.id = ".$_REQUEST['id'];
	$data = $DB->get_record_sql($updatesql);
	$buttonValue = 'Update Price';

} else {
	$buttonValue = 'Add Price';
	$adnewbutton  = '';
}
function getCourse(){
	global $DB,$CFG;
	$sql = "SELECT *
			FROM {course}
			WHERE category != 0 AND visible = 1 ";
	$hscourses = $DB->get_records_sql($sql);
	return 	$hscourses;
}

$courselist = getCourse();

if(!empty($courselist)){
	$courseOutput = '<select id="id_course" name="id_course">';
	$courseOutput .='<option value="">Please select course</option>';
	foreach($courselist as $k => $v){
		if(@$data->fullname==$v->fullname){
			$selected  = 'selected';
		}
		else {
			$selected='';
		}
		$courseOutput .= "<option value=\"$v->id\" $selected> $v->fullname</option>";
	}
	$courseOutput .= '</select>';
} else {
	$courseOutput ='No Course available';
}
	 
	echo "<div class='clearfix'></div>";
	echo "<div class='gridStyle mt-30'>";
	echo "<form method='post' id='course_subscription'>";
	echo "<div class='p-20'>";
	echo "<input type='hidden' name='flag' id='flag' value=$action>";
	echo "<input type='hidden' name='id' id ='id_price' value=$id>";
	echo "<div>";
	echo "<div class='span4'>";
	echo "<label for='cname'>Course List </label>";
	echo "<div> $courseOutput</div>";
	echo "</div>";

	echo "<div class='span4'>";
	echo "<label for='cname'>Price</label>";
	echo "<div><input type='text' name='price' id='course_subscription_price' value=$data->price></div> ";
	echo "</div>";
	echo "</div>";

	
	echo "<div class='clearfix'></div>";
	echo "</div>";
	echo "<div class='text-right border1Top p-20'><input type='reset' name='cancel' value='Cancel' id='cancel'> <input type='submit' name='save' value=\"$buttonValue\"> </div>";
	echo "</form>";
	echo "</div>";

	echo "<div class='clearfix'></div>";


	echo "<h2 class='primaryTitle'>Course Package Details</h2>"; 
	echo "<div class='gridStyle mt-0'>"; 
	echo "<div id=\"price_validate\"></div>";
	echo "<table>";
	echo "<table id='coursePriceTable' class='coloredTH table-learnac courseprice' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Course Name</th>
                <th>Price</th>
				<th>Action Items</th>
            </tr>
        </thead>
</table>";
echo "</div>";
	?>
	<script>
	$("#course_subscription" ).validate({
	  rules: {
		id_course: {
			  required: true
			},
		price: {
		  required: true,
		  number: true,
		  min: 0
		}
	  },
    messages: {
		id_course: {
			required: 'Please select Course'
			},
		price: {
		  required: 'Please enter Price for this course',
		  number: 'Only number is allow e.g. 10,15',
		  min: 'less then 0 is not allow ',
		}
    }
	});
	</script>
	<script type="text/javascript" language="javascript" >
	$.noConflict();
		jQuery(document).ready(function($) {
			$('#coursePriceTable').DataTable( {
			processing: false,
			serverSide: true,
			searching: true,
			lengthChange: false,
			language: { search: "" },
			ajax: {url:"server_processing.php?funct=getPriceList"}
			/// $('#dg').datagrid('reload'); 
			});
			
			$("#course_subscription").submit(function(event){
			event.preventDefault();
			var siteurl  = "<?php echo $CFG->wwwroot;?>";
			var courseid = $("#id_course").val();
			var price = $("#course_subscription_price").val();
			var id = $("#id_price").val();
			var url  = siteurl+"/blocks/simplehtml/coursePrice.php";
			if(courseid != '' && price != '' ){
			$.ajax({
			url : url+"?courseid="+courseid+"&price="+price+"&id="+id
			}).done(function(data) {
			alert(data);
			window.location= siteurl+'/blocks/simplehtml/course_subscription.php';
			});
			}
			});
			var siteurl = "<?php echo $CFG->wwwroot; ?>";
			$("#cancel").click(function(event){
			event.preventDefault();
			var siteurl  = "<?php echo $CFG->wwwroot;?>";
			window.location.href = siteurl+'/blocks/simplehtml/course_subscription.php';
			});
	
	$(document).on('click','.delete_button',function(){
		var coursepriceid = $(this).attr('id');
		var siteurl = "<?php echo $CFG->wwwroot; ?>"; 
 		var url  = "server_processing.php?funct=delteRecord&id="+coursepriceid;
		$.ajax({
			url : url
		}).done(function(data) {
			alert(data);
		 location.reload();
		}); 
	});
	
	$(document).on('click','.edit_button',function(){
		var coursepriceid = $(this).attr('id');
		var siteurl = "<?php echo $CFG->wwwroot; ?>";	
	});
	
	
	$('#coursePriceTable').on( 'click', 'tbody td', function () {
	//myTable.cell( this ).edit();
	} );
	});
	</script>
	<?php
	echo $OUTPUT->footer();
?>


		
