<?php error_reporting(0);
include("config.php");
/* //$token = 'aecf41cdc65177945a80ad4787b60b40'; //Local
$token = '6eae37bfb49fdabacddebd958a68370e'; //TKA VM Setup Access token
$domainname = $CFG->wwwroot;
$functionname='core_course_get_courses';
$method="get";
$params='';
$output_enroll_course=json_decode(getData($token,$domainname,$functionname,'json',$params,$method));
//print_r($output_enroll_course);
function getData($token,$domainname,$functionname,$restformat,$params,$method)
{

	//header('Content-Type: text/plain');
	$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
	require_once('curl.php');
	$curl = new curl;
	//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
	$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
	$resp = $curl->$method($serverurl . $restformat, $params);
	return $resp;
} */
?>
<html>
<head>
</head>
<body>
<table width="40%" align="center" border="1">
<tr><td> <b>Web service</b></td></tr>
<tr><td style="padding-left:20%">
<form method="post" action="moodle_api.php">
   User name:<br>
  <input type="text" name="uname">
  <br>
  
   Email:<br>
  <input type="text" name="email">
  <br>
  
   <!--Course :<br>
   
    <select id="courseid" name="courseid">
	  <?php foreach($output_enroll_course as $course) { ?>
	   <option value="<?php echo $course->id ?>"><?php echo $course->fullname ?></option>
	  <?php }?>
	  
	</select> -->
   
  <!--input type="text" name="courseid">-->
  <br>
  
  
  <input type="hidden" name="pwrd" value="Test_1234"/>
  <input type="hidden" name="API_KEY" value="1234"/>
  
   First name:<br>
  <input type="text" name="firstname">
  <br>
 
  
   last name:<br>
  <input type="text" name="lastname">
  <br>
  
   User Country:<br>
 <select id="country" name="country">
    <option value="AX">ALAND ISLANDS</option><option value="AL">ALBANIA</option><option value="DZ">ALGERIA </option><option value="AS">AMERICAN SAMOA</option><option value="AD">ANDORRA</option><option value="AI">ANGUILLA</option><option value="AQ">ANTARCTICA </option><option value="AG">ANTIGUA AND BARBUDA</option><option value="AR">ARGENTINA</option><option value="AM">ARMENIA</option><option value="AW">ARUBA</option><option value="AU">AUSTRALIA</option><option value="AT">AUSTRIA</option><option value="AZ">AZERBAIJAN</option><option value="BS">BAHAMAS</option><option value="BH">BAHRAIN</option><option value="BD">BANGLADESH</option><option value="BB">BARBADOS</option><option value="BE">BELGIUM</option><option value="BZ">BELIZE</option><option value="BJ">BENIN</option><option value="BM">BERMUDA</option><option value="BT">BHUTAN</option><option value="BA">BOSNIA-HERZEGOVINA</option><option value="BW">BOTSWANA</option><option value="BV">BOUVET ISLAND </option><option value="BR">BRAZIL</option><option value="IO">BRITISH INDIAN OCEAN TERRITORY </option><option value="BN">BRUNEI DARUSSALAM</option><option value="BG">BULGARIA</option><option value="BF">BURKINA FASO</option><option value="CA">CANADA</option><option value="CV">CAPE VERDE</option><option value="KY">CAYMAN ISLANDS</option><option value="CF">CENTRAL AFRICAN REPUBLIC </option><option value="CL">CHILE</option><option value="CN">CHINA</option><option value="CX">CHRISTMAS ISLAND </option><option value="CC">COCOS (KEELING) ISLANDS</option><option value="CO">COLOMBIA</option><option value="CK">COOK ISLANDS</option><option value="CR">COSTA RICA</option><option value="CY">CYPRUS</option><option value="CZ">CZECH REPUBLIC</option><option value="DK">DENMARK</option><option value="DJ">DJIBOUTI</option><option value="DM">DOMINICA</option><option value="DO">DOMINICAN REPUBLIC</option><option value="EC">ECUADOR</option><option value="EG">EGYPT</option><option value="SV">EL SALVADOR</option><option value="EE">ESTONIA</option><option value="FK">FALKLAND ISLANDS (MALVINAS)</option><option value="FO">FAROE ISLANDS</option><option value="FJ">FIJI</option><option value="FI">FINLAND</option><option value="FR">FRANCE</option><option value="GF">FRENCH GUIANA</option><option value="PF">FRENCH POLYNESIA</option><option value="TF">FRENCH SOUTHERN TERRITORIES</option><option value="GA">GABON</option><option value="GM">GAMBIA</option><option value="GE">GEORGIA</option><option value="DE">GERMANY</option><option value="GH">GHANA</option><option value="GI">GIBRALTAR</option><option value="GR">GREECE</option><option value="GL">GREENLAND</option><option value="GD">GRENADA</option><option value="GP">GUADELOUPE</option><option value="GU">GUAM</option><option value="CG">GUERNSEY</option><option value="GY">GUYANA</option><option value="HM">HEARD ISLAND AND MCDONALD ISLANDS </option><option value="VA">HOLY SEE (VATICAN CITY STATE)</option><option value="HN">HONDURAS</option><option value="HK">HONG KONG</option><option value="HU">HUNGARY</option><option value="IS">ICELAND</option><option value="IN" selected>INDIA</option><option value="ID">INDONESIA</option><option value="IE">IRELAND</option><option value="IM">ISLE OF MAN</option><option value="IL">ISRAEL</option><option value="IT">ITALY</option><option value="JM">JAMAICA</option><option value="JP">JAPAN</option><option value="JE">JERSEY</option><option value="JO">JORDAN</option><option value="KZ">KAZAKHSTAN</option><option value="KI">KIRIBATI</option><option value="KR">KOREA, REPUBLIC OF</option><option value="KW">KUWAIT</option><option value="KG">KYRGYZSTAN</option><option value="LV">LATVIA</option><option value="LS">LESOTHO</option><option value="LI">LIECHTENSTEIN</option><option value="LT">LITHUANIA</option><option value="LU">LUXEMBOURG</option><option value="MO">MACAO</option><option value="MK">MACEDONIA</option><option value="MG">MADAGASCAR</option><option value="MW">MALAWI</option><option value="MY">MALAYSIA</option><option value="MT">MALTA</option><option value="MH">MARSHALL ISLANDS</option><option value="MQ">MARTINIQUE</option><option value="MR">MAURITANIA</option><option value="MU">MAURITIUS</option><option value="YT">MAYOTTE</option><option value="MX">MEXICO</option><option value="FM">MICRONESIA, FEDERATED STATES OF</option><option value="MD">MOLDOVA, REPUBLIC OF</option><option value="MC">MONACO</option><option value="MN">MONGOLIA</option><option value="ME">MONTENEGRO</option><option value="MS">MONTSERRAT</option><option value="MA">MOROCCO</option><option value="MZ">MOZAMBIQUE</option><option value="NA">NAMIBIA</option><option value="NR">NAURU</option><option value="NP">NEPAL </option><option value="NL">NETHERLANDS</option><option value="AN">NETHERLANDS ANTILLES</option><option value="NC">NEW CALEDONIA</option><option value="NZ">NEW ZEALAND</option><option value="NI">NICARAGUA</option><option value="NE">NIGER</option><option value="NU">NIUE</option><option value="NF">NORFOLK ISLAND</option><option value="MP">NORTHERN MARIANA ISLANDS</option><option value="NO">NORWAY</option><option value="OM">OMAN</option><option value="PW">PALAU</option><option value="PS">PALESTINE</option><option value="PA">PANAMA</option><option value="PY">PARAGUAY</option><option value="PE">PERU</option><option value="PH">PHILIPPINES</option><option value="PN">PITCAIRN</option><option value="PL">POLAND</option><option value="PT">PORTUGAL</option><option value="PR">PUERTO RICO</option><option value="QA">QATAR</option><option value="RE">REUNION</option><option value="RO">ROMANIA</option><option value="RU">RUSSIAN FEDERATION</option><option value="RW">RWANDA</option><option value="SH">SAINT HELENA</option><option value="KN">SAINT KITTS AND NEVIS</option><option value="LC">SAINT LUCIA</option><option value="PM">SAINT PIERRE AND MIQUELON</option><option value="VC">SAINT VINCENT AND THE GRENADINES</option><option value="WS">SAMOA</option><option value="SM">SAN MARINO</option><option value="ST">SAO TOME AND PRINCIPE </option><option value="SA">SAUDI ARABIA</option><option value="SN">SENEGAL</option><option value="RS">SERBIA</option><option value="SC">SEYCHELLES</option><option value="SG">SINGAPORE</option><option value="SK">SLOVAKIA</option><option value="SI">SLOVENIA</option><option value="SB">SOLOMON ISLANDS</option><option value="ZA">SOUTH AFRICA</option><option value="GS">SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS</option><option value="ES">SPAIN</option><option value="SR">SURINAME</option><option value="SJ">SVALBARD AND JAN MAYEN</option><option value="SZ">SWAZILAND</option><option value="SE">SWEDEN</option><option value="CH">SWITZERLAND</option><option value="TW">TAIWAN, PROVINCE OF CHINA</option><option value="TZ">TANZANIA, UNITED REPUBLIC OF</option><option value="TH">THAILAND</option><option value="TL">TIMOR-LESTE</option><option value="TG">TOGO</option><option value="TK">TOKELAU</option><option value="TO">TONGA</option><option value="TT">TRINIDAD AND TOBAGO</option><option value="TN">TUNISIA</option><option value="TR">TURKEY</option><option value="TM">TURKMENISTAN</option><option value="TC">TURKS AND CAICOS ISLANDS</option><option value="TV">TUVALU</option><option value="UG">UGANDA</option><option value="UA">UKRAINE</option><option value="AE">UNITED ARAB EMIRATES</option><option value="GB">UNITED KINGDOM</option><option value="US">UNITED STATES</option><option value="UM">UNITED STATES MINOR OUTLYING ISLANDS</option><option value="UY">URUGUAY</option><option value="UZ">UZBEKISTAN</option><option value="VU">VANUATU</option><option value="VE">VENEZUELA</option><option value="VN">VIET NAM</option><option value="VG">VIRGIN ISLANDS, BRITISH</option><option value="VI">VIRGIN ISLANDS, U.S.</option><option value="WF">WALLIS AND FUTUNA</option><option value="EH">WESTERN SAHARA</option><option value="ZM">ZAMBIA</option><option value="ZM">ZAMBIA</option>
</select>
  <br>
  
  City<br>
  <input type="text" name="city">
  <br>
  <input type="submit" name="submit" value="Submit"/>
</form> 
</td></tr>
</table>
</body>
</html>