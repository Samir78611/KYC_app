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

}
