<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PanController extends Controller
{
    public function panOcrApi(Request $request)
    {
        // Get dynamic parameters from the request
        $token = $request->input('token'); // Replace with default token for testing
        $apiKey = $request->input('apiKey'); // Replace with your API key for testing
        $filePath = $request->file('front_image'); // Handle the uploaded file

        //// Validate file input
        //if (!$filePath || !$filePath->isValid()) {
        //    return response()->json([
        //        'message' => 'Invalid or missing front_image file.',
        //    ], 400);
        //}

        // Construct the URL
        $url = "https://api-prod.tartanhq.com/aphrodite/external/v1/verification/file";

        // Prepare the cURL file
        $file = new \CURLFile($filePath->getPathname(), $filePath->getMimeType(), $filePath->getClientOriginalName());

        // Prepare the form data
        $formData = [
            'mode' => 'PROD',
            'category' => 'individual-pii-data',
            'type' => 'pan-ocr',
            'applicationId' => 'test',
            'front_image' => $file,
        ];

        // Initialize cURL
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $formData,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'x-api-key: ' . $apiKey,
            ],
        ]);

        // Execute the request and capture the response
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Check for cURL errors
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);

            return response()->json([
                'message' => 'cURL error occurred',
                'error' => $error,
            ], 500);
        }

        curl_close($curl);

        // Return the response based on HTTP code
        if ($httpCode === 200) {
            return response()->json([
                'message' => 'PAN OCR processed successfully',
                'data' => json_decode($response, true),
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to process PAN OCR',
                'response' => json_decode($response, true),
            ], $httpCode);
        }
    }

    public function createItrLink(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'name' => 'required|string',
            'applicationId' => 'required|string',
            'email' => 'required|email',
        ]);

        // Replace placeholders with actual values
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/create-link/itr';
        $apiKey = $request->input('apiKey'); // Retrieve API key from request body
        $token = $request->input('token'); // Retrieve token from request body

        // Payload
        $payload = json_encode([
            'name' => $validated['name'],
            'applicationId' => $validated['applicationId'],
            'email' => $validated['email'],
        ]);

        // Initialize cURL
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 0, // Disable SSL host verification
            CURLOPT_SSL_VERIFYPEER => 0, // Disable SSL peer verification
            CURLOPT_CUSTOMREQUEST => 'POST', // HTTP POST method
            CURLOPT_POSTFIELDS => $payload, // JSON payload
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'x-api-key: ' . $apiKey,
                'Content-Type: application/json',
            ],
        ]);

        // Execute the request
        $response = curl_exec($curl);

        // Check for errors
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            return response()->json([
                'success' => false,
                'message' => 'Curl error occurred.',
                'error' => $error,
            ], 500);
        }

        // Close cURL session
        curl_close($curl);

        // Parse and return the response
        $responseData = json_decode($response, true);

        return response()->json([
            'success' => true,
            'data' => $responseData,
        ], 200);
    }

    public function verifyPan(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'pan' => 'required',
            'endUserToken' => 'required',

        ]);

        // API details
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/itr/pan';
        $apiKey = $request->input('x-api-key');
        $token = $request->input('token');



        // Payload
        $payload = json_encode([
            'pan' => $validated['pan'],
            'token' => $validated['endUserToken'],
        ]);

        // Initialize cURL
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 0, // Disable SSL host verification
            CURLOPT_SSL_VERIFYPEER => 0, // Disable SSL peer verification
            CURLOPT_CUSTOMREQUEST => 'POST', // HTTP POST method
            CURLOPT_POSTFIELDS => $payload, // JSON payload
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'x-api-key: ' . $apiKey,
                'Content-Type: application/json',
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

        // Parse and return the response
        $responseData = json_decode($response, true);

        return response()->json([
            'success' => true,
            'data' => $responseData,
        ], 200);
    }
    // Async Status API
    public function getLoginStatus(Request $request)
    {
        // Fetch input parameters
        $endUserToken = $request->input('endUserToken'); // Token for end-user
        $apiKey = $request->input('apiKey'); // API Key

        // // Validate inputs
        // if (!$host || !$endUserToken || !$apiKey) {
        //     return response()->json([
        //         'message' => 'Missing required parameters.',
        //     ], 400);
        // }

        // Construct the API URL
        $url = "https://api-prod.tartanhq.com/aphrodite/api/link/v1/login/status?token={$endUserToken}";

        try {
            // Initialize cURL
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30, // Set timeout
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'x-api-key: ' . $apiKey,
                ],
            ]);

            // Execute the request
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl); // Capture cURL error
            curl_close($curl);

            // Handle cURL errors
            if ($response === false) {
                return response()->json([
                    'message' => 'cURL request failed.',
                    'error' => $curlError,
                ], 500);
            }

            // Parse the response
            $responseData = json_decode($response, true);

            // Return success or error based on HTTP code
            if ($httpCode >= 200 && $httpCode < 300) {
                return response()->json([
                    'message' => 'Request successful.',
                    'response' => $responseData,
                ], $httpCode);
            } else {
                return response()->json([
                    'message' => 'Failed to fetch login status.',
                    'error' => $responseData,
                ], $httpCode);
            }
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function itrLogin(Request $request)
    {
        // Fetch input parameters
        $pan = $request->input('pan'); // PAN number
        $password = $request->input('password'); // Login password
        $endUserToken = $request->input('endUserToken'); // Token for end-user
        $sessionId = $request->input('sessionId'); // Session ID

        // Construct the API URL
        $url = "https://api-prod.tartanhq.com/aphrodite/external/v1/itr/login";

        try {
            // Initialize cURL
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30, // Set timeout
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode([
                    'pan' => $pan,
                    'password' => $password,
                    'token' => $endUserToken,
                    'sessionId' => $sessionId,
                ]),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                ],
            ]);

            // Execute the request
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl); // Capture cURL error
            curl_close($curl);

            // Handle cURL errors
            if ($response === false) {
                return response()->json([
                    'message' => 'cURL request failed.',
                    'error' => $curlError,
                ], 500);
            }

            // Parse the response
            $responseData = json_decode($response, true);

            // Return success or error based on HTTP code
            if ($httpCode >= 200 && $httpCode < 300) {
                return response()->json([
                    'message' => 'Login successful.',
                    'response' => $responseData,
                ], $httpCode);
            } else {
                return response()->json([
                    'message' => 'Failed to log in.',
                    'error' => $responseData,
                ], $httpCode);
            }
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        // Fetch input parameters
        $pan = $request->input('pan'); // PAN number
        $endUserToken = $request->input('endUserToken');

        // Construct the API URL
        $url = "https://api-prod.tartanhq.com/aphrodite/external/v1/itr/forgot-password";

        try {
            // Initialize cURL
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30, // Set timeout
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode([
                    'pan' => $pan,
                    'token' => $endUserToken
                ]),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                ],
            ]);

            // Execute the request
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl); // Capture cURL error
            curl_close($curl);

            // Handle cURL errors
            if ($response === false) {
                return response()->json([
                    'message' => 'cURL request failed.',
                    'error' => $curlError,
                ], 500);
            }

            // Parse the response
            $responseData = json_decode($response, true);

            // Return success or error based on HTTP code
            if ($httpCode >= 200 && $httpCode < 300) {
                return response()->json([
                    'message' => 'Forgot password request successful.',
                    'response' => $responseData,
                ], $httpCode);
            } else {
                return response()->json([
                    'message' => 'Failed to process forgot password request.',
                    'error' => $responseData,
                ], $httpCode);
            }
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function forgotPasswordOtp(Request $request)
    {
        // Fetch input parameters
        $apiKey = $request->input('apiKey'); // API Key
        $pan = $request->input('pan'); // PAN number
        $token = $request->input('token'); // Token for the request
        $sessionId = $request->input('sessionId'); // Session ID
        $otp = $request->input('otp');

        // Construct the API URL
        $url = "https://api-prod.tartanhq.com/aphrodite/external/v1/itr/forgot-password-otp";

        try {
            // Initialize cURL
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30, // Set timeout
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode([
                    'pan' => $pan,
                    'token' => $token,
                    'sessionId' => $sessionId,
                    'otp' => $otp,
                ]),
                CURLOPT_HTTPHEADER => [
                    'x-api-key: ' . $apiKey,
                    'Content-Type: application/json',
                ],
            ]);

            // Execute the request
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl); // Capture cURL error
            curl_close($curl);

            // Handle cURL errors
            if ($response === false) {
                return response()->json([
                    'message' => 'cURL request failed.',
                    'error' => $curlError,
                ], 500);
            }

            // Parse the response
            $responseData = json_decode($response, true);

            // Return success or error based on HTTP code
            if ($httpCode >= 200 && $httpCode < 300) {
                return response()->json([
                    'message' => 'OTP verification successful.',
                    'response' => $responseData,
                ], $httpCode);
            } else {
                return response()->json([
                    'message' => 'Failed to verify OTP.',
                    'error' => $responseData,
                ], $httpCode);
            }
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getDashboardData(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'apiKey' => 'required|string',
            'token' => 'required|string',
        ]);

        // Fetch input parameters
        $apiKey = $validated['apiKey']; // API Key
        $token = $validated['token']; // Bearer Token

        // Construct the API URL
        $url = "https://api-prod.tartanhq.com/aphrodite/api/dashboard/v1/itr/{$id}";


        try {
            // Initialize cURL
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30, // Set timeout
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $token,
                    'x-api-key: ' . $apiKey,
                    'Content-Type: application/json',
                ],
            ]);

            // Execute the request
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl); // Capture cURL error
            curl_close($curl);

            // Handle cURL errors
            if ($response === false) {
                return response()->json([
                    'message' => 'cURL request failed.',
                    'error' => $curlError,
                ], 500);
            }

            // Parse the response
            $responseData = json_decode($response, true);

            // Return success or error based on HTTP code
            if ($httpCode >= 200 && $httpCode < 300) {
                return response()->json([
                    'message' => 'Request successful.',
                    'response' => $responseData,
                ], $httpCode);
            } else {
                return response()->json([
                    'message' => 'Failed to fetch dashboard data.',
                    'error' => $responseData,
                ], $httpCode);
            }
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    //json data
    public function getItrData(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'apiKey' => 'required|string',
            'token' => 'required|string',
        ]);

        // Fetch input parameters
        $apiKey = $validated['apiKey']; // API Key
        $token = $validated['token']; // Bearer Token

        // Construct the API URL
        $url = "https://api-prod.tartanhq.com/aphrodite/api/dashboard/v1/itr/" . urlencode($id);

        try {
            // Initialize cURL
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30, // Set timeout
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $token,
                    'x-api-key: ' . $apiKey,
                    'Content-Type: application/json',
                ],
            ]);

            // Execute the request
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl); // Capture cURL error
            curl_close($curl);

            // Handle cURL errors
            if ($response === false) {
                return response()->json([
                    'message' => 'cURL request failed.',
                    'error' => $curlError,
                ], 500);
            }

            // Parse the response
            $responseData = json_decode($response, true);

            // Return success or error based on HTTP code
            if ($httpCode >= 200 && $httpCode < 300) {
                return response()->json([
                    'message' => 'Request successful.',
                    'response' => $responseData,
                ], $httpCode);
            } else {
                return response()->json([
                    'message' => 'Failed to fetch dashboard data.',
                    'error' => $responseData,
                ], $httpCode);
            }
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getItrDataDownload(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'apiKey' => 'required|string',
            'token' => 'required|string',
            'type' => 'required|array',
            'type.*' => 'string',
        ]);

        // Fetch input parameters
        $apiKey = $validated['apiKey']; // API Key
        $token = $validated['token']; // Bearer Token
        $types = $validated['type']; // Types array

        // Construct the API URL with query parameters
        $queryParams = http_build_query(['type' => $types]);
        $url = "https://api-prod.tartanhq.com/aphrodite/api/dashboard/v1/itr/download/{$id}?" . $queryParams;

        try {
            // Initialize cURL
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30, // Set timeout
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $token,
                    'x-api-key: ' . $apiKey,
                    'Content-Type: application/json',
                ],
            ]);

            // Execute the request
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl); // Capture cURL error
            curl_close($curl);

            // Handle cURL errors
            if ($response === false) {
                return response()->json([
                    'message' => 'cURL request failed.',
                    'error' => $curlError,
                ], 500);
            }

            // Parse the response
            $responseData = json_decode($response, true);

            // Return success or error based on HTTP code
            if ($httpCode >= 200 && $httpCode < 300) {
                return response()->json([
                    'message' => 'Request successful.',
                    'response' => $responseData,
                ], $httpCode);
            } else {
                return response()->json([
                    'message' => 'Failed to fetch dashboard data.',
                    'error' => $responseData,
                ], $httpCode);
            }
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //chnage password
    public function changePassword(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'pan' => 'required',
            'endUserToken' => 'required',
            'sessionId' => 'required',
            'password' => 'required', // Ensures password and confirmPassword match
        ]);

        // API URL
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/itr/change-password';

        // Create the payload
        $payload = json_encode([
            'pan' => $validated['pan'],
            'token' => $validated['endUserToken'],
            'sessionId' => $validated['sessionId'],
            'password' => $validated['password'],
            'confirmPassword' => $validated['password'], // Using the same password as confirmPassword
        ]);

        // Initialize cURL
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 0, // Disable SSL host verification
            CURLOPT_SSL_VERIFYPEER => 0, // Disable SSL peer verification
            CURLOPT_CUSTOMREQUEST => 'POST', // HTTP POST method
            CURLOPT_POSTFIELDS => $payload, // JSON payload
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json', // Set content type to JSON
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


    // ...existing code...

    public function initiateUdyam(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'udyamRegistrationNumber' => 'required',
            'customerApplicationId' => 'required',
            'authorization' => 'required',
            'x-api-key' => 'required',
        ]);
    
        // API URL
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v2/udyam/initiate';
    
        // Create the payload
        $payload = json_encode([
            'udyamRegistrationNumber' => $validated['udyamRegistrationNumber'],
            'customerApplicationId' => $validated['customerApplicationId'],
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
            'message' => 'Udyam initiation successful',
            'data' => $responseData,
        ], 200);
    }



    public function udyamStatus(Request $request, $jobId)
    {
        // Validate the request data
        $validated = $request->validate([
            'authorization' => 'required',
            'x-api-key' => 'required',
        ]);
    
        // API URL
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v2/udyam/status/' . $jobId;
    
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
            CURLOPT_CUSTOMREQUEST => 'GET',
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
    
        // Check if data is found
        if (!empty($responseData)) {
            return response()->json([
                'success' => true,
                'message' => 'Udyam status retrieval successful',
                'data' => $responseData,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No data found',
            ], 404);
        }
    }


}
