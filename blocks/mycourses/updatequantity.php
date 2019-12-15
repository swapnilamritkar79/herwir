<?php
require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once(dirname(__FILE__) . '/../iomad_commerce/lib.php');

$popup = optional_param('popup', 0, PARAM_INT);
$iitemid = required_param('iitemid',  PARAM_INT);
$quantity =required_param('quantity', PARAM_INT);

global $SESSION,$DB;
if($quantity == 0)
{
	  if ($DB->record_exists_sql('SELECT ii.id
                                      FROM {invoiceitem} ii
                                INNER JOIN {course} c ON ii.invoiceableitemid = c.id
                                     WHERE ii.id = :toberemoved
                                       AND
                                    EXISTS ( SELECT id
                                             FROM {invoice} i
                                             WHERE i.id = :basketid
                                             AND i.status = :status
                                             AND i.id = ii.invoiceid
                                             )', array('basketid' => $SESSION->basketid, 'status' => INVOICESTATUS_BASKET, 'toberemoved' => $iitemid))) {
												 $DB->delete_records('invoiceitem', array('id' => $iitemid));
											 }
	
}
else
{
	 if ($DB->record_exists_sql('SELECT ii.id
                                      FROM {invoiceitem} ii
                                INNER JOIN {course} c ON ii.invoiceableitemid = c.id
                                     WHERE ii.id = :toberemoved
                                       AND
                                    EXISTS ( SELECT id
                                             FROM {invoice} i
                                             WHERE i.id = :basketid
                                             AND i.status = :status
                                             AND i.id = ii.invoiceid
                                             )', array('basketid' => $SESSION->basketid, 'status' => INVOICESTATUS_BASKET, 'toberemoved' => $iitemid))) {
												 
	$invoiceitem = $DB->get_record('invoiceitem', array('id' => $SESSION->basketid, 'id' => $iitemid));
	$invoiceitem->quantity = $quantity;
	$DB->update_record('invoiceitem', $invoiceitem); 
		}
}
if($popup != 1)
{
	redirect(new \moodle_url('/blocks/mycourses/basket.php'));
}
else
{
	echo "success";
}
?>