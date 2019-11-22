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

/**
 * Iomad - admin settings
 *
 * @package    Iomad
 * @copyright  2011 onwards E-Learn Design Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Basic navigation settings
require($CFG->dirroot . '/local/iomad/lib/basicsettings.php');

$url = new moodle_url( '/local/report_user_logins/index.php' );
$ADMIN->add('IomadReports',
            new admin_externalpage('repuserlogings',
                                   get_string('repuserlogins',
                                   'local_report_user_logins'),
                                   $url,
                                   'local/report_user_logins:view'));
