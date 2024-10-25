<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GSTController extends Controller
{
    public function requestOTP(Request $request) {
        $url = 'https://uatapi.alankitgst.com/taxpayerapi/v1.0/authenticate';
        $appkey2 = "1k6ogtiOoRUL76qYOsr6JQ287Q423BJx";
        $certificateFilePath = storage_path('app/GSTN_G2B_SANDBOX_UAT_public.cert.cer');
    
        // Get the public key from the certificate file
        $publicKey = openssl_get_publickey(file_get_contents($certificateFilePath));
        if ($publicKey === false) {
            die('Failed to read the public key from the certificate file.');
        }
    
        // Encrypt the app key
        $data = $appkey2;
        openssl_public_encrypt($data, $encryptedData, $publicKey);
        $Base64EncryptedPayload = base64_encode($encryptedData);
    
        // Get username and app_key from the request
        $username = $request->input('username');
        $gstin = $request->input('gstin');
        $state_cd = $request->input('state-cd');

        $app_key = $Base64EncryptedPayload;
    
        // Prepare the JSON payload
        $jsonData = [
            "action" => "OTPREQUEST",
            "username" => $username,
            "app_key" => $app_key,
        ];
        $jsonData = json_encode($jsonData);
        
    
        // Define the headers
        $headers = array(
            'Content-Type: application/json',
            'ip-usr:152.58.20.233',
            'client-secret:5a35ac266ea44bc18fdeb4bed07529d5',
            'username:' . $username,
            'gstin:' . $gstin, // Add your gstin value from the request
            'ocp-apim-subscription-key:ALSND0W1t0a9J1C6v8x9',
            'clientid:l7xxda1af7c62c6c40449602e5a9f448f2ef',
            'state-cd:' . $state_cd, // Add your state-cd value
            'txn:ALAN00000000002'
        );


        // Initialize cURL
        $curl = curl_init();
    
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER => $headers,
        ));
    
        // Execute the request and handle the response
        $response = curl_exec($curl);
    
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


    //token
    public function requestToken(Request $request){
        
        $username = $request->input('username'); // Retrieve username from the request
        $otp = $request->input('otp');
        $gstin = $request->input('gstin', '33AAMCO3917E2ZI');
        $appkey = "1k6ogtiOoRUL76qYOsr6JQ287Q423BJx";
        $url = 'https://uatapi.alankitgst.com/taxpayerapi/v1.0/authenticate';

        // Define the path to your certificate file (replace with your actual certificate file path)
        $certificateFilePath = storage_path('app/GSTN_G2B_SANDBOX_UAT_public.cert.cer');
        $publicKey = openssl_get_publickey(file_get_contents($certificateFilePath));

        if ($publicKey === false) {
            return response()->json(['error' => 'Failed to read the public key from the certificate file.'], 500);
        }

        // Encrypt the Base64-encoded payload using the public key
        openssl_public_encrypt($appkey, $encryptedData, $publicKey);
        $Base64EncryptedPayload = base64_encode($encryptedData);

        // Encrypt the OTP using AES 256 ECB mode and PKCS5 padding
        $encryptedOtp = openssl_encrypt($otp, 'aes-256-ecb', $appkey, OPENSSL_RAW_DATA);
        $encryptedBase64Otp = base64_encode($encryptedOtp);

        // Prepare JSON data
        $jsonData = [
            "action" => "AUTHTOKEN",
            "username" => $username,
            "app_key" => $Base64EncryptedPayload,
            "otp" => $encryptedBase64Otp
        ];

        $jsonDataWithoutSpaces = json_encode($jsonData);
        // dd($jsonDataWithoutSpaces);

        // Set headers
        $clientSecret = 'your-client-secret'; // replace with your actual client secret
        $clientId = 'your-client-id'; // replace with your actual client ID
        $subscriptionKey = 'your-subscription-key'; // replace with your actual subscription key

        $headers = array(
            'Content-Type: application/json',
            'ip-usr:152.58.20.233',
            'client-secret:5a35ac266ea44bc18fdeb4bed07529d5',
            'username:' . $username,
            'gstin:' . $gstin, // Add your gstin value from the request
            'Ocp-Apim-Subscription-Key:ALSND0W1t0a9J1C6v8x9',
            'clientid:l7xxda1af7c62c6c40449602e5a9f448f2ef',
            'state-cd:33', // Add your state-cd value
            'txn:ALAN00000000002'
        );
        // dd($headers);

        // Initialize cURL session
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataWithoutSpaces);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Disable SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable host verification

        // Execute cURL request
        $response = curl_exec($ch);

        // Check for cURL errors
        if ($response === false) {
            return response()->json(['error' => 'cURL error: ' . curl_error($ch)], 500);
        }

        // Get HTTP status code
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close cURL session
        curl_close($ch);

        // Check if response is not 200
        if ($httpStatus != 200) {
            return response()->json(['error' => 'API error: ' . $response], $httpStatus);
        }
        $responseData = json_decode($response, true);
        return response()->json($responseData);
    }


    //get reference id

    public function saveGSTR1 (Request $request) {
        // Prepare the payload
    //    $payload = [
    //       'gstin' => '33AAMCO3917E2ZI',
    //       'fp' => '122016',
    //       'gt' => 3782969.01,
    //       'cur_gt' => 3782969.01,
    //       'b2b' => [
    //           [
    //               'ctin' => '33AAMCO3917E2ZI',
    //               'inv' => [
    //                   [
    //                       'inum' => 'S008400',
    //                       'idt' => '24-11-2016',
    //                       'val' => 729248.16,
    //                       'pos' => '06',
    //                       'rchrg' => 'N',
    //                       'etin' => '01AABCE5507R1C4',
    //                       'inv_typ' => 'R',
    //                       'diff_percent' => 0.65,
    //                       'itms' => [
    //                           [
    //                               'num' => 1,
    //                               'itm_det' => [
    //                                   'rt' => 5,
    //                                   'txval' => 10000,
    //                                   'iamt' => 325,
    //                                   'csamt' => 500
    //                               ]
    //                           ]
    //                       ]
    //                   ]
    //               ]
    //           ]
    //       ],
    //       'b2ba' => [
    //           [
    //               'ctin' => '33AAMCO3917E2ZI',
    //               'inv' => [
    //                   [
    //                       'oinum' => 'S008400',
    //                       'oidt' => '24-11-2016',
    //                       'inum' => 'S008400',
    //                       'idt' => '24-11-2016',
    //                       'val' => 729248.16,
    //                       'pos' => '06',
    //                       'rchrg' => 'N',
    //                       'etin' => '01AABCE5507R1C4',
    //                       'inv_typ' => 'R',
    //                       'diff_percent' => 0.65,
    //                       'itms' => [
    //                           [
    //                               'num' => 1,
    //                               'itm_det' => [
    //                                   'rt' => 5,
    //                                   'txval' => 10000,
    //                                   'iamt' => 325,
    //                                   'camt' => 0,
    //                                   'samt' => 0,
    //                                   'csamt' => 500
    //                               ]
    //                           ]
    //                       ]
    //                   ]
    //               ]
    //           ]
    //       ],
    //       'b2cl' => [
    //           [
    //               'pos' => '05',
    //               'inv' => [
    //                   [
    //                       'inum' => '92661',
    //                       'idt' => '10-01-2016',
    //                       'val' => 784586.33,
    //                       'etin' => '27AHQPA8875L1CU',
    //                       'diff_percent' => 0.65,
    //                       'itms' => [
    //                           [
    //                               'num' => 1,
    //                               'itm_det' => [
    //                                   'rt' => 5,
    //                                   'txval' => 10000,
    //                                   'iamt' => 325,
    //                                   'csamt' => 500
    //                               ]
    //                           ]
    //                       ]
    //                   ]
    //               ]
    //           ]
    //       ],
    //       'b2cla' => [
    //           [
    //               'pos' => '06',
    //               'inv' => [
    //                   [
    //                       'oinum' => '9266',
    //                       'oidt' => '10-02-2016',
    //                       'diff_percent' => 0.65,
    //                       'inum' => '92661',
    //                       'idt' => '10-01-2016',
    //                       'val' => 784586.33,
    //                       'etin' => '01AABCE5507R1C4',
    //                       'itms' => [
    //                           [
    //                               'num' => 1,
    //                               'itm_det' => [
    //                                   'rt' => 5,
    //                                   'txval' => 10000,
    //                                   'iamt' => 833.33
    //                               ]
    //                           ]
    //                       ]
    //                   ]
    //               ]
    //           ]
    //       ],
    //       'cdnr' => [
    //           [
    //               'ctin' => '33AAMCO3917E2ZI',
    //               'nt' => [
    //                   [
    //                       'ntty' => 'C',
    //                       'nt_num' => '533515',
    //                       'nt_dt' => '23-09-2016',
    //                       'pos' => '03',
    //                       'rchrg' => 'Y',
    //                       'inv_typ' => 'DE',
    //                       'val' => 123123,
    //                       'diff_percent' => 0.65,
    //                       'itms' => [
    //                           [
    //                               'num' => 1,
    //                               'itm_det' => [
    //                                   'rt' => 10,
    //                                   'txval' => 5225.28,
    //                                   'iamt' => 339.64,
    //                                   'csamt' => 789.52
    //                               ]
    //                           ]
    //                       ]
    //                   ]
    //               ]
    //           ]
    //       ],
    //       'cdnra' => [
    //           [
    //               'ctin' => '33AAMCO3917E2ZI',
    //               'nt' => [
    //                   [
    //                       'ntty' => 'C',
    //                       'ont_num' => '533515',
    //                       'ont_dt' => '23-09-2016',
    //                       'nt_num' => '533515',
    //                       'nt_dt' => '23-09-2016',
    //                       'diff_percent' => 0.65,
    //                       'inv_typ' => 'DE',
    //                       'pos' => '03',
    //                       'rchrg' => 'Y',
    //                       'val' => 123123,
    //                       'itms' => [
    //                           [
    //                               'num' => 1,
    //                               'itm_det' => [
    //                                   'rt' => 10,
    //                                   'txval' => 5225.28,
    //                                   'iamt' => 845.22,
    //                                   'camt' => 0,
    //                                   'samt' => 0,
    //                                   'csamt' => 789.52
    //                               ]
    //                           ]
    //                       ]
    //                   ]
    //               ]
    //           ]
    //       ],
    //       'b2cs' => [
    //           [
    //               'sply_ty' => 'INTER',
    //               'diff_percent' => 0.65,
    //               'rt' => 5,
    //               'typ' => 'E',
    //               'etin' => '01AABCE5507R1C4',
    //               'pos' => '05',
    //               'txval' => 110,
    //               'iamt' => 10,
    //               'csamt' => 10
    //           ],
    //           [
    //               'rt' => 5,
    //               'sply_ty' => 'INTER',
    //               'diff_percent' => 0.65,
    //               'typ' => 'OE',
    //               'txval' => 100,
    //               'iamt' => 10,
    //               'csamt' => 10,
    //               'pos' => '05'
    //           ]
    //       ],
    //       'b2csa' => [
    //           [
    //               'omon' => '122016',
    //               'sply_ty' => 'INTER',
    //               'diff_percent' => 0.65,
    //               'typ' => 'E',
    //               'etin' => '01AABCE5507R1C4',
    //               'pos' => '05',
    //               'itms' => [
    //                   [
    //                       'rt' => 5,
    //                       'txval' => 110,
    //                       'iamt' => 10,
    //                       'camt' => 0,
    //                       'samt' => 0,
    //                       'csamt' => 10
    //                   ],
    //                   [
    //                       'rt' => 12,
    //                       'txval' => 110,
    //                       'iamt' => 10,
    //                       'camt' => 0,
    //                       'samt' => 0,
    //                       'csamt' => 10
    //                   ]
    //               ]
    //           ]
    //       ],
    //       'exp' => [
    //           [
    //               'exp_typ' => 'WPAY',
    //               'inv' => [
    //                   [
    //                       'inum' => '81542',
    //                       'idt' => '12-02-2016',
    //                       'val' => 995048.36,
    //                       'sbpcode' => 'ASB991',
    //                       'sbnum' => '7896542',
    //                       'sbdt' => '04-10-2016',
    //                       'itms' => [
    //                           [
    //                               'txval' => 10000,
    //                               'rt' => 5,
    //                               'iamt' => 833.33,
    //                               'csamt' => 100
    //                           ]
    //                       ]
    //                   ]
    //               ]
    //           ],
    //           [
    //               'exp_typ' => 'WPAY',
    //               'inv' => [
    //                   [
    //                       'inum' => '81542',
    //                       'idt' => '12-02-2016',
    //                       'val' => 995048.36,
    //                       'sbpcode' => 'ASB991',
    //                       'sbnum' => '7896542',
    //                       'sbdt' => '04-10-2016',
    //                       'itms' => [
    //                           [
    //                               'txval' => 10000,
    //                               'rt' => 5,
    //                               'iamt' => 833.33,
    //                               'csamt' => 100
    //                           ]
    //                       ]
    //                   ]
    //               ]
    //           ]
    //       ],
    //       'expa' => [
    //           [
    //               'exp_typ' => 'WPAY',
    //               'oinum' => '81542',
    //               'oidt' => '12-02-2016',
    //               'inum' => '8154',
    //               'idt' => '12-02-2016',
    //               'val' => 995048.36,
    //               'sbpcode' => 'ASB991',
    //               'sbnum' => '7896542',
    //               'sbdt' => '04-10-2016',
    //               'itms' => [
    //                   [
    //                       'rt' => 5,
    //                       'txval' => 10000,
    //                       'iamt' => 833.33,
    //                       'csamt' => 100
    //                   ]
    //               ]
    //           ]
    //       ],
    //       'txpd' => [
    //           [
    //               'pos' => '06',
    //               'sply_ty' => 'INTER',
    //               'itms' => [
    //                   [
    //                       'num' => 1,
    //                       'itm_det' => [
    //                           'rt' => 5,
    //                           'ad_amt' => 10000,
    //                           'iamt' => 200
    //                       ]
    //                   ]
    //               ]
    //           ]
    //       ],
    //       'at' => [
    //           [
    //               'pos' => '05',
    //               'sply_ty' => 'INTER',
    //               'itms' => [
    //                   [
    //                       'num' => 1,
    //                       'itm_det' => [
    //                           'txval' => 12345.12,
    //                           'rt' => 12,
    //                           'iamt' => 123.12,
    //                           'csamt' => 0
    //                       ]
    //                   ]
    //               ]
    //           ]
    //       ],
    //       'ata' => [
    //           [
    //               'omon' => '102016',
    //               'pos' => '05',
    //               'sply_ty' => 'INTER',
    //               'itms' => [
    //                   [
    //                       'num' => 1,
    //                       'itm_det' => [
    //                           'txval' => 12345.12,
    //                           'rt' => 12,
    //                           'iamt' => 123.12,
    //                           'csamt' => 0
    //                       ]
    //                   ]
    //               ]
    //           ]
    //       ],
    //       'nil' => [
    //           'inv' => [
    //               [
    //                   'sply_ty' => 'INTRB2B',
    //                   'expt_amt' => 0,
    //                   'nil_amt' => 5000,
    //                   'ngsup_amt' => 500
    //               ]
    //           ]
    //       ],
    //       'hsn' => [
    //           'data' => [
    //               [
    //                   'num' => 1,
    //                   'hsn_sc' => '1005',
    //                   'desc' => 'CORN',
    //                   'uqc' => '1',
    //                   'qty' => 15.265,
    //                   'val' => 4500000.00,
    //                   'txval' => 72000.00,
    //                   'iamt' => 10000.00,
    //                   'camt' => 10000.00,
    //                   'samt' => 5000.00,
    //                   'csamt' => 0
    //               ],
    //               [
    //                   'num' => 2,
    //                   'hsn_sc' => '1005',
    //                   'desc' => 'WHEAT',
    //                   'uqc' => '1',
    //                   'qty' => 15.265,
    //                   'val' => 4500000.00,
    //                   'txval' => 72000.00,
    //                   'iamt' => 10000.00,
    //                   'camt' => 10000.00,
    //                   'samt' => 5000.00,
    //                   'csamt' => 0
    //               ]
    //           ]
    //       ],
    //       'doc_issue' => [
    //           'doc_det' => [
    //               [
    //                   'doc_num' => 1,
    //                   'docs' => [
    //                       'doc_type' => 'Invoices for outward supply',
    //                       'from' => 'GST2',
    //                       'to' => 'GST11',
    //                       'totnum' => 8,
    //                       'cancel' => 1,
    //                       'net_issue' => 6
    //                   ]
    //               ],
    //               [
    //                   'doc_num' => 2,
    //                   'docs' => [
    //                       'doc_type' => 'Invoices for inward supply from unregistered person',
    //                       'from' => 'GST2',
    //                       'to' => 'GST11',
    //                       'totnum' => 8,
    //                       'cancel' => 1,
    //                       'net_issue' => 6
    //                   ]
    //               ]
    //           ]
    //       ]
    //   ];

      
    //   postamn request parameters

    $sek=$request->input('sek');
    $auth_token=$request->input('auth-token');
    $gstin=$request->input('gstin');
    //$state_cd=$request->input('state-cd');
    $username=$request->input('username');
    $ret_period=$request->input('ret_period');

    $payload=$request->input('payload');
//parse json string
    $jsonString = json_encode($payload);


    //   $sek="R1QecOqyaFc8jmFtsyspVmEtWU71geKV83OXYWrXDag2l+p2ZiQ64JrPO8OpzLAl";
    //   $auth_token="43b3326de08441af94110160d56baca9";
       $EncryptedSek= $sek;
       $AppKey = "1k6ogtiOoRUL76qYOsr6JQ287Q423BJx"; 
       $base64appkey=base64_encode($AppKey);    
       $options = 0;
       $encryption = $EncryptedSek; 
       $ciphering = "AES-256-ECB";
       $decryption_key = base64_decode($base64appkey);
       $decryption_iv = '';
       $decryption=openssl_decrypt($encryption, $ciphering,$decryption_key, $options, $decryption_iv);
       $base64encodeddecryptedsek = base64_encode($decryption);
       
       //saev gstr1
       $Base64RequestPayload = base64_encode(base64_encode($jsonString));
       $encrypted = $this->encryptBySymmetricKey($Base64RequestPayload, $base64encodeddecryptedsek);
       
       //hmac key
        $encryptbase64 = base64_decode($Base64RequestPayload); 
        // Decrypt SEK
         $decryptedsek = base64_decode($base64encodeddecryptedsek);
         // Calculate HMAC-SHA256
         $hmac = hash_hmac('sha256', $encryptbase64, $decryptedsek, true);
         // Base64 encode the calculated HMAC
         $base64Hmac = base64_encode($hmac);
       
         $payload = [
             "action" => "RETSAVE",
             "data" => $encrypted,
             "hmac" => $base64Hmac
         ];
       
         $jsonPayload = json_encode($payload);
       
       
       
       
        // Make HTTP request with headers
        $headers = array(
           'content-type' => 'application/json',
           'ip-usr' => '152.58.20.233',
           'client-secret' => '5a35ac266ea44bc18fdeb4bed07529d5',
           'txn' => 'ALAN00000000002',
           'clientid' => 'l7xxda1af7c62c6c40449602e5a9f448f2ef',
           'state-cd' => '33',
           'username' => $username,
           'gstin' => $gstin,
           'auth-token' => $auth_token,
           'ret_period' => $ret_period,
           'ocp-apim-subscription-key' => 'ALSND0W1t0a9J1C6v8x9',
       );
       $response = Http::withHeaders($headers)
       ->withoutVerifying() // Skip SSL verification
       ->put('https://uatapi.alankitgst.com/taxpayerapi/v3.1/returns/gstr1', [
           'action' => 'RETSAVE',
           'data' => $encrypted,
           'hmac' => $base64Hmac
       ])->json();

        if (isset($response['status_cd']) && $response['status_cd'] == "1") {
            // Extract the response values
            $data = $response['data'];
            $rek = $response['rek'];
            $hmac = $response['hmac'];

        }

       
         //decrypted rek
                       $appkey12 = $base64encodeddecryptedsek;
                       $options = 0;
                       $rek_key= $rek;
                       $encryption = $rek_key;
                       $ciphering = "AES-256-ECB";
                       $decryption_key =  base64_decode($appkey12);
                       $decryption_iv = '';
                       $method = "aes-256-cbc";
                       $decryption=openssl_decrypt ($encryption, $ciphering,$decryption_key, $options, $decryption_iv);
                       $rekdecrypt= base64_encode($decryption);
       // dd($rekdecrypt);
       //decrypted reponse using decrypte rek key
                       //decrypt response
                       $dataoptions = 0;
                       $data_key= $data;
                       $dataencryption = $data_key;
                       $dataciphering = "AES-256-ECB";
                       $datadecryption_key =  base64_decode($rekdecrypt);
                       $datadecryption_iv = '';
                       $method = "aes-256-cbc";
                       $datadecryption=openssl_decrypt($dataencryption, $dataciphering,$datadecryption_key, $dataoptions, $datadecryption_iv);
                       $datadecrypt= base64_encode($datadecryption);
                       $decodedata = base64_decode($datadecrypt);
                       $decodedata2 = base64_decode($decodedata);

                       $decodedArray = json_decode($decodedata2, true);
                       // dd($decodedArray);
       
       // Extract ref_id from decoded data
       $refId = isset($decodedArray['reference_id']) ? $decodedArray['reference_id'] : '';
       
            return response()->json($refId);
           
       
       
       }
       
       
       function encryptBySymmetricKey($dataB64, $sekB64){
               
           $data = base64_decode($dataB64);                                              // the data to encrypt
           $sek = base64_decode($sekB64);                                                  // the SEK
           $encDataB64 = openssl_encrypt($data, "AES-256-ECB", $sek, 0);                   // the Base64 encoded ciphertext
           return $encDataB64;
       }
       }
       
       





