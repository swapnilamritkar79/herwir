<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');


class course_cart_form extends moodleform {
    protected $title = '';
    protected $description = '';
    protected $selectedcompany = 0;
    protected $context = null;

    public function __construct($actionurl,$customdata) {
        global $CFG;

        parent::__construct($actionurl,$customdata,'post', '', array('id'=>'editcart'));
    }

    public function definition() {
		global $CFG;

        $mform =& $this->_form;
		
        // Then show the fields about where this block appears.
       //$mform->addElement('header', 'header','Cart');
		$invoiceitems        = $this->_customdata['invoiceitems'];
	
		if (!empty($CFG->commerce_admin_currency)) {
            $currency = get_string($CFG->commerce_admin_currency, 'core_currencies');
        } else {
            $currency = get_string('GBP', 'core_currencies');
        }
		
    $mform->addElement('html', '<div class="shopping-cart-hold shopping-cart-all">
        <table style="width:100%" class="shopping-cart-table">
          <thead class="">
            <tr>
              <th>Course Name</th>
              <th>Course QTY</th>
              <th>Unit Price ('. $currency.')</th>
			  <th>Price ('. $currency.') </th>
			 
              <th>Total ('. $currency.')</th>
            </tr>
          </thead><tbody class=" content">');
		  
		$actualprice = $tax =$total = 0;
		foreach($invoiceitems as $invoiceitem)
		{
			$actualprice  += $invoiceitem->price * $invoiceitem->quantity;
			$tax  += ($invoiceitem->price * $invoiceitem->quantity)*($CFG->tax/100);
			$total += $actualprice;
           $mform->addElement('html','<tr>
              <td>
                <div class="course-name">'.$invoiceitem->fullname.'</div>
                <div class="course-dec"><small>'.$invoiceitem->summary.'</small></div>
              </td>
              <td>');
			  $removeurl = new \moodle_url('/blocks/mycourses/buynow.php',array('courseid' => $invoiceitem->invoiceableitemid,'popup'=>1,'remove'=>1));
			$mform->addElement('html','<input type="number" min="1" class="price-textbox" name="quantity['.$invoiceitem->id.']" onchange="changequantity(this,'.$invoiceitem->id.')" value="'.$invoiceitem->quantity.'" onkeypress="return isNumberKey(event)">');
			  $mform->addElement('html','<a class="removecart" courseid="'.$invoiceitem->invoiceableitemid.'" href="'.$removeurl.'"><i class="fa fa-times remove-cart "
                  aria-hidden="true"></i></a></td>
              <td>
                <div class="price">'.$invoiceitem->price.'</div>
              </td>
              <td>
                <div class="total">'.sprintf ("%.2f",($invoiceitem->price * $invoiceitem->quantity)).'</div>
              </td>
			 
			   <td>
                <div class="total">'.sprintf ("%.2f",($invoiceitem->price * $invoiceitem->quantity)).'</div>
              </td>

            </tr>');
			
			
			
		}
		$mform->addElement('html','</tbody></table>
		
		<div class="final-total">
          <div class="text">Total Price ('. $currency.')</div>
          <div class="number">'.sprintf ("%.2f",$actualprice).'</div>
        </div>
		<div class="final-total">
          <div class="text">Tax ('. $currency.')</div>
          <div class="number">'.sprintf ("%.2f",$tax).'</div>
        </div>
		<div class="final-total">
          <div class="text">Total ('. $currency.')</div>
          <div class="number">'.sprintf ("%.2f",($tax+$actualprice)).'</div>
        </div>
		<div class="final-total">
          <div class="text">Discount ('. $currency.')</div>
          <div class="number">'.sprintf ("%.2f",($tax+$actualprice)*($CFG->discount/100)).'</div>
        </div>
		<div class="final-total">
          <div class="text">Total('. $currency.')</div>
          <div class="number">'.sprintf ("%.2f",(($tax+$actualprice)- (($tax+$actualprice)*($CFG->discount/100)))).'</div>
        </div>
        <div class="divhold">
          <div class="btnhold"><a href="'.new \moodle_url('/my/?mycoursestab=all').'"><span class="buynow-btn">Add More Course</span></a> 
			</div>
		<div class="btnhold"><a href="'.new \moodle_url('/blocks/mycourses/checkout.php').'"><span class="buynow-btn">Check Out</span></a> 
			</div>
			
		</div>');
	}
}
