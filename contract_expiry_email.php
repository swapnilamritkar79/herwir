<?php

require(__DIR__ . '/config.php');
$PAGE->set_context(context_system::instance());

global $DB, $USER, $CFG;

//$CFG->debugsmtp = 1;
$currentTime = time();
$fivedaysLater = strtotime(date('Y-m-d h:i:s', strtotime('+5 days')));
//$fivedaysLater = 1608809645;

//get the  records whose contract expiry in next 5 days
$sql = "SELECT c.id as companyid, c.name as companyName, c.validto, u.id as userid, u.email, u.firstname, u.lastname FROM mdl_company c 
 INNER JOIN mdl_company_users cu ON c.id = cu.companyid
 INNER JOIN mdl_user u ON cu.userid = u.id
 INNER JOIN mdl_role_assignments ra ON u.id = ra.userid
 WHERE u.deleted !=1 and ra.roleid = 10 
 and c.suspended = 0";
$company_list = $DB->get_records_sql($sql);
//send mail to admins whose expiry date is in next 5 days
foreach ($company_list as $record) {
	$companyId = $record->companyid;
	$companyname = $record->name;
	$myUser = new stdClass();	
	$myUser->id = $record->userid;
	$myUser->firstname = $record->firstname;
	$myUser->lastname = $record->lastname;
	$myUser->validTo = $record->validto; 
	$myUser->email = $record->email;
	$myUser->mailformat = 1;
	$myUser->maildisplay = 1;

	if ($myUser->validTo <= $currentTime) {
		//suspend company if validity is expired
		$data =  new stdClass;
		$data->id = $companyId;
		$data->suspended = 1;
		$DB->update_record('company', $data);
		echo "<br/>company with id '.$companyId.' suspended";

	} elseif ($myUser->validTo > $currentTime && $myUser->validTo <= $fivedaysLater) {
		//send mail to admin for contract expiry
		$from = get_admin();
		$subject = "Notification for Contract Expirary <this is system generated email for your information,do not reply>";
		$output = '';
		$output .= 'Dear user,<br>';
		$output .= '<br>NOTE: This email is just for  informing all that your contract will be expired on '. date('Y-m-d h:i:s',$myUser->validTo) .'
	After that you cannot login to the system. Please contact administrator.';
		$output .= '<br><br><Warm Regards,<br>';
		$output .= 'Wirehouse Support Team,<br>';
		$output .= 'http://www.wirehouse.com';

		$mailresults = email_to_user($myUser, $from, $subject, '', $output, false);
		echo "mail sent";
	}
}