<?php

class UploadHandlerRkcsd extends UploadHandler{
	protected function get_file_name($file_path, $name, $size, $type, $error,
	                                 $index, $content_range) {
		$name = $this->trim_file_name($file_path, $name, $size, $type, $error,
			$index, $content_range);
		return $name;
	}

	protected function trim_file_name($file_path, $name, $size, $type, $error,
	                                  $index, $content_range) {
		// Remove path information and dots around the filename, to prevent uploading
		// into different directories or replacing hidden system files.
		// Also remove control characters and spaces (\x00..\x20) around the filename:
		$name = trim(basename(stripslashes($name)), ".\x00..\x20");
		$name = str_replace(array('ä','Ä','ö','Ö','ü','Ü','ß'),array('ae','Ae','oe','Oe','ue','Ue','ss'),$name);
		$name = preg_replace("/[^A-Za-z0-9\_\-\.]/", "_", str_replace(chr(0), "", $name));
		// Use a timestamp for empty filenames:
		if (!$name) {
			$name = str_replace('.', '-', microtime(true));
		}
		return $name;
	}

}