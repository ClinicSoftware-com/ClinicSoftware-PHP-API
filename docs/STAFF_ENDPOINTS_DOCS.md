# Staff API Endpoints

This documentation covers the staff-related API endpoints. These endpoints are designed for managing staff members in a salon system. All endpoints expect POST requests with the specified parameters. Responses are returned as JSON. Errors are returned with an appropriate HTTP status code and a JSON error message (e.g., `{"error": "Invalid salon ID"}`).

Endpoints are called using an API wrapper like `$api->call()`, where the `action` parameter specifies the endpoint name (without the leading underscore from the code).

## Add Staff

### Description
Creates a new staff member. Supports creating a new staff type if specified, generating or validating a barcode, setting permissions, next-of-kin, and adding shifts for weekdays. Automatically grants access to all services, courses, and tests. Staff type aliases like "manager" or "support" are reserved and cannot be used.

### Parameters
- `salon_id` (int, required): The ID of the salon to assign the staff to.
- `user_type_id` (int, optional): The ID of an existing staff type. Required if `user_type_new` is empty.
- `user_type_new` (string, optional): A new staff type name to create. Required if `user_type_id` is empty. If it exists, uses the existing ID.
- `nickname` (string, required): The staff nickname (username).
- `full_name` (string, required): The staff full name.
- `password` (string, required): The password (must be at least 4 characters).
- `barcode` (string, optional): Staff barcode. Use 'auto' to generate one, or provide a 6/8/13-digit numeric code (CODE39/EAN8/EAN13). Must be unique per salon.
- `phone` (string, optional): Staff phone number.
- `email` (string, optional): Staff email address.
- `manager_rights` (int, optional): Set to 1 to enable manager rights (default: 0).
- `reports_enabled` (int, optional): Set to 1 to enable reports access (default: 0).
- `daybook_enabled` (int, optional): Set to 1 to enable daybook access (default: 0).
- `online_booking` (int, optional): Set to 1 to enable online booking (default: 0).
- `daybook_notifications_enabled` (int, optional): Set to 1 to enable daybook notifications (default: 0).
- `sort_order` (int, optional): Sort order for display (default: 0).
- `kin_name` (string, optional): Next-of-kin name.
- `kin_phone` (string, optional): Next-of-kin phone number.
- `staff_permissions` (array, optional): Array of permission keys.
- For shifts (optional, for each weekday where 1=Monday to 7=Sunday):
  - `wd{i}_confirm` (int, optional): Set to 1 to add/update shifts for weekday i (e.g., `wd1_confirm` for Monday).
  - `wd{i}_date_start` (string, required if confirm=1): Start date in 'd/m/Y' format (must be >= current date).
  - `wd{i}_date_end` (string, required if confirm=1): End date in 'd/m/Y' format (must be > start date, max 1 year range).
  - `wd{i}_time_start` (string, required if confirm=1): Start time in 'HH:MM' format.
  - `wd{i}_time_end` (string, required if confirm=1): End time in 'HH:MM' format (must be > start time).

### Returns
A JSON object with:
- `message`: Success message.
- `staff_id`: The new staff ID.
- `staff_data`: The created staff details (excluding password).

Errors (HTTP 400) for invalid inputs, duplicates, or reserved types.

### Example
```
$result = $api->call([
    'action' => 'add_staff',
    'salon_id' => 1,
    'user_type_id' => 2, // existing staff type ID
    'user_type_new' => '', // leave empty if using user_type_id
    'nickname' => 'johnny',
    'full_name' => 'John Doe',
    'password' => 'abcd1234',
    'barcode' => 'auto', // or 6/8/13 digits like "123456"
    'phone' => '0712345678',
    'email' => 'john@example.com',

    // rights / toggles
    'manager_rights' => 1,
    'reports_enabled' => 1,
    'daybook_enabled' => 1,
    'online_booking' => 1,
    'daybook_notifications_enabled' => 1,
    'sort_order' => 10,

    // optional next-of-kin
    'kin_name' => 'Jane Doe',
    'kin_phone' => '0798765432',

    // optional permissions
    'staff_permissions' => [
        'can_edit_clients',
        'can_view_reports'
    ],

    // optional shifts for each weekday (Mon=1 ... Sun=7)
    'wd1_confirm' => 1,
    'wd1_date_start' => '25/09/2025',
    'wd1_date_end' => '25/10/2025',
    'wd1_time_start' => '09:00',
    'wd1_time_end' => '17:00',

    'wd3_confirm' => 1,
    'wd3_date_start' => '25/09/2025',
    'wd3_date_end' => '25/10/2025',
    'wd3_time_start' => '12:00',
    'wd3_time_end' => '20:00',
]);
```

## Get Staff List

### Description
Retrieves a list of staff members. If a `salon_id` is provided, it fetches staff for that specific salon (with optional search). If no `salon_id` is provided, it fetches all active staff across salons (with optional search). Supports pagination and search filtering. Staff passwords are excluded from the response.

### Parameters
- `salon_id` (int, optional): The ID of the salon to filter staff by. If omitted, returns all active staff.
- `search_query` (string, optional): A search term to filter staff by name or other details.
- `page` (int, optional): The page number for pagination (default: 1).
- `limit` (int, optional): The number of staff per page (default: 50, max based on system limits).

### Returns
A JSON object containing:
- `staff`: An array of staff objects (each with details like ID, name, etc., but excluding `password`).
- `pagination`: An object with `page` (current page), `limit` (items per page), `total_count` (total staff matching the query), and `total_pages` (calculated total pages).

If an invalid `salon_id` is provided, returns an error (HTTP 400).

### Example
```
$result = $api->call([
    'action' => 'get_staff',
    'salon_id' => 1, // optional, filters by salon
    'search_query' => 'john', // optional, for searching
    'page' => 2, // optional
    'limit' => 20 // optional
]);
```

## Get Staff by ID

### Description
Retrieves detailed information for a specific staff member by ID, including permissions and next-of-kin details. Staff password is excluded from the response.

### Parameters
- `staff_id` (int, required): The ID of the staff member to retrieve.

### Returns
A JSON object with the staff details (e.g., ID, name, phone, email, etc.), plus:
- `permissions`: An array of permissions for the staff.
- `next_of_kin`: An object with next-of-kin details (if set).

If the `staff_id` is missing, returns an error (HTTP 400). If the staff member is not found, returns an error (HTTP 404).

### Example
```
$result = $api->call([
    'action' => 'get_staff_by_id',
    'staff_id' => 139
]);
```

## Update Staff

### Description
Updates an existing staff member. Similar to add, but for an existing ID. Supports changing staff type, barcode (if different), password (optional), and updating shifts (only for specified weekdays; use 'nc' to skip). Existing shifts are updated or inserted; permissions are replaced. Staff type aliases like "manager" or "support" are reserved.

### Parameters
- `staff_id` (int, required): The ID of the staff to update.
- `salon_id` (int, required): The new salon ID (must be valid).
- `user_type_id` (int, optional): The ID of an existing staff type. Required if `user_type_new` is empty.
- `user_type_new` (string, optional): A new staff type name to create. Required if `user_type_id` is empty. If it exists, uses the existing ID.
- `nickname` (string, required): The staff nickname.
- `full_name` (string, required): The staff full name.
- `password` (string, optional): New password (must be at least 4 characters if provided).
- `barcode` (string, optional): New barcode. Use 'auto' to generate, or provide a 6/8/13-digit numeric code. Must be unique per salon if changed.
- `phone` (string, optional): Staff phone number.
- `email` (string, optional): Staff email address.
- `manager_rights` (int, optional): Set to 1 to enable manager rights (default: 0).
- `reports_enabled` (int, optional): Set to 1 to enable reports access (default: 0).
- `daybook_enabled` (int, optional): Set to 1 to enable daybook access (default: 0).
- `online_booking` (int, optional): Set to 1 to enable online booking (default: 0).
- `daybook_notifications_enabled` (int, optional): Set to 1 to enable daybook notifications (default: 0).
- `sort_order` (int, optional): Sort order for display (default: 0).
- `kin_name` (string, optional): Next-of-kin name (updates or inserts if provided).
- `kin_phone` (string, optional): Next-of-kin phone number.
- `staff_permissions` (array, optional): Array of permission keys (replaces existing; must match system permissions).
- For shifts (optional, for each weekday where 1=Monday to 7=Sunday):
  - `wd{i}_confirm` (string/int, optional): Set to 1 (or any non-'nc') to update/add shifts for weekday i; set to 'nc' or omit to skip.
  - `wd{i}_date_start` (string, required if confirm != 'nc'): Start date in 'd/m/Y' format (must be >= current date).
  - `wd{i}_date_end` (string, required if confirm != 'nc'): End date in 'd/m/Y' format (must be > start date, max 1 year range).
  - `wd{i}_time_start` (string, required if confirm != 'nc'): Start time in 'HH:MM' format.
  - `wd{i}_time_end` (string, required if confirm != 'nc'): End time in 'HH:MM' format (must be > start time; '00:00' auto-converts to '23:59').

### Returns
A JSON object with:
- `message`: Success message.
- `staff_id`: The updated staff ID.
- `staff_data`: The updated staff details (excluding password).

If the `staff_id` is missing, returns an error (HTTP 400). If not found, returns an error (HTTP 404). Other errors (HTTP 400) for invalid inputs.

### Example
```
$result = $api->call([
    'action' => 'update_staff',
    'staff_id' => 139,
    'salon_id' => 1,
    'user_type_id' => 2, // existing staff type ID
    'user_type_new' => '', // leave empty if using user_type_id
    'nickname' => 'johnny_updated',
    'full_name' => 'John Doe Updated',
    'password' => 'newpass1234', // optional
    'barcode' => '12345678', // optional, if changing
    'phone' => '0712345678',
    'email' => 'john_updated@example.com',

    // rights / toggles
    'manager_rights' => 0,
    'reports_enabled' => 0,
    'daybook_enabled' => 1,
    'online_booking' => 1,
    'daybook_notifications_enabled' => 1,
    'sort_order' => 5,

    // optional next-of-kin
    'kin_name' => 'Jane Doe Updated',
    'kin_phone' => '0798765432',

    // optional permissions
    'staff_permissions' => [
        'can_edit_clients'
    ],

    // optional shifts for each weekday (Mon=1 ... Sun=7)
    'wd1_confirm' => 1, // set to 1 to update/add
    'wd1_date_start' => '25/09/2025',
    'wd1_date_end' => '25/10/2025',
    'wd1_time_start' => '10:00',
    'wd1_time_end' => '18:00',

    'wd2_confirm' => 'nc' // set to 'nc' to skip/no change
]);
```

## Delete Staff

### Description
Deactivates a staff member by setting their state to 'inactive'. Does not permanently delete the record.

### Parameters
- `staff_id` (int, required): The ID of the staff member to deactivate.

### Returns
A JSON object with:
- `message`: Success message.
- `staff_id`: The deactivated staff ID.

If the `staff_id` is missing, returns an error (HTTP 400). If not found, returns an error (HTTP 404). If already inactive, returns an error (HTTP 400).

### Example
```
$result = $api->call([
    'action' => 'delete_staff',
    'staff_id' => 139
]);
```
