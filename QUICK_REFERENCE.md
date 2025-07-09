# ClinicSoftware PHP API - Quick Reference

## Setup
```php
require_once 'api_config.php';
require_once 'lib/salon_api.php';

$api = new Salon_api($client_key, $client_secret, $business_alias, $api_url);
```

## Core Methods

| Method | Description |
|--------|-------------|
| `setDebug($debug)` | Enable/disable debug mode |
| `setURL($url)` | Set API endpoint URL |
| `getLastResult()` | Get last API call result |
| `getLastStatus()` | Get last API call status |
| `getLastError()` | Get last error message |
| `readLog()` | Read debug log |

## Client Management

| Method | Parameters | Description |
|--------|------------|-------------|
| `getClients($last_modified, $limit = 10, $offset = 0, $whitelist = null)` | string, int, int, array\|null | Get list of clients |
| `getClientByID($client_id, $is_online_account = 1)` | int, int | Get client by ID |
| `getClientByEmail($client_email, $is_online_account = 1)` | string, int | Get client by email |
| `getClientByEmailAndPassword($client_email, $client_password, $is_online_account = 1)` | string, string, int | Authenticate client |
| `getClientByPhone($client_phone, $is_online_account = 1)` | string, int | Get client by phone |
| `getClientByName($client_name, $is_online_account = 1)` | string, int | Get client by name |
| `addClient($data)` | array | Add new client |
| `updateClient($client_id, $data)` | int, array | Update client |
| `deleteClient($client_id)` | int | Delete client |
| `subscribeClientToNewsletter($client_id)` | int | Subscribe to newsletter |
| `unsubscribeClientFromNewsletter($client_id)` | int | Unsubscribe from newsletter |

## Appointment Management

| Method | Parameters | Description |
|--------|------------|-------------|
| `getAppointments($from = "2021-11-01", $to = "2021-12-20", $last_modified = null)` | string, string, string\|null | Get appointments in date range |
| `getClientAppointments($client_id)` | int | Get client's appointments |
| `addAppointment(AppointmentObject $appointment)` | AppointmentObject | Add new appointment |
| `appointment_availability(DateTime $date, int $duration, array $items)` | DateTime, int, array | Check availability |
| `cancelAppointment(int $appointmentID, int $staffID = 0)` | int, int | Cancel appointment |
| `appointment_get_statuses()` | - | Get appointment statuses |

### AppointmentObject Properties
```php
$appointment = new AppointmentObject($salon_id);
$appointment->clientID = 12345;         // Required
$appointment->staffID = 4;              // Required
$appointment->duration = 30;            // Required (minutes)
$appointment->datetime = new DateTime(); // Required
$appointment->status = "booked";        // Required
$appointment->items = [["item_id" => 2781]]; // Required
$appointment->title = "Appointment Title";   // Optional
$appointment->notes = "Notes";               // Optional
```

## Service Management

| Method | Parameters | Description |
|--------|------------|-------------|
| `get_services($id = null, $last_modified = null, int $limit = 10, int $offset = 0)` | int\|array\|null, string\|DateTime\|null, int, int | Get services |

## Document Management

| Method | Parameters | Description |
|--------|------------|-------------|
| `add_document(int $client_id, string $base64, string $document_name, string $mime_type, bool $automatically_rename = true)` | int, string, string, string, bool | Add document |
| `get_documents(int $client_id)` | int | Get client documents |
| `download_documents(int $client_id, string $filePath)` | int, string | Download document |

## Voucher Management

| Method | Parameters | Description |
|--------|------------|-------------|
| `getVoucherByBarcode($voucher_barcode)` | string | Get voucher by barcode |
| `addVoucher($data)` | array | Add new voucher |
| `updateVoucherByBarcode($voucher_barcode, $data)` | string, array | Update voucher |
| `deleteVoucherByBarcode($voucher_barcode)` | string | Delete voucher |
| `assignVoucherBarcodeToClient($voucher_barcode, $client_id)` | string, int | Assign voucher to client |
| `getClientVouchers($client_id)` | int | Get client's vouchers |

## Messaging

| Method | Parameters | Description |
|--------|------------|-------------|
| `getClientMessagesBySalon($client_id, $salon_id, $date_start = null, $date_end = null, $last_message_id = 0, $mark_messages_as_read = 0)` | int, int, string\|null, string\|null, int, int | Get client messages |
| `addClientMessage($client_id, $salon_id, $message)` | int, int, string | Add client message |
| `addStaffMessage($client_id, $salon_id, $staff_id, $message)` | int, int, int, string | Add staff message |
| `getClientNofUnreadMessagesGlobal($client_id, $last_message_id = 0)` | int, int | Get global unread count |
| `getClientNofUnreadMessagesBySalon($client_id, $salon_id, $last_message_id = 0)` | int, int, int | Get salon unread count |

## Reporting & History

| Method | Parameters | Description |
|--------|------------|-------------|
| `getClientBalance($client_id)` | int | Get client balance |
| `getClientBalanceHistory($client_id)` | int | Get balance history |
| `getClientReceipts($client_id)` | int | Get client receipts |
| `emailReceiptToClient($bill_id)` | int | Email receipt to client |
| `getClientTreatmentRecords($client_id)` | int | Get treatment records |
| `getClientNofSessionCourses($client_id, $date_from = null, $date_to = null, $treatment = null)` | int, string\|null, string\|null, string\|null | Get session courses count |
| `getClientSessionCoursesPag($client_id, $date_from = null, $date_to = null, $treatment = null, $offset = 0, $row_count = 0)` | int, string\|null, string\|null, string\|null, int, int | Get paginated session courses |
| `getClientNofMinutesCourses($client_id, $date_from = null, $date_to = null, $treatment = null)` | int, string\|null, string\|null, string\|null | Get minutes courses count |
| `getClientMinutesCoursesPag($client_id, $date_from = null, $date_to = null, $treatment = null, $offset = 0, $row_count = 0)` | int, string\|null, string\|null, string\|null, int, int | Get paginated minutes courses |
| `getClientTrackSessionsHistory($client_id)` | int | Get session tracking history |
| `getClientTrackMinutesHistory($client_id)` | int | Get minutes tracking history |
| `getClientCoursesInstallmentsHistory($client_id)` | int | Get courses installments history |
| `getClientPowerPlatesHistory($client_id)` | int | Get power plates history |
| `getClientTanningHistory($client_id)` | int | Get tanning history |

## Utility Functions

| Method | Parameters | Description |
|--------|------------|-------------|
| `getSalons()` | - | Get all salons |
| `getResources()` | - | Get API resources |
| `getBarcodeImage($barcode)` | string | Get barcode image |
| `getShifts(DateTime $dateFrom, DateTime $dateTo, ?int $staff_id = null)` | DateTime, DateTime, int\|null | Get staff shifts |
| `getRelationship(int $client_id)` | int | Get client relationships |
| `getLeadByPhone($client_phone)` | string | Get lead by phone |
| `addLead($data)` | array | Add new lead |
| `reqOnlineBookingAuthToken($client_id, $expires = 120)` | int, int | Get online booking token |
| `getClientSignedConsentFormsList($client_id)` | int | Get consent forms list |
| `getClientConsentFormPDF($client_consent_form_id)` | int | Get consent form PDF |

## Common Data Structures

### Client Data (addClient/updateClient)
```php
$client_data = [
    'name' => 'John',                    // Required for add
    'surname' => 'Doe',                  // Optional
    'email' => 'john@example.com',       // Required for add, must be unique
    'password' => '1234',                // Required for add, min 4 chars
    'phone' => '1234567890',             // Optional
    'phone_work' => '0987654321',        // Optional
    'postcode' => 'SW1A 1AA',            // Optional
    'address' => '123 Main Street',      // Optional
    'sex' => 'm',                        // Optional: 'm', 'f', 'not_set'
    'dob' => '1990-01-01',               // Optional: YYYY-MM-DD
    'discount_value' => 5.0,             // Optional: float
    'notes' => 'Client notes',           // Optional
    'salon_id' => 1,                     // Optional: defaults to first salon
    'courses_barcode' => '1234567890'    // Optional: unique barcode
];
```

### Voucher Data
```php
$voucher_data = [
    'barcode' => '1234567890',           // Required, unique
    'value' => 50.00,                    // Required
    'type' => 'monetary',                // Required
    'expires' => '2023-12-31'            // Optional
];
```

### Lead Data
```php
$lead_data = [
    'name' => 'Jane Smith',              // Required
    'email' => 'jane@example.com',       // Optional
    'phone' => '0987654321',             // Optional
    'source' => 'website',               // Optional
    'notes' => 'Interested in services'  // Optional
];
```

## Error Handling Pattern

```php
$result = $api->someMethod();

if ($result === null) {
    $error = $api->getLastError();
    if (!empty($error)) {
        echo "Error: " . $error;
        // Handle error
    }
} else {
    // Process successful result
    $status = $api->getLastStatus(); // 'ok' or 'error'
}
```

## Quick Examples

### Basic Client Operations
```php
// Get client
$client = $api->getClientByID(12345);

// Add client
$new_client = $api->addClient([
    'name' => 'John',
    'email' => 'john@example.com',
    'password' => '1234'
]);

// Update client
$api->updateClient(12345, ['phone' => '0987654321']);
```

### Basic Appointment Operations
```php
// Create appointment
$appointment = new AppointmentObject(1);
$appointment->clientID = 12345;
$appointment->staffID = 4;
$appointment->duration = 30;
$appointment->datetime = new DateTime('2023-12-25 14:30:00');
$appointment->status = "booked";
$appointment->items = [["item_id" => 2781]];

$result = $api->addAppointment($appointment);
```

### Basic Service Operations
```php
// Get all services
$services = $api->get_services();

// Get specific services
$services = $api->get_services([2781, 2782]);

// Get services with pagination
$services = $api->get_services(null, null, 100, 0);
```

---

**Note:** All methods return `null` on error. Always check for errors using `getLastError()` and `getLastStatus()`.

For complete documentation with detailed examples, see [API_DOCUMENTATION.md](API_DOCUMENTATION.md).