<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CinController;
use App\Http\Controllers\GSTController;
use App\Http\Controllers\KYCController;
use App\Http\Controllers\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;







/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('request-otp',[GSTController::class,'requestOTP']);
Route::post('request-token',[GSTController::class,'requestToken']);
Route::post('extension_token',[GSTController::class,'extension_token']);

Route::post('saveGSTR1',[GSTController::class,'saveGSTR1']);


//authentication
Route::post('/authenticate_token',[KYCController::class,'authenticate']);

//Bank statements
Route::post('/verify_bank_account',[KYCController::class,'verifyBankAccount']);
Route::post('/verify_mobile_to_bank',[KYCController::class,'verifyMobileToBank']);


//Identify and Personal
Route::post('/verify_aadhar_to_pan',[KYCController::class,'verifyAadhaarToPan']);
Route::post('/aadhar_validation',[KYCController::class,'verifyAadhaarAdvanced']);


//Aadhaar Verification (w OTP)
Route::post('/get_otp',[KYCController::class,'verifyAadhaarOfflineOtp']);
Route::post('/submit_otp',[KYCController::class,'submitData']);


//nikita side apis 
//KYC Apis
Route::post('authenticate', [AuthController::class, 'authenticate']);

Route::post('verify-bank-account', [AuthController::class, 'verifyBankAccount']);
Route::post('mobile-to-bank-verification', [AuthController::class, 'mobileToBankVerification']);

//Aadhaar Verification (w OTP)
Route::post('aadhaar-get-otp', [AuthController::class, 'aadhaarGetOtp']);
Route::post('aadhaar-submit-otp', [AuthController::class, 'aadhaarSubmitOtp']);

//Aadhar To Pan Number
Route::post('aadhaar-to-pan', [AuthController::class, 'aadhaarToPan']);
//Aadhaar Validation (w/o OTP, w/o Demographic)
Route::post('aadhaar-advanced-verification', [AuthController::class, 'aadhaarAdvancedVerification']);
Route::post('verify', [AuthController::class, 'aadhaarVerify']);

// Mobile to Pan Number
Route::post('mobile-To-PanNumber', [VerificationController::class, 'mobileToPanNumber']);

//PAN - Detailed II
Route::post('pan-detailed-II', [VerificationController::class, 'panDetail']);

Route::post('pan-verification-basic', [VerificationController::class, 'panVerificationBasic']);
//Address Insight
Route::post('address-Insight', [VerificationController::class, 'addressInsight']);
// Address Geocode
Route::post('geocode-address', [VerificationController::class, 'geocodeAddress']);
//CA Membership Verification
Route::post('ca-verification', [VerificationController::class, 'caVerification']);
//CIN Pull - Basic
Route::post('cin-basic-verification', [VerificationController::class, 'cinBasic']);
//cin-advanced
Route::post('cin-pull-detailed', [VerificationController::class, 'cinPullDetailed']);
//Proprietor Hunter
Route::post('proprietor-hunter', [CinController::class, 'proprietorHunter']);
//business-pan-detailed
Route::post('business-pan-detailed', [CinController::class, 'businessPanDetailed']);
//Employer Default Check
Route::post('employer-check', [CinController::class, 'employerCheck']);
//din-advanced
Route::post('din-advanced', [CinController::class, 'dinAdvanced']);
//company-name-to-cin
Route::post('company-name-to-cin', [CinController::class, 'companyNameToCin']);
//Company Name to GST
Route::post('company-name-to-gst', [CinController::class, 'companyNameToGst']);
//Employer EPFO - Basic Information
Route::post('epfo-pull-basic', [CinController::class, 'epfoBasic']);
//Employer EPFO - Detailed Information
Route::post('epfo-detailed', [CinController::class, 'epfoDetailed']);
//FSSAI License Verification
Route::post('fssai-verification', [BusinessController::class, 'fssaiVerification']);
//Import - Export Code Verification
Route::post('importExport-verification', [BusinessController::class, 'importExportVerification']);
Route::post('gst-details', [BusinessController::class, 'verify']);