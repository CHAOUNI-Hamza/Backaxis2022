<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Agency\AxisController;
use App\Http\Controllers\Contact\ContactController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Service\ServiceController;
use App\Http\Controllers\Produit\ProduitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Front\FrontController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([

    'middleware' => 'api',
    'prefix' => 'v1/admin'

], function ($router) {

    Route::post('login',[ UserController::class, 'login' ])->name('admin.'); // Login Admin
    Route::post('logout',[ UserController::class, 'logout' ])->name('admin.'); // Logout Admin
    Route::post('refresh',[ UserController::class, 'refresh' ])->name('admin.'); // Refresh Admin
    Route::get('me',[ UserController::class, 'me' ])->name('admin.'); // Auth Admin
    Route::post('store',[ UserController::class, 'store' ])->name('admin.'); // Create Admin
    Route::get('index',[ UserController::class, 'index' ])->name('admin.'); // Lists Admin
    Route::post('update/{id}',[ UserController::class, 'update' ])->name('admin.'); // Update Admin 
    Route::get('trashed',[ UserController::class, 'trashed' ])->name('admin.'); // Trashed Admin 
    Route::delete('destroy/{id}',[ UserController::class, 'destroy' ])->name('admin.'); // Destroy Admin 
    Route::post('restore/{id}',[ UserController::class, 'restore' ])->name('admin.'); // Restore Admin 
    Route::post('forced/{id}',[ UserController::class, 'forced' ])->name('admin.'); // Forced Admin 
    Route::post('/forgot-password',[ UserController::class, 'forgotpassword' ])->name('admin.'); // Forgot Password Admin 
    Route::post('/reset-password',[ UserController::class, 'resetpassword' ])->name('admin.'); // Forgot Password Admin



});



 // Start Routes Api < CONTACT >
 Route::group([

    //'middleware' => 'api',
    'prefix' => 'v1/contacts',
 
 ],   function ($router) {
 
     Route::post('store',[ ContactController::class, 'store' ])->name('contact.'); // Create Contact
     Route::get('index',[ ContactController::class, 'index' ])->name('contact.'); // Lists Contact
     Route::get('trashed',[ ContactController::class, 'trashed' ])->name('contact.'); // Trashed Contact 
     Route::delete('destroy/{id}',[ ContactController::class, 'destroy' ])->name('contact.'); // Destroy Contact 
     Route::post('restore/{id}',[ ContactController::class, 'restore' ])->name('contact.'); // Restore Contact 
     Route::post('forced/{id}',[ ContactController::class, 'forced' ])->name('contact.'); // Forced Contact 
 
 });
 // End Routes Api < CONTACT >


 // Start Routes Api < CLIENT >
 Route::group([

    //'middleware' => 'api',
    'prefix' => 'v1/clients',
 
 ],   function ($router) {
 
     Route::post('store',[ ClientController::class, 'store' ])->name('client.'); // Create Client
     Route::post('update/{id}',[ ClientController::class, 'update' ])->name('client.'); // Update Client
     Route::get('index',[ ClientController::class, 'index' ])->name('client.'); // Lists Client
     Route::get('trashed',[ ClientController::class, 'trashed' ])->name('client.'); // Trashed Client 
     Route::delete('destroy/{id}',[ ClientController::class, 'destroy' ])->name('client.'); // Destroy Client 
     Route::post('restore/{id}',[ ClientController::class, 'restore' ])->name('client.'); // Restore Client 
     Route::post('forced/{id}',[ ClientController::class, 'forced' ])->name('client.'); // Forced Client 
 
 });
 // End Routes Api < CLIENT >


  // Start Routes Api < SERVICE >
  Route::group([

    //'middleware' => 'api',
    'prefix' => 'v1/services',
 
 ],   function ($router) {
 
     Route::post('store',[ ServiceController::class, 'store' ])->name('client.'); // Create Service
     Route::post('update/{id}',[ ServiceController::class, 'update' ])->name('client.'); // Update Service
     Route::get('index',[ ServiceController::class, 'index' ])->name('client.'); // Lists Service
     Route::get('trashed',[ ServiceController::class, 'trashed' ])->name('client.'); // Trashed Service 
     Route::delete('destroy/{id}',[ ServiceController::class, 'destroy' ])->name('client.'); // Destroy Service 
     Route::post('restore/{id}',[ ServiceController::class, 'restore' ])->name('client.'); // Restore Service 
     Route::post('forced/{id}',[ ServiceController::class, 'forced' ])->name('client.'); // Forced Service 
 
 });
 // End Routes Api < SERVICE >

// Start Routes Api < AXIS >
   Route::group([

    //'middleware' => 'api',
    'prefix' => 'v1/company',
 
 ],   function ($router) {
 
     Route::post('store',[ CompanyController::class, 'store' ])->name('client.'); // Create Company
     Route::post('update/{id}',[ CompanyController::class, 'update' ])->name('client.'); // Update Company
     Route::get('index',[ CompanyController::class, 'index' ])->name('client.'); // Lists Company
     Route::get('trashed',[ CompanyController::class, 'trashed' ])->name('client.'); // Trashed Company 
     Route::delete('destroy/{id}',[ CompanyController::class, 'destroy' ])->name('client.'); // Destroy Company 
     Route::post('restore/{id}',[ CompanyController::class, 'restore' ])->name('client.'); // Restore Company 
     Route::post('forced/{id}',[ CompanyController::class, 'forced' ])->name('client.'); // Forced Company 
 
 });
 // End Routes Api < AXIS >


 // Start Routes Api < PRODUIT >
 Route::group([

    //'middleware' => 'api',
    'prefix' => 'v1/produit',
 
 ],   function ($router) {
 
     Route::post('store',[ ProduitController::class, 'store' ])->name('client.'); // Create Produit
     Route::post('update/{id}',[ ProduitController::class, 'update' ])->name('client.'); // Update Produit
     Route::get('index',[ ProduitController::class, 'index' ])->name('client.'); // Lists Produit
     Route::get('trashed',[ ProduitController::class, 'trashed' ])->name('client.'); // Trashed Produit 
     Route::delete('destroy/{id}',[ ProduitController::class, 'destroy' ])->name('client.'); // Destroy Produit 
     Route::post('restore/{id}',[ ProduitController::class, 'restore' ])->name('client.'); // Restore Produit 
     Route::post('forced/{id}',[ ProduitController::class, 'forced' ])->name('client.'); // Forced Produit 
 
 });
 // End Routes Api < PRODUIT >

 // Start Routes Api < Front >
 Route::group([

    //'middleware' => 'api',
    'prefix' => 'v1/front',
 
 ],   function ($router) {
 
     Route::get('axis',[ FrontController::class, 'axis' ])->name('axis'); // get Axis
     Route::get('clients',[ FrontController::class, 'clients' ])->name('clients'); // get Clients
     Route::get('produits',[ FrontController::class, 'produits' ])->name('produits'); // get Produits
     Route::get('services',[ FrontController::class, 'services' ])->name('services'); // get Services 
 
 });
 // End Routes Api < PRODUIT >
