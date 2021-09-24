<?php
require 'api_config.php';
require '../lib/salon_api.php';

$api = new Salon_api($api_config['client_key'], $api_config['client_secret'],  $api_config['business_alias'],  $api_config['api_url']);

// dev - init
$api->setURL($api_config['api_url']);
$api->setDebug(true);


// request
$client_id = 20052;
$data = array();
$data['name']            = 'API Client Name Updated'; // mandatory
$data['surname']         = 'Client Surname';
$data['postcode']        = 'BR1 1AB';
$data['address']         = 'Client address';
$data['phone']           = '011111111';
$data['phone_work']      = '022222222';

// set this field if you need to change the email address
$data['email']           = 'apiclient9@domain.com'; // must be valid and unique

// set this field if you need to change the password
$data['password']        = '1234';  // plain password containing at least 4 characters (not counting spaces), hashed on server side

$data['sex']             = 'm'; // gender, possible values: 'm', 'f', 'not_set'
$data['dob']             = '2000-01-01'; // YYYY-MM-DD format
$data['discount_value']  = 5.56; // float, global client discount
$data['notes']           = 'test notes';
$data['salon_id']        = 0;  // if value > 0 then salon id is verified
$data['courses_barcode'] = ''; // valid and unique EAN8/EAN13 barcode

// returns null, check $api->getLastStatus() == 'ok' to verify if operation succeeded

$result = $api->updateClient($client_id, $data);
$status = $api->getLastStatus();


// dev - result
header('Content-Type: text/plain; charset=UTF-8');
if (!empty($result)) print_r($result);

echo "\nStatus: {$status}\n";

$error = $api->getLastError();
if (!empty($error)) echo $error;

echo "\n\n" . $api->readLog();
