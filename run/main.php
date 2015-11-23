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
 * Last modified: 2015/05/19 17:45 CEST
 * Author: Alexander Eifler
 *
 * ===Notes===========================================
 * There are currently no notes.
 * ===================================================
 */
	$browserLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

	function getAvailableLangs(){
		$outputArr = array();
		$handle = opendir ("lang");
		while ($file = readdir ($handle)) {
			if(!is_dir($file)) $outputArr[] = substr($file,0,-4);
		}
		closedir($handle);
		asort($outputArr);
		return $outputArr;
	}
	$availableLangs = getAvailableLangs();

	if(isset($_GET['changeLang']) && $_GET['changeLang']!=""){
		if(file_exists("./lang/".$_GET['changeLang'].".php")){
			$_SESSION['lang'] = $_GET['changeLang'];
		}
	}

	if(isset($_SESSION['lang']) && $_SESSION['lang']!=""){
		require_once("./lang/".$_SESSION['lang'].".php");
	}else {
		if ($browserLang != "") {
			$browserLangs = explode(',', $browserLang);
			$matches = false;
			foreach ($availableLangs as $elem) {
				foreach ($browserLangs as $langs) {
					$browserLangss = explode(';', $langs);
					if (!isset($browserLangss[0])) continue;

					$curElem = strtolower($elem);
					$curLang = strtolower($browserLangss[0]);
					if ($curElem == $curLang || ($curLang != "" && strpos($curElem, $curLang) !== false)) {
						require_once("./lang/" . $elem . ".php");
						$matches = true;
						break;
					}
				}
				if ($matches)
					break;
			}
			if (!$matches)
				require_once("./lang/en-GB.php");

		} else {
			require_once("./lang/en-GB.php");
		}
	}

	$initialActualPath = "/";
	if(isset($_GET['dir'])){
		$initialActualPath = pathSecurity($_GET['dir']);
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>rkWFS</title>
		<link rel="icon" href="<?= WDU?>images/favicon.ico">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=600" />
		<link rel="stylesheet" href="<?= WDU?>styles/style.css">
		<script src="<?= WDU?>scripts/lib/jquery-1.11.3.min.js" type="text/javascript" charset="utf-8"></script>
		<script>
			<?php
				foreach($lang['js'] as $key=>$value){
					echo "var LANG".$key." = '".$value."';\n";
				}
				echo "var basePath = '".WDU.PROJECT_NAME."';\n";
				echo "var initialActualPath = '".$initialActualPath."';\n";
			?>
		</script>
		<script src="<?= WDU?>scripts/lib/moment-2.10.6.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= WDU?>scripts/lib/jquery.ui.widget.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= WDU?>scripts/lib/jquery.iframe-transport.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= WDU?>scripts/lib/jquery.fileupload.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= WDU?>scripts/lib/md5.min.js"></script>
		<script src="<?= WDU?>scripts/filetypes.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= WDU?>scripts/script.js" type="text/javascript" charset="utf-8"></script>
	</head>
	<body id="dropzone">
		<header>
			<img src="<?= WDU?>images/logo.png" alt="Logo" id="logo" />
			<h1><?= PROJECT_FULLNAME ?></h1>
			<div id="languageStrip">
				<?php
				foreach($availableLangs as $elem){
					echo "<a href=\"?changeLang=".$elem."\"><img src=\"".WDU."images/lang/".$elem.".png\" /></a> ";
				}
				?>
			</div>
		</header>
		<aside id="fileMgmt">
			<div id="sideArrow">
				<img src="<?= WDU?>images/right.png" id="sideArrowButton"/>
			</div>
			<div id="fileUploadArea">
				<?= $lang['filesUpload']?>:<br/>
				<input id="fileupload" type="file" name="files[]" data-url="<?= WDU.PROJECT_NAME."/upload?dir=".$initialActualPath?>" multiple/><br/><br/>
				<input id="uploadAll" type="button" name="uploadAll" value="<?= $lang['uploadAll'];?>" class="uploadAllButton"/><br/><br/>
				<?= $lang['progress']?>:
				<div id="progress">
					<div class="bar" style="width: 0%;">0%</div>
				</div>
				<div id="uploadList"></div>
			</div>
		</aside>
		<section id="listMgmt">
			<div id="content">
				<div id="filterBar">
					<input type="text" name="txtFilterBar" value="" id="txtFilterBar" placeholder="<?= $lang['filter'];?>..."/>
					<img src="<?= WDU?>images/folder-new.png" id="newFolder" onclick="createNewFolder()"/>
					<div id="shortLinks">
						[<a href="<?= WDU."?dir=".$initialActualPath ?>" id="permaLink">Perma-Link</a>]
						[<a href="<?= WDU.PROJECT_NAME."/zip?dir=".$initialActualPath ?>" id="zip">ZIP</a>]
					</div>
					<div id="saveMobile">
						<a href="<?= WDU.PROJECT_NAME."/zip?dir=".$initialActualPath ?>" id="zipMobile"><img src="<?= WDU?>images/save.png"/></a>
					</div>
				</div>
				<div id="statusBar">
					<div id="actualPath">
						<?= $lang['path'];?>: <span id="realPath"><?= $initialActualPath ?></span>
					</div>
					<div id="elemCount">
						<span id="folderCount">0</span> <?= $lang['folders'];?> - <span id="fileCount">0</span> <?= $lang['files'];?>
					</div>
				</div>
				<div id="fileList">
					<table>
						<colgroup>
							<col style="width:4em"/>
							<col />
							<col style="width:8em"/>
							<col style="width:14em"/>
							<col style="width:1em"/>
						</colgroup>
						<thead>
							<tr>
								<td nowrap><a href="#" onclick="sort('type')"><?= $lang['type']?> <span id="sortModeIcon_type"></span></a></td>
								<td nowrap><a href="#" onclick="sort('name')"><?= $lang['filename']?> <span id="sortModeIcon_name"></span></a></td>
								<td nowrap><a href="#" onclick="sort('size')"><?= $lang['size']?> <span id="sortModeIcon_size"></span></a></td>
								<td nowrap><a href="#" onclick="sort('date')"><?= $lang['modificationDate']?> <span id="sortModeIcon_date"></span></a></td>
								<td nowrap>&nbsp;</td>
							</tr>
						</thead>
						<tbody id="fileListData"></tbody>
					</table>
				</div>
			</div>
		</section>
		<footer>
			<span id="footerText">
				Copyright &copy; <?= date("Y", time())?> René Knipschild Custom Software Development - <a target="_blank" href="http://www.rkcsd.com/">www.rkcsd.com</a>
			</span>
		</footer>
	</body>
</html>