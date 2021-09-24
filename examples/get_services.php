<?php
require 'api_config.php';
require '../lib/salon_api.php';

$api = new Salon_api($api_config['client_key'], $api_config['client_secret'],  $api_config['business_alias'],  $api_config['api_url']);

// dev - init
$api->setURL($api_config['api_url']);
$api->setDebug(false);

// Get a single service
// $result = $api->get_services(242, null, 1000);
// Get multiple services
$result = $api->get_services(null, "2000-01-01", 1000);


// $status = $api->getLastStatus();


// dev - result
header_remove("Content-Type");
header('Content-Type: text/plain; charset=UTF-8');

if (!empty($result)) print_r($result);
// echo "\nStatus: {$status}\n";

$error = $api->getLastError();
if (!empty($error)) echo $error;

// echo "\n\n" . $api->readLog();
