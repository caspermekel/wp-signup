<?php

require_once("countries.php");

function countrySelect($selected = 'DE', $lang='de_DE'){
	if(!$lang){
	$lang = $_SESSION['lang'];
	}
	
	global $countryListGrouped;
	// $return .= '<select name="s_country" class="span10" id="s_country">';
	$return = '';
	foreach ($countryListGrouped[$lang] as $continent => $countries) {
	$return .= '<optgroup label="'.$continent.'">';
	foreach($countries as $short => $long){
	  $sel = '';
	  if($selected == $short){
	    $sel = 'SELECTED';
	  }
	  $return .= '<option value="'.$short.'" '. $sel .'>'.$long.'</option>';
	}
	$return .= '</optgroup>';
	}
	// $return .= '</select>';
	return $return;
}
if(!function_exists('ip_info')){
	function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
	    $output = NULL;
	    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
	        $ip = $_SERVER["REMOTE_ADDR"];
	        if ($deep_detect) {
	            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
	                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
	                $ip = $_SERVER['HTTP_CLIENT_IP'];
	        }
	    }
	    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
	    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
	    $continents = array(
	        "AF" => "Africa",
	        "AN" => "Antarctica",
	        "AS" => "Asia",
	        "EU" => "Europe",
	        "OC" => "Australia (Oceania)",
	        "NA" => "North America",
	        "SA" => "South America"
	    );
	    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {

	    	$ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, "http://www.geoplugin.net/json.gp?ip=" . $ip);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    $rawdat = curl_exec($ch);
		    curl_close($ch);

	    	// $data = file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip);
	    	// echo strlen($data);
	        $ipdat = @json_decode($rawdat);
	        
	        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
	            switch ($purpose) {
	                case "location":
	                    $output = array(
	                        "city"           => @$ipdat->geoplugin_city,
	                        "state"          => @$ipdat->geoplugin_regionName,
	                        "country"        => @$ipdat->geoplugin_countryName,
	                        "country_code"   => @$ipdat->geoplugin_countryCode,
	                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
	                        "continent_code" => @$ipdat->geoplugin_continentCode
	                    );
	                    break;
	                case "address":
	                    $address = array($ipdat->geoplugin_countryName);
	                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
	                        $address[] = $ipdat->geoplugin_regionName;
	                    if (@strlen($ipdat->geoplugin_city) >= 1)
	                        $address[] = $ipdat->geoplugin_city;
	                    $output = implode(", ", array_reverse($address));
	                    break;
	                case "city":
	                    $output = @$ipdat->geoplugin_city;
	                    break;
	                case "state":
	                    $output = @$ipdat->geoplugin_regionName;
	                    break;
	                case "region":
	                    $output = @$ipdat->geoplugin_regionName;
	                    break;
	                case "country":
	                    $output = @$ipdat->geoplugin_countryName;
	                    break;
	                case "countrycode":
	                    $output = @$ipdat->geoplugin_countryCode;
	                    break;
	            }
	        }
	    }
	    return $output;
	}
}
?>