<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once(dirname(__FILE__) . '/../iomad_commerce/lib.php');


require_commerce_enabled();
$basketid = get_basket_id();

$transaction = $DB->start_delegated_transaction();
	
    $data->id = $basketid;	
	$data->status = INVOICESTATUS_PAID;
	/*if (!empty($CFG->commerce_admin_currency)) {
		$currency = $CFG->commerce_admin_currency;
	} else {
		$currency = '&pound';
	}*/
	$currency = '&pound';
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
		$tot = $amount=$tax =0;;
		$data->pp_ordertime = time();
		$data->pp_currencycode =$currency;
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
	
	foreach($invoiceitems as $invoiceitem)
	{
        // Get name for company license.
        $company = company::get_company_byuserid($invoice->userid);
        $course = $DB->get_record('course', array('id' => $invoiceitem->invoiceableitemid), 'id, shortname', MUST_EXIST);
        $licensename = $invoice->id." - ".$company->shortname . " [" . $course->shortname . "] " . date($CFG->iomad_date_format);
        $count = $DB->count_records_sql("SELECT COUNT(*) FROM {companylicense} WHERE name LIKE '" .
                                        (str_replace("'", "\'", $licensename)) . "%'");
        if ($count) {
            $licensename .= ' (' . ($count + 1) . ')';
        }
		
		$dlicensename = $company->shortname.'-'.$course->shortname.'-'.$invoice->id;
		$count = $DB->count_records_sql("SELECT COUNT(*) FROM {license} WHERE shortname LIKE '" .
                                        (str_replace("'", "\'", $dlicensename)) . "%'");
        if ($count) {
            $dlicensename .= '(' . ($count + 1) . ')';
        }
		
		$license = new stdClass;
		$license->shortname = $dlicensename;
		$license->fullname=$licensename;
		$license->source='';
		$license->version=date("YmdHis");
		$license->enabled=1;
		$rslicense = $DB->insert_record('license', $license);

        // Create mdl_companylicense record.
        $companylicense = new stdClass;
        $companylicense->name = $licensename;
        $companylicense->allocation = $invoiceitem->license_allocation ;
        $companylicense->humanallocation = $invoiceitem->license_allocation * $invoiceitem->quantity ;
        $companylicense->validlength = $invoiceitem->license_validlength;
        $companylicense->startdate = time();
       
		$companylicense->expirydate = (9999 * 86400) + time();
		// 86400 = 24*60*60 = number of seconds in a day.
        $companylicense->reference = $invoice->id;
        $companylicense->companyid = $company->id;
        $companylicenseid = $DB->insert_record('companylicense', $companylicense);
        // Create mdl_companylicense_courses record for the course.
        $clc = new stdClass;
        $clc->licenseid = $companylicenseid;
        $clc->courseid = $course->id;
        $DB->insert_record('companylicense_courses', $clc);
        // Mark the invoice item as processed.
        $invoiceitem->processed = 1;
        $DB->update_record('invoiceitem', $invoiceitem);
		
		$coursedetails = (array) $DB->get_record('iomad_courses', array('courseid' => $course->id));
		
		if(!empty($coursedetails->id))
		{
			$coursedetails['licensed'] = 1;
			$coursedetails['shared'] = 1;
			$DB->update_record('iomad_courses', $coursedetails);
		}
		else
		{
			$coursedetails = Array();
			$coursedetails['validlength'] = 0;
			$coursedetails['courseid'] = $course->id;
			$coursedetails['licensed'] = 1;
			$coursedetails['shared'] = 1;
			$DB->insert_record('iomad_courses', $coursedetails);
		}
		$group = (array) $DB->get_record('groups', array('courseid' => $course->id,'idnumber'=>$invoice->userid));
		if(empty($group->id))
		{
		$group = Array();
		$group['courseid'] =$course->id;
		$group['idnumber'] =$invoice->id;
		$group['description'] = $licensename;
		$group['descriptionformat'] = 0;
		$group['timecreated'] =time();
		$group['timemodified']=time();
		$group['hidepicture']=0;
		$group['picture']=0;
		$groupid  = $DB->insert_record('groups', $group);
		}
		else
		{
				
			$group['description'] = $licensename;
			$group['descriptionformat'] = 0;
			
			$group['timemodified']=time();
			$group['hidepicture']=0;
			$group['picture']=0;
			$groupid  = $DB->update_record('groups', $group);
		}
		$grouppivot = (array) $DB->get_record('company_course_groups', array('courseid' => $course->id,'companyid'=>$company->id,'groupid'=>$groupid));
		if(empty($grouppivot->id))
		{
			$grouppivot = array();
			$grouppivot['companyid'] = $company->id;
			$grouppivot['courseid'] = $course->id;
			$grouppivot['groupid'] = $groupid;
			
			// Write the data to the DB.
			$DB->insert_record('company_course_groups', $grouppivot);
		}
		
		
		
	}	
		
        $transaction->allow_commit();
		unset($SESSION->basketid);
		redirect(new moodle_url('/blocks/iomad_company_admin/company_license_users_form.php'));
?>