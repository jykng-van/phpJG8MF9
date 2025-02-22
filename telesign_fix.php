<?php
function isValidPhoneNumber($phone_number, $customer_id, $api_key) {
    $api_url = "https://rest-ww.telesign.com/v1/phoneid/$phone_number";

    $headers = [
        "Authorization: Basic " . base64_encode("$customer_id:$api_key"), //acceptable
        "Content-Type: application/json", //is required, it's JSON not form-urlencoded
        "Accept: application/json", //is required
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, 1); //needs to be post
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //should include this, it's used in the documentation
    $json_body = json_encode([
        'consent' => ['method' => 1]
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);

    $response = curl_exec($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    //originally returns a 405 code
    if ($http_code !== 200) {
        return false; // API request failed
    }

    $data = json_decode($response, true);
    var_dump($data); //for debugging

    if (!isset($data['phone_type'])) { //phone_type is not under numbering, it belongs to the root
        return false; // Unexpected API response
    }

    $valid_types = ["FIXED_LINE", "MOBILE", "VALID"]; //pointed out to be true in the description
    //And Prepaid, VOIP, Invalid, Payphone and Restricted to be false, there are other types not mentioned like Pager and Toll Free, not mentioned but assumed to be false for this
    //So phonetype is something like {"phone_type": {"code": "2", "description": "MOBILE"}}
    return in_array(strtoupper($data['phone_type']['description']), $valid_types); //changed JSON path because $valid_types values are in description
}

// Usage example
$phone_number = "<PHONE_NUMBER>"; // using an actual phone number with country code
$customer_id = "your_customer_id";
$api_key = "your_api_key";
$result = isValidPhoneNumber($phone_number, $customer_id, $api_key);
var_dump($result);

//Reference
//https://developer.telesign.com/enterprise/docs/phone-id-get-started
//https://developer.telesign.com/enterprise/reference/submitphonenumberforidentity