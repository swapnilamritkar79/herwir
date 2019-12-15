<?php
require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once(dirname(__FILE__) . '/../iomad_commerce/lib.php');

$popup = optional_param('popup', 0, PARAM_INT);
$iitemid = required_param('iitemid',  PARAM_INT);
$quantity =required_param('quantity', PARAM_INT);

global $SESSION,$DB;


$invoiceitem = $DB->get_record('invoiceitem', array('id' => $SESSION->basketid, 'id' => $iitemid));
$invoiceitem->quantity = $quantity;
$DB->update_record('invoiceitem', $invoiceitem); 
if($popup != 1)
{
	redirect(new \moodle_url('/blocks/mycourses/basket.php'));
}
else
{
	echo "success";
}
?>