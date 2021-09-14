<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['cors', 'json.response', 'auth:api'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['cors', 'json.response']], function () {

    // public routes
    Route::post('/login', 'Auth\ApiAuthController@login')->name('login.api');
    Route::post('/register','Auth\ApiAuthController@register')->name('register.api');
    Route::post('/logout', 'Auth\ApiAuthController@logout')->name('logout.api');
    Route::get('/getUserExist', 'GETController@getUserExist')->name('getUserExist');
    Route::get('/getQuoteDetail', 'GETController@getQuoteDetail')->name('getQuoteDetail');
    Route::get('/getQuoteDetailConfirmed', 'GETController@getQuoteDetailConfirmed')->name('getQuoteDetailConfirmed');
});

Route::middleware('auth:api')->group(function () {
    //Route::get('/articles', 'ArticleController@index')->middleware('api.admin')->name('articles')
    
    // GET Request
    Route::get('/getPOHdr', 'GETController@getPOHdr')->middleware('api.admin')->name('getPOHdr');
    Route::get('/getSumHdr', 'GETController@getSumHdr')->middleware('api.admin')->name('getSumHdr');
    Route::get('/getPODtl', 'GETController@getPODtl')->middleware('api.admin')->name('getPODtl');
    Route::get('/getDelivHdr', 'GETController@getDelivHdr')->middleware('api.admin')->name('getDelivHdr');
    Route::get('/getDelivDtl', 'GETController@getDelivDtl')->middleware('api.admin')->name('getDelivDtl');
    Route::get('/trackOrder', 'GETController@trackOrder')->middleware('api.admin')->name('trackOrder');
    Route::get('/dashboardOrderCustGroup', 'GETController@dashboardOrderCustGroup')->middleware('api.admin')->name('dashboardOrderCustGroup');
    Route::get('/dashboardOrderbyCustID', 'GETController@dashboardOrderbyCustID')->middleware('api.admin')->name('dashboardOrderbyCustID');
    Route::get('/getOutstandingDeliv', 'GETController@getOutstandingDeliv')->middleware('api.admin')->name('getOutstandingDeliv');
    Route::get('/getListEntity', 'GETController@listEntity')->middleware('api.admin')->name('getListEntity');
    Route::post('/getListProduk', 'GETController@getListProduk')->middleware('api.admin')->name('getListProduk');




    // POST Request
    Route::post('/setApprove', 'POSTController@setApprove')->middleware('api.admin')->name('setApprove');
    Route::post('/setReject', 'POSTController@setReject')->middleware('api.admin')->name('setReject');
    Route::post('/setUnApprove', 'POSTController@setUnApprove')->middleware('api.admin')->name('setUnApprove');
    Route::post('/setDelivConfirm', 'POSTController@setDelivConfirm')->middleware('api.admin')->name('setDelivConfirm');
    Route::post('/submitApproval', 'POSTController@submitApproval')->middleware('api.admin')->name('submitApproval');
    
    
});

