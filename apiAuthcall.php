<?php
require(__DIR__ . '/config.php');
$PAGE->set_context(context_system::instance());

global $DB, $USER, $CFG;

$url = 'https://guardianapi.azurewebsites.net/api/clients';

$ch = curl_init($url);
$username  = 'harbingergroup';
$password = '_+wzH/m!Q4B#h^6b3n7K';

curl_setopt($ch, CURLOPT_HTTPHEADER, 
array('Content-Type: application/json',
  'API_KEY: 0c488e65-6180-4ef8-be55-6473870674aa',
  'Accept: application/json'
)
); 
curl_setopt($ch, CURLOPT_URL, $url);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$return = curl_exec($ch);	

curl_close($ch);
//echo $return;
$result = json_decode(json_decode($return,true));
//$result = $result[1];
function isValidTimeStamp($timestamp)
{
    return ((string) (int) $timestamp === $timestamp) 
        && ($timestamp <= PHP_INT_MAX)
        && ($timestamp >= ~PHP_INT_MAX);
}

foreach($result as $com)
{
	$companyName = strtolower($com->Name);	
	if(empty($companyName)){
		continue;
	}
	//print_r($companyName);
	$contractEndDate = strtotime($com->ContractEndDate);	
	if ($contractEndDate < 0)
	{
	   continue;
	}
	//print_r($contractEndDate);die;
	$DB->set_debug(true);
	$sql = "select id, name as name from mdl_company where lower(name)='".$companyName."'";	
	$record = $DB->get_record_sql($sql);
	//print_r($record);die;
	$company_details = (int)$record->id;
	
	$data =  new stdClass();
	$data->id = $company_details;
	$data->validto = $contractEndDate;
	
	$response = $DB->update_record('company', $data);
	print_R($response); 
	echo '>>';
	
}
				
?>