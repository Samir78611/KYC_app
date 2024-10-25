<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KYCController extends Controller
{
    //main logic for authentication
    public function authenticate (Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Prepare API URL and API key
        $url = 'https://api-prod.tartanhq.com/aphrodite/api/auth/v1/authenticate';
        $apiKey = 'UJklQWM09I9RHyv7qvhK7KyVvkUnDp552UeNSnh7';

        // Prepare JSON data for the API request
        $jsonData = json_encode([
            "username" => $validatedData['username'],
            "password" => $validatedData['password']
        ]);

        // Prepare headers
        $headers = [
            'x-api-key: ' . $apiKey,
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

    //Bank account verification

    public function verifyBankAccount(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'accountNumber' => 'required|string',
            'ifsc' => 'required|string',
        ]);

        // API endpoint URL
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';
        
        // Authentication token and API key
        $token = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJTYW5kYm94X0ZpbmFuYWx5eiIsImV4cCI6MTcyOTYwNzI1NCwiaWF0IjoxNzI5NTg5MjU0fQ.4Y4RQzhGP17R8pW6O7XLpj0qKmD3eUC73q8mJz8AkzhaAkIJxZ2X1HDD3LlolXwUMNSLNMeeuCxHAXxvtAYs_g';  // Replace with the actual token
        $apiKey = 'UJklQWM09I9RHyv7qvhK7KyVvkUnDp552UeNSnh7';    // Replace with the actual API key

        // Prepare the JSON data for the API request
        $jsonData = json_encode([
            "category" => "financial-pii-data",
            "type" => "bank-account-verify",
            "applicationId" => "test",
            "data" => [
                "accountNumber" => $validatedData['accountNumber'],
                "ifsc" => $validatedData['ifsc']
            ]
        ]);
      

        // Prepare the headers
        $headers = [
            'Authorization: Bearer ' . $token,
            'x-api-key: ' . $apiKey,
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

        return response()->json($responseData);

        // Handle the API response
        //if (isset($responseData['status']) && $responseData['status'] == 'success') {
        //    return response()->json([
        //        'message' => 'Bank account verification successful.',
        //        'data' => $responseData['data'],
        //    ]);
        //} else {
        //    return response()->json([
        //        'message' => 'Bank account verification failed.',
        //        'error' => $responseData['error'] ?? 'Unknown error.',
        //    ], 400);
        //}
    }

    //mobile to bank verify

    public function verifyMobileToBank(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'mobile_no' => 'required|string',
        ]);

        // API endpoint URL
        $url = 'https://your-api-host.com/aphrodite/external/v1/verification';
        
        // Authentication token and API key
        $token = 'your-auth-token';  // Replace with the actual token
        $apiKey = 'your-api-key';    // Replace with the actual API key

        // Prepare the JSON data for the API request
        $jsonData = json_encode([
            "category" => "financial-pii-data",
            "type" => "mobile-to-bank",
            "applicationId" => "test",
            "data" => [
                "mobile_no" => $validatedData['mobile_no']
            ]
        ]);
  
        // Prepare the headers
        $headers = [
            'Authorization: Bearer ' . $token,
            'x-api-key: ' . $apiKey,
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
        if (isset($responseData['status']) && $responseData['status'] == 'success') {
            return response()->json([
                'message' => 'Mobile to bank verification successful.',
                'data' => $responseData['data'],
            ]);
        } else {
            return response()->json([
                'message' => 'Mobile to bank verification failed.',
                'error' => $responseData['error'] ?? 'Unknown error.',
            ], 400);
        }
    }

    //Aadhar to pan number 

    public function verifyAadhaarToPan(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'aadhaarNo' => 'required|string',
        ]);

        // API endpoint URL
        $url = 'https://your-api-host.com/aphrodite/external/v1/verification';
        
        // Authentication token and API key
        $token = 'your-auth-token';  // Replace with the actual token
        $apiKey = 'your-api-key';    // Replace with the actual API key

        // Prepare the JSON data for the API request
        $jsonData = json_encode([
            "category" => "individual-pii-data",
            "type" => "aadhaar-to-pan",
            "applicationId" => "Dashboard-realtime-KYC",
            "data" => [
                "aadhaarNo" => $validatedData['aadhaarNo'],
            ]
        ]);

        // Prepare the headers
        $headers = [
            'Authorization: Bearer ' . $token,
            'x-api-key: ' . $apiKey,
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
        if (isset($responseData['status']) && $responseData['status'] == 'success') {
            return response()->json([
                'message' => 'Aadhaar to PAN verification successful.',
                'data' => $responseData['data'],
            ]);
        } else {
            return response()->json([
                'message' => 'Aadhaar to PAN verification failed.',
                'error' => $responseData['error'] ?? 'Unknown error.',
            ], 400);
        }
    }


    //aadhar validation

    public function verifyAadhaarAdvanced(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'aadhaarNumber' => 'required|string',
        ]);
        // API endpoint URL
        $url = 'https://your-api-host.com/aphrodite/external/v1/verification';
        
        // Authentication token and API key
        $token = 'your-auth-token';  // Replace with the actual token
        $apiKey = 'your-api-key';    // Replace with the actual API key

        // Prepare the JSON data for the API request
        $jsonData = json_encode([
            "category" => "individual-pii-data",
            "type" => "aadhaar-advanced",
            "applicationId" => "test",
            "data" => [
                "aadhaarNumber" => $validatedData['aadhaarNumber']
            ]
        ]);

        // Prepare the headers
        $headers = [
            'Authorization: Bearer ' . $token,
            'x-api-key: ' . $apiKey,
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
        if (isset($responseData['status']) && $responseData['status'] == 'success') {
            return response()->json([
                'message' => 'Aadhaar advanced verification successful.',
                'data' => $responseData['data'],
            ]);
        } else {
            return response()->json([
                'message' => 'Aadhaar advanced verification failed.',
                'error' => $responseData['error'] ?? 'Unknown error.',
            ], 400);
        }
    }


    public function verifyAadhaarOfflineOtp(Request $request)
    {
        // Validate the input
        $validatedData = $request->validate([
            'aadhaarNo' => 'required|string',
        ]);

        // API endpoint URL
        $url = 'https://your-api-host.com/aphrodite/external/v1/verification'; // Replace with actual API host
        
        // Authentication token and API key
        $token = 'your-auth-token';  // Replace with the actual token
        $apiKey = 'your-api-key';    // Replace with the actual API key

        // Prepare the JSON payload
        $jsonData = json_encode([
            "category" => "individual-pii-data",
            "type" => "aadhaar-offline-otp",
            "applicationId" => "test",
            "data" => [
                "aadhaarNo" => $validatedData['aadhaarNo']
            ]
        ]);

        // Set the headers
        $headers = [
            'Authorization: Bearer ' . $token,
            'x-api-key: ' . $apiKey,
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

        // Execute the cURL request and capture the response
        $response = curl_exec($curl);

        // Check for errors in cURL
        if (curl_errno($curl)) {
            return response()->json(['error' => curl_error($curl)], 500);
        }

        curl_close($curl);

        // Decode the response as JSON
        $responseData = json_decode($response, true);

        // Ensure that the response is in JSON format
        return response()->json($responseData);
    }



    public function submitData(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'aadhaarNo' => 'required|string',
            'requestId' => 'required|string',
            'otp'       => 'required|string'
        ]);

        // API endpoint URL
        $url = 'https://your-api-host.com/aphrodite/external/v1/verification';  // Replace with your actual API host
        
        // Authentication token and API key
        $token = 'your-auth-token';   // Replace with the actual token
        $apiKey = 'your-api-key';     // Replace with the actual API key

        // Prepare the JSON payload
        $jsonData = json_encode([
            "category" => "individual-pii-data",
            "type" => "aadhaar-offline-file",
            "applicationId" => "test",
            "data" => [
                "aadhaarNo"  => $validatedData['aadhaarNo'],
                "requestId"  => $validatedData['requestId'],
                "otp"        => $validatedData['otp']
            ]
        ]);

        // Set the headers
        $headers = [
            'Authorization: Bearer ' . $token,
            'x-api-key: ' . $apiKey,
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

        // Execute the cURL request
        $response = curl_exec($curl);

        // Check for errors in cURL
        if (curl_errno($curl)) {
            return response()->json(['error' => curl_error($curl)], 500);
        }

        curl_close($curl);

        // Decode the response as JSON
        $responseData = json_decode($response, true);

        // Return the JSON response
        return response()->json($responseData);
    }


}
