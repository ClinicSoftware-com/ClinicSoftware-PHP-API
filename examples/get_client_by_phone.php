<?php
require 'api_config.php';
require '../lib/salon_api.php';

$api = new Salon_api($api_config['client_key'], $api_config['client_secret'],$api_config['business_alias']);

// dev - init
$api->setURL($api_config['api_url']);
$api->setDebug(true);


// request
$client_phone = '07490009777';
$result = $api->getClientByPhone($client_phone, 0);
$status = $api->getLastStatus();


// dev - result
header('Content-Type: text/plain; charset=UTF-8');
if (!empty($result)) print_r($result);

echo "\n\nStatus: {$status}";

$error = $api->getLastError();
if (!empty($error)) echo $error;

echo "\n\n" . $api->readLog();
