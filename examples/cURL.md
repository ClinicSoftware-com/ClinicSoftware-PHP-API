
## Sending out a cURL Request

1. Firstly we will have to create a hash so that the server can verify server-side
PHP Method:
```php
hash('sha256', "you_can_type_anything_here" . 1634300823 . $API_SECRET))
// OUTPUT: 7ce155b944636d524e17b7881a398bebc786a38073dc28b867e7d952cf0ebb46
// ^^ This is our hash that we should send to the server so that both sides can verify^^
```
Javascript Method:
```js
const crypto = require('crypto');

const api_client_salt = "you_can_type_anything_here";
const api_client_time = (new Date()).toString()
const api_secret      = "your_clinicsoftware_client_secret_goes_here"; // This is the most important

const rawString = api_client_salt + api_client_time + api_secret

// The final sha256 hash that will also be checked server-side
const myHash = 'crypto.createHash('sha256').update(rawString).digest('utf-8');
```
---
1.1 Here's the payload as a JSON, you must include all those fields
```json
{
    "action": "get_client_by_email",
    "api_client_key": "291b10d6d4fbd5e781b2d4a0aadf1210",
    "api_client_time": "1634300823",
    "api_client_salt": "you_can_type_anything_here",
    "api_client_hash": "7ce155b944636d524e17b7881a398bebc786a38073dc28b867e7d952cf0ebb46",
    "client_email": "daniel@clinicsoftware.com",
    "is_online_account": "0"
}
```

1.2 Encode the JSON from above to be URL_ENCODED_DATA for a `cURL` request.

2. Now we can send out the request as such:
```bash
curl --request GET \
    --url https://server3.clinicsoftware.com/Api_business \
    -d "business_client_alias=demo2&action=get_client_by_email&api_client_hash=7ce155b944636d524e17b7881a398bebc786a38073dc28b867e7d952cf0ebb46&api_client_key=291b10d6d4fbd5e781b2d4a0aadf1210&api_client_salt=you_can_type_anything_here&api_client_time=1634300823&client_email=daniel%40clinicsoftware.com&is_online_account=0" \
    -X POST
```
