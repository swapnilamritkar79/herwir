<?php
defined('MOODLE_INTERNAL') || die();
class local_custom_events_observer {
    public static function observe_all(\core\event\base $event) {
        global $DB,$USER,$CFG;
		$eventname = $event->eventname;
		
					if($eventname == '\core\event\user_updated'){
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

    }	
}