<?php
require 'api_config.php';
require '../lib/salon_api.php';

$api = new Salon_api($api_config['client_key'], $api_config['client_secret'],  $api_config['business_alias'],  $api_config['api_url']);

// dev - init
$api->setURL($api_config['api_url']);
$api->setDebug(false);

// Just going to get a random unsplash image to send to the server
$fileb64 = base64_encode(file_get_contents("https://source.unsplash.com/random/250x250"));

// Get a single service
$result = $api->add_document(20224, $fileb64, "test_image.png", "image/png");


// $status = $api->getLastStatus();


// dev - result
header_remove("Content-Type");
header('Content-Type: text/plain; charset=UTF-8');

if (!empty($result)) print_r($result);
// echo "\nStatus: {$status}\n";

$error = $api->getLastError();
if (!empty($error)) echo $error;

// echo "\n\n" . $api->readLog();
