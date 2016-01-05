<?php
	ini_set('memory_limit', '-1');
	ini_set('max_execution_time', '-1');
	define('WP_MEMORY_LIMIT', '1024M');
	require_once('simple_html_dom.php');

	if (isset($_POST["format"])){ $format = $_POST["format"]; }
	else{ return 0; }
	if (isset($_POST["url"]) && strlen($_POST["url"]) > 0){ $url = "html/" . $_POST["url"]; }
	else{ $url = "html/dhh-fall2014.html"; }
	if (isset($_POST["min"])){ $min = $_POST["min"]; }
	else{ $min = ""; }
	if (isset($_POST["max"])){ $max = $_POST["max"]; }
	else{ $max = ""; }


	$output = "";
	if(!($dom = file_get_html($url))){
		$dom = file_get_html("roster.html");
	}

	file_put_contents("test.txt", "");
	foreach($dom->find("tr") as $person){
		$pDom = str_get_html($person);
		$td = $pDom->find("td");

		if(strpos($person, "nbsp;") || count($td) < 8){
			file_put_contents("test.txt", "$person\n", FILE_APPEND);
			continue;
		}
		$room = strip_tags($td[5]->innertext);
		if($min != "" && intval($room) < $min) continue;
		if($max != "" && intval($room) > $max) continue;

		$pOut = str_replace("#name#", trim(str_replace("<b> (RA)</b>", "", $td[0]->innertext)), $format);

		$nameParts = explode(",", trim($td[0]->innertext));
		$firstParts = explode(" ", trim($nameParts[1]));
		$pOut = str_replace("#first#", ucwords(strtolower(trim(str_replace(",", "", $firstParts[0])))), $pOut);
		$pOut = str_replace("#last#", str_replace(" ", "", ucwords(strtolower(trim(str_replace(",", "", $nameParts[0]))))), $pOut);

		$pOut = str_replace("#gender#", trim($td[1]->innertext), $pOut);
		$pOut = str_replace("#age#", trim($td[2]->innertext), $pOut);
		$pOut = str_replace("#class#", trim($td[3]->innertext), $pOut);

		$building = trim($td[4]->innertext);
		//if room contains e, make it ew or em
		if(strpos(strip_tags($td[5]->innertext), 'E') !== false)
			$building = "E" . substr($building, -1);

		$pOut = str_replace("#build#", $building, $pOut);
		$pOut = str_replace("#room#", trim(strip_tags($td[5]->innertext)), $pOut);
		$pOut = str_replace("#phone#", trim($td[6]->innertext), $pOut);
		$pOut = str_replace("#email#", trim(strip_tags($td[7]->innertext)), $pOut);

		$pOut = str_replace("#id#", str_replace("@mtu.edu", "", trim(strip_tags($td[7]->innertext))), $pOut);

		$output .= "$pOut\n";
	}

	$date = date('Y-m-d_H-i-s');
	file_put_contents("out/$date.txt", $output);
	echo "out/$date.txt";
?>
