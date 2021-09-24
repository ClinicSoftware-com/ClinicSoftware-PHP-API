<?php
require 'api_config.php';
require '../lib/salon_api.php';

$api = new Salon_api($api_config['client_key'], $api_config['client_secret'],  $api_config['business_alias'],  $api_config['api_url']);

// dev - init
$api->setURL($api_config['api_url']);
$api->setDebug(true);


// request
$client_id  = 20001;
$salon_id   = 1;
$date_start = null; // YYYY-MM-DD
$date_end   = null; // YYYY-MM-DD
$last_message_id       = 0; // download starting from this id
$mark_messages_as_read = 0; // mark downloaded messages as read 

$result = $api->getClientMessagesBySalon($client_id, $salon_id, $date_start, $date_end, $last_message_id, $mark_messages_as_read);
$status = $api->getLastStatus();


// dev - result
header('Content-Type: text/plain; charset=UTF-8');
if (!empty($result)) print_r($result);

echo "\n\nStatus: {$status}";

$error = $api->getLastError();
if (!empty($error)) echo $error;

echo "\n\n" . $api->readLog();
