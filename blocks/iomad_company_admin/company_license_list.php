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

require_once('lib.php');
require_once($CFG->dirroot.'/user/profile/definelib.php');

$delete       = optional_param('delete', 0, PARAM_INT);
$confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   // Md5 confirmation hash.
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', $CFG->iomad_max_list_licenses, PARAM_INT);        // How many per page.
$companyid    = optional_param('companyid', 0, PARAM_INTEGER);
$save         = optional_param('save', 0, PARAM_INTEGER);
$showexpired  = optional_param('showexpired', 0, PARAM_INTEGER);

$context = context_system::instance();

require_login(null, false); // Adds to $PAGE, creates $OUTPUT.

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('company_license_list_title', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_license_list.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('myhome') . " - $linktext");
$PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'));
$PAGE->navbar->add($linktext, $linkurl);

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);

$baseurl = new moodle_url(basename(__FILE__), array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage, 'showexpired' => $showexpired));
$returnurl = $baseurl;

// Get the appropriate company department.
$companydepartment = company::get_company_parentnode($companyid);
if (iomad::has_capability('block/iomad_company_admin:edit_licenses', context_system::instance())) {
    $departmentid = $companydepartment->id;
} else {
    $userlevel = $company->get_userlevel($USER);
    $departmentid = $userlevel->id;
}

if ($delete and confirm_sesskey()) {              // Delete a selected company, after confirmation.

    if ($company->is_child_license($delete)) {
        iomad::require_capability('block/iomad_company_admin:edit_my_licenses', $context);
    } else {
        iomad::require_capability('block/iomad_company_admin:edit_licenses', $context);
    }

    $license = $DB->get_record('companylicense', array('id' => $delete), '*', MUST_EXIST);
    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        // TODO SET THE LICENSE NAME.
        $name = $license->name;
        if ($license->used > 0) {
            notice(get_string('licenseinuse', 'block_iomad_company_admin'), $linkurl);
        } else {
            echo $OUTPUT->heading(get_string('deletelicense', 'block_iomad_company_admin'), 2, 'headingblock header');
            $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());

            echo $OUTPUT->confirm(get_string('companydeletelicensecheckfull', 'block_iomad_company_admin', "'$name'"),
                                  new moodle_url('company_license_list.php', $optionsyes), 'company_license_list.php');
            echo $OUTPUT->footer();
            die;
        }
    } else if (data_submitted()) {
        // Actually delete license.
        if (!$DB->delete_records('companylicense', array('id' => $delete))) {
            print_error('error while deleting license');
        }

        // Create an event to deal with an parent license allocations.
        $eventother = array('licenseid' => $license->id,
                            'parentid' => $license->parentid);

        $event = \block_iomad_company_admin\event\company_license_deleted::create(array('context' => context_system::instance(),
                                                                                        'userid' => $USER->id,
                                                                                        'objectid' => $license->parentid,
                                                                                        'other' => $eventother));
        $event->trigger();

        redirect($returnurl, get_string('licensedeletedok', 'block_iomad_company_admin'), null, \core\output\notification::NOTIFY_SUCCESS);
    }
}

echo $OUTPUT->header();

// Check we can actually do anything on this page.
iomad::require_capability('block/iomad_company_admin:view_licenses', $context);

$company = new company($companyid);
echo "<h3>".$company->get_name()."</h3>";

flush();

$stredit   = get_string('edit');
$strdelete = get_string('delete');
$strallocate = get_string('licenseallocate', 'block_iomad_company_admin');
$strsplit = get_string('split', 'block_iomad_company_admin');
$straddlicense = get_string('licenseaddnew', 'block_iomad_company_admin');
$strlicensename = get_string('licensename', 'block_iomad_company_admin');
$strlicensereference = get_string('licensereference', 'block_iomad_company_admin');
$strlicensetype = get_string('licensetype', 'block_iomad_company_admin');
$strlicenseprogram = get_string('licenseprogram', 'block_iomad_company_admin');
$strlicenseinstant = get_string('licenseinstant', 'block_iomad_company_admin');
$strcoursesname = get_string('allocatedcourses', 'block_iomad_company_admin');
$strlicenseshelflife = get_string('licenseexpires', 'block_iomad_company_admin');
$strlicenseduration = get_string('licenseduration', 'block_iomad_company_admin');
$strlicenseallocated = get_string('licenseallocated', 'block_iomad_company_admin');
$strlicenseremaining = get_string('licenseremaining', 'block_iomad_company_admin');
$licensetypes = array(get_string('standard', 'block_iomad_company_admin'),
                      get_string('reusable', 'block_iomad_company_admin'),
                      get_string('educator', 'block_iomad_company_admin'),
                      get_string('educatorreusable', 'block_iomad_company_admin'));

$table = new html_table();
$table->head = array ($strlicensename,
                      $strlicensereference,
                      $strlicensetype,
                      $strlicenseprogram,
                      $strlicenseinstant,
                      $strcoursesname,
                      $strlicenseshelflife,
                      $strlicenseduration,
                      $strlicenseallocated,
                      $strlicenseremaining,
                      "",
                      "");
$table->align = array ("left", "left", "left", "left", "left", "left", "left", "center", "center", "center", "center");
$table->width = "95%";

if ($departmentid == $companydepartment->id) {

    // Do we have any child companies?
    if ($childcompanies = $company->get_child_companies_recursive()) {
        $showcompanies = true;
        $gotchildren = true;
        array_unshift($table->head, get_string('company', 'block_iomad_company_admin'));
        $childsql = "OR companyid IN (" . join(',', array_keys($childcompanies)) . ")";
    } else {
        $showcompanies = false;
        $gotchildren = false;
        $childsql = "";
    }

    // Are we showing the expired licenses?
    if (empty($showexpired)) {
        $expiredsql = " AND expirydate > :time ";
    } else {
        $expiredsql = "";
    }

    // Get the licenses.
    $licenses = $DB->get_records_sql("SELECT * FROM {companylicense}
                                      WHERE companyid = :companyid
                                      $childsql
                                      $expiredsql
                                      ORDER BY expirydate DESC",
                                      array('companyid' => $companyid, 'time' => time()), $page * $perpage, $perpage);

    $objectcount = $DB->count_records_sql("SELECT count(id) FROM {companylicense}
                                        WHERE companyid = :companyid
                                        $childsql
                                        $expiredsql",
                                        array('companyid' => $companyid, 'time' => time()));

    // Cycle through the results.
    foreach ($licenses as $license) {
        // Set up the edit buttons.
        $deletebutton = "";
        $editbutton = "";
        $allocatebutton = "";

        if (iomad::has_capability('block/iomad_company_admin:edit_licenses', $context) ||
            (iomad::has_capability('block/iomad_company_admin:edit_my_licenses', $context) && !empty($license->parentid))) {
                // Is this above the user's company allocation?
                if (iomad::has_capability('block/iomad_company_admin:edit_licenses', $context) ||
                    $DB->get_record_sql("SELECT id FROM {company_users}
                                         WHERE userid = :userid
                                         AND companyid = (
                                            SELECT companyid FROM {companylicense}
                                            WHERE id = :parentid)",
                                         array('userid' => $USER->id,
                                               'parentid' => $license->parentid))) {
                $deletebutton = "<a class='btn btn-primary' href='".
                                 new moodle_url('company_license_list.php', array('delete' => $license->id,
                                                                                  'sesskey' => sesskey())) ."'>$strdelete</a>";
                $editbutton = "<a class='btn btn-primary' href='" . new moodle_url('company_license_edit_form.php',
                               array("licenseid" => $license->id, 'departmentid' => $departmentid)) . "'>$stredit</a>";
            }
        }

        if (iomad::has_capability('block/iomad_company_admin:allocate_licenses', $context)) {
            $allocatebutton = "<a class='btn btn-primary' href='".
                                 new moodle_url('company_license_users_form.php', array('licenseid' => $license->id)) ."'>$strallocate</a>";
        } else {
            $allocatebutton = "";
        }
        // does the company the license is allocated to have any kids?
        $licensecompany = new company($license->companyid);
        if ($childcompanies = $licensecompany->get_child_companies_recursive()) {
            $gotchildren = true;
        } else {
            $gotchildren = false;
        }

        // Set up the edit buttons.
        if ((iomad::has_capability('block/iomad_company_admin:edit_licenses', $context) ||
            iomad::has_capability('block/iomad_company_admin:edit_my_licenses', $context) ||
            iomad::has_capability('block/iomad_company_admin:split_my_licenses', $context)) &&
            $license->used < $license->allocation &&
            $gotchildren) {
            $splitbutton = "<a class='btn btn-primary' href='" . new moodle_url('company_license_edit_form.php',
                           array("parentid" => $license->id)) . "'>$strsplit</a>";
        } else {
            $splitbutton = "";
        }
        $licensecourses = $DB->get_records('companylicense_courses', array('licenseid' => $license->id));
        $coursestring = "";
        if (is_siteadmin()) {
            $issiteadmin = true;
        } else {
            $issiteadmin = false;
        }
        $coursestring = "";
        $first = true;
        if (count($licensecourses) > 5) {
            $coursestring = "<details><summary>" . get_string('view') . "</summary>";
        }
        foreach ($licensecourses as $licensecourse) {
            $coursename = $DB->get_record('course', array('id' => $licensecourse->courseid));
            if ($first) {
                if ($issiteadmin) {
                    $coursestring .= "<a href='".new moodle_url('/course/view.php',
                                       array('id' => $licensecourse->courseid))."'>".format_string($coursename->fullname, true, 1)."</a>";
                    $first = false;
                } else {
                    $coursestring .= format_string($coursename->fullname, true, 1);
                }
            } else {
                if ($issiteadmin) {
                    $coursestring .= ",</br><a href='".new moodle_url('/course/view.php',
                                   array('id' => $licensecourse->courseid))."'>".format_string($coursename->fullname, true, 1)."</a>";
                } else {
                    $coursestring .= ",</br>". format_string($coursename->fullname, true, 1);
                }
            }
        }
        if (count($licensecourses) > 5) {
            $coursestring .= "</details>";
        }

        // Deal with allocation numbers if a program.
        if (!empty($license->program)) {
            $programstring = get_string('yes');
            $allocation = $license->allocation / count($licensecourses);
            $used = $license->used / count($licensecourses);
        } else {
            $programstring = get_string('no');
            $allocation = $license->allocation;
            $used = $license->used;
        }

        // Deal with allocation numbers if a program.
        if (!empty($license->instant)) {
            $instantstring = get_string('yes');
        } else {
            $instantstring = get_string('no');
        }

        // Deal with valid length if a subscription.
        if ($license->type == 1) {
            $validlength = "-";
        } else {
            $validlength = $license->validlength;
        }

        // Create the table data.
        $dataarray = array ($license->name,
                           $license->reference,
                           $licensetypes[$license->type],
                           $programstring,
                           $instantstring,
                           $coursestring,
                           date($CFG->iomad_date_format, $license->expirydate),
                           $validlength,
                           $allocation,
                           $used,
                           $editbutton . ' ' .
                           $splitbutton . ' ' .
                           $deletebutton . ' ' .
                           $allocatebutton);
        // Add in the company name if we have any.
        if ($showcompanies) {
            $liccompany = new company($license->companyid);
            array_unshift($dataarray, $liccompany->get_name());
        }
        $table->data[] = $dataarray;
    }
} else if ($licenses = company::get_recursive_departments_licenses($companydepartment->id)) {
    $objectcount = count($licenses);
    foreach ($licenses as $licenseid) {

        // Get the license record.
        $license = $DB->get_record('companylicense', array('id' => $licenseid->licenseid));

        // Set up the edit buttons.
        if (iomad::has_capability('block/iomad_company_admin:edit_licenses', $context)) {
            $deletebutton = "<a href=\"company_license_list.php?delete=$license->id&amp;sesskey=".sesskey()."\">$strdelete</a>";
            $editbutton = "<a href='" . new moodle_url('company_license_edit_form.php',
                                                        array("licenseid" => $license->id, 'departmentid' => $departmentid)) .
                                                        "'>$stredit</a>";
        } else {
            $deletebutton = "";
            $editbutton = "";
        }

        // Deal with allocation numbers if a program.
        if (!empty($license->program)) {
            $programstring = get_string('yes');
            $allocation = $license->allocation / count($licensecourses);
            $used = $license->used / count($licensecourses);
        } else {
            $programstring = get_string('no');
            $allocation = $license->allocation;
            $used = $license->used;
        }

        // Deal with allocation numbers if a program.
        if (!empty($license->instant)) {
            $instantstring = get_string('yes');
        } else {
            $instantstring = get_string('no');
        }

        // Deal with valid length if a subscription.
        if ($license->type == 1) {
            $validlength = "-";
        } else {
            $validlength = $license->validlength;
        }

        $table->data[] = array ($license->name,
                                $license->reference,
                                $licensetypes[$license->type],
                                $programstring,
                                $instantstring,
                                $coursestring,
                                date($CFG->iomad_date_format, $license->expirydate),
                                $license->validlength,
                                $license->allocation,
                                $license->used,
                                $editbutton,
                                $deletebutton);
    }
}


echo '<div class="buttons">';
if ($showexpired) {
    $showexpiredstring = get_string('hideexpiredlicenses', 'block_iomad_company_admin');
} else {
    $showexpiredstring = get_string('showexpiredlicenses', 'block_iomad_company_admin');
}
echo $OUTPUT->single_button(new moodle_url('company_license_list.php', array('showexpired' => !$showexpired)),
                                            $showexpiredstring);
if (iomad::has_capability('block/iomad_company_admin:edit_licenses', $context)) {
    echo $OUTPUT->single_button(new moodle_url('company_license_edit_form.php'),
                                                get_string('licenseaddnew', 'block_iomad_company_admin'), 'get');
}
echo '</div>';

// Display the list of licenses.
if (!empty($table)) {
    echo $OUTPUT->paging_bar($objectcount, $page, $perpage, $baseurl);
    echo html_writer::table($table);
    echo $OUTPUT->paging_bar($objectcount, $page, $perpage, $baseurl);
}

echo $OUTPUT->footer();
