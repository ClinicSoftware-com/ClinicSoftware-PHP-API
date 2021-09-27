<?php

require 'api_config.php';
require '../lib/salon_api.php';

$api = new Salon_api($api_config['client_key'], $api_config['client_secret'], $api_config['business_alias'], $api_config['api_url']);

// dev - init
$api->setURL($api_config['api_url']);
$api->setDebug(true);

// Simply importing some dummy data from a free API so that we can generate random lead details.
$dummy_data = json_decode(file_get_contents("https://randomuser.me/api/"), true)['results'][0];

// request
$data = [];

$data['name']            = $dummy_data['name']['first']; // mandatory
$data['surname']         = $dummy_data['name']['last'];
$data['postcode']        = $dummy_data['location']['postcode'];
$data['address']         = $dummy_data['location']['street']['name'];
$data['phone']           = '011111111' . random_int(10, 1000);
$data['phone_work']      = '022222222' . random_int(10, 1000);
$data['email']           = $dummy_data['email']; // mandatory, must be valid and unique
$data['password']        = '1231424124124124124124124' . random_int(10, 1000); // mandatory, plain password containing at least 4 characters (not counting spaces), hashed on server side
$data['sex']             = 'm'; // gender, possible values: 'm', 'f', 'not_set', defaults to 'not_set'
$data['dob']             = $dummy_data['dob']['date']; // YYYY-MM-DD format
$data['discount_value']  = 5.56; // float, global client discount
$data['notes']           = 'test notes';
$data['salon_id']        = 0;  // defaults to first location found if not specified or 0
$data['courses_barcode'] = ''; // valid and unique EAN8/EAN13 barcode, generated automatically if not specified
$data['marketing_list_name'] = 'download_guide';
$data['marketing_list_name'] = 'Virtual Consultations';
$data['description']         = 'This message can be in a form provided by a lead or programatically added based on the form\'s location or any other method, it will be added into the lead\'s description';


// note: the newly added client will have the following attributes set to 1: user_id (MANAGER), is_confirmed, is_online_account
// mandatory fields: name, email, password
// returns client id

$result = $api->addLead($data);
$status = $api->getLastStatus();


// dev - result
header('Content-Type: text/plain; charset=UTF-8');
if (!empty($result)) print_r($result);

echo "\nStatus: {$status}\n";

$error = $api->getLastError();
if (!empty($error)) echo $error;

echo "\n\n" . $api->readLog();

