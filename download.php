<?php

$courseid = filter_input(INPUT_GET, 'courseid', FILTER_SANITIZE_URL);

$file = "image/course-id-$courseid.tar";

header("Content-Description: File Transfer");
header("Content-Type: application/x-tar");
// header("Content-Type: application/octet-stream");
header("Content-Length: " . filesize( $file ));
header('Content-Disposition: attachment; filename="'.basename($file).'"');
readfile($file);