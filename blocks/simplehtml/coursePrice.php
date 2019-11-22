<?php
require_once("../../config.php");
global $SESSION, $USER, $CFG, $DB; 

$id_price=$_REQUEST['id'];
$courseid=$_REQUEST['courseid'];
$price=$_REQUEST['price'];
$time = time();

$checkRecordSql = "select id from {course_price} where courseid=$courseid limit 1";
$checkRecord = $DB->get_record_sql($checkRecordSql);
if(isset($id_price) && $id_price >0){
	$myData = new stdClass();
	$myData->id = $id_price;
	$myData->courseid = $courseid;
	$myData->price = $price;
	$myData->modifydate = time();
	$myData->createdby = $USER->id;
	$result	=	$DB->update_record('course_price',$myData);
	$output = 'Record updated successfully';
}
else if($checkRecord==0){
	$myData = new stdClass();
	$myData->courseid = $courseid;
	$myData->price = $price;
	$myData->timecreated = time();
	$myData->modifydate = time();
	$myData->createdby = $USER->id;
	$DB->insert_record('course_price', $myData);
	$output = 'New Record Added Successfully';
} else {
		$output ='Record already exist. Please update from below list.';
}
echo $output;
?>