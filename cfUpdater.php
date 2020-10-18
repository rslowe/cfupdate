<?php
// Update these values with your own information.

$hosts = array(
	"KEY_GOES_HERE" => "ENTER_EVERYTHING_BEFORE_THE_DOMAIN",
);
$apiKey       = "<CLOUDFLARE_GLOBAL_API_KEY>";                   // Your CloudFlare API Key.
$myDomain     = "example.net";                                   // Your domain name.
$emailAddress = "exampleemailaccount@example.net";               // The email address of your CloudFlare account.
$v6v4ExclusiveModeEnable = FALSE;                                // Prevent ipv4 and ipv6 records from mixing.
$v4OnlyPrefix = "ip4.";                                          // String (in domain) to ensure v4 only entry. (must end in .) (Only if $v6v4ExclusiveModeEnable = TRUE) 
$v6OnlyPrefix = "ip6.";                                          // String (in domain) to ensure v6 only entry. (must end in .) (Only if $v6v4ExclusiveModeEnable = TRUE) 
$baseUrl      = 'https://api.cloudflare.com/client/v4/';         // The URL for the CloudFlare API, Change if an Update is Pushed by CF.

// The values below do not need to be changed.

// Check the calling client has a valid auth key.
if (empty($_GET['auth'])) {
	header("HTTP/1.1 401 Unauthorized");
	die("notoken\n");
} elseif (!array_key_exists($_GET['auth'], $hosts)) {
	header("HTTP/1.1 403 Forbidden");
	die("badtoken\n");
}
if (empty($hosts[$_GET['auth']])){
    $ddnsAddress  = $myDomain;                              // If no subdomain is given, update the domain itself.
}
else {
    $ddnsAddress  = $hosts[$_GET['auth']].".".$myDomain;   // The subdomain that will be updated.
}

if(empty($_GET['ip'])){ // If we don't have an IP in the Query String
	if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) { // Cloudflare Patch. If the web server is behind CF's Proxy, this is the header we want.
		$_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
	}
	$ip = $_SERVER['REMOTE_ADDR'];                    // The IP of the client calling the script.
}
else { // Allow the client to specify the IP in the Query String.
	$ip = $_GET['ip'];
}

// Array with the headers needed for every request
$headers = array(
	"X-Auth-Email: ".$emailAddress,
	"X-Auth-Key: ".$apiKey,
	"Content-Type: application/json"
);
// Sends request to CloudFlare and returns the response.
function send_request($requestType) {
	global $url, $fields, $headers;
	$fields_string="";
	if ($requestType == "POST" || $requestType == "PUT") {
		$fields_string = json_encode($fields);
	}
	// Send the request to the CloudFlare API.
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, "curl");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	if ($requestType == "POST" || $requestType == "PUT") {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	return json_decode($result);
}
// Prints errors and messages and kills the script
function print_err_msg() {
	header("HTTP/1.1 500 Internal Server Error");
	echo "Something went Wrong...\n";
	echo "<br><br>";
	global $data;
	if (!empty($data->errors)) {
		echo "Errors:\n";
		print_r($data->errors);
		echo "\n";
	}
	if (!empty($data->messages)) {
		echo "Messages:\n";
		print_r($data->messages);
		echo "\n";
	}
	die();
}
if ($v6v4ExclusiveModeEnable) {
	// Determine protocol version and set record type.
	if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) && strpos($ddnsAddress, $v6OnlyPrefix) !== false) {
		$type = 'AAAA';
	} elseif (strpos($ddnsAddress, $v4OnlyPrefix) !== false) {
		$type = 'A';
	} else{
		header("HTTP/1.1 418 Teapot"); // Stupid Error. 
		die("IP Address Mismatch. ".$ip." not allowed to be used for ".$ddnsAddress."\n");
	}
}
//Update $baseUrl
$baseUrl .= 'zones';
// Build the request to fetch the zone ID.
// https://api.cloudflare.com/#zone-list-zones
$url = $baseUrl.'?name='.$myDomain;
$data = send_request("GET");
// Continue if the request succeeded.
if ($data->success) {
	// Extract the zone ID (if it exists) and update $baseUrl
	if (!empty($data->result)) {
		$zoneID = $data->result[0]->id;
		$baseUrl .= '/'.$zoneID.'/dns_records';
	} else {
        header("HTTP/1.1 400 Bad Request");
		die("Zone ".$myDomain." doesn't exist\n");
	}
// Print error message if the request failed.
} else {
    echo "Error while fetching zone.";
	print_err_msg();
}
// Build the request to fetch the record ID.
// https://api.cloudflare.com/#dns-records-for-a-zone-list-dns-records
$url = $baseUrl.'?type='.$type;
$url .= '&name='.$ddnsAddress;
$data = send_request("GET");
// Continue if the request succeeded.
if ($data->success) {
	// Extract the record ID (if it exists) for the subdomain we want to update.
	$rec_exists = false;					// Assume that the record doesn't exist.
	if (!empty($data->result)) {
		$rec_exists = true;			// If this runs, it means that the record exists.
		$id = $data->result[0]->id;
		$cfIP = $data->result[0]->content;	// The IP Cloudflare has for the subdomain.
	}
// Print error message if the request failed.
} else {
	echo "Error while fetching record.";
	print_err_msg();
}
// Create a new record if it doesn't exist.
if (!$rec_exists) {
	// Build the request to create a new DNS record.
	// https://api.cloudflare.com/#dns-records-for-a-zone-create-dns-record
	$fields = array(
		'type' => $type,
		'name' => $ddnsAddress,
		'content' => $ip,
	);
	$url = $baseUrl;
	$data = send_request("POST");
	// Print success/error message.
	if ($data->success) {
		echo "warning ".$ip."\n";
		echo "<br><br>";
		echo $ddnsAddress."/".$type." is a new record that has been successfully created\n";
	} else {
		echo "Error while creating record.";
		print_err_msg();
	}
// Only update the entry if the IP addresses do not match.
} elseif ($ip != $cfIP) {
	// Build the request to update the DNS record with our new IP.
	// https://api.cloudflare.com/#dns-records-for-a-zone-update-dns-record
	$fields = array(
		'name' => $ddnsAddress,
		'type' => $type,
		'content' => $ip,
		'ttl' => 120 
	);
	$url = $baseUrl.'/'.$id;
	$data = send_request("PUT");
	// Print success/error message.
	if ($data->success) {
		echo "good ".$ip."\n";
		echo "<br><br>";
		echo $ddnsAddress."/".$type." successfully updated to ".$ip."\n";
	} else {
        echo "Error while updating IP.";
		print_err_msg();
	}
} else {
	echo "nochg ".$ip."\n";
	echo "<br><br>";
	echo $ddnsAddress."/".$type." is already up to date\n";
}
?>
