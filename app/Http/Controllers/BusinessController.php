<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BusinessController extends Controller
{
    //FSSAI License Verification
    public function fssaiVerification(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'id_number' => 'required|string',
        ]);

        // Extract validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $idNumber = $validatedData['id_number'];

        // API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Prepare the request payload
        $payload = [
            "category" => "self-employed",
            "type" => "fssai-verification",
            "applicationId" => "test",
            "data" => [
                "id_number" => $idNumber,
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
        curl_close($curl); // Close cURL session

        // Check if response is not 200
        if ($httpStatus != 200) {
            return response()->json(['error' => 'API error: ' . $response], $httpStatus);
        }

        // Return the API response
        return response()->json(json_decode($response), 200);
    }

    //Import - Export Code Verification
    public function importExportVerification(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'iec_number' => 'required|string',
        ]);

        // Extract validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $iecNumber = $validatedData['iec_number'];

        // API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Prepare the request payload
        $payload = [
            "category" => "self-employed",
            "type" => "iec-verification",
            "applicationId" => "test",
            "data" => [
                "iec_number" => $iecNumber,
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

    //GST details 
    public function verify(Request $request)
    {
        // Get dynamic values from request
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';
        $token = $request->input('token');
        $apiKey = $request->input('apiKey');
        $idNumber = $request->input('data.id_number'); // Nested data object




        // Define the payload
        $payload = [
            "category" => "employer-profiling",
            "type" => "gst-advanced-2",
            "applicationId" => "test",
            "data" => [
                "id_number" => $idNumber
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

    //KYB 1 (Company Search)
    public function companySearch(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'query' => 'required|string',
            'countryCode' => 'nullable|string',
        ]);

        // Extract validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $query = $validatedData['query'];
        $countryCode = $validatedData['countryCode'] ?? '';

        // API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Prepare the request payload
        $payload = [
            "category" => "internation-business-profiling",
            "type" => "v1-company-search",
            "applicationId" => "test",
            "data" => [
                "query" => $query,
                "countryCode" => $countryCode,
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

    public function panGstAdvanced(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'panNumber' => 'required|string',
        ]);

        // Extract validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $panNumber = $validatedData['panNumber'];

        // API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Prepare the request payload
        $payload = [
            "category" => "employer-profiling",
            "type" => "pan-gst-advanced-l2",
            "applicationId" => "test",
            "data" => [
                "panNumber" => $panNumber,
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

    public function basicMobileName(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'mobileNo' => 'required|string',
        ]);

        // Extract validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $mobileNo = $validatedData['mobileNo'];

        // API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Prepare the request payload
        $payload = [
            "category" => "utility-pii-data",
            "type" => "mobile-verification",
            "applicationId" => "test",
            "data" => [
                "mobileNo" => $mobileNo,
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

    public function generateOtp(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'mobileNo' => 'required|string',
        ]);

        // Extract validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $mobileNo = $validatedData['mobileNo'];

        // API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Prepare the request payload
        $payload = [
            "category" => "utility-pii-data",
            "type" => "mobile-number-verify-generate-otp",
            "applicationId" => "test",
            "data" => [
                "mobileNo" => $mobileNo,
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

    public function submitOtp(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'mobileNo' => 'required|string',
            'otp' => 'required|string',
            'referenceId' => 'required|string',
        ]);

        // Extract validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $mobileNo = $validatedData['mobileNo'];
        $otp = $validatedData['otp'];
        $referenceId = $validatedData['referenceId'];

        // API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/verification';

        // Prepare the request payload
        $payload = [
            "category" => "utility-pii-data",
            "type" => "mobile-number-verify-submit-otp",
            "applicationId" => "test",
            "data" => [
                "mobileNo" => $mobileNo,
                "otp" => $otp,
                "referenceId" => $referenceId
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

    public function advanceWorkEmailVerify(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'email' => 'required|email',
            'employeeName' => 'required|string',
            'employerName' => 'required|string',
            'mode' => 'required|string',
        ]);

        // Extract validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $email = $validatedData['email'];
        $employeeName = $validatedData['employeeName'];
        $employerName = $validatedData['employerName'];
        $mode = $validatedData['mode'];

        // API endpoint
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/advance_work_email_verify';

        // Prepare the request payload
        $payload = [
            "applicationId" => "test",
            "email" => $email,
            "employeeName" => $employeeName,
            "employerName" => $employerName,
            "mode" => $mode
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

    //Advance work email verification (w OTP)

     public function verifyWorkEmailOtp(Request $request)
    {
        // Define the URL and get dynamic values from the request
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/advance_work_email_otp_verify';
        $token = $request->input('token');
        $apiKey = $request->input('apiKey');
        
        // Define the payload with dynamic request data
        $payload = [
            "applicationId" => $request->input('applicationId', 'test'),
            "email" => $request->input('email'),
            "employeeName" => $request->input('employeeName'),
            "employerName" => $request->input('employerName'),
            "mode" => $request->input('mode', 'PROD')
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

    public function emailVerificationRequestOtp(Request $request)
    {
        // Define the URL and get dynamic values from the request
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/advance_work_email_otp/send';
        $token = $request->input('token');
        $apiKey = $request->input('apiKey');
        $id = $request->input('id'); // Retrieve the ID from the request

        // Define the payload with dynamic ID
        $payload = [
            "id" => $id
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

    public function SubmitWorkEmailOtp(Request $request)
    {
        // Define the URL and get dynamic values from the request
        $url = 'https://api-prod.tartanhq.com/aphrodite/external/v1/advance_work_email_otp/verify';
        $token = $request->input('token');
        $apiKey = $request->input('apiKey');
        $id = $request->input('id');
        $otp = $request->input('otp'); // Get OTP from request

        // Define the payload with dynamic ID and OTP
        $payload = [
            "id" => $id,
            "otp" => $otp
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
}
