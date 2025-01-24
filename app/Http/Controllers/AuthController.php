<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{

    public function authenticate(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'apiKey' => 'required|string', // Accept API key from request
        ]);

        // Prepare API URL
        $url = 'https://api-prod.tartanhq.com/aphrodite/api/auth/v1/authenticate';

        // Get the API key from the validated data
        $apiKey = $validatedData['apiKey']; // Dynamically get the API key

        // Prepare JSON data for the API request
        $jsonData = json_encode([
            "username" => $validatedData['username'],
            "password" => $validatedData['password']
        ]);

        // Prepare headers
        $headers = [
            'x-api-key: ' . $apiKey, // Include the API key in the header
            'Content-Type: application/json',
        ];

        // Initialize cURL
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        // Execute cURL request and capture the response
        $response = curl_exec($curl);

        // Check for cURL errors
        if (curl_errno($curl)) {
            return response()->json(['error' => curl_error($curl)], 500);
        }

        curl_close($curl);

        // Decode the API response
        $responseData = json_decode($response, true);

        // Handle the API response
        if (isset($responseData['token'])) {
            return response()->json([
                'message' => 'Authentication successful.',
                'token' => $responseData['token'],
            ]);
        } else {
            return response()->json([
                'message' => 'Authentication failed.',
                'error' => $responseData['error'] ?? 'Unknown error.',
            ], 400);
        }
    }


    public function verifyBankAccount(Request $request)
    {
        // Validate incoming request
        $validatedData = $request->validate([
            'accountNumber' => 'required|string',
            'ifsc' => 'required|string',
            'apiKey' => 'required|string',    // API key from request
            'token' => 'required|string',      // Token from request
        ]);

        // Define the endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Get the API key and token from the validated data
        $apiKey = $validatedData['apiKey']; // API key from request
        $token = $validatedData['token'];    // Token from request

        // Prepare the JSON payload
        $payload = [
            "category" => "financial-pii-data",
            "type" => "bank-account-verify",
            "applicationId" => "test",
            "data" => [
                "accountNumber" => $validatedData['accountNumber'],
                "ifsc" => $validatedData['ifsc'],
            ]
        ];

        // Define the headers
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
            'x-api-key: ' . $apiKey,
        ];

        // Initialize cURL
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => $headers,
        ]);

        // Execute the request
        $response = curl_exec($curl);

        // Check for cURL errors
        if ($response === false) {
            return response()->json(['error' => 'cURL error: ' . curl_error($curl)], 500);
        }

        // Get HTTP status code
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close cURL session
        curl_close($curl);

        // Check if response is not 200
        if ($httpStatus != 200) {
            return response()->json(['error' => 'API error: ' . $response], $httpStatus);
        }

        // Return response
        return response()->json(json_decode($response), 200);
    }

    // Mobile-to-bank verification method
    public function mobileToBankVerification(Request $request)
    {
        // Validate incoming request
        $validatedData = $request->validate([
            'mobile_no' => 'required|string',
            'apiKey' => 'required|string',  // API key from request
            'token' => 'required|string',    // Token from request
        ]);

        // Define the endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Get the API key and token from the validated data
        $apiKey = $validatedData['apiKey']; // API key from request
        $token = $validatedData['token'];    // Token from request

        // Prepare the JSON payload
        $payload = [
            "category" => "financial-pii-data",
            "type" => "mobile-to-bank",
            "applicationId" => "test",
            "data" => [
                "mobile_no" => $validatedData['mobile_no'],
            ]
        ];

        // Define the headers
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
            'x-api-key: ' . $apiKey,
        ];

        // Initialize cURL
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => $headers,
        ]);

        // Execute the request
        $response = curl_exec($curl);

        // Check for cURL errors
        if ($response === false) {
            return response()->json(['error' => 'cURL error: ' . curl_error($curl)], 500);
        }

        // Get HTTP status code
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close cURL session
        curl_close($curl);

        // Check if response is not 200
        if ($httpStatus != 200) {
            return response()->json(['error' => 'API error: ' . $response], $httpStatus);
        }

        // Return response
        return response()->json(json_decode($response), 200);
    }


    //Aadhaar Verification (w OTP)
    public function aadhaarGetOtp(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'aadhaarNo' => 'required|string|size:12', // Aadhaar number must be exactly 12 digits
            'apiKey' => 'required|string',             // API key from request
            'token' => 'required|string',               // Token from request
        ]);

        // Define the API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Get the API key and token from the validated data
        $apiKey = $validatedData['apiKey']; // API key from request
        $token = $validatedData['token'];    // Token from request

        // Prepare the JSON payload
        $payload = [
            "category" => "individual-pii-data",
            "type" => "aadhaar-offline-otp",
            "applicationId" => "test",
            "data" => [
                "aadhaarNo" => $validatedData['aadhaarNo'], // Ensure this comes as a string
            ]
        ];

        // Define the headers for the request
        $headers = [
            'Authorization: Bearer ' . $token,
            'x-api-key: ' . $apiKey,
            'Content-Type: application/json',
        ];

        // Initialize cURL
        $curl = curl_init();

        // Set cURL options
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload), // Ensure payload is JSON-encoded
            CURLOPT_HTTPHEADER => $headers,
        ]);

        // Execute the request
        $response = curl_exec($curl);
        // dd($response);

        // Check for cURL errors
        if ($response === false) {
            return response()->json(['error' => 'cURL error: ' . curl_error($curl)], 500);
        }

        // Get HTTP status code
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close cURL session
        curl_close($curl);

        // Check if response is not 200
        if ($httpStatus != 200) {
            return response()->json(['error' => 'API error: ' . $response], $httpStatus);
        }

        // Return the API response
        return response()->json(json_decode($response), 200);
    }

    public function aadhaarSubmitOtp(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'aadhaarNo' => 'required|string',
            'requestId' => 'required|string',
            'otp' => 'required|string',
            'token' => 'required|string',   // Added dynamic token
            'apiKey' => 'required|string',  // Added dynamic API key
        ]);

        // Define the API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Get the API key and token dynamically from the request
        $token = $validatedData['token'];   // Dynamic bearer token
        $apiKey = $validatedData['apiKey']; // Dynamic API key

        // Prepare the JSON payload dynamically from the request
        $payload = [
            "category" => "individual-pii-data",
            "type" => "aadhaar-offline-file",
            "applicationId" => "test", // Replace with actual application ID if needed
            "data" => [
                "aadhaarNo" => $validatedData['aadhaarNo'],
                "requestId" => $validatedData['requestId'],
                "otp" => $validatedData['otp']
            ]
        ];

        // Define the headers for the request
        $headers = [
            'Authorization: Bearer ' . $token,
            'x-api-key: ' . $apiKey,
            'Content-Type: application/json',
        ];

        // Initialize cURL
        $curl = curl_init();

        // Set cURL options
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload), // Send JSON payload
            CURLOPT_HTTPHEADER => $headers,             // Set headers dynamically
        ]);

        // Execute the request
        $response = curl_exec($curl);

        // Check for cURL errors
        if ($response === false) {
            return response()->json(['error' => 'cURL error: ' . curl_error($curl)], 500);
        }

        // Get HTTP status code
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close cURL session
        curl_close($curl);

        // Check if response is not 200
        if ($httpStatus != 200) {
            return response()->json(['error' => 'API error: ' . $response], $httpStatus);
        }

        // Return the API response
        return response()->json(json_decode($response), 200);
    }

    // Aadhaar to PAN verification
    public function aadhaarToPan(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'aadhaarNo' => 'required|string',
            'token' => 'required|string',  // Dynamic bearer token
            'apiKey' => 'required|string', // Dynamic API key
        ]);

        // Define the API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Get the API key and token dynamically from the request
        $token = $validatedData['token'];   // Dynamic bearer token
        $apiKey = $validatedData['apiKey']; // Dynamic API key

        // Prepare the JSON payload dynamically from the request
        $payload = [
            "category" => "individual-pii-data",
            "type" => "aadhaar-to-pan",
            "applicationId" => "Dashboard-realtime-KYC",
            "data" => [
                "aadhaarNo" => $validatedData['aadhaarNo'],
            ]
        ];

        // Define the headers for the request
        $headers = [
            'Authorization: Bearer ' . $token,
            'x-api-key: ' . $apiKey,
            'Content-Type: application/json',
        ];

        // Initialize cURL
        $curl = curl_init();

        // Set cURL options
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  // Disable host verification (use cautiously)
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);  // Disable peer certificate verification (use cautiously)
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,              // Return the response as a string
            CURLOPT_ENCODING => '',                      // Accept all encodings
            CURLOPT_MAXREDIRS => 10,                     // Maximum number of redirects
            CURLOPT_TIMEOUT => 0,                        // No timeout
            CURLOPT_FOLLOWLOCATION => true,              // Follow "Location" headers
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // HTTP version 1.1
            CURLOPT_CUSTOMREQUEST => 'POST',             // POST method
            CURLOPT_POSTFIELDS => json_encode($payload), // Send JSON payload
            CURLOPT_HTTPHEADER => $headers,              // Set headers dynamically
        ]);

        // Execute the request
        $response = curl_exec($curl);

        // Check for cURL errors
        if ($response === false) {
            return response()->json(['error' => 'cURL error: ' . curl_error($curl)], 500);
        }

        // Get HTTP status code
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close cURL session
        curl_close($curl);

        // Check if response is not 200
        if ($httpStatus != 200) {
            return response()->json(['error' => 'API error: ' . $response], $httpStatus);
        }

        // Return the API response
        return response()->json(json_decode($response), 200);
    }


    //  Aadhaar Validation (w/o OTP, w/o Demographic)
    public function aadhaarAdvancedVerification(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'aadhaarNumber' => 'required|string',
            'token' => 'required|string',  // Dynamic bearer token
            'apiKey' => 'required|string', // Dynamic API key
        ]);

        // Define the API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Get the API key and token dynamically from the request
        $token = $validatedData['token'];   // Dynamic bearer token
        $apiKey = $validatedData['apiKey']; // Dynamic API key

        // Prepare the JSON payload dynamically from the request
        $payload = [
            "category" => "individual-pii-data",
            "type" => "aadhaar-advanced",
            "applicationId" => "test",
            "data" => [
                "aadhaarNumber" => $validatedData['aadhaarNumber'],
            ]
        ];

        // Define the headers for the request
        $headers = [
            'Authorization: Bearer ' . $token,
            'x-api-key: ' . $apiKey,
            'Content-Type: application/json',
        ];

        // Initialize cURL
        $curl = curl_init();

        // Set cURL options
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  // Disable host verification (use cautiously)
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);  // Disable peer certificate verification (use cautiously)
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,              // Return the response as a string
            CURLOPT_ENCODING => '',                      // Accept all encodings
            CURLOPT_MAXREDIRS => 10,                     // Maximum number of redirects
            CURLOPT_TIMEOUT => 0,                        // No timeout
            CURLOPT_FOLLOWLOCATION => true,              // Follow "Location" headers
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // HTTP version 1.1
            CURLOPT_CUSTOMREQUEST => 'POST',             // POST method
            CURLOPT_POSTFIELDS => json_encode($payload), // Send JSON payload
            CURLOPT_HTTPHEADER => $headers,              // Set headers dynamically
        ]);

        // Execute the request
        $response = curl_exec($curl);

        // Check for cURL errors
        if ($response === false) {
            return response()->json(['error' => 'cURL error: ' . curl_error($curl)], 500);
        }

        // Get HTTP status code
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close cURL session
        curl_close($curl);

        // Check if response is not 200
        if ($httpStatus != 200) {
            return response()->json(['error' => 'API error: ' . $response], $httpStatus);
        }

        // Return the API response
        return response()->json(json_decode($response), 200);
    }


    // Aadhar Validation (w/o OTP) - Demographic Details
    public function aadhaarVerify(Request $request)
    {
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';
        $token = $request->input('token');
        $apiKey = $request->input('apiKey');

        // Define the payload with dynamic request data
        $payload = [
            "category" => "individual-pii-data",
            "type" => "aadhaar-advanced",
            "applicationId" => $request->input('applicationId', 'test'),
            "data" => [
                "aadhaarNumber" => $request->input('aadhaarNumber')
            ]
        ];

        // Initialize cURL
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'x-api-key: ' . $apiKey,
                'Content-Type: application/json',
            ],
        ]);

        // Execute the cURL request
        $response = curl_exec($curl);

        // Check for errors
        if (curl_errno($curl)) {
            // Handle cURL error
            $error = curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => $error], 500);
        }

        // Close the cURL session
        curl_close($curl);

        // Return the response
        return response()->json(['response' => json_decode($response, true)]);
    }

    public function bankAccountPennyLess(Request $request)
    {
        $validated = $request->validate([
            'authtoken' => 'required|string',
            'apikey' => 'required|string',
            'accountNumber' => 'required|string',
            'ifsc' => 'required|string',
        ]);

        $url = 'https://api-ext-prod.tartanhq.com/aphrodite/external/v1/verification';
        $payload = [
            'category' => 'financial-pii-data',
            'type' => 'bank-account-verify-pennyless',
            'applicationId' => 'test',
            'data' => [
                'accountNumber' => $validated['accountNumber'],
                'ifsc' => $validated['ifsc'],
            ],
        ];

        $headers = [
            'Authorization: Bearer ' . $validated['authtoken'],
            'x-api-key: ' . $validated['apikey'],
            'Content-Type: application/json',
        ];

        // Initialize cURL
        $curl = curl_init();

        // Set cURL options
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  // Disable host verification (use cautiously)
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);  // Disable peer certificate verification (use cautiously)
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,              // Return the response as a string
            CURLOPT_ENCODING => '',                      // Accept all encodings
            CURLOPT_MAXREDIRS => 10,                     // Maximum number of redirects
            CURLOPT_TIMEOUT => 0,                        // No timeout
            CURLOPT_FOLLOWLOCATION => true,              // Follow "Location" headers
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // HTTP version 1.1
            CURLOPT_CUSTOMREQUEST => 'POST',             // POST method
            CURLOPT_POSTFIELDS => json_encode($payload), // Send JSON payload
            CURLOPT_HTTPHEADER => $headers,              // Set headers dynamically
        ]);

        // Execute the request
        $response = curl_exec($curl);

        // Check for cURL errors
        if ($response === false) {
            return response()->json(['error' => 'cURL error: ' . curl_error($curl)], 500);
        }

        // Get HTTP status code
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close cURL session
        curl_close($curl);

        // Check if response is not 200
        if ($httpStatus != 200) {
            return response()->json(['error' => 'API error: ' . $response], $httpStatus);
        }

        // Return the API response
        return response()->json(json_decode($response), 200);
    }


    public function ifscVerification(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'ifscCode' => 'required|string',
            'authorization' => 'required|string',
            'x-api-key' => 'required|string',
        ]);

        // API URL
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Create the payload
        $payload = [
            "category" => "financial-and-credit",
            "type" => "ifsc",
            "applicationId" => "test",
            "data" => [
                "ifscCode" => $validated['ifscCode'],
            ],
        ];

        // Initialize cURL
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300, // Increase timeout to 300 seconds
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $validated['authorization'],
                'x-api-key: ' . $validated['x-api-key'],
                'Content-Type: application/json',
            ],
            CURLOPT_SSL_VERIFYHOST => 0, // Disable SSL host verification
            CURLOPT_SSL_VERIFYPEER => 0, // Disable SSL peer verification
        ]);

        // Execute the request
        $response = curl_exec($curl);

        // Check for cURL errors
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            return response()->json([
                'success' => false,
                'message' => 'Curl error occurred.',
                'error' => $error,
            ], 500);
        }

        // Get HTTP status code
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl); // Close cURL session

        // Parse the response
        $responseData = json_decode($response, true);

        // Check if response is not 200
        if ($httpStatus != 200) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process IFSC verification.',
                'error' => $responseData,
            ], $httpStatus);
        }

        // Return the response to the client
        return response()->json([
            'success' => true,
            'message' => 'IFSC verification successful.',
            'data' => $responseData,
        ], 200);
    }

    //Equifax fetch verification
public function fetchCreditReport(Request $request)
{
    // Validate the request data
    $validated = $request->validate([
        'name' => 'required|string',
        'id_number' => 'required|string',
        'id_type' => 'required|string',
        'mobile' => 'required|string',
        'consent' => 'required|string',
        'authorization' => 'required|string',
    ]);

    // API URL
    $url = 'https://sandbox.surepass.io/api/v1/credit-report-v2/fetch-report';

    // Create the payload
    $payload = [
        'name' => $validated['name'],
        'id_number' => $validated['id_number'],
        'id_type' => $validated['id_type'],
        'mobile' => $validated['mobile'],
        'consent' => $validated['consent'],
    ];

    // Initialize cURL
    $curl = curl_init();

    // Set cURL options
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 300, // Increase timeout to 300 seconds
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $validated['authorization'],
            'Content-Type: application/json',
        ],
        CURLOPT_SSL_VERIFYHOST => 0, // Disable SSL host verification
        CURLOPT_SSL_VERIFYPEER => 0, // Disable SSL peer verification
    ]);

    // Execute the request
    $response = curl_exec($curl);

    // Check for cURL errors
    if (curl_errno($curl)) {
        $error = curl_error($curl);
        curl_close($curl);
        return response()->json([
            'success' => false,
            'message' => 'Curl error occurred.',
            'error' => $error,
        ], 500);
    }

    // Get HTTP status code
    $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl); // Close cURL session

    // Parse the response
    $responseData = json_decode($response, true);

    // Check if response is not 200
    if ($httpStatus != 200) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch credit report.',
            'error' => $responseData,
        ], $httpStatus);
    }

    // Return the response to the client
    return response()->json([
        'success' => true,
        'message' => 'Credit report fetched successfully.',
        'data' => $responseData,
    ], 200);
}

//fetch pdf report

public function fetchPdfCreditReport(Request $request)
{
    // Validate the request data
    $validated = $request->validate([
        'name' => 'required|string',
        'id_number' => 'required|string',
        'id_type' => 'required|string',
        'mobile' => 'required|string',
        'consent' => 'required|string',
        'authorization' => 'required|string',
    ]);

    // API URL
    $url = 'https://sandbox.surepass.io/api/v1/credit-report-v2/fetch-pdf-report';

    // Create the payload
    $payload = [
        'name' => $validated['name'],
        'id_number' => $validated['id_number'],
        'id_type' => $validated['id_type'],
        'mobile' => $validated['mobile'],
        'consent' => $validated['consent'],
    ];

    // Initialize cURL
    $curl = curl_init();

    // Set cURL options
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 300, // Increase timeout to 300 seconds
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $validated['authorization'],
            'Content-Type: application/json',
        ],
        CURLOPT_SSL_VERIFYHOST => 0, // Disable SSL host verification
        CURLOPT_SSL_VERIFYPEER => 0, // Disable SSL peer verification
    ]);

    // Execute the request
    $response = curl_exec($curl);
    //dd($response);

    // Check for cURL errors
    if (curl_errno($curl)) {
        $error = curl_error($curl);
        curl_close($curl);
        return response()->json([
            'success' => false,
            'message' => 'Curl error occurred.',
            'error' => $error,
        ], 500);
    }

    // Get HTTP status code
    $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl); // Close cURL session

    // Parse the response
    $responseData = json_decode($response, true);

    // Check if response is not 200
    if ($httpStatus != 200) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch PDF credit report.',
            'error' => $responseData,
        ], $httpStatus);
    }

    // Return the response to the client
    return response()->json([
        'success' => true,
        'message' => 'PDF credit report fetched successfully.',
        'data' => $responseData,
    ], 200);
}

public function fetchCibilReport(Request $request)
    {
        // Validate the required fields
        $validated = $request->validate([
            'mobile' => 'required|string',
            'pan' => 'required|string',
            'name' => 'required|string',
            'gender' => 'required|string|in:male,female,other',
            'consent' => 'required|string|in:Y,N',
            'token' => 'required|string', // Token must be passed securely
        ]);

        // API URL
        $url = 'https://sandbox.surepass.io/api/v1/credit-report-cibil/fetch-report';

        // Prepare the request payload
        $payload = [
            'mobile' => $validated['mobile'],
            'pan' => $validated['pan'],
            'name' => $validated['name'],
            'gender' => $validated['gender'],
            'consent' => $validated['consent'],
        ];

        // Initialize cURL
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 0, // Disable SSL host verification
            CURLOPT_SSL_VERIFYPEER => 0, // Disable SSL peer verification
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload), // Payload as JSON
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $validated['token'],
            ],
        ]);

        // Execute the request
        $response = curl_exec($curl);

        // Check for cURL errors
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            return response()->json([
                'success' => false,
                'message' => 'Curl error occurred.',
                'error' => $error,
            ], 500);
        }

        // Close the cURL session
        curl_close($curl);

        // Parse the response
        $responseData = json_decode($response, true);

        // Return the response to the client
        return response()->json([
            'success' => true,
            'data' => $responseData,
        ], 200);
    }
    public function fetchCibilReportPdf(Request $request)
    {
        // Validate the required fields
        $validated = $request->validate([
            'mobile' => 'required|string',
            'pan' => 'required|string',
            'name' => 'required|string',
            'gender' => 'required|string|in:male,female,other',
            'consent' => 'required|string|in:Y,N',
            'token' => 'required|string', // Token must be passed securely
        ]);

        // API URL
        $url = 'https://sandbox.surepass.io/api/v1/credit-report-cibil/fetch-report-pdf';

        // Prepare the request payload
        $payload = [
            'mobile' => $validated['mobile'],
            'pan' => $validated['pan'],
            'name' => $validated['name'],
            'gender' => $validated['gender'],
            'consent' => $validated['consent'],
        ];

        // Initialize cURL
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 0, // Disable SSL host verification
            CURLOPT_SSL_VERIFYPEER => 0, // Disable SSL peer verification
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload), // Payload as JSON
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $validated['token'],
            ],
        ]);

        // Execute the request
        $response = curl_exec($curl);

        // Check for cURL errors
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            return response()->json([
                'success' => false,
                'message' => 'Curl error occurred.',
                'error' => $error,
            ], 500);
        }

        // Close the cURL session
        curl_close($curl);

        // Parse the response
        $responseData = json_decode($response, true);

        // Return the response to the client
        return response()->json([
            'success' => true,
            'data' => $responseData,
        ], 200);
    }
}