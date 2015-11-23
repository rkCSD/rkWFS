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
 * File: upload.php
 * Version: 1.0.0
 * Last modified: 2015/05/19 17:45 CEST
 * Author: Alexander Eifler
 *
 * ===Notes===========================================
 * There are currently no notes.
 * ===================================================
 */

require_once('./run/classes/UploadHandler.php');
require_once('./run/classes/UploadHandlerRkcsd.php');

$dir = pathSecurity($_GET['dir'])."/";
$path = UPLOAD_FOLDER."/".PROJECT_NAME."/".$dir;

$settings = array(
	'upload_dir' => $path,
	'upload_url' => WDU."/".PROJECT_NAME."/".$dir,
	'image_versions' => array()
);

$upload_handler = new UploadHandlerRkcsd($settings);
