<?php
require 'api_config.php';
require '../lib/salon_api.php';

$api = new Salon_api($api_config['client_key'], $api_config['client_secret'],  $api_config['business_alias'],  $api_config['api_url']);

// dev - init
$api->setURL($api_config['api_url']);
$api->setDebug(true);


// request
$barcode = '50220380';
$result = $api->getBarcodeImage($barcode);
$status = $api->getLastStatus();

echo '<img src="data:image/png;base64,' . $result . '" />';

// dev - result
//header('Content-Type: text/plain; charset=UTF-8');
print('<pre>');
if (!empty($result)) print_r($result);

echo "\n\nStatus: {$status}";

$error = $api->getLastError();
if (!empty($error)) echo $error;

echo "\n\n" . $api->readLog();
