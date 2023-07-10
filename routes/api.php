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

    Route::get('/getCustSvrKMB', 'GETController@getCustSvrKMB')->name('getCustSvrKMB');
    Route::get('/getSalesSvrKMB', 'GETController@getSalesSvrKMB')->name('getSalesSvrKMB');

    Route::get('/getCustSvrVivo', 'GETController@getCustSvrVivo')->name('getCustSvrVivo');
    Route::get('/getSalesSvrVivo', 'GETController@getSalesSvrVivo')->name('getSalesSvrVivo');
    Route::get('/getOrderSvrVivo', 'GETController@getOrderSvrVivo')->name('getOrderSvrVivo');
    Route::post('/postDeclineOrderSvrVivo', 'POSTController@postDeclineOrderSvrVivo');

    Route::get('/getCustSvrGbrk', 'GETController@getCustSvrGbrk')->name('getCustSvrGbrk');
    Route::get('/getSalesSvrGbrk', 'GETController@getSalesSvrGbrk')->name('getSalesSvrGbrk');
    Route::get('/getOrderSvrGbrk', 'GETController@getOrderSvrGbrk')->name('getOrderSvrGbrk');
    Route::post('/postDeclineOrderSvrGbrk', 'POSTController@postDeclineOrderSvrGbrk');
    Route::get('/getMachineTypeSvrGbrk', 'GETController@getMachineTypeSvrGbrk')->name('getMachineTypeSvrGbrk');
    Route::get('/getBarangJadiSvrGbrk', 'GETController@getBarangJadiSvrGbrk')->name('getBarangJadiSvrGbrk');
    Route::post('/getDetailBarangJadiSvrGbrk', 'GETController@getDetailBarangJadiSvrGbrk')->name('getDetailBarangJadiSvrGbrk');
    
    Route::middleware('auth:api')->group(function () {
        Route::get('/getListEntity', 'GETController@listEntity')->middleware('api.admin')->name('getListEntity');
        Route::post('/getListProduk', 'GETController@getListProduk')->middleware('api.admin')->name('getListProduk');
        Route::get('/getCoilID', 'GETController@getCoilID')->middleware('api.admin')->name('getCoilID');

    });
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
    Route::get('/getListInvPiutangCust', 'GETController@getListInvPiutangCust')->middleware('api.admin')->name('getListInvPiutangCust');
    Route::get('/getListGiroCust', 'GETController@getListGiroCust')->middleware('api.admin')->name('getListGiroCust');


    // POST Request
    Route::post('/setApprove', 'POSTController@setApprove')->middleware('api.admin')->name('setApprove');
    Route::post('/setReject', 'POSTController@setReject')->middleware('api.admin')->name('setReject');
    Route::post('/setUnApprove', 'POSTController@setUnApprove')->middleware('api.admin')->name('setUnApprove');
    Route::post('/setDelivConfirm', 'POSTController@setDelivConfirm')->middleware('api.admin')->name('setDelivConfirm');
    Route::post('/submitApproval', 'POSTController@submitApproval')->middleware('api.admin')->name('submitApproval');
    Route::post('/getListOrder', 'POSTController@getListOrder')->middleware('api.admin')->name('getListOrder');
	Route::post('/getOrderTracking', 'POSTController@getOrderTracking')->middleware('api.admin')->name('getOrderTracking');
	Route::post('/getListOrdertoClose', 'POSTController@getListOrdertoClose')->middleware('api.admin')->name('getListOrdertoClose');
	Route::post('/getPricePreorder', 'POSTController@getPricePreorder')->middleware('api.admin')->name('getPricePreorder');    
    
});

