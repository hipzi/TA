#!/bin/bash

userid=$1
categoryid=$2
folderCourse="storage_"$categoryid"_"$userid
absolutePath="/var/www/moodle/local/backup/docker/"
pathtofolderCourse=$absolutePath$folderCourse
imageName="category-"$categoryid"-"$userid
folderName="image"
namefilecoursecategory="category_"$categoryid"_"$userid".txt";

mkdir -p $folderCourse

# Backup Course
cd /var/www/moodle

echo "List Course"
php moosh/moosh.php -n course-list
echo "Backup Course"
while IFS=$' \t\n' read -r idcourse
do
    ((lines++))
    filenameCourse="backupcourse_"$idcourse".mbz"
    php moosh/moosh.php -n course-backup -f "$pathtofolderCourse"/"$filenameCourse" --fullbackup $idcourse
done < $absolutePath$name"category/"$namefilecoursecategory
echo "Finish"

# Build Docker Image
cd /var/www/moodle/local/backup/docker

echo "Build Image"
docker build -t $imageName -f CategoryDockerfile . --build-arg foldercourse="$folderCourse"
echo "Save Image"
docker save -o "$imageName".tar $imageName
mv "$imageName".tar "../$folderName"/
echo "Remove Image"
docker rmi $imageName
echo "Finish"