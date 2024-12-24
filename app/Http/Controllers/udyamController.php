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
}
