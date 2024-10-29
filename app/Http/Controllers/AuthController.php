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
        $url = 'https://your-api-host.com/aphrodite/external/v1/verification';
    
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
        // Validate the incoming request data
        $validatedData = $request->validate([
            'aadhaarNumber' => 'required|string',
            'token' => 'required|string',      // Validate token
            'apiKey' => 'required|string',      // Validate API key
        ]);
    
        // Define the API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';
    
        // Prepare the JSON payload
        $payload = [
            'category' => 'individual-pii-data',
            'type' => 'aadhaar-advanced',
            "applicationId" => "test", // Default to 'test' if not provided
            'data' => [
                'aadhaarNumber' => $validatedData['aadhaarNumber'],
            ],
        ];
    
        // Define the headers for the request
        $headers = [
            'Authorization: Bearer ' . $validatedData['token'],
            'x-api-key: ' . $validatedData['apiKey'],
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
    
        // Return the API response
        return response()->json(json_decode($response), 200);
    }
}
