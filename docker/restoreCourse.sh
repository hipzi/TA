#!/bin/sh

categoryId=1
folderCourse="storage"
courseFullName="Course"
courseShortName="Course"
courseDescription="Berikut merupakan course hasil archive"
filenameCourse=filenamecourse

courseid=$(php moosh/moosh.php -n course-create --category $categoryId --fullname "$courseFullName" --description "$courseDescription" --visible=y $courseShortName)
echo "List Course"
php moosh/moosh.php -n course-list
echo "Restore Course"
php moosh/moosh.php -n course-restore --overwrite -e "$folderCourse"/"$filenameCourse" $courseid

echo "Finish"



