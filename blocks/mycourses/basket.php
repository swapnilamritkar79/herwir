<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once(dirname(__FILE__) . '/../iomad_commerce/lib.php');
require_once('edit_cart.php');


require_commerce_enabled();

$remove = optional_param('remove', 0, PARAM_INT);
$popup = optional_param('popup', 0, PARAM_INT);
$linktext = get_string('course_shop_title', 'block_iomad_commerce');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_commerce/shop.php');

// Page stuff:.
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
global $DB,$CFG;
if($popup != 1)
{	
// Correct the navbar.
// Set the name for the page.

$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);
$PAGE->set_heading($SITE->fullname);
//$PAGE->navbar->add($linktext, $linkurl);
$PAGE->navbar->add(get_string('basket', 'block_iomad_commerce'));

echo $OUTPUT->header();
}
flush();



if (!empty($SESSION->basketid)) {
    if ($remove) {
        // Before deleting
        // check that the record to be removed is on the current user's basket
        // (and not on an invoice or on somebody else's basket).
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
                                             )', array('basketid' => $SESSION->basketid, 'status' => INVOICESTATUS_BASKET, 'toberemoved' => $remove))) {
            $DB->delete_records('invoiceitem', array('id' => $remove));
        }
    }

    $baskethtml ='';
	$invoiceitems = $DB->get_records_sql("select ii.*,c.fullname,c.summary  from {invoiceitem} ii
                                INNER JOIN {course} c ON ii.invoiceableitemid = c.id where ii.invoiceid = :basketid",array('basketid' => $SESSION->basketid) );
	
	
	if(count($invoiceitems) >0)
	{
		
		
		
		if (isset($_REQUEST['quantity']) && count($_REQUEST['quantity']) >0) 
		{
			
			foreach($_REQUEST['quantity'] as $iitemid =>$quantity )
			{
				$invoiceitem = $DB->get_record('invoiceitem', array('id' => $SESSION->basketid, 'id' => $iitemid));
				$invoiceitem->quantity = $quantity;
				$invoiceitem->license_allocation = 1;
				$DB->update_record('invoiceitem', $invoiceitem);
			}
			if($popup != 1)
			{
				redirect(new \moodle_url('/blocks/mycourses/basket.php'));
			}
		}
		$invoiceitems = $DB->get_records_sql("select ii.*,c.fullname,c.summary  from {invoiceitem} ii
                                INNER JOIN {course} c ON ii.invoiceableitemid = c.id where ii.invoiceid = :basketid",array('basketid' => $SESSION->basketid) );
			$args = array(
				'invoiceitems' => $invoiceitems
			);
			$editform = new course_cart_form(null,$args);
			
		$editform->display(); 
	}
	else
	{
			echo '<p>' . get_string('emptybasket', 'block_iomad_commerce') . '</p>';
	}
} else {
    echo '<p>' . get_string('emptybasket', 'block_iomad_commerce') . '</p>';
}


if($popup != 1)
{
echo $OUTPUT->footer();
}

?>
<style>.modal-backdrop.in{display:none;}</style>
<script>
var courseid;
$("a.removecart").click(function(event){
    event.preventDefault();
	courseid = $(this).attr('courseid');
    console.log(courseid);
	$.get($(this).attr('href'), function(data, status){
		
		$.get("<?php echo new moodle_url('/blocks/mycourses/basket.php',array('popup'=>$popup))?>", function(data, status){
				var trigger = $('#myModal');
				var course_id = "#course_"+courseid;
				$(".modal-body").html(data);
				$(course_id).prop('checked',false);
					
			});
	});
});
function changequantity(obj,invoiceid)
{
	var url ="<?php echo new moodle_url('/blocks/mycourses/updatequantity.php',array('popup'=>$popup)); ?>";
	url = url + "&iitemid="+invoiceid+"&quantity="+obj.value;
	
	$.get(url, function(data, status){
		 
		$.get("<?php echo new moodle_url('/blocks/mycourses/basket.php',array('popup'=>$popup))?>", function(data, status){
				var trigger = $('#myModal');
				
				$(".modal-body").html(data);
					
			});
	});
	
}
function applycoupon()
{
	
}
$(".viewcart").click(function(event){
    event.preventDefault();
  
    
    $.get($(this).attr('href'), function(data, status){
        var trigger = $('#myModal');
       
        
        ModalFactory.create({
            
            title: 'View Cart',
            body: data,
            large : 1,
        }, trigger)
        .done(function(modal) {
            modal.show();
			$(".modal-backdrop.in").hide();
        });
            
    });
});
 function isNumberKey(evt)
      {
		console.log(evt);
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

         return true;
      }
	 
</script>
<?php 