<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(["middleware" => ["shopify-auth"]], function(){

// ShopController
    //install and unintstall
    Route::get('install','App\Http\Controllers\ShopController@generate_install_url')->name('install');
    Route::any('generate_token','App\Http\Controllers\ShopController@generate_and_save_token')->name('generate_token');
    Route::any('uninstall','App\Http\Controllers\ShopController@uninstall')->name('uninstall');
    //gdpr
    Route::any('gdpr_view_customer','App\Http\Controllers\ShopController@gdpr_view_customer');
    Route::any('gdpr_delete_customer','App\Http\Controllers\ShopController@gdpr_delete_customer');
    Route::any('gdpr_delete_shop','App\Http\Controllers\ShopController@gdpr_delete_shop');
    //app view and save setting
    Route::get('app_view','App\Http\Controllers\ShopController@app_view')->name('app_view');
    Route::get('/','App\Http\Controllers\ShopController@app_view')->name('app_view');
    Route::post('/fb_login','App\Http\Controllers\FacebookController@login')->name('fb_login');
    Route::post('/fb_posts','App\Http\Controllers\FacebookController@posts')->name('fb_posts');

    
    Route::post('save_setting','App\Http\Controllers\ShopController@saveSetting')->name('saveSetting');
    Route::post('get_settings','App\Http\Controllers\ShopController@getSettings')->name('getSettings');

// ShopController

// BillingController
    Route::post('create_charge','App\Http\Controllers\BillingController@createCharge')->name('createCharge');
    Route::post('cancel_charge','App\Http\Controllers\BillingController@cancelCharge')->name('cancelCharge');
    Route::post('check_billing','App\Http\Controllers\BillingController@check_billing')->name('checkCurrentChargeStatus');
    Route::get('create_charge/{id}','App\Http\Controllers\BillingController@create_charge')->name('charge.create');
    Route::get('change_store_plan/{id}','App\Http\Controllers\BillingController@change_plan')->name('plan.change');

    Route::post('get_current_charge_status','App\Http\Controllers\BillingController@checkCurrentChargeStatus')->name('checkCurrentChargeStatus');
// BillingController

// AppController
Route::post('get_variants','App\Http\Controllers\AppController@getVariants')->name('getVariants');
// AppController

});
