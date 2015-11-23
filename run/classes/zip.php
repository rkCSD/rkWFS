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
 * Last modified: 2015/08/13 21:33 CEST
 * Author: Alexander Eifler
 *
 * ===Notes===========================================
 * There are currently no notes.
 * ===================================================
 */

ini_set('max_execution_time', 900);

function removeOldZipArchives(){
	$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(realpath(TEMP_FOLDER)), RecursiveIteratorIterator::LEAVES_ONLY);

	foreach($files as $name => $file){
		if(!$file->isDir()){
			$filePath = $file->getRealPath();
			if(time()-filemtime($filePath)>MAX_ZIP_AGE){
				unlink($filePath);
			}
		}
	}
}

removeOldZipArchives();

$dir = pathSecurity($_GET['dir']);
$dir = $dir==""?"/":$dir;

$zipDir = substr($dir,0,1)=="/"?substr($dir,1):$dir;
$zipDir = substr($zipDir, -1)!="/"?$zipDir."/":$zipDir;

$rootPath = realpath(UPLOAD_FOLDER."/".PROJECT_NAME."/".$dir);

if(empty($rootPath)){
    echo "WRONG PATH!";
    exit;
}

$zipName = time()."-".rand(0,100000);
$zipFilePureName = $zipName.".zip";
$zipFileName = realpath(TEMP_FOLDER)."/".$zipFilePureName;

if(ZIP_METHOD == "BUILD_IN"){
	$zip = new ZipArchive();
	$zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
	$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);

	foreach($files as $name => $file){
		if(!$file->isDir()){
			$filePath = $file->getRealPath();
			$relativePath = substr($filePath, strlen($rootPath) + 1);
			if(substr($relativePath,0,1)=="." || strpos($relativePath,"/.")!==false)
				continue;
			$zip->addFile($filePath, $zipDir.$relativePath);
		}
	}

	$zip->close();
}elseif(ZIP_METHOD == "LINUX_EXT"){
	exec("cd \"".UPLOAD_FOLDER."/".PROJECT_NAME."/".$dir."\" && zip -r0 \"$zipFileName\" . -x \".*\"");
}else{
	echo "WRONG CONFIG-SETTING";
	exit;
}

$downloadName = WEBSITE_DEFAULT_URI."/temp/".$zipFilePureName;
header("Location: ".$downloadName);
