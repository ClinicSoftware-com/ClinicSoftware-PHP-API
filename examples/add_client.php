<?php
require 'api_config.php';
require '../lib/salon_api.php';

$api = new Salon_api($api_config['client_key'], $api_config['client_secret'],  $api_config['business_alias'],  $api_config['api_url']);

// dev - init
$api->setURL($api_config['api_url']);
$api->setDebug(true);


// request
$data = array();
$data['name']            = 'API Client Name'; // mandatory
$data['surname']         = 'Client Surname';
$data['postcode']        = 'BR1 1AB';
$data['address']         = 'Client address';
$data['phone']           = '011111111';
$data['phone_work']      = '022222222';
$data['email']           = 'apiclient9@domain.com'; // mandatory, must be valid and unique
$data['password']        = '1234'; // mandatory, plain password containing at least 4 characters (not counting spaces), hashed on server side
$data['sex']             = 'm'; // gender, possible values: 'm', 'f', 'not_set', defaults to 'not_set'
$data['dob']             = '2000-01-01'; // YYYY-MM-DD format
$data['discount_value']  = 5.56; // float, global client discount
$data['notes']           = 'test notes';
$data['salon_id']        = 0;  // defaults to first salon found if not specified or 0
$data['courses_barcode'] = ''; // valid and unique EAN8/EAN13 barcode, generated automatically if not specified

// note: the newly added client will have the following attributes set to 1: user_id (MANAGER), is_confirmed, is_online_account
// mandatory fields: name, email, password
// returns client id

$result = $api->addClient($data);
$status = $api->getLastStatus();


// dev - result
header('Content-Type: text/plain; charset=UTF-8');
if (!empty($result)) print_r($result);

echo "\nStatus: {$status}\n";

$error = $api->getLastError();
if (!empty($error)) echo $error;

echo "\n\n" . $api->readLog();
