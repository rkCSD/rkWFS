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
 * File: index.php
 * Version: 1.0.0
 * Last modified: 2015/10/25 17:45 CET
 * Author: Alexander Eifler
 *
 * ===Notes===========================================
 * There are currently no notes.
 * ===================================================
 */

define("WDU", WEBSITE_DEFAULT_URI."/");

function pathSecurity($string){
	return str_replace(array("../","/..","..\\","\\.."),"",$string);
}

$path = $_GET['root'];
$project = "";
$mode = "";
if(strpos($path,'/')===false){
	$project = $path;
	$mode = "";
}else{
	$project = substr($path,0,strpos($path,'/'));
	$mode = substr($path,strpos($path,'/')+1);
}

if(array_key_exists($project,$WFS_PROJECTS)){
	define("PROJECT_NAME", $project);
	define("PROJECT_FULLNAME", $WFS_PROJECTS[PROJECT_NAME]);

	if(!file_exists(UPLOAD_FOLDER."/".PROJECT_NAME))
		mkdir(UPLOAD_FOLDER."/".PROJECT_NAME);

	switch($mode){
		case "ajax":
			require_once("./run/_core/ajax.php");
			break;
		case "upload":
			require_once("./run/classes/upload.php");
			break;
		case "zip":
			require_once("./run/classes/zip.php");
			break;
		case "download":
		case "download.php": // Deprecated option, from rkWFS < 3.0
			require_once("./run/classes/download.php");
			break;
		default:
			$mode = "";
			require_once("./run/main.php");
	}
}else{
	// ERROR
}