#!/bin/sh

# idCourse=$1
idCourse=11
filenameCourse="backupcourse_"$idCourse".mbz"
folderCourse="storage"
pathtofolderCourse="/var/www/moodle/local/backup/docker/"$folderCourse
imageName="course-id-"$idCourse
folderName="image"

# # Backup Course
# cd /var/www/moodle

# echo "List Course"
# php moosh/moosh.php -n course-list
# echo "Backup Course"
# php moosh/moosh.php -n course-backup -f "$pathtofolderCourse"/"$filenameCourse" --fullbackup $idCourse
# echo "Finish"

# Build Docker Image

cd /var/www/moodle/local/backup/docker

echo "Build Image"
docker build -t $imageName . --build-arg filenamecourse="$filenameCourse"
# echo "Save Image"
# docker save -o "$imageName".tar $imageName
# mv "$imageName".tar "../$folderName"/
# echo "Remove Image"
# docker rmi $imageName
# echo "Finish"