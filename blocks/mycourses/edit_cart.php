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
	
		
		
    $mform->addElement('html', '<div class="shopping-cart-hold shopping-cart-all">
        <table style="width:100%" class="shopping-cart-table">
          <thead class="">
            <tr>
              <th>Course Name</th>
              <th>Course QTY</th>
              <th>Price </th>
              <th>Total</th>
            </tr>
          </thead><tbody class=" content">');
		  
		$total = 0;
		foreach($invoiceitems as $invoiceitem)
		{
			
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
                <div class="price">$'.$invoiceitem->price.'</div>
              </td>
              <td>
                <div class="total">$'.sprintf ("%.2f",$invoiceitem->price * $invoiceitem->quantity).'</div>
              </td>

            </tr>');
			$total += ($invoiceitem->price * $invoiceitem->quantity);
			
		}
		$mform->addElement('html','</tbody></table><div class="final-total">
          <div class="text">Total</div>
          <div class="number">$'.sprintf ("%.2f",$total).'</div>
        </div>
        <div class="divhold">
          <div class="btnhold"><a href="'.new \moodle_url('/my/?mycoursestab=all').'"><span class="buynow-btn">Add More Course</span></a> 
			</div>
		<div class="btnhold"><a href="'.new \moodle_url('/').'"><span class="buynow-btn">Check Out</span></a> 
			</div>
			
		</div>');
	}
}
