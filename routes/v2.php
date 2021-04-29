<?php

Route::group(['middleware' => 'auth:api'], function() {
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
        
        //Docket Section
        Route::get('docket/templete/list', 'Api\DocketController@getDocketTemplateList');
        Route::get('docket/templete/userid', 'Api\DocketController@getAssignedDocketTemplateByUserId');


        Route::get('docket/templete/detail/{key}', 'Api\DocketController@getDocketTemplateDetailsById');
        Route::get('v1/getDocketTemplateDetailsById/{key}', 'Api\V2\APIController@v1getDocketTemplateDetailsById');
        Route::post('v1/saveSentDefaultDocket','Api\V2\APIController@v1SaveSentDefaultDocket');

        Route::get('default/docket', 'Api\DocketController@getDefaultDocket');
        Route::get('docket/{id}','Api\DocketController@docket');
        Route::put('docket/{id}','Api\DocketController@update');

        Route::get('employee/list', 'Api\V2\APIController@getEmployeeList');
        Route::post('email/user', 'Api\V2\APIController@postEmailUser');
        Route::get('frequency', 'Api\V2\APIController@getFrequency');

        //messages api
        Route::get('message/list', 'Api\V2\MessageController@getMessagesList');
        Route::get('messages/{key}', 'Api\V2\MessageController@messages');
        Route::get('message/{key}', 'Api\V2\MessageController@message');
        Route::post('message/group/read', 'Api\V2\MessageController@markAsReads');



        Route::post('saveSentDefaultDocket', 'APIController@saveSentDefaultDocket');
        Route::post('demo', 'APIController@demo');

        Route::get('dockets/list', 'Api\V2\APIController@getDocketList');
        Route::group(['prefix' => 'latest'], function () {
            Route::get('conversation/list', 'Api\V2\APIController@getLatestConversationList');
            Route::get('email/conservation/list', 'Api\V2\APIController@getLatestEmailConversationList');
            Route::get('email/invoice/conservation/list', 'Api\V2\APIController@getLatestEmailInvoiceConversationList');
            Route::get('dockets','Api\V2\APIController@getLatestDockets');
            Route::get('email/docket', 'Api\V2\APIController@getLatestEmailDocketHome');
            Route::get('email/invoice', 'Api\V2\APIController@getLatestEmailInvoiceHome');
    
        });
        
        Route::get('conversation/chat/{key}', 'Api\V2\APIController@getConversationChatByUserId');
        Route::get('email/conversation/chat/{key}', 'Api\V2\APIController@getEmailConversationChatByUserId');
        Route::post('timeline/recipients','Api\V2\APIController@getTimelineChatByRecipients');
        Route::post('email/timeline/recipients','Api\V2\APIController@getEmailTimelineByRecipients');
        Route::get('email/timeline/{key}','Api\V2\APIController@getEmailTimelineByUserId');
        Route::get('email/invoice/timeline/{key}','Api\V2\APIController@getEmailInvoiceTimelineByUserId');

        Route::get('docket/detail/{key}','Api\V2\APIController@getDocketDetailsById');
        Route::get('email/docket/detail/{key}', 'Api\V2\APIController@getEmailDocketDetailsById');


        //dockets
        Route::group(['prefix' => 'dockets'], function () {
            Route::group(['prefix' => 'emailed'], function(){
                // Route::get('{key}', 'Api\EmailDocketController@show');
                Route::get('{key}', 'Api\V2\EmailDocketController@show');
            });
        });

        Route::post('email/client','Api\V2\APIController@saveEmailClient');




        //docket/invoice forwarding
        Route::group(['prefix' => 'forward'], function () {
            Route::get('docket/{key}', 'Api\V2\APIController@forwardDocketById');
            Route::get('invoice/{key}', 'Api\V2\APIController@forwardInvoiceById');
            Route::get('email/docket/{key}', 'Api\V2\APIController@forwardEmailDocketById');
            Route::get('email/invoice/{key}', 'Api\V2\APIController@forwardEmailInvoiceById');
        });
        

        Route::get('invoice/docket/list/{key}','Api\V2\APIController@getInvoiceableDocketList');
        Route::get('getInvoiceableEmailDocketList/{key}','Api\V2\APIController@getInvoiceableEmailDocketList');
        Route::post('approve/docket', 'Api\V2\APIController@approveDocketById');
        Route::post('filter/docket', 'Api\V2\APIController@filterDocket');
        Route::post('filter/document', 'Api\V2\APIController@filterDocument');

        Route::group(['prefix' => 'search/keyword'], function () {
            Route::post('docket', 'Api\V2\APIController@searchByKeywordDocket');
            Route::post('email/docket', 'Api\V2\APIController@searchByKeywordEmailDocket');
            Route::post('invoice', 'Api\V2\APIController@searchByKeywordInvoice');
            Route::post('email/invoice', 'Api\V2\APIController@searchByKeywordEmailInvoice');
        });
        

        Route::get('company/dockets', 'Api\V2\APIController@companyDockets');
        Route::get('docket/detail/webview/{key}', 'Api\V2\APIController@getDocketDetailByIdWebView');
        Route::get('email/docket/detail/webview/{id}', 'Api\V2\APIController@getEmailDocketDetailsByIdWebView');



        //*********************invoice section api*******************//
        Route::get('getInvoiceTemplateList', 'Api\V2\InvoiceController@getInvoiceTemplateList');
        Route::get('getInvoiceTemplateDetailsById/{key}', 'Api\V2\InvoiceController@getInvoiceTemplateDetailsById');
        Route::post('sent/invoice', 'Api\V2\InvoiceController@saveSentInvoice');
        Route::get('latest/invoice/home', 'Api\V2\InvoiceController@getLatestInvoiceHome');
        Route::get('latest/invoice/list', 'Api\V2\InvoiceController@getLatestInvoiceList');
        Route::get('conversation/invoice/chat/{key}', 'Api\V2\InvoiceController@getConversationInvoiceChatByUserId');
        Route::get('invoice/detail/{key}', 'Api\V2\InvoiceController@getInvoiceDetailsById');
        Route::get('email/invoice/detail/{key}','Api\V2\InvoiceController@getEmailInvoiceDetailsById');
        Route::get('invoice/timeline/{key}','Api\V2\InvoiceController@getInvoiceTimelineByUserId');


        //Invoiceable Docket filter
        Route::group(['prefix' => 'filter/invoice'], function () {
            Route::post('docket/parameter', 'Api\V2\APIController@getInvoiceDocketFilterParameter');
            Route::post('docket/', 'Api\V2\APIController@filterInvoiceableDocket');
            Route::post('email/docket/parameter', 'Api\V2\APIController@getInvoiceEmailDocketFilterParameter');
            Route::post('email/docket', 'Api\V2\APIController@filterInvoiceableEmailDocket');
        });
        Route::get('myPermission','Api\V2\APIController@myPermission');

        //draft
        Route::group(['prefix' => 'draft'], function () {
            Route::post('image/save','Api\V2\APIController@draftImageSave');
            Route::post('docket/save','Api\V2\APIController@saveDocketDraft');
            Route::get('docket/list','Api\V2\APIController@getDocketDraftList');
            Route::post('docket/update','Api\V2\APIController@updateDocketDraft');
        });
        Route::post('user/nextDocketId','Api\V2\APIController@nextDocketId');

        Route::get('logout', 'Api\V2\APIController@logout');
        Route::post('change/password', 'Api\V2\APIController@changePassword');
        Route::post('profile/update', 'Api\V2\APIController@profileUpdate');
        Route::post('name/update', 'Api\V2\APIController@nameUpdate');
        Route::post('sent/acivity', 'Api\V2\APIController@sentAcivity');
        Route::get('emailUserList','Api\V2\APIController@emailUserList');

        //user/group messages
        Route::group(['prefix' => 'notification'], function () {
            Route::get('list', 'Api\V2\APIController@getNotificationList');
            Route::get('list/update/android', 'Api\V2\APIController@getNotificationListUpdateAndroid');
            Route::get('read/{key}','Api\V2\APIController@markAsReadNotification');
        });
        

        //Timer
        Route::group(['prefix' => 'timer/'], function () {
            Route::get('old/session', 'Api\V2\TimerController@getcheckOldTimerSession');
            Route::post('start', 'Api\V2\TimerController@startNewTimerSession');
            Route::post('finish', 'Api\V2\TimerController@finishTimerSession');
            Route::get('saved/list', 'Api\V2\TimerController@getAllSavedTimer');
            Route::post('pause', 'Api\V2\TimerController@pauseTimer');
            Route::post('continue', 'Api\V2\TimerController@continueTimer');
            Route::post('submit/comments','Api\V2\TimerController@submitTimerComments');
            Route::post('search','Api\V2\TimerController@searchTimer');
            Route::post('attached/tag','Api\V2\TimerController@timerAttachedTag');
            Route::get('detail/{id}','Api\V2\TimerController@timerDetailsById');
        });
        
        
        Route::get('timerDetailsById/{id}','APIController@timerDetailsById');
        Route::get('mark/all/read','Api\V2\APIController@markAllAsRead');


        //docket Rejection
        Route::post('reject/docket', 'Api\V2\APIController@sentDocketReject');

        //ios in app purchase
        Route::post('receipt/validator', 'Api\V2\APIController@receiptValidator');
        Route::get('subscription/status','Api\V2\APIController@subscriptionStatus');
        Route::post('updateDeviceToken','APIController@updateDeviceToken');

        //prefiller
        Route::post('save/grid/prefiller', 'Api\V2\APIController@saveGridPrefiller');
        Route::post('save/prefiller','Api\V2\APIController@savePrefiller');
        Route::get('number/system','Api\V2\APIController@numberSystem');
        Route::post('delete/draft','Api\V2\APIController@deleteDraft');

        Route::post('save/sent/default/dockets', 'Api\V2\APIController@v1SaveSentDefaultDockets');
        Route::post('update/docket/approval','Api\V2\APIController@updateDocketAprovalMethod');

        Route::get('task/assign','Api\V2\APIController@taskManagement');
        Route::get('task/assign/{docket_id}','Api\V2\APIController@taskManagementById');
        Route::post('task/assign-status','Api\V2\APIController@taskStatusManagement');
    });
});