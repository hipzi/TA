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
    $courseimage = "image/course-id-$courseid.tar";
    $coursefile = "docker/storage/backupcourse_$courseid.mbz";

    unlink($courseimage);
    unlink($coursefile);

    shell_exec("php task.php $userid $courseid");

    $url = new moodle_url('/local/backup/manage.php');
} else if(!is_null($categoryid)) {
    $categoryimage = "image/category-$categoryid-$userid.tar";
    $filecoursecategory = "docker/category/category_".$categoryid."_".$userid;

    unlink($categoryimage);
    unlink($filecoursecategory);

    $folderName = "docker/storage_".$categoryid."_".$userid;
    deleteDirectory($folderName);

    function deleteDirectory($dirname) {
        if (is_dir($dirname))
            $dir = opendir($dirname);
        if (!$dir)
            return false;

        while($file = readdir($dir)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file))
                        unlink($dirname."/".$file);
                else
                deleteDirectory($dirname.'/'.$file);
            }
        }

        closedir($dir);
        rmdir($dirname);
        return true;
    }

    shell_exec("php task_category.php $userid $categoryid");
    $url = new moodle_url('/local/backup/managecategory.php');
} 
redirect($url);
