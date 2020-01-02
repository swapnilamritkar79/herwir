<?php
// This file is part of the Local welcome plugin
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
 * This plugin sends users a welcome message after logging in
 * and notify a moderator a new user has been added
 * it has a settings page that allow you to configure the messages
 * send.
 *
 * @package    local
 * @subpackage welcome
 * @copyright  2014 Bas Brands, basbrands.nl, bas@sonsbeekmedia.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'custom events';
$string['email_template_enabled'] = 'Enable';
$string['email_template_enabled_desc'] = 'Description';
$string['event_list'] = 'Select an Event';
$string['event_list_desc'] = 'Select an Event';
$string['message_user_enabled_desc'] = 'This tickbox enables the sending of welcome messages to new users';
$string['template_title'] = 'Template';
$string['template_box_desc'] = '';

$string['last_attempt_failed_email_text'] = 'Hi {$a->recepient_firstname},<br><br>{$a->quiztaker_firstname} {$a->quiztaker_lastname} who reports to you, has been failed in final attempt of quiz \'{$a->quiz_name}\' in the course \'{$a->course_name}\'<br><br>Please click {$a->approval_link} to approve extra attempts for {$a->quiztaker_firstname} for this quiz.<br><br>Thanks,<br>Team HDFC Aspire';

$string['approve_extra_attempt_email_message'] = '<br>Hi {$a->recepient_firstname},<br><br>{$a->supervisor_firstname} {$a->supervisor_lastname} who is reporting head of {$a->user_firstname} {$a->user_lastname} has approved extra attempts of quiz \'{$a->quiz_name}\' in the course \'{$a->course_name}\' for {$a->user_firstname}.<br><br>Please click {$a->override_link} and follow below steps to approve extra attempts for user<br><br>Steps to follow:<br><ul><li>Click on button \'Add user override\'</li><li>Select the user from the list for whom you want to add extra attempts. You can search the user if the list is too long.<li>Select the number of Total attempts you want to allow for this user by selecting it from \'Attempts allowed\' dropdown.</ul><br><br>Thanks,<br>Team HDFC Aspire';

$string['extra_attempt_added_email_student'] = 'Hi {$a->user_firstname},<br><br>Admin has added extra attempts for you for quiz \'{$a->quiz_name}\' in the course \'{$a->course_name}\'.<br><br>Click {$a->quiz_link} to take the quiz.<br><br>Thanks,<br>Team HDFC Aspire';

$string['extra_attempt_added_email_supervisor'] = 'Hi {$a->supervisor_firstname},<br><br>As per your request, admin has added extra attempts for \'{$a->user_firstname} {$a->user_lastname}\' for quiz \'{$a->quiz_name}\' in the course \'{$a->course_name}\'.<br><br>Thanks,<br>Team HDFC Aspire';





