<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function experianCreditReport(Request $request)
    {
        $validated = $request->validate([
            'authorization_token' => 'required|string',
            'name' => 'required|string',
            'consent' => 'required|string|in:Y,N',
            'mobile' => 'required|string',
            'pan' => 'required|string',
        ]);

        $url = 'https://sandbox.surepass.io/api/v1/credit-report-experian/fetch-report';
        $payload = [
            'name' => $validated['name'],
            'consent' => $validated['consent'],
            'mobile' => $validated['mobile'],
            'pan' => $validated['pan'],
        ];

        $headers = [
            'Authorization: Bearer ' . $validated['authorization_token'],
            'Content-Type: application/json',
        ];

        // Initialize cURL
        $curl = curl_init();

        // Set cURL options
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  // Disable host verification (use cautiously)
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);  // Disable peer certificate verification (use cautiously)
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,              
            CURLOPT_ENCODING => '',                      
            CURLOPT_MAXREDIRS => 10,                     // Maximum number of redirects
            CURLOPT_TIMEOUT => 0,                        // No timeout
            CURLOPT_FOLLOWLOCATION => true,              
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

    public function experianCreditReportPdf(Request $request)
    {
        $validated = $request->validate([
            'authorization_token' => 'required|string',
            'name' => 'required|string',
            'consent' => 'required|string|in:Y,N',
            'mobile' => 'required|string',
            'pan' => 'required|string',
        ]);

        $url = 'https://sandbox.surepass.io/api/v1/credit-report-experian/fetch-report-pdf';
        $payload = [
            'name' => $validated['name'],
            'consent' => $validated['consent'],
            'mobile' => $validated['mobile'],
            'pan' => $validated['pan'],
        ];

        $headers = [
            'Authorization: Bearer ' . $validated['authorization_token'],
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
}
