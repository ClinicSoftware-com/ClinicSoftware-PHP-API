<?php
require 'api_config.php';
require '../lib/salon_api.php';

$api = new Salon_api("cd85e453cccbbf67bb0430a9d70f4f23", "fb4686b5efa01837918818ae4de00ad1", "demo2");


// dev - init
$api->setURL("https://secure.clinicsoftware.com/api_business");
$api->setDebug(true);


// request
$result = $api->getLeadByPhone("07781268423");
$status = $api->getLastStatus();


// dev - result
header('Content-Type: text/plain; charset=UTF-8');
if (!empty($result)) print_r($result);

echo "\nStatus: {$status}\n";

$error = $api->getLastError();
if (!empty($error)) echo $error;

echo "\n\n" . $api->readLog();
