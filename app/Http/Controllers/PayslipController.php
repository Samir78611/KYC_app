<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Codedge\Fpdf\Fpdf\Fpdf;


class PayslipController extends Controller
{
    //payslip

    public function generatePayslip(Request $request)
    {
        // Validate request data
        $request->validate([
            'applicationId' => 'required|string',
            'password'      => 'required|string',
            'employerName'  => 'required|string',
            'employeeName'  => 'required|string',
            'salaryMonth'   => 'required|string',
            'basicPay'      => 'required|numeric',
            'deductions'    => 'required|numeric',
        ]);
    
        // Get input data
        $applicationId = $request->input('applicationId');
        $password      = $request->input('password');
        $employerName  = $request->input('employerName');
        $employeeName  = $request->input('employeeName');
        $salaryMonth   = $request->input('salaryMonth');
        $basicPay      = $request->input('basicPay');
        $deductions    = $request->input('deductions');
        $netPay        = $basicPay - $deductions;
    
        // Initialize FPDF instance
        $pdf = new Fpdf();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);
    
        // Add Payslip Details
        $pdf->Cell(0, 10, 'Payslip', 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->Cell(0, 10, 'Application ID: ' . $applicationId, 0, 1);
        $pdf->Cell(0, 10, 'Password: ' . $password, 0, 1); // Include password
        $pdf->Cell(0, 10, 'Employer Name: ' . $employerName, 0, 1);
        $pdf->Cell(0, 10, 'Employee Name: ' . $employeeName, 0, 1);
        $pdf->Cell(0, 10, 'Salary Month: ' . $salaryMonth, 0, 1);
        $pdf->Ln(5);
        $pdf->Cell(0, 10, 'Basic Pay: Rs. ' . number_format($basicPay), 0, 1);
        $pdf->Cell(0, 10, 'Deductions: Rs. ' . number_format($deductions), 0, 1);
        $pdf->Cell(0, 10, 'Net Pay: Rs. ' . number_format($netPay), 0, 1);
    
        // Output PDF for download
        return response($pdf->Output('S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="payslip.pdf"');
    }



    //extract payslip
    public function extractPayslip(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'authorization' => 'required|string',
            'x_api_key' => 'required|string',
            'applicationId' => 'required|string',
            //'password' => 'required|string',
            //'employerName' => 'required|string',
            //'employeeName' => 'required|string',
            'payslip' => 'required|file',
        ]);

        // Get dynamic values from the request
        $authorization = $request->input('authorization');
        $xApiKey = $request->input('x_api_key');
        $applicationId = $request->input('applicationId');
        $password = $request->input('password');
        $employerName = $request->input('employerName');
        $employeeName = $request->input('employeeName');
        $payslipFile = $request->file('payslip');

        // Set up CURL
        $url = 'https://api-ext-prod.tartanhq.com/aphrodite/external/v1/payslip';
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
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Authorization: Bearer ' . $authorization,
                'x-api-key: ' . $xApiKey,
            ],
            CURLOPT_POSTFIELDS => [
                'applicationId' => $applicationId,
                'password' => $password,
                'employerName' => $employerName,
                'employeeName' => $employeeName,
                'payslip' => curl_file_create($payslipFile->getPathname(), $payslipFile->getMimeType(), $payslipFile->getClientOriginalName()),
            ],
        ]);

        // Execute CURL request
        $response = curl_exec($curl);

        // Check for CURL errors
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => $error], 500);
        }

        // Close CURL and return response
        curl_close($curl);
        return response()->json(json_decode($response, true));
    }


    //get api
    public function getPayslipData($id, Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'authorization' => 'required|string',
            'x_api_key' => 'required|string',
        ]);

        // Retrieve headers dynamically from the request
        $authorization = $request->input('authorization');
        $xApiKey = $request->input('x_api_key');

        // Construct the API URL with the dynamic ID
        $url = "https://api-ext-prod.tartanhq.com/aphrodite/external/v1/applications/{$id}";

        // Set up cURL
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
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Authorization: Bearer ' . $authorization,
                'x-api-key: ' . $xApiKey,
            ],
        ]);

        // Execute cURL request
        $response = curl_exec($curl);

        // Check for cURL errors
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => $error], 500);
        }

        // Close cURL and return response
        curl_close($curl);
        return response()->json(json_decode($response, true));
    }

    
}
