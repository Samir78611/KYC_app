<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class udyamController extends Controller
{
    //Udyam

    public function getApplication(Request $request, $id)
    {
        // Validate the request data
        $validated = $request->validate([
            'authorization' => 'required',
            'x-api-key' => 'required',
        ]);
    
        // API URL with dynamic ID
        $url = "https://api-prod.tartanhq.com/aphrodite/api/dashboard/v1/application/{$id}";
    
        // Initialize cURL session
        $curl = curl_init();
    
        // Set cURL options
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // Disable SSL host verification
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // Disable SSL peer verification
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '', // Handle compressed responses
            CURLOPT_MAXREDIRS => 10, // Maximum number of redirects
            CURLOPT_TIMEOUT => 0, // No timeout
            CURLOPT_FOLLOWLOCATION => true, // Follow redirects
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // Use HTTP 1.1
            CURLOPT_CUSTOMREQUEST => 'GET', // HTTP GET request
            CURLOPT_HTTPHEADER => [
                'x-api-key: ' . $validated['x-api-key'],
                'Authorization: Bearer ' . $validated['authorization'],
                'Content-Type: application/json',
            ],
        ]);
    
        // Execute cURL request
        $response = curl_exec($curl);
    
        // Check for cURL errors
        if ($error = curl_error($curl)) {
            curl_close($curl);
            return response()->json([
                'error' => 'cURL Error: ' . $error,
            ], 500);
        }
    
        // Close cURL session
        curl_close($curl);
    
        // Decode and return the API response
        $responseData = json_decode($response, true);
    
        if (isset($responseData['message']) && $responseData['message'] === 'Invalid request') {
            return response()->json([
                'error' => 'Invalid request: Please check your input.',
            ], 400);
        }
    
        return response()->json($responseData);
    }

    //Udyam Consented External Login
    public function consentedExternalLogin(Request $request)
    {
        // Dynamic input for token, API key, and other parameters
        $token = $request->input('token');
        $apiKey = $request->input('apiKey');
        $udyamRegistrationNumber = $request->input('udyamRegistrationNumber');
        $mobileNumber = $request->input('mobileNumber');
        $otpType = $request->input('otpType');
        $customerApplicationId = $request->input('customerApplicationId');

        // Define the API URL
        $url = "https://api-prod.tartanhq.com/aphrodite/external/v2/udyam/consented/initiate";

        // Request payload
        $payload = json_encode([
            'udyamRegistrationNumber' => $udyamRegistrationNumber,
            'mobileNumber' => $mobileNumber,
            'otpType' => $otpType,
            'customerApplicationId' => $customerApplicationId,
        ]);

        // Initialize cURL session
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                "Authorization: Bearer {$token}",
                "x-api-key: {$apiKey}",
            ],
            CURLOPT_SSL_VERIFYHOST => 0, // Disable SSL host verification
            CURLOPT_SSL_VERIFYPEER => 0, // Disable SSL peer verification
        ]);

        // Execute cURL request
        $response = curl_exec($curl);

        // Check for errors
        if ($response === false) {
            $error = curl_error($curl);
            curl_close($curl);
            return response()->json([
                'success' => false,
                'message' => 'cURL Error: ' . $error,
            ], 500);
        }

        // Close cURL session
        curl_close($curl);

        // Parse the response
        $responseData = json_decode($response, true);

        if (is_null($responseData)) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to parse API response.',
                'response' => $response,
            ], 500);
        }

        if (isset($responseData['error'])) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate request',
                'errors' => $responseData,
            ], 400);
        }

        // Return successful response
        return response()->json([
            'success' => true,
            'message' => 'Request initiated successfully',
            'data' => $responseData,
        ]);
    }

    //udyam create link
    public function udyamCreateLink(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'name' => 'required',
            'applicationId' => 'required',
            'email' => 'required|email',
            'isConsentedFlow' => 'required|boolean',
            'mode' => 'required',
            'authorization' => 'required',
            'x-api-key' => 'required',
        ]);
    
        // API URL
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/create-link/udyam';
    
        // Create the payload
        $payload = json_encode([
            'name' => $validated['name'],
            'applicationId' => $validated['applicationId'],
            'email' => $validated['email'],
            'isConsentedFlow' => $validated['isConsentedFlow'],
            'mode' => $validated['mode'],
        ]);
    
        // Initialize cURL
        $curl = curl_init();
    
        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $validated['authorization'],
                'x-api-key: ' . $validated['x-api-key'],
                'Content-Type: application/json',
            ],
        ]);
    
        // Execute cURL request
        $response = curl_exec($curl);
    
        // Check for errors
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => $error_msg], 500);
        }
    
        // Close cURL session
        curl_close($curl);
    
        // Decode the response
        $responseData = json_decode($response, true);
    
        // Return the response with success message
        return response()->json([
            'success' => true,
            'message' => 'Udyam create link successful',
            'data' => $responseData,
        ], 200);
    }

    public function fetchDetails($jobId, Request $request)
    {
        // Validate required inputs
        $validated = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
        ]);

        // API URL
        $url = "https://api-prod.tartanhq.com/aphrodite/external/v2/udyam/fetch/{$jobId}";

        // Initialize cURL
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 0, // Disable SSL host verification
            CURLOPT_SSL_VERIFYPEER => 0, // Disable SSL peer verification
            CURLOPT_CUSTOMREQUEST => 'GET', // HTTP GET method
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $validated['token'], // Authorization header
                'x-api-key: ' . $validated['apiKey'], // API Key header
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

public function consentedOtpLogin(Request $request)
{
    // Validate the request data
    $validated = $request->validate([
        'sessionId' => 'required|string',
        'otp' => 'required|string',
        'token' => 'required|string',
        'authorization' => 'required|string',
        'x-api-key' => 'required|string',
    ]);

    // API URL
    $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/udyam/consented/login/otp';

    // Create the payload
    $payload = json_encode([
        'sessionId' => $validated['sessionId'],
        'otp' => $validated['otp'],
        'token' => $validated['token'],
    ]);

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
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $validated['authorization'],
            'x-api-key: ' . $validated['x-api-key'],
            'Content-Type: application/json',
        ],
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
            'message' => 'Failed to process consented OTP login.',
            'error' => $responseData,
        ], $httpStatus);
    }

    // Return the response to the client
    return response()->json([
        'success' => true,
        'message' => 'Consented OTP login successful.',
        'data' => $responseData,
    ], 200);
}

}
