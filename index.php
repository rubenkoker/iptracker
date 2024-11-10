<?php

function detect_client_ip() {
	$ip_address = 'UNKNOWN';
	if (isset($_SERVER['HTTP_CLIENT_IP'])) $ip_address = $_SERVER['HTTP_CLIENT_IP'];
	else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else if (isset($_SERVER['HTTP_X_FORWARDED'])) $ip_address = $_SERVER['HTTP_X_FORWARDED'];
	else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) $ip_address = $_SERVER['HTTP_FORWARDED_FOR'];
	else if (isset($_SERVER['HTTP_FORWARDED'])) $ip_address = $_SERVER['HTTP_FORWARDED'];
	else if (isset($_SERVER['REMOTE_ADDR'])) $ip_address = $_SERVER['REMOTE_ADDR'];
	return $ip_address;
}
function register_site($db, $site, $ip) {
	$address = inet_pton($ip);
	$stmt = $db->prepare("INSERT INTO `ip` (`site`,`ip`) VALUES (?,?) ON DUPLICATE KEY UPDATE `ip` = ?");
	// I still don't know why this must be sss insteas of sbb, php is weird.
	$stmt->bind_param("sss", $site, $address, $address);
	$stmt->execute() or die($stmt->error);
	$stmt->close();
}
function ip_for_site($db, $site) {
	$address = null;
	$stmt = $db->prepare("SELECT `ip` FROM `ip` WHERE site = ?");
	$stmt->bind_param("s", $site);
	$stmt->execute() or die($stmt->error);
	$stmt->bind_result($address);
	$found = $stmt->fetch();
	$stmt->close();
	if (!$found) return null;
	$bytes = unpack("N4", $address); // Network orders is always Big Endian
	if ($bytes[2] === 0 && $bytes[3] === 0 && $bytes[4] === 0) {
		// Assume IPv4
		return long2ip($bytes[1]);
	} else {
		// Assume IPv6
		return inet_ntop($address);
	}
}


function getUserIP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
              $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
              $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}


$user_ip = getUserIP();


$t=time();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "iptracker";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO ip (IPadress, time)
VALUES ('$user_ip', '$t')";

if ($conn->query($sql) === TRUE) {
  //echo "New record created successfully";
} else {
  //echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
<script>
	setTimeout(1000);
	window.location.replace("https://112-rijnmond.nl/drie-aanhoudingen-na-aantreffen-grote-hoeveelheid-drugs-in-woning-savornin-lohmanlaan-rotterdam/");
</script>
