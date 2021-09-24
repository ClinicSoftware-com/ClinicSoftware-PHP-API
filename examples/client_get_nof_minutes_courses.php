<?php
require 'api_config.php';
require '../lib/salon_api.php';

$api = new Salon_api($api_config['client_key'], $api_config['client_secret'],  $api_config['business_alias'],  $api_config['api_url']);

// dev - init
$api->setURL($api_config['api_url']);
$api->setDebug(true);


// request
$client_id = 1;
$date_from = null;
$date_to   = null;
$treatment = null;

$result = $api->getClientNofMinutesCourses($client_id, $date_from, $date_to, $treatment);
$status = $api->getLastStatus();


// dev - result
header('Content-Type: text/plain; charset=UTF-8');
if (!empty($result)) print_r($result);

echo "\nStatus: {$status}\n";

$error = $api->getLastError();
if (!empty($error)) echo $error;

echo "\n\n" . $api->readLog();
