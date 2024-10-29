<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GSTController;
use App\Http\Controllers\KYCController;

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
//hii samir