<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once(dirname(__FILE__) . '/../iomad_commerce/lib.php');


require_commerce_enabled();
$basketid = get_basket_id();

$transaction = $DB->start_delegated_transaction();
	
    $data->id = $basketid;	
	$data->status = INVOICESTATUS_UNPAID;
	/*if (!empty($CFG->commerce_admin_currency)) {
		$currency = $CFG->commerce_admin_currency;
	} else {
		$currency = '&pound';
	}*/
	$currency = 'GBP';
	$data->pp_amount = 0;
	$data->pp_settleamt = 0;
	$data->pp_taxamt= 0;
		
	
		
	$invoice = $DB->get_record('invoice',array('id' => $data->id));
	$invoiceitems = $DB->get_records_sql('SELECT ii.*, c.fullname
                                            FROM {invoiceitem} ii
                                                INNER JOIN {course} c ON ii.invoiceableitemid = c.id
                                            WHERE ii.invoiceid = :invoiceid
                                            ORDER BY ii.id
                                           ', array('invoiceid' => $data->id));
	foreach($invoiceitems as $invoiceitem)
	{
		$tot = $amount=$tax =$disc = 0;
		$data->pp_ordertime = time();
		$data->pp_currencycode =htmlspecialchars($currency);
		$amount = round($invoiceitem->price * $invoiceitem->license_allocation * $invoiceitem->quantity,2);
		
		$amount = round($invoiceitem->price * $invoiceitem->license_allocation * $invoiceitem->quantity,2);
		$tax = round($amount*($CFG->tax/100),2);
		$data->pp_amount += $amount;
		$data->pp_taxamt += $tax;
		$tot = $amount+$tax;
		$disc = round($tot *$CFG->discount/100);
		$data->pp_settleamt += ($amount+$tax) - $disc ;
	}
	
	 
    $DB->update_record('invoice', $data, array('id' => $data->id));
$transaction->allow_commit();
unset($SESSION->basketid);
redirect(new moodle_url('/blocks/iomad_company_admin/company_license_users_form.php'));
?>