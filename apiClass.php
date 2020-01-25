<?php
require_once("config.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
global $SESSION, $USER, $CFG, $DB; 

class apiClass {
	
	public $conn;
	public $module;
	
	public function __construct()
	{
		global $CFG, $USER, $DB;
		
	}	
	
	public function setToken($serviceId = null){
		global $CFG;
		// in future we might be fethc token from database and set here, based on user
		
		$token = $CFG->token;
		return $token;
	}
	
	public function prePareDataforCompany($param){
		
	}
	
	function getData($token,$functionname,$restformat,$params=null,$method){
		global $CFG;
		
		
		/* $users['userid']=28;
		$users['companyid']=24;
		$users['departmentid']= 0 ;
		$users['managertype']= 1;	
		$paramsNew['users'] = array($users);
		$params = $paramsNew;
		print_R($params); */ 
		
		//echo $method;
		//echo http_build_query($params);die;
		// https://localhost/iomad/webservice/rest/server.php?wstoken=TOKEN&moodlewsrestformat=json&wsfunction=block_iomad_company_admin_assign_users&users[0][userid]=7&users[0][companyid]=4&users[0][departmentid]=0&users[0][managertype]=0"
		
		$serverurl = $CFG->wwwroot . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
		//require('curl.php');
		$curl = new curl;
		//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
		header('Content-Type: text/plain');
		$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
		$resp = $curl->$method($serverurl . $restformat, $params);
		
		return $resp;
	}
}

/* $apiClass = new apiClass();
$apiClass->getData('af3ca7a24352447ea6d342926301443a','block_iomad_company_admin_assign_users','json','','get');  */


?>