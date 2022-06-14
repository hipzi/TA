<?php

$courseid = filter_input(INPUT_GET, 'courseid', FILTER_SANITIZE_URL);
$categoryid = filter_input(INPUT_GET, 'categoryid', FILTER_SANITIZE_URL);
$userid = filter_input(INPUT_GET, 'userid', FILTER_SANITIZE_URL);

if(!is_null($courseid) && is_null($categoryid)){
    $file = "image/course-id-$courseid.tar";
} else if(!is_null($categoryid)) {
    $file = "image/category-$categoryid-$userid.tar";
} 

header("Content-Description: File Transfer");
header("Content-Type: application/x-tar");
header("Content-Length: " . filesize( $file ));
header('Content-Disposition: attachment; filename="'.basename($file).'"');
readfile($file);