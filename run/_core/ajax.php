<?php
/*
 * ###################################################
 * #                                                 #
 * # rkWFS                                           #
 * # rkcsd's Web-File-System                         #
 * #                                                 #
 * # (C) René Knipschild Custom Software Development #
 * #                                                 #
 * #           Developed by rkCSD <email@rkcsd.com>  #
 * #               www.customsoftwaredevelopment.de  #
 * #                                                 #
 * ###################################################
 *
 * File: ajax.php
 * Version: 1.0.0
 * Last modified: 2015/05/19 17:45 CEST
 * Author: Alexander Eifler
 *
 * ===Notes===========================================
 * There are currently no notes.
 * ===================================================
 */

$ajaxReq = "";
if(isset($_POST['ajaxReq']))
	$ajaxReq = $_POST['ajaxReq'];
if(isset($_GET['ajaxReq']))
	$ajaxReq = $_GET['ajaxReq'];

function fix($string) {
	return preg_replace("/[^A-Za-z0-9\.\-\_\s]/", "", $string);
}

switch($ajaxReq){
	case "delete":
		function rrmdir($dir) {
			if (is_dir($dir)) {
				$objects = scandir($dir);
				foreach ($objects as $object) {
					if ($object != "." && $object != "..") {
						if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
					}
				}
				reset($objects);
				rmdir($dir);
			}
		}

		$filename = pathSecurity($_POST["filename"]);
		$path = UPLOAD_FOLDER."/".PROJECT_NAME."/".$filename;
		echo $path;

		if(!file_exists($path))
			die("FATAL ERROR: The chosen file does not exist.");

		if(is_dir($path)){
			rrmdir($path);
		}else{
			unlink($path) || die("FATAL ERROR: The file couldn't be removed.");
		}
	break;

	case "enter":
		header("Content-type: application/json");

		$path = pathSecurity($_GET['path']);
		if(strlen($path)>=2 && substr($path,-2)=="/!"){
			$arr = explode("/",substr($path,0,-2));
			unset($arr[count($arr)-1]);
			$path = implode("/",$arr);
		}

		$newDir = UPLOAD_FOLDER."/".PROJECT_NAME."/".$path;

		if(!file_exists($newDir))
			$path = "/";

		if(substr($path,0,1)!="/")
			$path = "/".$path;

		$path = str_replace("//","/",$path);

		echo json_encode(array(
			"path" => $path==""?"/":$path
		));
	break;

	case "files":
		header("Content-type: application/json");

		$sortName = "name";
		$sortMode = "asc";

		if(isset($_GET['sortName'])) {
			$sortName = $_GET['sortName'];
		}

		if(isset($_GET['sortMode'])) {
			$sortMode = $_GET['sortMode'];
		}

		$files = array();
		$fileCount = 0;
		$folderCount = 0;
		$dir = UPLOAD_FOLDER."/".PROJECT_NAME."/".pathSecurity($_GET['actualPath']);
		$dirHandle = opendir($dir);

		$sortHelper = array();
		$sortDirHelper = array();
		while($file = readdir($dirHandle)) {
			if(substr($file,0,1)=="." && substr($file,1,1)!=".")
				continue;

			if($file==".." && str_replace(array("/","\\"),"",pathSecurity($_GET['actualPath']))=="")
				continue;

			$extension = "";
			if(is_dir($dir."/".$file)) {
				$extension = "_folder";
				if($file=="..")
					$extension = "_folderBack";
			}else{
				$extension = pathinfo($dir."/".$file, PATHINFO_EXTENSION);
				$extension = strtolower($extension);
				$extension = strlen($extension)>5?substr($extension,0,5):$extension;
			}

			$preview = array("width"=>0,"height"=>0);
			if($extension == "jpg" || $extension == "jpeg" || $extension == "gif" || $extension == "png"){
				$imgData = getimagesize($dir."/".$file);
				$preview = array("width"=>$imgData[0],"height"=>$imgData[1]);
			}

			$link = str_replace("//","/",pathSecurity($_GET['actualPath'])."/".($file==".."?"!":$file));

			$files[] = array(
				"filename" => ($file==".."?"!":$file),
				"link" => $link,
				"size" => is_dir($dir."/".$file)?"0":filesize($dir."/".$file),
				"modification" => filemtime($dir."/".$file),
				"ext" => $extension,
				"isDir" => is_dir($dir."/".$file),
				"preview" => $preview
			);

			if(is_dir($dir."/".$file)) {
				if($file != "..") {
					$folderCount++;
				}
			}else{
				$fileCount++;
			}

			$sortDirHelper[] = $files[count($files)-1]['isDir'];
			if($sortName=="name"){
				$sortHelper[] = strtolower($files[count($files)-1]['filename']);
			}elseif($sortName=="type"){
				$sortHelper[] = strtolower($files[count($files)-1]['ext']);
			}elseif($sortName=="size"){
				$sortHelper[] = $files[count($files)-1]['size'];
			}elseif($sortName=="date"){
				$sortHelper[] = $files[count($files)-1]['modification'];
			}
		}
		closedir($dirHandle);

		if($sortMode=="asc")
			array_multisort($sortDirHelper, SORT_DESC, $sortHelper, SORT_ASC, $files);
		if($sortMode=="desc")
			array_multisort($sortDirHelper, SORT_DESC, $sortHelper, SORT_DESC, $files);

		echo json_encode(array(
			"files" => $files,
			"sortName" => $sortName,
			"sortMode" => $sortMode,
			"fileCount" => $fileCount,
			"folderCount" => $folderCount,
			"count" => count($files)
		));
	break;

	case "newFolder":
		if(isset($_POST['newFolderName']) && $_POST['newFolderName']!="") {
			$newFolder = fix($_POST['newFolderName']);
			$actPath = fix($_POST['actualPath']);

			if (!file_exists(UPLOAD_FOLDER."/".PROJECT_NAME."/".$actPath."/".$newFolder)) {
				mkdir(UPLOAD_FOLDER."/".PROJECT_NAME."/".$actPath."/".$newFolder);
			}
		}
	break;
}