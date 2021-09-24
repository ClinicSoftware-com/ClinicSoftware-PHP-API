<?php
require 'api_config.php';
require '../lib/salon_api.php';

$api = new Salon_api($api_config['client_key'], $api_config['client_secret'],  $api_config['business_alias'],  $api_config['api_url']);

// dev - init
$api->setURL($api_config['api_url']);
$api->setDebug(true);


// request
$client_id = 20001;
$expires = 120;
$result = $api->reqOnlineBookingAuthToken($client_id, $expires);
$status = $api->getLastStatus();

//print_r($result);
//exit;

?>

<!doctype html>
<html>
<head>
	<style>body {background-color:#ccc;}</style>
</head>
<body>
	<iframe style="width:100%; height:600px; margin:200px 0 0 0;" src="http://x.y.z/my-account/auth/<?php echo $result['token'];?>?embed=1&redirect=online-booking"></iframe>
</body>
</html>