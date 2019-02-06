<?php

//if (! isset($_SESSION[$street][$city][$state][$zip]))
//{
//    session_start(['cookie_lifetime' => 600]);
//}

session_cache_expire(5);
session_start(['cookie_lifetime' => 600]);

print <<< END
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
"http://www.w3.org/TR/html4/loose.dtd">
<HTML lang="en">
<HEAD>
<TITLE>Request a map from Google</TITLE>
</HEAD>
END;

$googleBaseUrl = "https://maps.googleapis.com/maps/api/staticmap";
$googleCenter = "?center=";
$googleZoom = "&zoom=15";
$googleSize = "&size=400x400";
$googleKey = "&key=";
$googleApiKey = "AIzaSyBxQxvgL05WMbg_QYuxj4BuipWgOXgx1yc";

$street = (isset($_POST['street']) ? $_POST['street'] : '');
$city = (isset($_POST['city']) ? $_POST['city'] : '');
$state = (isset($_POST['state']) ? $_POST['state'] : '');
$zip = (isset($_POST['zip']) ? $_POST['zip'] : '');

if ($_SESSION[$street][$city][$state][$zip])
{
    $cachedFile = $_SESSION[$street][$city][$state][$zip];
} else {
    unset($cachedFile);
}

if (isset($_POST['getMap']) && ! $_SESSION[$street][$city][$state][$zip])
{
    //  request the map from Google
    $urlString = urlencode($street . "," . $city . "," . $state . "," . $zip);
    $URL = $googleBaseUrl.$googleCenter.$urlString.$googleZoom.
           $googleSize.$googleKey.$googleApiKey;
           
    $theMap = file_get_contents($URL);
    //  write it to a file
    $fileName = time() . ".png";
    $file = fopen($fileName,"wb");
    fwrite($file,$theMap);
    fclose($file);
    //  cache it
    $_SESSION[$street][$city][$state][$zip] = $fileName;
}

if (isset($_POST['clear']))
{
    $street = $city = $state = $zip = '';
}

print "<div align=center>\n";

print "<br><br>Enter a street address below, <br>and click Get Map to fetch a map of   the address.<br><br>\n";
print "<form method=POST action=$_SERVER[PHP_SELF]>\n";
print "<table>";
print "<tr><td>Street Address</td><td><input name=street type=text size=60 value=\"$street\"></input></td></tr>\n";
print "<tr><td>City</td><td><input name=city type=text size=60 value=\"$city\"></input></td></tr>\n";
print "<tr><td>State</td><td>" . create_state_select($state) . "</td></tr>\n";
print "<tr><td>Zip Code</td><td><input name=zip type=text size=10 value=\"$zip\"></input></td></tr>\n";
print "</table>";

print "<br><br><input name=getMap type=submit value=\"Get Map\">\n";
print "<br><br><input name=clear type=submit value=\"Clear Values\">\n";
print "</form></div>\n";

if (isset($_POST['getMap']))
{	
    $fromCache = ($cachedFile ? " (from cache)" : "");
    $theFile = ($cachedFile ? $cachedFile : $fileName);
    print "<br><center>Your map$fromCache:</center><br>\n";
    print "<div align=center><img src=\"$theFile\"></div>";
}

print <<< END
</BODY>
</HTML>
END;

exit;

function create_state_select($val)
{
    $states = array('', 'AL', 'AK', 'AS', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FM', 'FL', 'GA', 'GU', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MH', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'MP', 'OH', 'OK', 'OR', 'PW', 'PA', 'PR', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VI', 'VA', 'WA', 'WV', 'WI', 'WY', 'AE', 'AA', 'AP');
	$sel = "<select name=\"state\" class=\"inputtext\">";
    foreach ($states as $st) 
	{	
		$sel .= "<option" . ($st == $val ? " selected" : "") . ">$st</option>\n";
	}
	$sel .= "</select>\n";
	return $sel;
}

?>

