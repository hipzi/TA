#!/bin/sh

categoryId=1
folderCourse="storage"

echo "List Course"
php moosh/moosh.php -n course-list
echo "Restore Course"
for filename in $folderCourse/*.mbz; do
    archivename=${filename##*/}
    archiveid=$(echo $archivename | grep -o '[0-9]\+')
    courseFullName="Course "$archiveid
    courseShortName="Course"$archiveid
    courseDescription="Berikut merupakan course hasil archive "$archiveid
    courseid=$(php moosh/moosh.php -n course-create --category $categoryId --fullname "$courseFullName" --description "$courseDescription" --visible=y $courseShortName)
    php moosh/moosh.php -n course-restore --overwrite -e "$folderCourse"/"${filename##*/}" $courseid
done
php moosh/moosh.php -n course-restore --overwrite -e "$folderCourse"/"$filenameCourse" $courseid

echo "Finish"



