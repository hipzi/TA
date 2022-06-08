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
$PAGE->set_url(new moodle_url('/local/backup/detail.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Course Detail');

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

echo $OUTPUT->header();

$userid = $USER->id;

$courseid = optional_param('courseid', null, PARAM_INT);
$coursename = "Course $courseid";
$queuename = "message_$userid";

$filename = "image/course-id-$courseid.tar";
if (file_exists($filename)) {
    $fileexists = date ("d F Y H:i:s", filemtime($filename));
    $fileexistsflag = true;
} else {
    $fileexists = "File not found";
    $fileexistsflag = false;
}

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
    'courseid' => $courseid,
    'coursename' => $coursename,
    'fileexists' => $fileexists,
    'fileexistsflag' => $fileexistsflag,
    'status' => $status,
    'downloadurl' => new moodle_url('/local/backup/download.php'),
    'rabbitmqurl' => new moodle_url('/local/backup/rabbitmq.php'),
    'rerabbitmqurl' => new moodle_url('/local/backup/rerabbitmq.php'),
    'result' => $result,
];

echo $OUTPUT->render_from_template('local_backup/download', $templatecontext);
echo $OUTPUT->footer();