<?php
defined('MOODLE_INTERNAL') || die();
class local_custom_events_observer {
    public static function observe_all(\core\event\base $event) {
        global $DB,$USER,$CFG;
		$eventname = $event->eventname;
		
		if($eventname == '\core\event\user_updated')
		{
			$interestedin = $_REQUEST['interestedin'];
			if(!empty($interestedin)){
				$interestedinString = implode(',',$interestedin);
				if(!empty($interestedinString)){
				$SqlCondition['interestedin']= "interestedin = '".$interestedinString."'";
				}
			}
			if(count($SqlCondition)){
			$innerSQL = implode(',',$SqlCondition);
			$sql = "update {user} set $innerSQL where id = $event->objectid";
			$DB->execute($sql);
			}
		}
		if($eventname == '\core\event\user_created'){
			if(!empty($_REQUEST['customregistration'])){
				$apiClass = new apiClass();
			    $apiToken = $apiClass->setToken();
				$compayname = $_REQUEST['companyfullname'];
				$sqlQry = "SELECT * FROM {company} WHERE name like '%".$compayname."%'";
				$companyRecord = $DB->get_record_sql($sqlQry);
				$users['userid']=$event->objectid;
				$users['companyid']=$companyRecord->id;               
				$users['departmentid']= 0 ;
				$users['managertype']= 1;	
				//$users['managertype']= 1;	
				$params = array('users' => array($users));
				
				$roleAssignment = new stdClass();
				$roleAssignment->roleid = 16;
				$roleAssignment->contextid = 1;
				$roleAssignment->userid = $event->objectid;
				$roleAssignment->timemodified = time();
				$roleAssignment->modifierid = 2;
				
				$DB->insert_record('role_assignments', $roleAssignment);
				
				$output_report=json_decode($apiClass->getData($apiToken,'block_iomad_company_admin_assign_users','json',$params,'get'));
			}
		}
    }	
}