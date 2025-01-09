<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class udyamController extends Controller
{
    //Udyam

    public function getApplication(Request $request, $id)
    {
        // API URL with dynamic ID
        $url = "https://api-prod.tartanhq.com/aphrodite/api/dashboard/v1/application/{$id}";

        // Retrieve Bearer token dynamically from the request
        $bearerToken = $request->input('auth_token');

        // if (!$bearerToken) {
        //     return response()->json([
        //         'error' => 'Authorization token is required.',
        //     ], 400);
        // }

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
                'Accept: application/json',
                "Authorization: Bearer {$bearerToken}", // Dynamic Bearer token
            ],
        ]);

        // Execute cURL request
        $response = curl_exec($curl);
        // dd($response);

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
}
