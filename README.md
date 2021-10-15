# ClinicSoftware PHP Bindings

The ClinicSoftware PHP Library provides convenient access to the ClinicSoftware API from programs written in PHP. It provides a pre-defined class which manages most of the API calls that initializez itself dynamically.

## Requirements
PHP 7.4 or later recommended, the lib file can be converted for compatibility with lower versions of php quite easily.

## Dependencies
The bindings require the following php extensions in order to work properly:
- `curl`
- `json`
- `mbstring`

## Running Examples

⚠️ If you wouldd like a more "generic" example please check [This File Out](examples/cURL.md) ⚠️

1. Clone the repository 
```bash
git clone https://github.com/ClinicSoftware-com/ClinicSoftware-PHP-API
```
2. `cd` into the [`examples`](./examples) folder 📁
```
cd ./ClinicSoftware-PHP-API/examples/
```
3. Run any of the examples from the `CLI` 😲
```bash
php ./get_services.php;
```

## Getting Started
Simple usage looks like:
```php

// Import the configuration into your file
require_once 'api_config.php';
// Import the ClinicSoftware API
require_once '../lib/salon_api.php';

// Initialize the API
$api = new Salon_api($api_config['client_key'], $api_config['client_secret'],  $api_config['business_alias'],  $api_config['api_url']);

// Use any of the API calls
$result = $api->get_services(null, "2022-01-01", 1000);

// Have fun with the results 👻
foreach( $result as $r ) {
    // Look at all those Services!
}

```
## About
All API calls should be requested using the `POST` method over to the apropiate `URL` based on the license's server location, these most commonly are `clinicsoftware.com` sub-domains that are used to represent the different servers.
<br>
Connectees should provide this link, for the provided examples we will use the `demo.clinicsoftware.com` domain-base, under the mentioned server we will access the `demo` licese by using the proper API key/secret pair.
<small>`None of the example API keys are valid, please get a pair of development keys from your ClinicSoftware account`</small>

## Terminology

#### **Sending JSON**
Currently, if you would like to send `JSON` in the body of the request, please append the `GET` variable `json_input=1` to the end of your URI, this applies to direct API calls done outside of the ClinicSoftware SDK

#### **License**
A business account, this includes all staff memebers, all contacts, all services, etc... It's the top-level of the business's account.

#### **Payload**
A Payload is the data YOU send to CLINICSOFTWARE

#### **Alias**
ClinicSoftware licenses all have unique aliases that can be used to reference them, similar to a typical numeral IDentifier, these must be mentioned to know which license you would like to access.

#### **Online**
When an API key has the prefix of ONLINE, it refers to our Online Booking System, this usually is a public value or a value that interacts with the `Publicly Accessible` online booking system.

## More Example Calls
A couple examples of API calls available, please check [The github wiki](https://github.com/ClinicSoftware-com/ClinicSoftware-PHP-API/wiki) for more details.

### **`get_services`**
Get a list of services from the system, based on the provided details.
### Payload:
```js
{
    /* 
     * The maximum amount of returned rows, this works the same as the SQL LIMIT
     * MAX: 1000, if this goes over 1000, the reminder will overwrite OFFSET
     * ex: Limit 2000 = LIMIT 1000 OFFSET 1000
     * @typeof number
     */
    "limit": 10,

    /* 
     * The amount of items to offset by, this works the same as the SQL OFFSET
     * MAX: 2147483647
     * @typeof number
     */
    "offset": 0,

    /* 
     * The last date that the service has been modified at.
     * @typeof ISO 8601 date
     */
    "last_modified": "2019-04-27T11:19:51+49:23",

    /* 
     * A list of service IDs or a simple INT referencing an ID
     * @typeof number | Array | null
     */
    "id": [ 41, 42, 43 ]
}
```

### Response:
```js
{
    /*
     * Status will always be of type string, 
     * it's value can be of 'error' or 'ok'
     * Any error will also either return a 
     * HTTP RESPONSE 500, 401 or 200 ( no data, etc... )
     */
    "status": "ok",

    /*
     * Data will ALWAYS be of type Array, 
     * if you only requested one result simply fetch the first index.
     */
    "data": [
        {
            // The id of the service
            "id":                 1829,
            // The public name of the service
            "title":              "1 Site",
            // Internal description of the service
            "description":        "A simple example API call response.",
            // The name of the category the service is from
            "category":           "Botox Injection",
            // The id of the category the service is from
            "category_id":        41,
            // The name of the section the service is from
            "section":            "Anti Wrinkle",
            // The id of the section the service is from
            "section_id":         3,
            // The name of the location at which this 
            // service is available, NULL means ALL locations
            "location":           "London",
            // The id of the location at which this service 
            // is available, NULL means ALL locations
            "location_id":        1,
            // A simple description for public viewing, this can be empty.
            "online_description": "This is a public description which is available for anyone to publicly see from this business's online booking system.",
            // A barcode to represent quick access to the 
            // service in ClinicSoftware, this can be null.
            "barcode":            "00897654354",
            // The date at which the service has been added
            "date_added":         "2021-09-14T13:22:03+01:00",
            // The last time this service has been updated, 
            // this is part of the filter
            "date_modified":      "2021-09-24T08:08:23+01:00",
        },
        // Multiple services might be mentioned depending on your payload.
    ]
}
```
<small>`Note: You should format the full name of the product as SECTION + CATEGORY + TITLE`</small>

<br>

### **`get_relationship`**
Get the status of a contact's relationships with different contacts.
### Payload:
```js
{
    /* 
     * Id of the client we would like to know the relationships of.
     * @typeof number
     */
    "client_id": 321654,
}
```

### Response:
```js
{
    /*
     * Status will always be of type string, 
     * it's value can be of 'error' or 'ok'
     * Any error will also either return a 
     * HTTP RESPONSE 500, 401 or 200 ( no data, etc... )
     */
    "status": "ok",
    /*
     * Data will ALWAYS be of type Array, 
     * if you only requested one result simply fetch the first index.
     */
    "data": [
        {
            "client_id":            081249,
            "relationship_type":    "partner",
            "relationship_type_id": 1,
            "last_modified":        "2021-03-11T18:21:26+01:00",
        },
    ]
}
```
<small>`Note: Currently (24/09/2021) ClinicSoftware only supports PARTNER as a valid relationship, but the API has been tailored for future-proofing and might return different relationship types in the future.`</small>


<br>

### **`add_document`**
Add a document into the ClinicSoftware System.
### Payload:
```js
{
    /* 
     * The id of the client parent of the document
     * @typeof number
     */
    "client_id": 249,
    /* 
     * The name of the document.
     * @typeof string
     */
    "document_name": "BloodTests.png",
    /* 
     * The base64 encoded contents of the document.
     * @typeof string
     */
    "document_b64": "iVBORw0KGgoAAAANSUhEUgAAB3wAAAF7CAYAAAA5V/ox....",
    /* 
     * The appropiate IANA mime_type https://www.iana.org/assignments/media-types/media-types.xhtml
     * @typeof string
     */
    "mime_type": "image/png",
    /* 
     * Automatically rename the file if there already is a file present on the system with the same name on the server
     * @typeof bool
     */
    "automatically_rename": 1,
}
```

### Response:
```js
{
    /*
     * Status will always be of type string, 
     * it's value can be of 'error' or 'ok'
     * Any error will also either return a 
     * HTTP RESPONSE 500, 401 or 200 ( no data, etc... )
     */
    "status": "ok",
    /*
     * Data will ALWAYS be of type Array, 
     * if you only requested one result simply fetch the first index.
     */
    "data": {
        /*
         * What the file has been rename to because there already was a file with the same name
         */
        "automatically_renamed_to": null,
    }
}
```
<small>`Note 1: Please specify the extension for the file as well, this is NOT automatically added, the way you type the name is the way it will display in the system as well.`</small>
<br>
<small>`Note 2: If the file already exists on the server it will return an error, you can mitigate this by passing the 'automatically_rename=1'`</small>
