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
$PAGE->set_url(new moodle_url('/local/backup/detailcategory.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Category Detail');

echo $OUTPUT->header();

$userid = $USER->id;

$categoryid = optional_param('categoryid', null, PARAM_INT);
$categoryname = "Category $categoryid";
$queuename = "category_" . $userid . "_" . "$categoryid";
$directory = "docker/category/";
$namefilecoursecategory = "category_".$categoryid."_".$userid;

$filename = "image/category-$categoryid-$userid.tar";

if (file_exists($filename)) {
    $fileexists = date ("d F Y H:i:s", filemtime($filename));
    $fileexistsflag = true;
} else {
    $fileexists = "File not found";
    $fileexistsflag = false;
}

if ($userid == 2 ) {
    $sql = "select c.id as id from mdl_course c where c.category = :categoryid";
    $params = [
        'categoryid' => $categoryid,
    ];
    $records = $DB->get_records_sql($sql, $params);
} else if ($rolename <= 4 ) {
    $sql = "select c.id as id from mdl_course c join mdl_enrol e on e.courseid = c.id join mdl_user_enrolments ue on ue.enrolid = e.id join mdl_role_assignments ra on ra.userid = ue.userid join mdl_role r on r.id = ra.roleid where ue.userid = :userid and (r.shortname='manager' or r.shortname='coursecreator' or r.shortname='editingteacher' or r.shortname='teacher') and c.category = :categoryid";
    $params = [
        'userid' => $userid,
        'categoryid' => $categoryid,
    ];
    $records = $DB->get_records_sql($sql, $params);
} 

$allKeysOfRecords = array_keys($records);
$filecoursecategory = fopen($directory.$namefilecoursecategory.".txt", "w");

$outputfilecoursecategory = "";
foreach($allKeysOfRecords as &$tempKey) {
    $outputfilecoursecategory .= $records[$tempKey]->id."\n";
}
fwrite($filecoursecategory, $outputfilecoursecategory);
fclose($filecoursecategory); 

function buildQueue($queue){
    $buildqueue = shell_exec("rabbitmqadmin get queue=build_$queue | awk -F '|' 'NR==4{print $5}'");
    return $buildqueue;
}

function finishQueue($queue){
    $finishqueue = shell_exec("rabbitmqadmin get queue=finish_$queue | awk -F '|' 'NR==4{print $5}'");
    return $finishqueue;
}

$result = "-";

if(!is_null(buildQueue($queuename)) && is_null(finishQueue($queuename))){
    $result = buildQueue($queuename);
} else if(!is_null(finishQueue($queuename))) {
    $result = finishQueue($queuename);
} 

$templatecontext = (object)[
    'userid' => $userid,
    'categoryid' => $categoryid,
    'categoryname' => $categoryname,
    'fileexists' => $fileexists,
    'fileexistsflag' => $fileexistsflag,
    'status' => $status,
    'downloadurl' => new moodle_url('/local/backup/download.php'),
    'rabbitmqurl' => new moodle_url('/local/backup/rabbitmq.php'),
    'rerabbitmqurl' => new moodle_url('/local/backup/rerabbitmq.php'),
    'result' => $result,
];

echo $OUTPUT->render_from_template('local_backup/detailcategory', $templatecontext);
echo $OUTPUT->footer();