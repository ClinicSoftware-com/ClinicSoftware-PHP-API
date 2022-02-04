<?php
require 'api_config.php';
require '../lib/salon_api.php';

$api = new Salon_api($api_config['client_key'], $api_config['client_secret'],  $api_config['business_alias'],  $api_config['api_url']);

// dev - init
$api->setURL($api_config['api_url']);
// $api->setDebug(false);

// Generate the appointment
$appointment = new AppointmentObject(1);

// Basic needs
// Set the id of the client that will have the appointment
$appointment->clientID = 45555;
// 30 Minutes Duration
$appointment->duration = 30;
// Set the items that the client is going to book for
$appointment->items = [[
    "item_id" => 2781,
]];
// Setting a title as an example
$appointment->title = "Example API Booking";
$appointment->status = "booked";


// Set the date/time
$target_date = strtotime("+24 hours");
$appointment->datetime = \DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s", $target_date));
// Set the time to something nicer
$appointment->datetime->setTime(14, 30, 0, 0);
// Set the attending staff
$appointment->staffID = 4;



// request
$result = $api->addAppointment($appointment);
// $status = $api->getLastStatus();


// dev - result
header('Content-Type: text/plain; charset=UTF-8');
if (!empty($result)) print_r($result);

// echo "\n\nStatus: {$status}";

$error = $api->getLastError();
if (!empty($error)) echo $error;

// echo "\n\n" . $api->readLog();
