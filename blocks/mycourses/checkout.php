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

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once(dirname(__FILE__) . '/../iomad_commerce/lib.php');
require_once('edit_cart.php');


require_commerce_enabled();



class checkout_form extends moodleform {
    public function __construct($actionurl) {
        global $CFG;

        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG, $USER;

        $mform =& $this->_form;

        $mform->addElement('header', 'header', get_string('purchaser_details', 'block_iomad_commerce'));

        $mform->addElement('html', get_string('checkoutpreamble', 'block_iomad_commerce'));

        $strrequired = get_string('required');
		$mform->addElement('html', '<div class="col-12" style="float:left">');
		

        $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="50"');
        $mform->addRule('firstname', $strrequired, 'required', null, 'client');
        $mform->setType('firstname', PARAM_NOTAGS);

        $mform->addElement('text', 'lastname', get_string('lastname'), 'maxlength="100" size="50"');
        $mform->addRule('lastname', $strrequired, 'required', null, 'client');
        $mform->setType('lastname', PARAM_NOTAGS);

        $mform->addElement('text', 'company', get_string('company', 'block_iomad_company_admin'), 'maxlength="40" size="50"');
        $mform->addRule('company', $strrequired, 'required', null, 'client');
        $mform->setType('company', PARAM_NOTAGS);

        $mform->addElement('text', 'address1', get_string('address1','block_mycourses'), 'maxlength="70" size="50"');
        $mform->addRule('address1', $strrequired, 'required', null, 'client');
        $mform->setType('address1', PARAM_NOTAGS);
		
		 $mform->addElement('text', 'address2', get_string('address2','block_mycourses'), 'maxlength="70" size="50"');
        $mform->addRule('address2', $strrequired, 'required', null, 'client');
        $mform->setType('address2', PARAM_NOTAGS);
		

        $mform->addElement('text', 'city', get_string('city'), 'maxlength="120" size="50"');
        $mform->addRule('city', $strrequired, 'required', null, 'client');
        $mform->setType('city', PARAM_NOTAGS);

        $mform->addElement('text', 'postcode', get_string('postcode', 'block_iomad_commerce'), 'maxlength="20" size="20"');
        $mform->addRule('postcode', $strrequired, 'required', null, 'client');
        $mform->setType('postcode', PARAM_NOTAGS);

        $mform->addElement('text', 'state', get_string('state', 'block_iomad_commerce'), 'maxlength="20" size="20"');
        $mform->addRule('state', $strrequired, 'required', null, 'client');
        $mform->setType('state', PARAM_NOTAGS);

        $choices = get_string_manager()->get_list_of_countries();
        $choices = array('' => get_string('selectacountry').'...') + $choices;
        $mform->addElement('select', 'country', get_string('selectacountry'), $choices);
        $mform->addRule('country', $strrequired, 'required', null, 'client');

        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="50"');
        $mform->addRule('email', $strrequired, 'required', null, 'client');
        $mform->setType('email', PARAM_NOTAGS);

        $mform->addElement('text', 'phone1', get_string('phone'), 'maxlength="20" size="50"');
        $mform->setType('phone1', PARAM_NOTAGS);

        $linkurl = new moodle_url('/my/index.php');
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', 'Continue');
        $buttonarray[] = &$mform->createElement('button', 'resetbutton', 'Cancel',array('onclick'=>'window.location.href="'.$linkurl.'"'));
        $mform->addGroup($buttonarray, 'buttonbar', '', ' ', false);
		
		/*$mform->addElement('html', '<div class="col-6" style="float:left">');
		
		$mform->addElement('text', 'creditcardnumber', get_string('creditcardnumber'), 'maxlength="16" size="16"');
        $mform->addRule('creditcardnumber', $strrequired, 'required', null, 'client');
		$mform->addRule('creditcardnumber', 'Only Numerics Allowed', 'numeric', null, 'client');
        $mform->setType('creditcardnumber', PARAM_INT);
		
		$mform->addElement('text', 'cardholder', get_string('cardholder'), 'maxlength="100" size="50"');
        $mform->addRule('cardholder', $strrequired, 'required', null, 'client');
        $mform->setType('cardholder', PARAM_CLEAN);
		
		$choices = array('01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');
        $mform->addElement('select', 'cardmonth', get_string('cardmonth'), $choices);
        $mform->addRule('cardmonth', $strrequired, 'required', null, 'client');
        $mform->setType('cardmonth', PARAM_INT);
		
		$choicesyear = Array();
		for($i=0;$i<=10;$i++)
		{
			$choicesyear[date("y")+$i] =Date("Y")+$i;
		}
		
		
		$mform->addElement('select', 'cardyear', get_string('cardyear'), $choicesyear);
        $mform->addRule('cardyear', $strrequired, 'required', null, 'client');
        $mform->setType('cardyear', PARAM_INT);
		
		$mform->addElement('text', 'cvv', get_string('creditcardcvv'), 'maxlength="4" size="4"');
        $mform->addRule('cvv', $strrequired, 'required', null, 'client');
		$mform->addRule('cvv', 'Only Numerics Allowed', 'numeric', null, 'client');
        $mform->setType('cvv', PARAM_INT);
		
		$mform->addElement('html', '</div>');*/
		$mform->addElement('html', '</div>');
        $mform->addElement('hidden', 'userid', $USER->id);
        $mform->setType('userid', PARAM_INT);

        //$this->add_action_buttons(false, get_string('continue'));
		$buttonarray = array();
		
    }

}

require_login(null, false); // Adds to $PAGE, creates $OUTPUT.
$context = context_system::instance();

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('course_shop_title', 'block_iomad_commerce');
// Set the url.
$linkurl = new moodle_url('/blocks/mycourses/checkout.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);
$PAGE->set_heading(get_string('checkout', 'block_iomad_commerce'));

// Build the nav bar.
//$PAGE->navbar->add($linktext, $linkurl);
$PAGE->navbar->add(get_string('checkout', 'block_iomad_commerce'));


$data = clone $USER;
if (!empty($USER->company->name)) {
    $data->company = $USER->company->name;
} else {
    $data->company = "";
}

$linkurl = new moodle_url('/blocks/mycourses/paymentprocessing.php');

$mform = new checkout_form($linkurl);
$mform->set_data($data);

$error = '';
$displaypage = 1;

$basketid = get_basket_id();

if ($mform->is_cancelled()) {
    redirect('basket.php');

} else if ($data = $mform->get_data()) {
    $displaypage = 0;
	
	
   
}



echo $OUTPUT->header();

echo $error;

$mform->display();

echo get_basket_html();
?>
<style>
#fitem_id_company {
    margin-left: -16px !important;
}</style>
<?php


echo $OUTPUT->footer();
