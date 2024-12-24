<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class DigilockerController extends Controller
{
    public function getDigiLockerFile(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'requestId' => 'required|string',
            'uri' => 'required|string',
        ]);

        // Extract validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $requestId = $validatedData['requestId'];
        $uri = $validatedData['uri'];

        // API endpoint
        $url = "https://api-prod.tartanhq.com/aphrodite/api/tp/v1/kyc/digilocker";

        // Payload for the API request
        $payload = [
            "category" => "digilocker",
            "type" => "get-file",
            "data" => [
                "requestId" => $requestId,
                "uri" => $uri,
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
        if (curl_errno($curl)) {
            // Handle cURL error
            $error = curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => $error], 500);
        }

        // Close the cURL session
        curl_close($curl);

        // Return the API response
        return response()->json(['response' => json_decode($response, true)]);
    }

    public function getDigiLockerIssuedFileList(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'requestId' => 'required|string',
        ]);

        // Extract validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $requestId = $validatedData['requestId'];

        // API endpoint
        $url = "https://api-prod.tartanhq.com/aphrodite/api/tp/v1/kyc/digilocker";

        // Payload for the API request
        $payload = [
            "category" => "digilocker",
            "type" => "issued-file-list",
            "data" => [
                "requestId" => $requestId,
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
        if (curl_errno($curl)) {
            // Handle cURL error
            $error = curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => $error], 500);
        }

        // Close the cURL session
        curl_close($curl);

        // Return the API response
        return response()->json(['response' => json_decode($response, true)]);
    }

    public function getAllIssuedDocuments(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'token' => 'required|string',
            'apiKey' => 'required|string',
            'jobId' => 'required|string',
            'format' => 'required|string|in:xml,pdf',
        ]);

        // Extract validated data
        $token = $validatedData['token'];
        $apiKey = $validatedData['apiKey'];
        $jobId = $validatedData['jobId'];
        $format = $validatedData['format'];

        // API endpoint
        $url = "https://api-prod.tartanhq.com/aphrodite/api/tp/v1/verification?jobId={$jobId}";

        // Payload for the API request
        $payload = [
            "category" => "digilocker",
            "type" => "get-all-issued-document",
            "applicationId" => "test",
            "data" => [
                "format" => $format,
            ],
            "mode" => "PROD",
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
        if (curl_errno($curl)) {
            // Handle cURL error
            $error = curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => $error], 500);
        }

        // Close the cURL session
        curl_close($curl);

        // Return the API response
        return response()->json(['response' => json_decode($response, true)]);
    }
}
