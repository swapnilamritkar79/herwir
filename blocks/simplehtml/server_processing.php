<?php
require_once("../../config.php");
global $SESSION, $USER, $CFG, $DB; 

$ftcn = $_REQUEST['funct'];
$bc = new LearnerProgress();
$bc->$ftcn();

class LearnerProgress {
	public $conn;
	public $courseid;
	public $userlist;
	
public function __construct()
{
	global $DB,$CFG,$USER;
	$this->conn = mysqli_connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass,  $CFG->dbname) or die("Connection failed: " . mysqli_connect_error());
	mysqli_set_charset($this->conn,"utf8");
}

function delteRecord(){
	global $DB, $CFG;
	$result_del = $DB->delete_records('course_price', array('id'=>$_REQUEST['id']));
	echo 'Record Deleted';
}
function getPriceList() {
	global $DB, $CFG, $USER;				
	$sql="select cp.id,c.fullname,price from mdl_course_price cp
	JOIN mdl_course c on c.id = cp.courseid ";

	## Search 
	$searchValue = $_REQUEST['search']['value']; // Search value
	
	$searchQuery = " ";
	if($searchValue != ''){
		$searchCriteria = $this->searchCourseColumn($USER->id);
		if(count($searchCriteria) > 0){
			foreach($searchCriteria as $kk => $vv){
				$searchArray[] = " $vv like '%".$searchValue."%'";
			}
			$searchQuery = ' Where ( ' ;
			$searchQuery .= implode( ' OR ', $searchArray);
			$searchQuery .=' ) ';
		}
	}
	
	$sql .= $searchQuery;
	
	$query=mysqli_query($this->conn, $sql);
	$totalData = mysqli_num_rows($query);
	$totalFiltered = $totalData;  
	$requestData= $_REQUEST;
	$columns = array( 
	// datatable column index  => database column name
		0 =>'c.fullname',
		1=> 'price'
	);

	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";	
	$query=mysqli_query($this->conn, $sql);
	
	$data = array();
	while( $row=mysqli_fetch_array($query) ) {  // preparing an array
		$nestedData=array(); 
		$courseid = $row['id'];
		$nestedData[] = $row["fullname"];
		$nestedData[] = $row["price"];		
		$nestedData[] = '<span class="delete_button" id='.$row["id"].'><i class="fa fa-trash-o"></i></span> 
		<span class="edit_button" id='.$row["id"].'><a href=?a=u&id='.$courseid.'><i class="fa fa-pencil"></i></a></span>';		
		$data[] = $nestedData;
	}
	
	$json_data = array(
				"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => intval( $totalData ),  // total number of records
				"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => $data   // total data array
				);

	echo json_encode($json_data);  // send data as json format
	}
	
	function searchCourseColumn($userid = null, $table = null){
		global $DB,$USER;
		
		$searchQueryArray['fullname']= " fullname " ;
		$searchQueryArray['price']= " price " ;
		
		return $searchQueryArray;
	}
}
?>