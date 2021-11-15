<?php
require 'api_config.php';
require '../lib/salon_api.php';

$api = new Salon_api($api_config['client_key'], $api_config['client_secret'],  $api_config['business_alias'],  $api_config['api_url']);

// dev - init
$api->setURL($api_config['api_url']);
// $api->setDebug(false);


// request
$client_id = 1;
$result = $api->getAppointments("2021-08-01", "2021-12-31", "2021-11-01");
// $status = $api->getLastStatus();


// dev - result
header('Content-Type: text/plain; charset=UTF-8');
if (!empty($result)) print_r($result);

// echo "\n\nStatus: {$status}";

$error = $api->getLastError();
if (!empty($error)) echo $error;

// echo "\n\n" . $api->readLog();
