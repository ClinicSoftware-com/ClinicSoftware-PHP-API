# ClinicSoftware PHP API Documentation

## Table of Contents

1. [Overview](#overview)
2. [Installation and Setup](#installation-and-setup)
3. [Authentication](#authentication)
4. [Core API Class](#core-api-class)
5. [Client Management](#client-management)
6. [Appointment Management](#appointment-management)
7. [Service Management](#service-management)
8. [Document Management](#document-management)
9. [Voucher Management](#voucher-management)
10. [Messaging](#messaging)
11. [Reporting and History](#reporting-and-history)
12. [Utility Functions](#utility-functions)
13. [Error Handling](#error-handling)
14. [Examples](#examples)

---

## Overview

The ClinicSoftware PHP API provides convenient access to the ClinicSoftware platform from PHP applications. This library offers a comprehensive set of methods to manage clients, appointments, services, documents, vouchers, and more.

### Requirements

- PHP 7.4 or later (recommended)
- cURL extension
- JSON extension
- mbstring extension

### Dependencies

The library requires the following PHP extensions:
- `curl` - for HTTP requests
- `json` - for JSON parsing
- `mbstring` - for string manipulation

---

## Installation and Setup

### 1. Clone the Repository

```bash
git clone https://github.com/ClinicSoftware-com/ClinicSoftware-PHP-API
cd ClinicSoftware-PHP-API
```

### 2. Configuration

Create a configuration file based on the example:

```php
<?php
// api_config.php
$api_config = [];
$api_config['business_alias'] = 'your_business_alias';
$api_config['client_key']     = 'your_client_key';
$api_config['client_secret']  = 'your_client_secret';
$api_config['api_url']        = 'https://serverX.clinicsoftware.com/api_business';
?>
```

Replace `X` with your server number (e.g., `server7.clinicsoftware.com`).

### 3. Basic Usage

```php
<?php
require_once 'api_config.php';
require_once 'lib/salon_api.php';

// Initialize the API
$api = new Salon_api(
    $api_config['client_key'],
    $api_config['client_secret'],
    $api_config['business_alias'],
    $api_config['api_url']
);

// Optional: Enable debug mode
$api->setDebug(true);

// Make API calls
$result = $api->getSalons();
?>
```

---

## Authentication

The API uses SHA-256 hash-based authentication. Authentication is handled automatically by the library:

- **Client Key**: Your API client key
- **Client Secret**: Your API client secret
- **Business Alias**: Your business identifier
- **API URL**: Server-specific endpoint

---

## Core API Class

### Class: `Salon_api`

The main class that provides access to all API endpoints.

#### Constructor

```php
public function __construct($clientKey, $clientSecret, $businessAlias = '', $apiURL = '')
```

**Parameters:**
- `$clientKey` (string): Your API client key
- `$clientSecret` (string): Your API client secret
- `$businessAlias` (string): Your business alias
- `$apiURL` (string): API endpoint URL

#### Core Methods

##### `setDebug($debug)`

Enable or disable debug mode.

```php
$api->setDebug(true);  // Enable debug logging
$api->setDebug(false); // Disable debug logging
```

##### `setURL($url)`

Set the API endpoint URL.

```php
$api->setURL('https://server7.clinicsoftware.com/api_business');
```

##### `getLastResult()`

Get the last API call result.

```php
$result = $api->getLastResult();
```

##### `getLastStatus()`

Get the last API call status.

```php
$status = $api->getLastStatus(); // Returns 'ok' or 'error'
```

##### `getLastError()`

Get the last error message.

```php
$error = $api->getLastError();
if (!empty($error)) {
    echo "Error: " . $error;
}
```

---

## Client Management

### Get Clients

#### `getClients($last_modified, $limit = 10, $offset = 0, $whitelist = null)`

Retrieve a list of clients.

**Parameters:**
- `$last_modified` (string): ISO 8601 date string for filtering
- `$limit` (int): Maximum number of results (default: 10, max: 1000)
- `$offset` (int): Offset for pagination
- `$whitelist` (array|null): Array of client IDs to filter

**Example:**
```php
// Get clients modified in the last month
$clients = $api->getClients(date("c", strtotime("-1 month")), 50, 0);

// Get specific clients
$specific_clients = $api->getClients(
    date("c", strtotime("-1 month")), 
    50, 
    0, 
    [45592, 45581, 45580]
);
```

### Get Client by ID

#### `getClientByID($client_id, $is_online_account = 1)`

Get a client by their ID.

**Parameters:**
- `$client_id` (int): Client ID
- `$is_online_account` (int): Online account flag (1 = online, 0 = offline)

**Example:**
```php
$client = $api->getClientByID(12345);
```

### Get Client by Email

#### `getClientByEmail($client_email, $is_online_account = 1)`

Get a client by their email address.

**Example:**
```php
$client = $api->getClientByEmail('client@example.com');
```

### Get Client by Email and Password

#### `getClientByEmailAndPassword($client_email, $client_password, $is_online_account = 1)`

Authenticate and get a client by email and password.

**Example:**
```php
$client = $api->getClientByEmailAndPassword('client@example.com', 'password123');
```

### Get Client by Phone

#### `getClientByPhone($client_phone, $is_online_account = 1)`

Get a client by their phone number.

**Example:**
```php
$client = $api->getClientByPhone('1234567890');
```

### Get Client by Name

#### `getClientByName($client_name, $is_online_account = 1)`

Get a client by their name.

**Example:**
```php
$client = $api->getClientByName('John Doe');
```

### Add Client

#### `addClient($data)`

Add a new client to the system.

**Parameters:**
- `$data` (array): Client data array

**Required Fields:**
- `name` (string): Client name
- `email` (string): Valid and unique email address
- `password` (string): Plain password (min 4 characters)

**Optional Fields:**
- `surname` (string): Client surname
- `postcode` (string): Postal code
- `address` (string): Client address
- `phone` (string): Phone number
- `phone_work` (string): Work phone number
- `sex` (string): Gender ('m', 'f', 'not_set')
- `dob` (string): Date of birth (YYYY-MM-DD)
- `discount_value` (float): Global client discount
- `notes` (string): Client notes
- `salon_id` (int): Salon ID (defaults to first salon)
- `courses_barcode` (string): Unique barcode

**Example:**
```php
$client_data = [
    'name' => 'John',
    'surname' => 'Doe',
    'email' => 'john.doe@example.com',
    'password' => '1234',
    'phone' => '1234567890',
    'postcode' => 'SW1A 1AA',
    'address' => '123 Main Street',
    'sex' => 'm',
    'dob' => '1990-01-01',
    'discount_value' => 5.0,
    'notes' => 'VIP client',
    'salon_id' => 1
];

$result = $api->addClient($client_data);
```

### Update Client

#### `updateClient($client_id, $data)`

Update an existing client's information.

**Example:**
```php
$update_data = [
    'phone' => '0987654321',
    'address' => '456 New Street',
    'notes' => 'Updated client information'
];

$result = $api->updateClient(12345, $update_data);
```

### Delete Client

#### `deleteClient($client_id)`

Delete a client from the system.

**Example:**
```php
$result = $api->deleteClient(12345);
```

### Newsletter Management

#### `subscribeClientToNewsletter($client_id)`

Subscribe a client to the newsletter.

**Example:**
```php
$result = $api->subscribeClientToNewsletter(12345);
```

#### `unsubscribeClientFromNewsletter($client_id)`

Unsubscribe a client from the newsletter.

**Example:**
```php
$result = $api->unsubscribeClientFromNewsletter(12345);
```

---

## Appointment Management

### AppointmentObject Class

The `AppointmentObject` class represents an appointment structure.

#### Constructor

```php
$appointment = new AppointmentObject($salon_id);
```

#### Properties

- `$salon_id` (int, required): Salon ID
- `$title` (string, optional): Appointment title
- `$notes` (string, optional): Appointment notes
- `$datetime` (DateTime, required): Appointment date and time
- `$duration` (int, required): Duration in minutes
- `$staffID` (int, required): Staff member ID
- `$clientID` (int, required): Client ID
- `$status` (string, required): Appointment status (default: "booked")
- `$items` (array, required): Array of service items
- `$booking_type_id` (int, optional): Booking type ID
- `$booking_requested` (string, optional): Originally requested staff
- `$marketing_source_id` (int, optional): Marketing source ID

### Get Appointments

#### `getAppointments($from = "2021-11-01", $to = "2021-12-20", $last_modified = null)`

Get appointments within a date range.

**Parameters:**
- `$from` (string): Start date (Y-m-d format)
- `$to` (string): End date (Y-m-d format)
- `$last_modified` (string|null): Last modified date filter

**Example:**
```php
$appointments = $api->getAppointments('2023-01-01', '2023-01-31');
```

### Get Client Appointments

#### `getClientAppointments($client_id)`

Get all appointments for a specific client.

**Example:**
```php
$appointments = $api->getClientAppointments(12345);
```

### Add Appointment

#### `addAppointment(AppointmentObject $appointment)`

Add a new appointment to the system.

**Example:**
```php
$appointment = new AppointmentObject(1); // Salon ID = 1

$appointment->clientID = 12345;
$appointment->staffID = 4;
$appointment->duration = 30;
$appointment->title = "Hair Cut";
$appointment->status = "booked";
$appointment->datetime = DateTime::createFromFormat("Y-m-d H:i:s", "2023-12-25 14:30:00");
$appointment->items = [[
    "item_id" => 2781,
    "is_free" => false
]];

$result = $api->addAppointment($appointment);
```

### Appointment Availability

#### `appointment_availability(DateTime $date, int $duration, array $items)`

Check availability for services at a specific date and time.

**Parameters:**
- `$date` (DateTime): Appointment date and time
- `$duration` (int): Duration in minutes (min: 5, max: 1440)
- `$items` (array): Array of service IDs

**Example:**
```php
$date = new DateTime('2023-12-25 14:30:00');
$duration = 30;
$services = [2781, 2782];

$availability = $api->appointment_availability($date, $duration, $services);
```

### Cancel Appointment

#### `cancelAppointment(int $appointmentID, int $staffID = 0)`

Cancel an appointment.

**Parameters:**
- `$appointmentID` (int): Appointment ID
- `$staffID` (int): Staff ID performing the cancellation (0 = client)

**Example:**
```php
$result = $api->cancelAppointment(12345, 4);
```

### Get Appointment Statuses

#### `appointment_get_statuses()`

Get all available appointment statuses.

**Example:**
```php
$statuses = $api->appointment_get_statuses();
```

---

## Service Management

### Get Services

#### `get_services($id = null, $last_modified = null, int $limit = 10, int $offset = 0)`

Get services from the system.

**Parameters:**
- `$id` (int|array|null): Service ID(s) to filter
- `$last_modified` (string|DateTime|null): Last modified date filter
- `$limit` (int): Maximum number of results (default: 10)
- `$offset` (int): Offset for pagination

**Example:**
```php
// Get all services
$services = $api->get_services();

// Get specific services
$services = $api->get_services([2781, 2782]);

// Get services modified since a date
$services = $api->get_services(null, "2023-01-01", 100, 0);
```

**Response Format:**
```php
[
    [
        "id" => 1829,
        "title" => "Hair Cut",
        "description" => "Professional hair cutting service",
        "category" => "Hair Services",
        "category_id" => 41,
        "section" => "Beauty",
        "section_id" => 3,
        "location" => "London",
        "location_id" => 1,
        "online_description" => "Professional hair cutting service",
        "barcode" => "00897654354",
        "date_added" => "2021-09-14T13:22:03+01:00",
        "date_modified" => "2021-09-24T08:08:23+01:00"
    ]
]
```

---

## Document Management

### Add Document

#### `add_document(int $client_id, string $base64, string $document_name, string $mime_type, bool $automatically_rename = true)`

Add a document to a client's file.

**Parameters:**
- `$client_id` (int): Client ID
- `$base64` (string): Base64-encoded document content
- `$document_name` (string): Document name with extension
- `$mime_type` (string): MIME type (e.g., 'image/png', 'application/pdf')
- `$automatically_rename` (bool): Auto-rename if file exists

**Example:**
```php
$base64_content = base64_encode(file_get_contents('document.pdf'));
$result = $api->add_document(12345, $base64_content, 'test_document.pdf', 'application/pdf', true);
```

### Get Documents

#### `get_documents(int $client_id)`

Get all documents for a client.

**Example:**
```php
$documents = $api->get_documents(12345);
```

### Download Document

#### `download_documents(int $client_id, string $filePath)`

Download a specific document.

**Parameters:**
- `$client_id` (int): Client ID
- `$filePath` (string): Path to the document

**Example:**
```php
$document_data = $api->download_documents(12345, 'documents/test_document.pdf');
$file_content = base64_decode($document_data['data']);
```

---

## Voucher Management

### Get Voucher

#### `getVoucherByBarcode($voucher_barcode)`

Get voucher information by barcode.

**Example:**
```php
$voucher = $api->getVoucherByBarcode('1234567890');
```

### Add Voucher

#### `addVoucher($data)`

Add a new voucher to the system.

**Example:**
```php
$voucher_data = [
    'barcode' => '1234567890',
    'value' => 50.00,
    'type' => 'monetary',
    'expires' => '2023-12-31'
];

$result = $api->addVoucher($voucher_data);
```

### Update Voucher

#### `updateVoucherByBarcode($voucher_barcode, $data)`

Update voucher information.

**Example:**
```php
$update_data = [
    'value' => 75.00,
    'expires' => '2024-12-31'
];

$result = $api->updateVoucherByBarcode('1234567890', $update_data);
```

### Delete Voucher

#### `deleteVoucherByBarcode($voucher_barcode)`

Delete a voucher.

**Example:**
```php
$result = $api->deleteVoucherByBarcode('1234567890');
```

### Assign Voucher to Client

#### `assignVoucherBarcodeToClient($voucher_barcode, $client_id)`

Assign a voucher to a specific client.

**Example:**
```php
$result = $api->assignVoucherBarcodeToClient('1234567890', 12345);
```

### Get Client Vouchers

#### `getClientVouchers($client_id)`

Get all vouchers for a specific client.

**Example:**
```php
$vouchers = $api->getClientVouchers(12345);
```

---

## Messaging

### Get Client Messages

#### `getClientMessagesBySalon($client_id, $salon_id, $date_start = null, $date_end = null, $last_message_id = 0, $mark_messages_as_read = 0)`

Get messages for a client at a specific salon.

**Parameters:**
- `$client_id` (int): Client ID
- `$salon_id` (int): Salon ID
- `$date_start` (string|null): Start date filter
- `$date_end` (string|null): End date filter
- `$last_message_id` (int): Last message ID for pagination
- `$mark_messages_as_read` (int): Mark messages as read (1 = yes, 0 = no)

**Example:**
```php
$messages = $api->getClientMessagesBySalon(12345, 1);
```

### Add Client Message

#### `addClientMessage($client_id, $salon_id, $message)`

Add a message from a client.

**Example:**
```php
$result = $api->addClientMessage(12345, 1, "Hello, I'd like to reschedule my appointment.");
```

### Add Staff Message

#### `addStaffMessage($client_id, $salon_id, $staff_id, $message)`

Add a message from a staff member.

**Example:**
```php
$result = $api->addStaffMessage(12345, 1, 4, "Your appointment has been confirmed.");
```

### Get Unread Message Count

#### `getClientNofUnreadMessagesGlobal($client_id, $last_message_id = 0)`

Get the number of unread messages globally for a client.

**Example:**
```php
$unread_count = $api->getClientNofUnreadMessagesGlobal(12345);
```

#### `getClientNofUnreadMessagesBySalon($client_id, $salon_id, $last_message_id = 0)`

Get the number of unread messages for a client at a specific salon.

**Example:**
```php
$unread_count = $api->getClientNofUnreadMessagesBySalon(12345, 1);
```

---

## Reporting and History

### Client Balance

#### `getClientBalance($client_id)`

Get client's current balance.

**Example:**
```php
$balance = $api->getClientBalance(12345);
```

#### `getClientBalanceHistory($client_id)`

Get client's balance history.

**Example:**
```php
$balance_history = $api->getClientBalanceHistory(12345);
```

### Client Receipts

#### `getClientReceipts($client_id)`

Get all receipts for a client.

**Example:**
```php
$receipts = $api->getClientReceipts(12345);
```

#### `emailReceiptToClient($bill_id)`

Email a receipt to a client.

**Example:**
```php
$result = $api->emailReceiptToClient(67890);
```

### Treatment Records

#### `getClientTreatmentRecords($client_id)`

Get treatment records for a client.

**Example:**
```php
$records = $api->getClientTreatmentRecords(12345);
```

### Course History

#### `getClientNofSessionCourses($client_id, $date_from = null, $date_to = null, $treatment = null)`

Get the number of session courses for a client.

**Example:**
```php
$session_courses = $api->getClientNofSessionCourses(12345, '2023-01-01', '2023-12-31');
```

#### `getClientSessionCoursesPag($client_id, $date_from = null, $date_to = null, $treatment = null, $offset = 0, $row_count = 0)`

Get paginated session courses for a client.

**Example:**
```php
$session_courses = $api->getClientSessionCoursesPag(12345, '2023-01-01', '2023-12-31', null, 0, 10);
```

### Tracking History

#### `getClientTrackSessionsHistory($client_id)`

Get session tracking history for a client.

**Example:**
```php
$track_history = $api->getClientTrackSessionsHistory(12345);
```

#### `getClientTrackMinutesHistory($client_id)`

Get minutes tracking history for a client.

**Example:**
```php
$minutes_history = $api->getClientTrackMinutesHistory(12345);
```

---

## Utility Functions

### Get Salons

#### `getSalons()`

Get all salons in the system.

**Example:**
```php
$salons = $api->getSalons();
```

### Get Resources

#### `getResources()`

Get API client resources.

**Example:**
```php
$resources = $api->getResources();
```

### Get Barcode Image

#### `getBarcodeImage($barcode)`

Get a barcode image.

**Example:**
```php
$barcode_image = $api->getBarcodeImage('1234567890');
```

### Get Staff Shifts

#### `getShifts(DateTime $dateFrom, DateTime $dateTo, ?int $staff_id = null)`

Get staff shifts within a date range.

**Parameters:**
- `$dateFrom` (DateTime): Start date
- `$dateTo` (DateTime): End date (max 30 days from start)
- `$staff_id` (int|null): Optional staff ID filter

**Example:**
```php
$date_from = new DateTime('2023-12-01');
$date_to = new DateTime('2023-12-31');
$shifts = $api->getShifts($date_from, $date_to, 4);
```

### Get Client Relationships

#### `getRelationship(int $client_id)`

Get relationships for a client.

**Example:**
```php
$relationships = $api->getRelationship(12345);
```

### Lead Management

#### `getLeadByPhone($client_phone)`

Get lead information by phone number.

**Example:**
```php
$lead = $api->getLeadByPhone('1234567890');
```

#### `addLead($data)`

Add a new lead to the system.

**Example:**
```php
$lead_data = [
    'name' => 'Jane Smith',
    'email' => 'jane.smith@example.com',
    'phone' => '0987654321',
    'source' => 'website',
    'notes' => 'Interested in hair services'
];

$result = $api->addLead($lead_data);
```

### Online Booking

#### `reqOnlineBookingAuthToken($client_id, $expires = 120)`

Request an online booking authentication token.

**Parameters:**
- `$client_id` (int): Client ID
- `$expires` (int): Token expiration time in minutes

**Example:**
```php
$token = $api->reqOnlineBookingAuthToken(12345, 60);
```

### Consent Forms

#### `getClientSignedConsentFormsList($client_id)`

Get signed consent forms for a client.

**Example:**
```php
$consent_forms = $api->getClientSignedConsentFormsList(12345);
```

#### `getClientConsentFormPDF($client_consent_form_id)`

Get a signed consent form PDF.

**Example:**
```php
$pdf_data = $api->getClientConsentFormPDF(67890);
```

---

## Error Handling

### Standard Error Response

All API methods return `null` on error. Use the following methods to check for errors:

```php
$result = $api->getClientByID(12345);

if ($result === null) {
    $error = $api->getLastError();
    if (!empty($error)) {
        echo "Error: " . $error;
    }
}
```

### Status Checking

```php
$result = $api->getSalons();
$status = $api->getLastStatus();

if ($status === 'error') {
    $error = $api->getLastError();
    echo "API Error: " . $error;
} else {
    // Process successful result
    foreach ($result as $salon) {
        echo $salon['name'] . "\n";
    }
}
```

### Debug Mode

Enable debug mode to log all API requests and responses:

```php
$api->setDebug(true);

// Make API calls
$result = $api->getSalons();

// Read debug log
$log = $api->readLog();
echo $log;
```

---

## Examples

### Complete Client Management Example

```php
<?php
require_once 'api_config.php';
require_once 'lib/salon_api.php';

$api = new Salon_api(
    $api_config['client_key'],
    $api_config['client_secret'],
    $api_config['business_alias'],
    $api_config['api_url']
);

// Add a new client
$client_data = [
    'name' => 'John',
    'surname' => 'Doe',
    'email' => 'john.doe@example.com',
    'password' => 'secure123',
    'phone' => '1234567890',
    'postcode' => 'SW1A 1AA',
    'address' => '123 Main Street',
    'sex' => 'm',
    'dob' => '1990-01-01'
];

$new_client = $api->addClient($client_data);
if ($new_client === null) {
    echo "Error adding client: " . $api->getLastError();
    exit;
}

$client_id = $new_client['client_id'];
echo "Client added with ID: " . $client_id . "\n";

// Get client information
$client = $api->getClientByID($client_id);
if ($client) {
    echo "Client name: " . $client['name'] . " " . $client['surname'] . "\n";
}

// Update client information
$update_data = [
    'phone' => '0987654321',
    'notes' => 'Updated via API'
];

$result = $api->updateClient($client_id, $update_data);
if ($result) {
    echo "Client updated successfully\n";
}
?>
```

### Complete Appointment Booking Example

```php
<?php
require_once 'api_config.php';
require_once 'lib/salon_api.php';

$api = new Salon_api(
    $api_config['client_key'],
    $api_config['client_secret'],
    $api_config['business_alias'],
    $api_config['api_url']
);

// Check availability
$date = new DateTime('2023-12-25 14:30:00');
$duration = 30;
$services = [2781]; // Hair cut service

$availability = $api->appointment_availability($date, $duration, $services);
if ($availability === null) {
    echo "Error checking availability: " . $api->getLastError();
    exit;
}

// Create appointment
$appointment = new AppointmentObject(1); // Salon ID = 1
$appointment->clientID = 12345;
$appointment->staffID = 4;
$appointment->duration = 30;
$appointment->title = "Hair Cut Appointment";
$appointment->status = "booked";
$appointment->datetime = $date;
$appointment->items = [[
    "item_id" => 2781,
    "is_free" => false
]];

$result = $api->addAppointment($appointment);
if ($result === null) {
    echo "Error booking appointment: " . $api->getLastError();
    exit;
}

echo "Appointment booked successfully!\n";
?>
```

### Service Listing Example

```php
<?php
require_once 'api_config.php';
require_once 'lib/salon_api.php';

$api = new Salon_api(
    $api_config['client_key'],
    $api_config['client_secret'],
    $api_config['business_alias'],
    $api_config['api_url']
);

// Get all services
$services = $api->get_services(null, null, 100, 0);
if ($services === null) {
    echo "Error retrieving services: " . $api->getLastError();
    exit;
}

echo "Available Services:\n";
echo "==================\n";

foreach ($services as $service) {
    echo sprintf(
        "ID: %d | %s - %s - %s\n",
        $service['id'],
        $service['section'],
        $service['category'],
        $service['title']
    );
}
?>
```

### Document Management Example

```php
<?php
require_once 'api_config.php';
require_once 'lib/salon_api.php';

$api = new Salon_api(
    $api_config['client_key'],
    $api_config['client_secret'],
    $api_config['business_alias'],
    $api_config['api_url']
);

$client_id = 12345;

// Upload a document
$file_path = 'path/to/document.pdf';
$file_content = file_get_contents($file_path);
$base64_content = base64_encode($file_content);

$result = $api->add_document(
    $client_id,
    $base64_content,
    'medical_report.pdf',
    'application/pdf',
    true
);

if ($result === null) {
    echo "Error uploading document: " . $api->getLastError();
    exit;
}

echo "Document uploaded successfully\n";

// Get all documents for the client
$documents = $api->get_documents($client_id);
if ($documents) {
    echo "Client documents:\n";
    foreach ($documents['files'] as $file) {
        echo "- " . $file . "\n";
    }
}
?>
```

---

## Best Practices

### 1. Error Handling

Always check for errors after API calls:

```php
$result = $api->someMethod();
if ($result === null) {
    $error = $api->getLastError();
    if (!empty($error)) {
        // Handle error appropriately
        error_log("API Error: " . $error);
    }
}
```

### 2. Rate Limiting

Implement appropriate delays between API calls to avoid overwhelming the server:

```php
foreach ($clients as $client) {
    $result = $api->updateClient($client['id'], $update_data);
    usleep(100000); // 100ms delay
}
```

### 3. Data Validation

Validate input data before sending to the API:

```php
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

if (!validateEmail($client_data['email'])) {
    echo "Invalid email address";
    exit;
}
```

### 4. Secure Configuration

Store API credentials securely and never commit them to version control:

```php
// Use environment variables or secure configuration files
$api_config = [
    'client_key' => getenv('CLINIC_API_KEY'),
    'client_secret' => getenv('CLINIC_API_SECRET'),
    'business_alias' => getenv('CLINIC_BUSINESS_ALIAS'),
    'api_url' => getenv('CLINIC_API_URL')
];
```

### 5. Logging

Use debug mode during development but disable in production:

```php
$api->setDebug(getenv('APP_DEBUG') === 'true');
```

---

## Support

For additional support and documentation, visit:
- [GitHub Repository](https://github.com/ClinicSoftware-com/ClinicSoftware-PHP-API)
- [API Wiki](https://github.com/ClinicSoftware-com/ClinicSoftware-PHP-API/wiki)
- Contact your ClinicSoftware account manager for API keys and server information

---

## License

This library is provided under the terms specified in the LICENSE file.

---

*Last Updated: December 2023*