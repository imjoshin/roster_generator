<?php
	$files = scandir("html");
	$output = "<select id='file'>";
	foreach($files as $file){
		if(strpos($file, ".html")) $output .= "<option value='$file'>$file</option>";
	}
	echo $output;
?>