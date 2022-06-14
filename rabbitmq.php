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
$PAGE->set_url(new moodle_url('/local/backup/rabbitmq.php'));
$PAGE->set_context(\context_system::instance());

$userid = $USER->id;
$courseid = optional_param('courseid', null, PARAM_INT);
$categoryid = optional_param('categoryid', null, PARAM_INT);

if(!is_null($courseid) && is_null($categoryid)){
    shell_exec("php task.php $userid $courseid");
    $url = new moodle_url('/local/backup/manage.php');
} else if(!is_null($categoryid)) {
    shell_exec("php task_category.php $userid $categoryid");
    $url = new moodle_url('/local/backup/managecategory.php');
} 
redirect($url);
