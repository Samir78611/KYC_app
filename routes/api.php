<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CinController;
use App\Http\Controllers\DigilockerController;
use App\Http\Controllers\GSTController;
use App\Http\Controllers\KYCController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\PanController;
use App\Http\Controllers\udyamController;


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

Route::post('/generate-payslip', [App\Http\Controllers\PayslipController::class, 'generatePayslip']);
Route::post('/extract-payslip', [PayslipController::class, 'extractPayslip']);
Route::get('/get-payslip-data/{id}', [PayslipController::class, 'getPayslipData']);


//authentication
//Route::post('/authenticate_token',[KYCController::class,'authenticate']);

////Bank statements
//Route::post('/verify_bank_account',[KYCController::class,'verifyBankAccount']);
//Route::post('/verify_mobile_to_bank',[KYCController::class,'verifyMobileToBank']);


////Identify and Personal
//Route::post('/verify_aadhar_to_pan',[KYCController::class,'verifyAadhaarToPan']);
//Route::post('/aadhar_validation',[KYCController::class,'verifyAadhaarAdvanced']);


////Aadhaar Verification (w OTP)
//Route::post('/get_otp',[KYCController::class,'verifyAadhaarOfflineOtp']);
//Route::post('/submit_otp',[KYCController::class,'submitData']);


//nikita side apis ..
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
//CIN Pull - Elementary
Route::post('cin-intermediate', [VerificationController::class, 'cinIntermediate']);
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
//KYB 1 (Company Search)
Route::post('kyb-company-search', [BusinessController::class, 'companySearch']);
//PAN to GST Detailed
Route::post('pan-gst-detailed', [BusinessController::class, 'panGstAdvanced']);
// Basic Mobile Name Lookup (w/o OTP)
Route::post('basic-mobile-name', [BusinessController::class, 'basicMobileName']);
//Mobile Info (w OTP)
Route::post('mobile-number-verify-generate-otp', [BusinessController::class, 'generateOtp']);
Route::post('mobile-number-verify-submit-otp', [BusinessController::class, 'submitOtp']);
//Advance work email verification (w/o OTP)
Route::post('advance-work-email-verify', [BusinessController::class, 'advanceWorkEmailVerify']);
//Advance work email verification (w OTP)
Route::post('advance-work-email-verify-withOtp', [BusinessController::class, 'verifyWorkEmailOtp']);
Route::post('email-verify-requestOtp', [BusinessController::class, 'emailVerificationRequestOtp']);
Route::post('submit-verify-requestOtp', [BusinessController::class, 'SubmitWorkEmailOtp']);
//Email Verification with OTP
Route::get('/email-verification-with-generate-otp',[BusinessController::class,'verifyEmail']);
Route::get('/email-verification-with-submit-otp',[BusinessController::class,'verifyOtp']);
//Email Verification (w/o OTP)
Route::get('/email-verification-without-otp',[BusinessController::class,'EV_without_otp']);
//DigiLocker - Get File
Route::post('digilocker-file', [DigilockerController::class, 'getDigiLockerFile']);
//DigiLocker - Get issued file list
Route::post('digilocker-issued-file-list', [DigilockerController::class, 'getDigiLockerIssuedFileList']);
//DigiLocker - Get self issued document
Route::post('get-all-issued-documents', [DigilockerController::class, 'getAllIssuedDocuments']);
//Legal Verification APIs
Route::post('verify-court-record', [BusinessController::class, 'verifyCourtRecord']);
Route::post('aadhar-ocr', [DigilockerController::class, 'processAadhaarOcr']);


//pan ocr
Route::post('pan-ocr', [PanController::class, 'panOcrApi']);
//udyam //Non Consented Data Fetch //consented data
Route::get('cosented-data-fetched/{id}',[udyamController::class,'getApplication']);
Route::get('get-non-consented-data-fetch/{id}', [VerificationController::class, 'getApplicationById']);
Route::post('udyam-create-link', [udyamController::class, 'udyamCreateLink']);
Route::get('/udyam/fetch/{jobId}', [UdyamController::class, 'fetchDetails']);

//Itr apis
Route::post('/createItrs-Link', [PanController::class, 'createItrLink']);
Route::post('verifyPan-input', [PanController::class, 'verifyPan']);
Route::get('async-status-api', [PanController::class, 'getLoginStatus']);
Route::post('password-input', [PanController::class, 'itrLogin']);
Route::post('itr-forgot-password', [PanController::class, 'forgotPassword']);
Route::post('itr-forgot-password-otp', [PanController::class, 'forgotPasswordOtp']);
Route::get('get-itr-data/{id}',[PanController::class,'getItrData']);
Route::get('get-itr-data-download/{id}',[PanController::class,'getItrDataDownload']);
Route::post('/change-password', [PanController::class, 'changePassword']);

//Consented External Login udyam
Route::post('consented-external-login', [UdyamController::class, 'consentedExternalLogin']);
Route::post('udyam-external-login',[PanController::class,'initiateUdyam']);
Route::get('udyam-status/{jobId}', [PanController::class, 'udyamStatus']);
Route::post('consented-otp-login', [UdyamController::class, 'consentedOtpLogin']);
Route::post('verify-bank-account-penny-less', [AuthController::class, 'bankAccountPennyLess']);

