<?php
// This file is part of Moodle Course Rollover Plugin
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
 * @package     local_backup
 * @author      Zahratul Millah
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
$PAGE->set_url(new moodle_url('/local/backup/manage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('List Course');

echo $OUTPUT->header();

$userid = $USER->id;

$roleassignments = $DB->get_records('role_assignments', ['userid' => $userid]);
$role = key($roleassignments);
$rolename = $roleassignments[$role]->roleid;

if ($userid == 2 ) {
    $sql = "select c.id as id, c.fullname as fullname, c.shortname as shortname, ca.name as categoryname from mdl_course c join mdl_course_categories ca on ca.id = c.category";
    $records = $DB->get_records_sql($sql);
} else if ($rolename <= 4 ) {
    $sql = "select c.id as id, c.fullname as fullname, c.shortname as shortname, ca.name as categoryname from mdl_course c join mdl_course_categories ca on ca.id = c.category join mdl_enrol e on e.courseid = c.id join mdl_user_enrolments ue on ue.enrolid = e.id join mdl_role_assignments ra on ra.userid = ue.userid join mdl_role r on r.id = ra.roleid where ue.userid = :userid and (r.shortname='manager' or r.shortname='coursecreator' or r.shortname='editingteacher' or r.shortname='teacher')";
    $params = [
        'userid' => $userid,
    ];
    $records = $DB->get_records_sql($sql, $params);
} else {
    $url = new moodle_url('/index.php');
    redirect($url);
}

$templatecontext = (object)[
    'courses' => array_values($records),
    'detailurl' => new moodle_url('/local/backup/detail.php'),
];

echo $OUTPUT->render_from_template('local_backup/manage', $templatecontext);

echo $OUTPUT->footer();