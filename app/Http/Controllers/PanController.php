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

}
