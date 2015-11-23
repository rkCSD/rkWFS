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
 * File: script.js
 * Version: 1.0.0
 * Last modified: 2015/05/19 17:45 CEST
 * Author: Alexander Eifler
 *
 * ===Notes===========================================
 * There are currently no notes.
 * ===================================================
 */

var actualPath = initialActualPath;
var sortName = "name";
var sortMode = "asc";
var timerTime = 5000;

getAllFiles();

setInterval(function(){getAllFiles()}, timerTime);

function getAllFiles() {
	if($("#txtFilterBar").val() != "" && $("#txtFilterBar").val() != null)
		return;

	var newFileList = "";

    $("#sortModeIcon_name").html("");
    $("#sortModeIcon_size").html("");
    $("#sortModeIcon_type").html("");
    $("#sortModeIcon_date").html("");

	$.getJSON(basePath + "/ajax?ajaxReq=files&sortName=" + sortName + "&sortMode=" + sortMode + "&actualPath=" + actualPath, function(data) {
        var newFileList = "";
		for(id in data.files){
			var file = data.files[id];
			newFileList = newFileList + "<tr>" +
			"<td><img class=\"filetypePicture\" src=\"images/filetypes/" + getFileCategory(file.ext.toLowerCase()) + ".png\" alt=\"" + file.ext.toLowerCase() + "\"/><div class=\"filetypeText size" + file.ext.length + " " + getFileTextColor(file.ext.toLowerCase()) + "\">" + fileTypeTextFormatter(file.ext.toUpperCase()) + "</div></td>" +
			"<td onmouseout='imagePreviewRemove()'><a href=\"" + (!file.isDir ? basePath + "/download?dir=" + file.link : "#") + "\" " + (file.isDir ? " onclick=\"enterFolder('" + file.filename + "');\"": "") + "  onmouseover='imagePreview(\"" + file.filename + "\"," + file.preview.width + "," + file.preview.height + ")' onmouseout='imagePreviewRemove()'>" + (file.filename == "!" ? "["+LANGback+"]" : file.filename) + "</a></td>" +
			"<td>" + (!file.isDir ? formatSize(file.size) : "")  + "</td>" +
			"<td>" + (file.filename != "!" ? moment.unix(file.modification).locale(LANGmomentLocale).fromNow() + "<br>" + moment.unix(file.modification).locale(LANGmomentLocale).format("L, LTS") : "") + "</td>" +
			"<td>" + (file.filename != "!" ? "<img class=\"deletePicture\" src=\"images/delete.png\" alt=\"Löschen\" onclick=\"deleteAFile('" + file.filename + "')\" />" : "") + "</td>" +
			"</tr>";
		}
		$("#fileListData").html(newFileList);
        newFileList = null;
		$("#sortModeIcon_" + data.sortName).html("<img src=\"images/" + data.sortMode + ".png\" />");
		$('#folderCount').html(data.folderCount);
		$('#fileCount').html(data.fileCount);
	});
}

function imagePreview(filename,width,height){
	var folderPath = $("#realPath").html();

	if(folderPath.substr(folderPath.length-1,1)!="/")
		folderPath = folderPath + "/";

	var fullPath = basePath + "/download?dir=" + folderPath + filename;
	var fileEnding = filename.substr(filename.length-3);
	fileEnding = fileEnding.toLowerCase();

	if(fileEnding == "jpg" || fileEnding == "jpeg" ||  fileEnding == "gif" ||  fileEnding == "png"){
        if(width>height){
            var h = height/width;
            width = 21;
            height = 21*h;
        }else{
            var w = width/height;
            height = 21;
            width = 21*w;
        }

		$("section").append("<p id='preview' onclick='imagePreviewRemove()'><img style='width:"+width+"em;height:"+height+"em;position:fixed;bottom:1.9em;right:0.5em;border: 1px solid black;' src='" + fullPath + "' /></p>");
	}
}

function imagePreviewRemove(){
	$('#preview').remove();
}

function formatSize(bytes){
	var numberLength = (bytes + "").length - 1;
	var sizes = "BKMGTPEZ",
		factor = Math.floor(numberLength / 3),
		size = " " + sizes.substr(factor, 1);
	return floorDecimals(bytes / Math.pow(1024, factor)) + (size == " B" ? size : size + "B");
}

function floorDecimals(number, decimals){
	decimals = decimals || 2;
	return Math.floor(number * Math.pow(10, decimals)) / Math.pow(10, decimals);
}

function deleteAFile(filename){
	if(confirm('"' + filename + '"\n\n' + LANGwarningDeleteFile + "\n" + LANGwarningDeleteFile2)){
		$.post(basePath + "/ajax", { ajaxReq: "delete", filename: actualPath+"/"+filename });
		getAllFiles();
	}
}

function enterFolder(filename){
	$.getJSON(basePath + "/ajax?ajaxReq=enter&path="+actualPath + "/" + filename, function(data) {
        actualPath = data.path;
		$("#realPath").html(actualPath);
		$("#permaLink").attr('href', basePath + "?dir=" + actualPath);
		$("#zip").attr('href', basePath + "/zip?dir=" + actualPath);
        $('#fileupload').fileupload(
            'option',
            {
                url: basePath + "/upload?dir=" + actualPath
            }
        );
		getAllFiles();
	});
}

function sort(row){
    if(sortName == row){
        if(sortMode == "asc"){
            sortMode = "desc";
        }else{
            sortMode = "asc";
        }
    }
    sortName = row;
	getAllFiles();
}

function createNewFolder(){
	var newFolderName = prompt("Name des neuen Ordners");
	if(newFolderName != null){
		$.post(basePath + "/ajax", { ajaxReq: "newFolder", newFolderName: newFolderName , actualPath: actualPath});
		getAllFiles();
	}
}

$(function() {
	$('#txtFilterBar').keyup(function () {
		var table = document.getElementById('fileListData');
		var phrase = $("#txtFilterBar").val().toLowerCase();

		for (var r = 0; r < table.rows.length; r++) {
			var elem = table.rows[r].innerHTML;
			elem = elem.substr(elem.indexOf("<a href"));
			elem = elem.substr(elem.indexOf(">")+1);
			elem = elem.substr(0,elem.indexOf("</a>"));

			var displayStyle = 'none';
			if(elem.toLowerCase().indexOf(phrase) >= 0)
				displayStyle = '';
			else
				displayStyle = 'none';

			table.rows[r].style.display = displayStyle;
		}
	});

    $('#sideArrow').click(function(){
        if($('#fileUploadArea').is(":visible")){
            $('#sideArrowButton').attr("src", "images/left.png");
            $('#fileUploadArea').hide();
            $('#fileMgmt').css("width", "0.1em");
            $('#listMgmt').css("margin-right", "2.1em");
        }else{
            $('#sideArrowButton').attr("src", "images/right.png");
            $('#fileUploadArea').show();
            $('#fileMgmt').css("width", "20em");
            $('#listMgmt').css("margin-right", "22em");
        }
    });

    $( document ).ready(function() {
        if($( window ).width() <= 600) {
            $('#sideArrow').trigger("click");
        }
    });

    $(document).bind('dragover', function (e) {
		var dropZone = $('#dropzone'),
			timeout = window.dropZoneTimeout;
		if (!timeout) {
			dropZone.addClass('in');
		} else {
			clearTimeout(timeout);
		}
		var found = false,
			node = e.target;
		do {
			if (node === dropZone[0]) {
				found = true;
				break;
			}
			node = node.parentNode;
		} while (node != null);
		if (found) {
			dropZone.addClass('hover');
		} else {
			dropZone.removeClass('hover');
		}
		window.dropZoneTimeout = setTimeout(function () {
			window.dropZoneTimeout = null;
			dropZone.removeClass('in hover');
		}, 100);
	});

    $('#fileupload').fileupload({
        dataType: 'json',
        dropZone: $('#dropzone'),
        add: function (e, data) {
            data.context = $('<button class="uploadButton ' + md5(data.files[0].name) + '"/>').text(LANGupload)
                .appendTo("#uploadList")
                .click(function () {
                    data.context = $('<span class="uploadText ' + md5(data.files[0].name) + '"/>').text(LANGuploading).replaceAll($(this));
                    data.submit();
                });
            node = $('<button class="stopButton ' + md5(data.files[0].name) + '"/>').text(LANGdelete)
                .appendTo("#uploadList")
                .click(function () {
                    $("." + md5(data.files[0].name)).remove();
                    data.abort();
                });
        },
        done: function (e, data) {
            $("." + md5(data.files[0].name)).remove();
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .bar').css(
                'width',
                progress + '%'
            );
            $('#progress .bar').html(
                progress + "%"
            );
        }
    }).on('fileuploadadd', function (e, data) {
        data.context = $('<div class="fileElem"/>').appendTo('#uploadList');
        $.each(data.files, function (index, file) {

            var node = $('<p/>')
                .append($('<span/>').text(file.name));

            node.appendTo(data.context);
            data.context.addClass(md5(file.name));
        })
    });
    $('#uploadAll').click(function () {
        $('aside').find(".uploadButton").trigger("click");
    });
});