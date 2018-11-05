<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

// Define a destination
$targetFolder = 'data/upload'; //项目的存储上传文件的目录，是相对路径

//$verifyToken = md5('unique_salt' . $_POST['timestamp']);

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
	$arr=explode('.',$_FILES['Filedata']['name']);
	$new=time().rand(1,20000).'.'.$arr[1];
	$targetFile = rtrim($targetPath,'/') . '/' .$new ;
	//print_r($_FILES['Filedata']);
	
	// Validate the file type
	$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	
	if (in_array($fileParts['extension'],$fileTypes)) {
		move_uploaded_file($tempFile,$targetFile);
		echo $targetFolder.'/'.$new;//返回的是相等路径
	} else {
		echo 'Invalid file type.';
	}
}
?>