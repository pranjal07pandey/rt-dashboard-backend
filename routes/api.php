<?php

use Illuminate\Http\Request;



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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('docket', 'DocketAPIController@docket');
Route::post('login', 'Api\AuthController@login');
Route::post('registration','Api\AuthController@registration');
Route::get('getAppInfo','Api\AuthController@getAppInfo');
Route::get('validateEmail/{key}','Api\AuthController@validateEmail');

Route::group(['middleware' => 'apiAuthToken'], function() {
    // version 2.0 api
    Route::group(['prefix' => 'v2'], function() {
        //user Section
        Route::group(['prefix' => 'user','as' => 'api.','namespace' => 'Api\V2' ], function () {
            //docket Section

            Route::group(['prefix' => 'dockets'], function () {
                //DocketController
                Route::get('/', 'DocketController@index');
                //UserController

                Route::group(['prefix' => 'templates'], function () {
                    Route::get('/', 'DocketController@getAssignedDocketTemplateByUserId');
                    Route::get('/{key}', 'DocketController@show');
                });

            });

        });
    });



    //Docket Section
    Route::get('getDocketTemplateList', 'Api\DocketController@getDocketTemplateList');
    Route::get('getAssignedDocketTemplateByUserId', 'Api\DocketController@getAssignedDocketTemplateByUserId');


    Route::get('getDocketTemplateDetailsById/{key}', 'Api\DocketController@getDocketTemplateDetailsById');
    Route::get('v1/getDocketTemplateDetailsById/{key}', 'APIController@v1getDocketTemplateDetailsById');
    Route::post('v1/saveSentDefaultDocket','APIController@v1SaveSentDefaultDocket');
    Route::post('/gridPrefiller','APIController@getGridPrefiller');
    Route::post('/prefiller','APIController@getPrefiller');





    Route::get('getDefaultDocket', 'Api\DocketController@getDefaultDocket');// not used
    Route::get('docket/{id}','Api\DocketController@docket'); // not used
    Route::put('docket/{id}','Api\DocketController@update'); // not used


    Route::get('getEmployeeList', 'APIController@getEmployeeList');
    Route::post('postEmailUser', 'APIController@postEmailUser');
    Route::get('getFrequency', 'APIController@getFrequency');

    //messages api
    Route::get('getMessagesList', 'Api\MessageController@getMessagesList');
    Route::get('messages/{key}', 'Api\MessageController@messages');
    Route::get('message/{key}', 'Api\MessageController@message');
    Route::post('messageGroup/markAsReads', 'Api\MessageController@markAsReads');



    Route::post('saveSentDefaultDocket', 'APIController@saveSentDefaultDocket');
    Route::post('demo', 'APIController@demo');
    Route::get('getDocketList', 'APIController@getDocketList');
    Route::get('getLatestConversationList', 'APIController@getLatestConversationList');
    Route::get('getLatestEmailConversationList', 'APIController@getLatestEmailConversationList');
    Route::get('getLatestEmailInvoiceConversationList', 'APIController@getLatestEmailInvoiceConversationList');

    Route::get('getLatestDockets','APIController@getLatestDockets');
    Route::get('getLatestEmailDocketHome', 'APIController@getLatestEmailDocketHome');
    Route::get('getLatestEmailInvoiceHome', 'APIController@getLatestEmailInvoiceHome');

    Route::get('getConversationChatByUserId/{key}', 'APIController@getConversationChatByUserId');
    Route::get('getEmailConversationChatByUserId/{key}', 'APIController@getEmailConversationChatByUserId');
    Route::post('getTimelineByRecipients','APIController@getTimelineChatByRecipients');
    Route::post('getEmailTimelineByRecipients','APIController@getEmailTimelineByRecipients');
    Route::get('getEmailTimelineByUserId/{key}','APIController@getEmailTimelineByUserId');
    Route::get('getEmailInvoiceTimelineByUserId/{key}','APIController@getEmailInvoiceTimelineByUserId');

    Route::get('getDocketDetailsById/{key}','APIController@getDocketDetailsById');
    Route::get('getEmailDocketDetailsById/{key}', 'APIController@getEmailDocketDetailsById');


    //dockets
    Route::group(['prefix' => 'dockets'], function () {
        Route::group(['prefix' => 'emailed'], function(){
            // Route::get('{key}', 'Api\EmailDocketController@show');
            Route::get('{key}', 'Api\V2\EmailDocketController@show');
        });
    });

    Route::post('saveEmailClient','APIController@saveEmailClient');




    //docket/invoice forwarding
    Route::get('forwardDocketById/{key}', 'APIController@forwardDocketById');
    Route::get('forwardInvoiceById/{key}', 'APIController@forwardInvoiceById');
    Route::get('forwardEmailDocketById/{key}', 'APIController@forwardEmailDocketById');
    Route::get('forwardEmailInvoiceById/{key}', 'APIController@forwardEmailInvoiceById');

    Route::get('getInvoiceableDocketList/{key}','APIController@getInvoiceableDocketList');

    Route::get('getInvoiceableEmailDocketList/{key}','APIController@getInvoiceableEmailDocketList');


    Route::post('approveDocketById', 'APIController@approveDocketById');

    Route::post('filterDocket', 'APIController@filterDocket');
    Route::post('filterDocument', 'APIController@filterDocument');

    Route::post('searchByKeywordDocket', 'APIController@searchByKeywordDocket');
    Route::post('searchByKeywordEmailDocket', 'APIController@searchByKeywordEmailDocket');
    Route::post('searchByKeywordInvoice', 'APIController@searchByKeywordInvoice');
    Route::post('searchByKeywordEmailInvoice', 'APIController@searchByKeywordEmailInvoice');

    Route::get('companyDockets', 'APIController@companyDockets');
    Route::get('getDocketDetailByIdWebView/{key}', 'APIController@getDocketDetailByIdWebView');
    Route::get('getEmailDocketDetailsByIdWebView/{id}', 'APIController@getEmailDocketDetailsByIdWebView');



    //*********************invoice section api*******************//
    // Route::get('getInvoiceTemplateList', 'Api\InvoiceController@getInvoiceTemplateList');
    Route::get('getInvoiceTemplateList', 'Api\V2\InvoiceController@getInvoiceTemplateList');
    // Route::get('getInvoiceTemplateDetailsById/{key}', 'Api\InvoiceController@getInvoiceTemplateDetailsById');
    Route::get('getInvoiceTemplateDetailsById/{key}', 'Api\V2\InvoiceController@getInvoiceTemplateDetailsById');
    Route::post('saveSentInvoice', 'Api\InvoiceController@saveSentInvoice');
    Route::get('getLatestInvoiceHome', 'Api\InvoiceController@getLatestInvoiceHome');
    Route::get('getLatestInvoiceList', 'Api\InvoiceController@getLatestInvoiceList');
    Route::get('getConversationInvoiceChatByUserId/{key}', 'Api\InvoiceController@getConversationInvoiceChatByUserId');
    Route::get('getInvoiceDetailsById/{key}', 'Api\InvoiceController@getInvoiceDetailsById');
    Route::get('getEmailInvoiceDetailsById/{key}','Api\InvoiceController@getEmailInvoiceDetailsById');
    Route::get('getInvoiceTimelineByUserId/{key}','Api\InvoiceController@getInvoiceTimelineByUserId');


    //Invoiceable Docket filter
    Route::post('/invoiceDocketFilterParameter', 'APIController@getInvoiceDocketFilterParameter');
    Route::post('/filterInvoiceableDocket', 'APIController@filterInvoiceableDocket');
    Route::post('/invoiceEmailDocketFilterParameter', 'APIController@getInvoiceEmailDocketFilterParameter');
    Route::post('/filterInvoiceableEmailDocket', 'APIController@filterInvoiceableEmailDocket');
    Route::get('myPermission','APIController@myPermission');

    //draft
    Route::post('draftImageSave','APIController@draftImageSave');
    Route::post('saveDocketDraft','APIController@saveDocketDraft');
    Route::get('getDocketDraftList','APIController@getDocketDraftList');
    Route::post('updateDocketDraft','APIController@updateDocketDraft');
    Route::post('user/nextDocketId','APIController@nextDocketId');















    Route::get('logout', 'APIController@logout');
    Route::post('changePassword', 'APIController@changePassword');
    Route::post('profileUpdate', 'APIController@profileUpdate');
    Route::post('nameUpdate', 'APIController@nameUpdate');
    Route::post('sentAcivity', 'APIController@sentAcivity');
    Route::get('emailUserList','APIController@emailUserList');



    //user/group messages
    Route::get('getNotificationList', 'APIController@getNotificationList');
    Route::get('markAsRead/{key}','APIController@marAsRead');



    //user/group messages
    Route::get('getNotificationListUpdateAndroid', 'APIController@getNotificationListUpdateAndroid');
    Route::get('markAsRead/{key}','APIController@marAsRead');



    //Timer
    Route::get('getcheckOldTimerSession', 'Api\TimerController@getcheckOldTimerSession');
    Route::post('startNewTimer', 'Api\TimerController@startNewTimerSession');
    Route::post('finishTimer', 'Api\TimerController@finishTimerSession');
    Route::get('getAllSavedTimer', 'Api\TimerController@getAllSavedTimer');
    Route::post('pauseTimer', 'Api\TimerController@pauseTimer');
    Route::post('continueTimer', 'Api\TimerController@continueTimer');
    Route::post('submitTimerComments','Api\TimerController@submitTimerComments');
    Route::post('searchTimer','Api\TimerController@searchTimer');
    Route::post('timerAttachedTag','Api\TimerController@timerAttachedTag');
    Route::get('timerDetailsById/{id}','APIController@timerDetailsById');
    Route::get('markAllAsRead','APIController@markAllAsRead');


    //docket Rejection
    Route::post('sentDocketReject', 'APIController@sentDocketReject');

    //ios in app purchase
    Route::post('receiptValidator', 'APIController@receiptValidator');
    Route::get('subscriptionStatus','APIController@subscriptionStatus');
    Route::post('updateDeviceToken','APIController@updateDeviceToken');

    //prefiller
    Route::post('saveGridPrefiller', 'APIController@saveGridPrefiller');
    Route::post('savePrefiller','APIController@savePrefiller');
    Route::get('numberSystem','APIController@numberSystem');
    Route::post('deleteDraft','APIController@deleteDraft');

    Route::post('v1/saveSentDefaultDockets', 'APIController@v1SaveSentDefaultDockets');

    Route::post('docket/updateAprovalMethod','APIController@updateDocketAprovalMethod');


    Route::get('storeImages3','APIController@storeImages3');

    Route::get('task/assign','Api\V2\APIController@taskManagement');
    Route::get('task/assign/detail','Api\V2\APIController@taskManagementById');
    Route::post('task/assign-status','Api\V2\APIController@taskStatusManagement');

    Route::post('draft/edit/{draft_id}', 'DocketManager\DocketsController@apiAssignDocketdraftUser')->name('dockets.assign.draftEdit');
});




require 'v2.php';

Route::group(['prefix' => 'web'], function () {
    Route::post('email/user', 'Api\V2\APIController@postEmailUser');
    Route::post('email/client','Api\V2\APIController@saveEmailClient');
    Route::post('docket/fields/{docket_templete_id}','APIController@v1getDocketTemplateDetailsById');
    // Route::post('docket/fields/{docket_templete_id}','Api\V2\APIController@v1getDocketTemplateDetailsById');
    Route::post('files/upload', 'Api\V2\APIController@uploadFiles');
    Route::post('docket/sent', 'Api\V2\APIController@webSendDocket');
    Route::post('/gridPrefiller','APIController@getGridPrefiller');
    Route::post('/prefiller','APIController@getPrefiller');

});


//apis for admin dashboard

// Route::get('companyDetails/',function(){
//     return Company::all();
// });


Route::get('companyDetails/', 'AdminDashboard\DashboardController@getCompanyDetails');
Route::get('companyDetails/{company_id}/', 'AdminDashboard\DashboardController@getCompanyById');
Route::put('companyDetails/{company_id}/', 'AdminDashboard\DashboardController@updateCompanyDetails');

Route::get('companyCountsByMonth/', 'AdminDashboard\DashboardController@companiesCountByMonth');
Route::get('employeeDetails/{company_id}/','AdminDashboard\DashboardController@getEmployeesFromCompanyId');
Route::get('docketDetails/{company_id}/','AdminDashboard\DashboardController@getDocketsFromCompanyId');
Route::get('docketDetailsFromUserId/{user_id}/','AdminDashboard\DashboardController@getDocketsFromUserId');
Route::get('invoiceDetailsFromUserId/{user_id}/', 'AdminDashboard\DashboardController@getInvoicesFromUserId');

Route::get('allUsers/','AdminDashboard\DashboardController@getAllUsers');
Route::get('userById/{user_id}/','AdminDashboard\DashboardController@getUserById');
Route::put('userInfo/{user_id}/', 'AdminDashboard\DashboardController@updateUserInfo');
Route::get('companyByUserId/{user_id}/', 'AdminDashboard\DashboardController@getCompanyFromUserId');

Route::get('usersCountByMonth/', 'AdminDashboard\DashboardController@usersCountByMonth');
Route::get('filterUsersByDate/{start_date}/{end_date}/','AdminDashboard\DashboardController@filterUsersByDate');

Route::get('mostUsedDocket/{company_id}/', 'AdminDashboard\DashboardController@mostFrequentlyUsedDocket');

//get dockets and invoices (email dockets and email invoices as well) per month
Route::get('docketsCountByMonth/', 'AdminDashboard\DashboardController@docketsCountByMonth');
Route::get('invoicesCountByMonth/', 'AdminDashboard\DashboardController@invoicesCountByMonth');
Route::get('emailDocketsCountByMonth/', 'AdminDashboard\DashboardController@emailDocketsByMonth');
Route::get('emailInvoicesCountByMonth/', 'AdminDashboard\DashboardController@emailInvoicesByMonth');