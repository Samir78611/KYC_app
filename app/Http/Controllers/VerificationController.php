<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VerificationController extends Controller
{
    public function mobileToPanNumber(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'name' => 'required|string',
            'mobile_no' => 'required|string|size:10',
        ]);

        // Extract the validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $name = $validatedData['name'];
        $mobile_no = $validatedData['mobile_no'];

        // API endpoint
        $url = "https://api-prod.tartanhq.com/aphrodite/external/v1/verification";

        // Prepare request body
        $payload = [
            "category" => "individual-pii-data",
            "type" => "mobile-to-pan",
            "applicationId" => "Dashboard-realtime-KYC",
            "data" => [
                "name" => $name,
                "mobile_no" => $mobile_no
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
            return response()->json([
                'error' => 'Request failed',
                'details' => $response,
            ], $httpStatus);
        }

        // Return the API response
        return response()->json(json_decode($response), 200);
    }


    public function panDetail(Request $request)
    {
        // Get the required inputs from the request
        $panNumber = $request->input('panNumber');
        $token = $request->input('token');
        $apiKey = $request->input('apiKey');

        // Set the actual host URL
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification'; // Replace with actual API URL

        // Prepare payload for the API request
        $payload = [
            'category' => 'individual-pii-data',
            'type' => 'pan-detail-v2',
            'applicationId' => 'test',
            'data' => [
                'panNumber' => $panNumber
            ]
        ];

        // Prepare headers
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
            'x-api-key: ' . $apiKey
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

    public function panVerificationBasic(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'panNumber' => 'required|string|size:10', // PAN number must be exactly 10 characters
        ]);

        // Extract the validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $panNumber = $validatedData['panNumber'];

        // API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification'; // Replace {{host}} with actual host

        // Prepare request body
        $payload = [
            "category" => "financial-pii-data",
            "type" => "pan-basic",
            "applicationId" => "test",
            "data" => [
                "panNumber" => $panNumber,
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
            return response()->json([
                'error' => 'Request failed',
                'details' => $response,
            ], $httpStatus);
        }

        // Return the API response
        return response()->json(json_decode($response), 200);
    }

    public function addressInsight(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'address' => 'required|string',
            'pincode' => 'nullable|string|max:6',  // Optional pincode
            'city' => 'nullable|string',            // Optional city
            'state' => 'nullable|string',           // Optional state
            'language' => 'nullable|string',        // Optional language
        ]);

        // Extract validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $address = $validatedData['address'];
        $pincode = $validatedData['pincode'] ?? null; // Default to null if not provided
        $city = $validatedData['city'] ?? null;       // Default to null if not provided
        $state = $validatedData['state'] ?? null;     // Default to null if not provided
        $language = $validatedData['language'] ?? null; // Default to null if not provided

        // API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Prepare request body
        $data = [
            "category" => "address-verification",
            "type" => "validate-address",
            "applicationId" => "test",
            "data" => [
                "address" => $address,
                "pincode" => $pincode,
                "city" => $city,
                "state" => $state,
                "language" => $language,
            ],
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
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'x-api-key: ' . $apiKey,
                'Content-Type: application/json',
            ],
        ]);

        // Execute the request
        $response = curl_exec($curl);

        // Check for cURL errors
        if ($response === false) {
            return response()->json(['error' => 'cURL error: ' . curl_error($curl)], 500);
        }

        // Get HTTP status code
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl); // Close cURL session

        // Check if response is not 200
        if ($httpStatus != 200) {
            return response()->json(['error' => 'API error: ' . $response], $httpStatus);
        }

        // Return the API response
        return response()->json(json_decode($response), 200);
    }

    public function geocodeAddress(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'address' => 'required|string',
            'pincode' => 'nullable|string|max:6',  // Optional pincode
            'city' => 'nullable|string',            // Optional city
            'state' => 'nullable|string',           // Optional state
        ]);


        //dd($request->all());

        // Extract validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $address = $validatedData['address'];
        $pincode = $validatedData['pincode'] ?? null; // Default to null if not provided
        $city = $validatedData['city'] ?? null;       // Default to null if not provided
        $state = $validatedData['state'] ?? null;     // Default to null if not provided

        // API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Prepare request body
        $data = [
            "category" => "address-verification",
            "type" => "geocode-address",
            "applicationId" => "test",
            "data" => [
                "address" => $address,
                "pincode" => $pincode,
                "city" => $city,
                "state" => $state,
            ],
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
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'x-api-key: ' . $apiKey,
                'Content-Type: application/json',
            ],
        ]);

        // Execute the request
        $response = curl_exec($curl);

        // Check for cURL errors
        if ($response === false) {
            return response()->json(['error' => 'cURL error: ' . curl_error($curl)], 500);
        }

        // Get HTTP status code
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl); // Close cURL session

        // Check if response is not 200
        if ($httpStatus != 200) {
            return response()->json(['error' => 'API error: ' . $response], $httpStatus);
        }

        // Return the API response
        return response()->json(json_decode($response), 200);
    }

    public function caVerification(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'membership_no' => 'required|string',
        ]);

        // Extract validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $membership_no = $validatedData['membership_no'];

        // API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Prepare request body
        $data = [
            "category" => "professional-verification",
            "type" => "ca-auth",
            "applicationId" => "test",
            "data" => [
                "membership_no" => $membership_no,
            ],
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
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'x-api-key: ' . $apiKey,
                'Content-Type: application/json',
            ],
        ]);

        // Execute the request
        $response = curl_exec($curl);

        // Check for cURL errors
        if ($response === false) {
            return response()->json(['error' => 'cURL error: ' . curl_error($curl)], 500);
        }

        // Get HTTP status code
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl); // Close cURL session

        // Check if response is not 200
        if ($httpStatus != 200) {
            return response()->json(['error' => 'API error: ' . $response], $httpStatus);
        }

        // Return the API response
        return response()->json(json_decode($response), 200);
    }

    public function cinBasic(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'cinNumber' => 'required|string',
        ]);

        // Extract validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $cinNumber = $validatedData['cinNumber'];

        // API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Prepare request body
        $data = [
            "category" => "employer-profiling",
            "type" => "cin-basic",
            "applicationId" => "test",
            "data" => [
                "cinNumber" => $cinNumber,
            ],
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
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'x-api-key: ' . $apiKey,
                'Content-Type: application/json',
            ],
        ]);

        // Execute the request
        $response = curl_exec($curl);

        // Check for cURL errors
        if ($response === false) {
            return response()->json(['error' => 'cURL error: ' . curl_error($curl)], 500);
        }

        // Get HTTP status code
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl); // Close cURL session

        // Check if response is not 200
        if ($httpStatus != 200) {
            return response()->json(['error' => 'API error: ' . $response], $httpStatus);
        }

        // Return the API response
        return response()->json(json_decode($response), 200);
    }

    public function cinPullDetailed(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'cinNumber' => 'required|string',
        ]);

        // Extract validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $cinNumber = $validatedData['cinNumber'];

        // API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Prepare request body
        $data = [
            "category" => "employer-profiling",
            "type" => "cin-advanced",
            "applicationId" => "test",
            "data" => [
                "cinNumber" => $cinNumber,
            ],
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
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'x-api-key: ' . $apiKey,
                'Content-Type: application/json',
            ],
        ]);

        // Execute the request
        $response = curl_exec($curl);

        // Check for cURL errors
        if ($response === false) {
            return response()->json(['error' => 'cURL error: ' . curl_error($curl)], 500);
        }

        // Get HTTP status code
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl); // Close cURL session

        // Check if response is not 200
        if ($httpStatus != 200) {
            return response()->json(['error' => 'API error: ' . $response], $httpStatus);
        }

        // Return the API response
        return response()->json(json_decode($response), 200);
    }

    public function cinIntermediate(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'cinNumber' => 'required|string',
        ]);

        // Extract validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $cinNumber = $validatedData['cinNumber'];

        // API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Prepare the request payload
        $payload = [
            "category" => "employer-profiling",
            "type" => "cin-intermediate",
            "applicationId" => "test",
            "data" => [
                "cinNumber" => $cinNumber,
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

        // Check for cURL errors
        if ($response === false) {
            return response()->json(['error' => 'cURL error: ' . curl_error($curl)], 500);
        }

        // Get HTTP status code
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // Check if response is not 200
        if ($httpStatus != 200) {
            return response()->json(['error' => 'API error: ' . $response], $httpStatus);
        }

        // Return the API response
        return response()->json(json_decode($response), 200);
    }

    //Non Consented Data Fetch
    public function getApplicationById(Request $request, $id)
    {
        $authorizationToken = $request->header('Authorization'); // Get Authorization token from the header

        // if (!$authorizationToken) {
        //     return response()->json(['error' => 'Authorization token is required'], 400);
        // }

        $baseUrl = "https://api-prod.tartanhq.com/aphrodite/api/dashboard/v1/application/$id";

        // Initialize cURL session
        $curl = curl_init();

        // Set cURL options
        curl_setopt($curl, CURLOPT_URL, $baseUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: ' . $authorizationToken,
        ]);

        // Execute cURL request
        $response = curl_exec($curl);

        // Handle cURL errors
        if ($response === false) {
            return response()->json(['error' => 'cURL error: ' . curl_error($curl)], 500);
        }

        // Get HTTP response status
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // Handle non-200 HTTP responses
        if ($httpStatus != 200) {
            $responseDecoded = json_decode($response, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return response()->json([
                    'error' => 'API error',
                    'details' => $responseDecoded
                ], $httpStatus);
            } else {
                return response()->json([
                    'error' => 'API error',
                    'details' => $response
                ], $httpStatus);
            }
        }

        // Return the successful response
        return response()->json(json_decode($response, true), 200);
    }
}
