<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once(dirname(__FILE__) . '/../iomad_commerce/lib.php');
require_once('ipg-util.php');


require_commerce_enabled();
$basketid = get_basket_id();



$invoice = $DB->get_record('invoice',array('id' => $basketid));
$invoiceitems = $DB->get_records_sql('SELECT ii.*, c.fullname
										FROM {invoiceitem} ii
											INNER JOIN {course} c ON ii.invoiceableitemid = c.id
										WHERE ii.invoiceid = :invoiceid
										ORDER BY ii.id
									   ', array('invoiceid' => $basketid));
$pp_amount = $pp_taxamt =   $pp_settleamt=0;
foreach($invoiceitems as $invoiceitem)
	{
		$amount = round($invoiceitem->price * $invoiceitem->license_allocation * $invoiceitem->quantity,2);
		$tax = round($amount*($CFG->tax/100),2);
		$pp_amount += $amount;
		$pp_taxamt += $tax;
		$tot = $amount+$tax;
		$disc = round(($tot *($CFG->discount/100)),2);
		$pp_settleamt += ($amount+$tax) - $disc ;
		//echo $pp_settleamt."****".(($amount+$tax) - $disc)."*******".$amount."*******".$disc;
	}
	

?>

<form method="post" id="frmsubmit" action="https://test.ipg-online.com/connect/gateway/processing?storename=2220540392">
<input type="hidden" name="txntype" value="sale">
<input type="hidden" name="timezone" value="Europe/London" />
<input type="hidden" name="txndatetime" value="<?php echo getDateTime() ?>"/>
<input type="hidden" name="hash_algorithm" value="SHA256"/>
<input type="hidden" name="hash" value="<?php echo createHash("<?php echo sprintf('%01.2f',$pp_settleamt);?>","826" ) ?>"/>
<input type="hidden" name="storename" value="2220540392"/>
<input type="hidden" name="mode" value="payonly"/>
<input type="hidden" name="paymentMethod" value="M"/>
<input type="hidden" name="chargetotal" value="<?php echo sprintf('%01.2f',$pp_settleamt);?>"/>
<input type="hidden" name="currency" value="826"/>
<input type="hidden" name="invoicenumber" value="<?php echo $basketid;?>"/>
<input type="hidden" name="responseSuccessURL" value="<?php echo new moodle_url('/blocks/mycourses/successpayment.php') ?>"/>
<input type="hidden" name="responseFailURL" value="<?php echo new moodle_url('/blocks/mycourses/failedpayment.php') ?>"/>
<input type="hidden" name="bcompany" value="<?php echo $_POST['company']; ?>"/>
<input type="hidden" name="baddr1" value="<?php echo $_POST['address1']; ?>"/>
<input type="hidden" name="baddr2" value="<?php echo $_POST['address2']; ?>"/>
<input type="hidden" name="bcity" value="<?php echo $_POST['city']; ?>"/>
<input type="hidden" name="bstate" value="<?php echo $_POST['state']; ?>"/>
<input type="hidden" name="bcountry" value="<?php echo $_POST['country']; ?>"/>
<input type="hidden" name="bzip" value="<?php echo $_POST['postcode']; ?>"/>
<input type="hidden" name="email" value="<?php echo $_POST['email']; ?>"/>
<input type="hidden" name="phone" value="<?php echo $_POST['phone1']; ?>"/>
<input type="hidden" name="bname" value="<?php echo $_POST['firstname']." ".$_POST['lastname']; ?>"/>
</form>
<script
  src="https://code.jquery.com/jquery-3.4.1.js"
  integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
  crossorigin="anonymous"></script>

<script>
$( document ).ready(function() {
  // $("#frmsubmit").submit();
});
</script>
