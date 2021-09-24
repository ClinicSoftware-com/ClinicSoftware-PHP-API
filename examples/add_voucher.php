<?php
require 'api_config.php';
require '../lib/salon_api.php';

$api = new Salon_api($api_config['client_key'], $api_config['client_secret'],  $api_config['business_alias'],  $api_config['api_url']);

// dev - init
$api->setURL($api_config['api_url']);
$api->setDebug(true);


// request
$data = array();
$data['barcode']   = '9522220000062';  // valid and unique EAN8/EAN13 barcode
$data['client_id'] = 0; // optional, assign newly created voucher to client
$data['amount']    = 0; // optional, non-negative voucher value, defaults to 0

// returns voucher_id

$result = $api->addVoucher($data);
$status = $api->getLastStatus();


// dev - result
header('Content-Type: text/plain; charset=UTF-8');
if (!empty($result)) print_r($result);

echo "\nStatus: {$status}\n";

$error = $api->getLastError();
if (!empty($error)) echo $error;

echo "\n\n" . $api->readLog();
