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
 * File: download.php
 * Version: 1.0.0
 * Last modified: 2015/05/19 17:45 CEST
 * Author: Alexander Eifler
 *
 * ===Notes===========================================
 * There are currently no notes.
 * ===================================================
 */

$filename = "";
if($mode=="download.php"){ // Deprecated option, from rkWFS < 3.0
	$filename = urldecode($_SERVER["QUERY_STRING"]);
	$arr = explode("&",$filename);
	$filename = $arr[count($arr)-1];
}else{
	$filename = urldecode($_GET['dir']);
}

if($filename=="..") $filename = "";
$filename = UPLOAD_FOLDER."/".PROJECT_NAME."/".str_replace(array("\\", "/"), "/", $filename);
$filename = str_replace(array("../","/.."),"",$filename);
$pureFilename = substr($filename,strrpos($filename,"/")+1);

if(strpos($filename,".htaccess") || !file_exists($filename))
	die("FATAL ERROR: The file you are looking for does not exist.");

$size = filesize($filename);
header("Content-Type: application/octet-stream");
header("Content-Transfer-Encoding: Binary");
header("Content-length: $size");
header("Content-disposition: attachment; filename = \"".$pureFilename."\"");

readfile($filename);
