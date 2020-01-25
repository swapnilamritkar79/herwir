<?php
error_reporting(0);
include("config.php");

// block_iomad_company_admin_create_companies // to create company
// core_user_create_users to create user
// block_iomad_company_admin_assign_users to assign company admin

/// SETUP - NEED TO BE CHANGED
$token = '6eae37bfb49fdabacddebd958a68370e'; //Local
//$token = 'd42ee956782d55dab0c8e7026d4e531c'; //Stage
//$token = '12a2ba8a2c5cb1203357b296f6c84fef'; //Live

$domainname = $CFG->wwwroot;
if($_REQUEST['API_KEY'] == '1234' && $_REQUEST['email']!="") {
$courseid = 0;

$uname = strtolower($_REQUEST['uname']);
$email = $_REQUEST['email'];
$courseid_ID_NUMBER = $_REQUEST['courseid'];
$password = $_REQUEST['pwrd'];
$firstname = $_REQUEST['firstname'];
$lastname = $_REQUEST['lastname'];
$country = $_REQUEST['country'];
$city = $_REQUEST['city'];



//echo $uname = "testemail20";
/*
$email = "testemail18@moodle.com";
$password = "Test_1234";
$firstname = "Test First Name user12";
$lastname = "Test Last Name user12";
$phone1 = 12233;
$phone2 = 121222;
$country = "in";
$city = "pune";
*/

$coursedetail_idnumber=array('courseid' => $courseid_ID_NUMBER);
$functionname = 'local_wscourseid_course';
$params = array( 'data' => array($coursedetail_idnumber));
$method='get';
$output_report_course_id=json_decode(getData($token,$domainname,$functionname,'json',$params,$method));

//Get the course ID as global
$courseid=$output_report_course_id[0]->course->id;


//Set the course duration
$courseduration=0;
$coursedetail=array('courseid' => $courseid,'coursetype'=>'manual');
$functionname = 'local_wscoursedate_course';
$params = array( 'data' => array($coursedetail));
$method='get';
$output_report=json_decode(getData($token,$domainname,$functionname,'json',$params,$method));
if($output_report[0]->user->courseend>0) {
	$courseduration=$output_report[0]->user->courseend;
} else {
	$courseduration=90*86400;
}	

//Get the Course details if course exists and get the duration of course
$functionname = 'core_course_get_contents';
	$params = array(
	 'courseid' => $courseid
	 );
$method='get';
$output_course=json_decode(getData($token,$domainname,$functionname,'json',$params,$method));
if(sizeof($output_course)>1) {


	// To check users exists or not through email id
	$functionname = 'core_user_get_users';
	$username = array( 'key' => 'email' ,  'value' => $email );
	$params = array( 'criteria' => array($username));
	$method='get';
	$output=json_decode(getData($token,$domainname,$functionname,'json',$params,$method));
	$enrollment_userid=0;
	if($output->users[0]->id)
	{
			
			$enrollment_userid=$output->users[0]->id;
			$replace_str = array('"', "'", ","); 
			$userfirstname = str_replace($replace_str, "", $output->users[0]->firstname);
			$userlastname = str_replace($replace_str, "", $output->users[0]->lastname);
			$username = stripslashes($userfirstname). " " .stripslashes($userlastname);
				
			$userid=array('userid' => $output->users[0]->id,'username'=>$username);
			// Insert in to learners table
			$functionname = 'local_wsenrol_get_users_course_list';
			$params = array( 'data' => array($userid));
			$method='get';
			$output_report=json_decode(getData($token,$domainname,$functionname,'json',$params,$method));
						
	} else {
		//create new users
		
		$user1 = new stdClass();
		$user1->username = $uname;
		$user1->password =$password;
		$user1->firstname = $firstname;
		$user1->lastname = $lastname;
		$user1->email = $email;
		$user1->auth = 'manual';
		$user1->idnumber = '';
		$user1->lang = 'en';
		$user1->theme = '';
		$user1->timezone = '';
		$user1->mailformat = 0;
		$user1->description = '';
		$user1->city = $city;
		//$user1->phone1 = 9898981;
		//$user1->phone2 = 9898981;
		$user1->country = $country;
		$preferencename1 = 'preference1';
		$preferencename2 = 'preference2';
		$user1->preferences = array(
			array('type' => $preferencename1, 'value' => 'preferencevalue1'),
			array('type' => $preferencename2, 'value' => 'preferencevalue2'));
		
		
		$users = array($user1);
		$functionname = 'core_user_create_users';
		$params=array();
		$params =array('users' => $users);
		$method='post';
		$output_users=json_decode(getData($token,$domainname,$functionname,'json',$params,$method));
		//print_r($output_users);
		/* New Account created */
		$enrollment_userid=$output_users[0]->id;
		// Add to the learners reports table
		
		$replace_str = array('"', "'", ","); 
		$userfirstname = str_replace($replace_str, "",$firstname);
		$userlastname = str_replace($replace_str, "", $lastname);
		$username = stripslashes($userfirstname). " " .stripslashes($userlastname);
			
		$userid=array('userid' => $output_users[0]->id,'username'=>$username);
		$functionname = 'local_wsenrol_get_users_course_list';
		$params = array( 'data' => array($userid));
		$method='get';
		$output_report=json_decode(getData($token,$domainname,$functionname,'json',$params,$method));
		//echo "<br/>Enrolled to the Mdl_learners table : ".$output_users[0]->id;
		//print_r($output_report[0]->user->username);	
	}
		
		// Get enroll course list by users wise
		$functionname = 'core_enrol_get_users_courses';
			$params = array(
			 'userid' => $enrollment_userid
			 );
		$method='get';
		$output_enroll_course=json_decode(getData($token,$domainname,$functionname,'json',$params,$method));
		//print_r($output_enroll_course);
		$arr=array();
		foreach($output_enroll_course as $mycourse) {
			$arr[]=$mycourse->id;
		}

		if (in_array($courseid, $arr)) {
			// "Already assigned";
		} else {
			//Write to temp course and enroll the course
			
			// Enroll to the current course
			$enrolment = new stdClass();
			//estudante(student) -> 5; moderador(teacher) -> 4; professor(editingteacher) -> 3;
			$enrolment->roleid = 5; 
			$enrolment->userid =$enrollment_userid;
			$enrolment->courseid = $courseid; 
			//$enrolment->timestart =1449100800;
			//$enrolment->timeend =1480723200;
			$enrolments = array( $enrolment);
			$params = array('enrolments' => $enrolments);
			$functionname = 'enrol_manual_enrol_users';
			$method='post';
			$output_course=json_decode(getData($token,$domainname,$functionname,'json',$params,$method));
			/* Course Enrolled sucessfully */
			
			//Add data to our custom table 
			$userid=array('userid' => $enrollment_userid,'courseid'=>$courseid,'courseduration'=>$courseduration);
			$functionname = 'local_wscourseexpiry_course';
			$params = array( 'data' => array($userid));
			$method='post';
			$output_report=json_decode(getData($token,$domainname,$functionname,'json',$params,$method));
			//echo "<br/>Enrolled to the Mdl_my_expiry table : ".$output_users[0]->id;
			//print_r($output_report[0]->user->courseend);
			echo "Successfull";
				
			
		}
} else {
	echo "Invalid Course Id";
}
} else {
	echo "Unauthorized Access ! Please Verify API Key!";
}
function getData($token,$domainname,$functionname,$restformat,$params,$method)
{

	header('Content-Type: text/plain');
	$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
	require_once('curl.php');
	$curl = new curl;
	//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
	$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
	$resp = $curl->$method($serverurl . $restformat, $params);
	return $resp;
}

?>