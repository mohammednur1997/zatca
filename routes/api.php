<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZatcaController;

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

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::get('generate-csr', [ZatcaController::class, 'generate_csr']);
Route::get('signing', [ZatcaController::class, 'signing_invoice']);
Route::get('signing_invoice_osama', [ZatcaController::class, 'signing_invoice_osama']);

Route::get('generate_qr', [ZatcaController::class, 'generate_qr']);

