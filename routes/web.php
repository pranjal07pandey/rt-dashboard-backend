<?php

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

//stripe webhooks
Route::group(['prefix' => 'webhooks'], function () {
    Route::post('stripe', 'StripeWebhooks@webhooks');
});


Route::get('/email', 'HomeController@email');
Route::get('/home', 'HomeController@index');
Route::get('/', 'Auth\LoginController@showLoginForm');
Route::get('login', 'Auth\LoginController@showLoginForm')->name("login");
/* Route::get('refresh-csrf', function () {return csrf_token();}); */
Route::get('registration', array('as' => 'registration', 'uses' => 'Auth\LoginController@showRegistrationForm'));
Route::post('registration', array('uses' => 'Auth\LoginController@register'));
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', array('as' => 'logout', 'uses' => 'Auth\LoginController@logout'));
Route::get('logout', 'Auth\LoginController@logout');
Route::post('password/email', 'Auth\ForgotPasswordController@postEmail');
Route::get('password/reset', array('as' => 'password.request', 'uses' => 'Auth\ForgotPasswordController@showResetForm'));
Route::get('email_verification/{key}', 'Auth\LoginController@emailVerification');
Route::get('redirect/{key}', 'WebsiteController@redirect');
Route::get('signup/success', 'Auth\LoginController@emailVerificationMessage');


//public view docket/invoice routes
Route::group(['prefix' => 'docket'], function(){

    //default prefiler
    Route::get('prefiller', 'Website\DocketController@prefiller');

    //emailed docket
    Route::get('emailed/{key}/download', 'Website\EmailDocketController@download');
    Route::get('emailed/{key}/{recipient}', 'Website\EmailDocketController@view');
    Route::get('emailed/{key}/{hashKey}/approve', 'Website\EmailDocketController@approve');
    Route::post('emailed/{key}/{hashKey}/approve', 'Website\EmailDocketController@approve');
    Route::get('emailed/approved', 'Website\EmailDocketController@approved');
    Route::get('emailed/{key}', 'Website\EmailDocketController@showCopyEmailDocketView');

    //docket
    Route::get('{key}/download', 'Website\DocketController@download');
    Route::post('{key}/reject', 'Website\DocketController@reject');
    Route::get('{key}/{recipient}', 'Website\DocketController@view');
    Route::get('{key}', 'Website\DocketController@showCopyDocketView');
    Route::get('{key}/{hashKey}/approve', 'Website\DocketController@approve');
    Route::post('{key}/{hashKey}/approve', 'Website\DocketController@approve');
});

Route::group(['prefix' => 'invoice'], function(){
    //emailed invoice
    Route::get('emailed/{key}/{hashKey}', 'Website\EmailInvoiceController@show');
        Route::get('emailed/{key}/download', 'Website\EmailInvoiceController@download');
});


//approve emailed docket
Route::get('approveDocket/{id}/{hashKey}', 'APIController@approveEmailedDocket');
Route::post('approveDocketEmail/', 'APIController@approveEmailedDocketByApprovalType');
Route::get('approveDocketByEmail/{id}/{hashKey}', 'APIController@approveDocketByEmail');
Route::post('approvedDocketSignature/', 'APIController@approvedDocketSignature');

Route::group(['middleware' => ['auth']], function() {
    Route::group(['middleware' => ['RTMiddleware']], function() {
        Route::prefix('dashboard')->group(base_path('routes/dashboard/admin/admin.php'));
    });
});

Route::group(['prefix' => 'dashboard', 'middleware' => 'auth'], function () {
    Route::get('/', 'DashboardController@index');
    Route::get('/errorpage', 'DashboardController@errorpage');

    Route::group(['prefix' => 'company', 'middleware' => 'CompanyMiddleware'], function () {

        //refactoring
        Route::get('/', 'CompanyDashboard@index')->name('companyDashboard');

        Route::prefix('employeeManagement')->group(base_path('routes/web/company/employee-management.php'));
        Route::prefix('clientManagement')->group(base_path('routes/web/company/client-management.php'));
        Route::prefix('messages')->group(base_path('routes/web/company/message-reminder.php'));

        //All Docket related routes
        Route::group(['prefix'=> 'docketBookManager'], function(){
            Route::get('/', 'DocketManager\DocketsController@allDockets')->name('docketBookManager');
            Route::group(['prefix' => 'dockets'], function(){
                Route::get('all', 'DocketManager\DocketsController@allDockets')->name('dockets.allDockets');
                Route::get('sent', 'DocketManager\DocketsController@sentDockets')->name('dockets.sentDockets');
                Route::get('create', 'DocketManager\DocketsController@createDockets')->name('dockets.createDockets');
                Route::get('draft/edit/{draft_id}', 'DocketManager\DocketsController@docketdraftUser')->name('dockets.draftEdit');
                Route::post('draft/edit/{draft_id}', 'DocketManager\DocketsController@assignDocketdraftUser')->name('dockets.assign.draftEdit');
                Route::get('received', 'DocketManager\DocketsController@receivedDockets')->name('dockets.receivedDockets');
                Route::get('emailed', 'DocketManager\DocketsController@emailedDockets')->name('dockets.emailedDockets');
                Route::get('draft', 'DocketManager\DocketsController@docketDraft')->name('dockets.docketDraft');
                Route::post('draft', 'DocketManager\DocketsController@docketDraftSave')->name('dockets.draft.save');

                Route::post('cancelDocket', 'Docket\SentDocketController@cancelDocket');
                Route::post('submitDeleteDocket', 'Docket\SentDocketController@submitDeleteDocket');
                Route::post('docketfieldName', 'Docket\SentDocketController@docketfieldName');


                //docket labeling
                Route::group(['prefix' => 'labels'], function(){
                    Route::post('assign', 'Docket\LabelController@assign');
                    Route::post('delete', 'Docket\LabelController@delete');
                });

                Route::group(['prefix' => 'template'], function(){
                    Route::get('/', 'DocketManager\DocketTemplateController@index')->name('dockets.template.index');
                });

                Route::post('filterDocket', 'DocketManager\DocketsController@filterDocket')->name('dockets.advancedFilter');
                Route::get('filterDocket', 'DocketManager\DocketsController@filterDocket')->name('dockets.advancedFilter');
            });
        });

        //All Invoice related routes
        Route::group(['prefix' => 'invoiceManager'], function(){
            Route::group(['prefix' => 'invoices'], function(){
                Route::get('all', 'InvoiceManager\InvoicesController@allInvoices')->name('invoices.allInvoices');
                Route::get('sent', 'InvoiceManager\InvoicesController@sentInvoices')->name('invoices.sentInvoices');
                Route::get('received', 'InvoiceManager\InvoicesController@receivedInvoices')->name('invoices.receivedInvoices');
                Route::get('emailed', 'InvoiceManager\InvoicesController@emailedInvoices')->name('invoices.emailedInvoices');

                //invoice labeling
                Route::group(['prefix' => 'labels'], function(){
                    Route::post('assign', 'Invoice\LabelController@assign');
                    Route::post('delete', 'Invoice\LabelController@delete');
                });

                Route::group(['prefix' => 'create'], function(){
                    Route::get('/', 'InvoiceManager\SentInvoiceController@index')->name('invoices.create');
                    Route::post('/recipient', 'InvoiceManager\SentInvoiceController@recipient')->name('invoices.create.recipient');
                    Route::post('/dockets', 'InvoiceManager\SentInvoiceController@dockets')->name('invoices.create.dockets');
                    Route::post('/invoice', 'InvoiceManager\SentInvoiceController@invoice')->name('invoices.create.invoice');
                    Route::post('/send', 'V2\SentInvoiceController@send')->name('invoices.create.send');
                    // Route::post('/send', 'InvoiceManager\SentInvoiceController@send')->name('invoices.create.send');
                });
            });
        });



        Route::get('email', 'CompanyDashboard@emailDocketView');

        Route::post('/saveDefaultRecipient','CompanyDashboard@saveDefaultRecipient');
        Route::post('/deleteDefaultRecipient','CompanyDashboard@deleteDefaultRecipient');
        Route::post('/updateAprovalMethod','CompanyDashboard@updateAprovalMethod');









        Route::post('employeeManagement/sendMessage', 'EmployeeManagement@sendMessage');


        Route::get('/timers', 'CompanyDashboard@timers');
        Route::get('/timers/count', 'CompanyDashboard@getTimersCount');

        Route::get('allUser', array('as' => 'allUser', 'uses' => 'CompanyDashboard@allUser'));


        Route::get('vue',  'CompanyDashboard@vue');



        //=============================SentInvoice==================================//

        Route::post('/sentInvoice/filterInvoiceableDocket', 'InvoiceManager\SentInvoiceController@filterInvoiceableDocket')->name('filterInvoiceableDocket');
        Route::post('/sentInvoice/filterInvoiceableEmailDocket', 'InvoiceManager\SentInvoiceController@filterInvoiceableEmailDocket')->name('filterInvoiceableEmailDocket');


        Route::get('/templateBank', 'CompanyDashboard@templateBank')->name('templateBank');
        Route::post('/publishDocketTemplate', 'CompanyDashboard@publishDocketTemplate');
        Route::post('/unpublishDocketTemplate', 'CompanyDashboard@unpublishDocketTemplate');
        Route::post('/installDocketTemplate', 'CompanyDashboard@installDocketTemplate');
        Route::get('/templateBank/preview/{id}', 'CompanyDashboard@templatePreview');
        Route::get('/searchDocketTemplate', 'CompanyDashboard@searchDocketTemplate');


        //===============================ENDD============================================//



        //=============================XERO INTEGRATION==================================//
        Route::get('/xero', 'XeroController@index');
        Route::get('/xero/contacts/view', 'XeroController@xeroContactGet')->name('Xero.Index');
        Route::get('/xero/contacts/', 'XeroController@getContacts');
        Route::get('/xero/postemployees', 'XeroController@postEmployees');
        Route::get('/xero/employee', 'XeroController@getEmployee');
        Route::get('/xero/employee/create', 'XeroController@createEmployee');
        Route::get('/xero/create/expense/claim', 'XeroController@createExpenseClaim');
        Route::get('/xero/create/invoice/accpay', 'XeroController@createInvoiceAccPay');
        Route::get('/xero/create/invoice/tracking', 'XeroController@createInvoiceWithTracking');
        Route::get('/xero/create/invoice/authorised', 'XeroController@createInvoiceAuthorised');
        Route::get('/xero/create/invoice/accrec', 'XeroController@createInvoiceAccRec');
        //Route::get('/xero/contactsEmailInvoice/{id}',  'XeroController@getEmailInvoice');
        Route::get('/xero/invoice/{id}', 'XeroController@getInvoice');
        Route::get('/xero/taxInvoice', 'XeroController@taxInvoice');

        Route::get('xero/connect/{scope_check}', 'XeroConnectionController@connect');
        Route::get('xero/connectionCallBack', 'XeroConnectionController@connectionCallBack');
        Route::get('xero/connectionSucess', 'XeroConnectionController@connectionSucess');
        Route::get('/xeroTimeOut', 'XeroConnectionController@xeroTimeOut');
        Route::get('/xero/xeroEmailInvoice/{id}', 'XeroConnectionController@xeroEmailInvoice');
        Route::get('/xero/xeroInvoice/{id}', 'XeroConnectionController@xeroInvoice');
        Route::get('/xero/disconnected/', 'XeroConnectionController@xeroDisconnected');
        Route::get('/xero/xeroEmailInvoiceView/{id}', 'XeroConnectionController@xeroEmailInvoiceView');
        Route::get('/xero/xeroInvoiceView/{id}', 'XeroConnectionController@xeroInvoiceView');
        Route::get('/xero/xeroTimeSheet/', 'XeroConnectionController@xeroTimeSheet');
        Route::get('/xero/xeroTimeSheet1/', 'XeroConnectionController@xeroTimeSheet1');
        Route::get('/xero/companyXeroManager', 'XeroConnectionController@companyXeroManager')->name('timesheet.index');
        Route::get('xero/timesheet/{id}', 'XeroConnectionController@timesheet');
        Route::post('xero/checkedPayPeriod', 'XeroConnectionController@checkedPayPeriod');
        Route::post('xero/syncTimeSheet', 'XeroConnectionController@syncTimeSheet');
        Route::post('xero/timesheetDetail', 'XeroConnectionController@timesheetDetail');
        Route::get('/xero/reset/{id}', 'XeroConnectionController@xeroReset');
        Route::get('xero/view/{id}', 'XeroConnectionController@viewTimesheetDetail');
        Route::get('xero/searchTimeSheet', 'XeroConnectionController@searchTimeSheet');
        Route::post('xero/bulkSyncPayPeriod', 'XeroConnectionController@bulkSyncPayPeriod');
        Route::post('xero/syncAllData', 'XeroConnectionController@syncAllData');



        //===============================ENDD============================================//


        Route::group(['prefix' => 'timers'], function () {
            Route::get('/all', 'TimerController@index')->name('timers');
            Route::get('/create', 'TimerController@create')->name('timers.create');
            Route::post('/store', 'TimerController@store')->name('timer.store');
            Route::get('/pause/{id}', 'TimerController@pause')->name('timers.pause');
            Route::post('/pause/store', 'TimerController@pauseStore')->name('timer.pause.store');
            Route::get('/resume/{id}', 'TimerController@resume')->name('timers.resume');
            Route::post('/resume/store', 'TimerController@resumeStore')->name('timer.resume.store');
            Route::get('/stop/{id}', 'TimerController@stop')->name('timers.stop');
            Route::post('/stop/store', 'TimerController@stopStore')->name('timer.stop.store');

            Route::get('/{id}/view', 'TimerController@view')->name('timers.view');
            Route::get('/{id}/download', 'TimerController@download')->name('timers.download');
            Route::post('filterTimer', 'TimerController@searchTimer')->name('timers.searchTimer');
            Route::get('timerFilter', 'TimerController@timerFilter')->name('timers.timerFilter');
            Route::post('filterNonEmployeeTimer', 'TimerController@filterNonEmployeeTimer')->name('timers.filterNonEmployeeTimer');
            Route::get('filterNonEmployee', 'TimerController@filterNonEmployee')->name('timers.filterNonEmployee');

            //Created By anoter Company
            Route::get('/nonEmployee', 'TimerController@nonEmployee')->name('timers.nonemployee');
            Route::get('/nonEmployeeTemplate', 'TimerController@nonEmployeeTemplate')->name('timers.nonEmployeeTemplate');
            Route::get('/employeeTemplate', 'TimerController@employeeTemplate')->name('timers.employeeTemplate');
            Route::get('/employeeActive', 'TimerController@employeeActive')->name('timers.employeeActive');
            Route::get('/nonEmployeeActive', 'TimerController@nonEmployeeActive')->name('timers.nonEmployeeActive');

            //Timer Settings
            Route::post('/store/timer/settings', 'TimerController@storeTimerSettings');
            Route::post('/update/timer/settings', 'TimerController@updateTimerSettings');
        });


        //======================================== DOCKET BOOK MANAGER ROUTES ============================================//
        Route::group(['prefix' => 'docketBookManager'], function () {
            Route::get('intro', array('as' => 'intro', 'uses' => 'CompanyDashboard@intro'));

            Route::get('docket/allDockets', array('as' => 'companyAllDocketBookManager', 'uses' => 'CompanyDashboard@companyAllDocketBookManager'));

            //Route::get('docket/search', array('as' => 'search', 'uses' => 'CompanyDashboard@search'));


            Route::get('docket/all', 'CompanyDashboard@allDockets')->name('allDockets');
            Route::get('docket/view/emailed/{key}', 'CompanyDashboard@companyDocketViewEmailed');
            Route::get('docket/viewEmailed/{key}', 'CompanyDashboard@companyDocketViewEmailed');
            Route::get('docket/downloadViewDocket/{key}', 'CompanyDashboard@downloadViewDocket');
            Route::get('docket/downloadViewemailed/{key}', 'CompanyDashboard@downloadViewemailed');


            //docket Draft
            Route::get('docket/draft/view/{key}', 'CompanyDashboard@companyDocketViewDocketDraft');



            //<>-- DOCKET TEMPLATE ROUTES --<>//
            Route::get('designDocket/{key}', 'CompanyDashboard@designDocket');
            Route::get('mobileView/{id}', 'CompanyDashboard@designMobileViewDocket');

            //Get Grid Table
            Route::get('grid/table/{key}', 'CompanyDashboard@gridTable')->name('grid.table');
            Route::post('grid/table/save', 'CompanyDashboard@gridTableSave')->name('grid.table.save');
            Route::post('grid/table/update', 'CompanyDashboard@gridTableUpdate')->name('grid.table.update');
            Route::post('grid/update/label/update', 'CompanyDashboard@gridTableLableUpdate')->name('grid.table.label.update');
            Route::post('grid/column/delete', 'CompanyDashboard@gridColumnDelete')->name('grid.column.delete');
            Route::post('grid/column/order/update', 'CompanyDashboard@gridColumnOrderUpdate')->name('grid.column.orderUpdate');


            //ExportCSV
            Route::get('docket/exportDocket', 'CompanyDashboard@exportDocket')->name('exportDocket');
            Route::get('docket/exportEmailDocket', 'CompanyDashboard@exportEmailDocket')->name('exportEmailDocket');
            Route::get('docket/exportAllDocket', 'CompanyDashboard@exportAllDocket')->name('exportAllDocket');
            Route::get('docket/exportDaycrsDocket', 'CompanyDashboard@exportDaycrsDocket')->name('exportDaycrsDocket');
            Route::get('docket/exportDaycrsMU2', 'CompanyDashboard@exportDaycrsMU2');

            Route::get('docket/exportEmailDockets', 'CompanyDashboard@exportEmailDockets')->name('exportEmailDockets');

            Route::post('docket/downloadZip', 'CompanyDashboard@downloadDocketPdfZip')->name('downloadZip');

            Route::get('docket/downloadEmailDocketZip', 'CompanyDashboard@makePdfEmailedDocket')->name('downloadEmailDocketZip');

            Route::get('docket/sendDocket', array('as' => 'companySendDocket', 'uses' => 'CompanyDashboard@companySendDocket'));
            Route::post('docket/sendDocket/docketTemplete/{id}', 'CompanyDashboard@docketTemplete');
        });

        Route::group(['prefix' => 'folder'], function () {
            Route::post('/', 'FolderController@index');
            Route::post('/folderstru', 'FolderController@folderstru');
            Route::post('/ajax', 'FolderController@ajax');
            Route::get('/getFolderStru', 'FolderController@getFolderStru');
            Route::post('/saveFolderItems', 'FolderController@saveFolderItems');
            Route::post('/viewFolderData', 'FolderController@viewFolderData');
            Route::post('/newFolderCreate', 'FolderController@newFolderCreate');
            Route::get('/createFolderSelect', 'FolderController@createFolderSelect');
            Route::post('/removeFolder', 'FolderController@removeFolder');
            Route::post('/updateFolder', 'FolderController@updateFolder');
            Route::post('/removeItemsFolder', 'FolderController@removeItemsFolder');
            Route::post('/searchFolder', 'FolderController@searchFolder');
            Route::get('/searchFolderItems', 'FolderController@searchFolderItems');
            Route::post('/showFolderAdvanceFilter', 'FolderController@showFolderAdvanceFilter');
            Route::post('advanceSearch/AdvanceFilter', 'FolderController@AdvanceFilter');
            Route::post('/folderLabelSave', 'FolderController@folderLabelSave');
            Route::post('/folderInvoiceLabelSave', 'FolderController@folderInvoiceLabelSave');
            Route::post('/deleteAssignLable', 'FolderController@deleteAssignLable');
            Route::post('/assignTemplateFolder', 'FolderController@assignTemplateFolder');
            Route::post('/unassignTemplateFolder', 'FolderController@unassignTemplateFolder');
            Route::post('/cancelRtItems', 'FolderController@cancelRtItems');
            Route::post('/searchFolderById', 'FolderController@searchFolderById');
            Route::get('/viewFolderReload', 'FolderController@viewFolderReload');
            Route::post('/downloadPdf', 'FolderController@downloadPdf');
            Route::post('/recoverFolderItem', 'FolderController@recoverFolderItem');
            Route::post('/saveShareableUsers', 'FolderController@saveShareableUsers');
            Route::post('/viewShareableData', 'FolderController@viewShareableData');
            Route::post('/updateShareableType', 'FolderController@updateShareableType');
            Route::post('/deleteShareableUser', 'FolderController@deleteShareableUser');
            Route::post('/updateShareableUser', 'FolderController@updateShareableUser');


        });

        //======================================= DOCKET FREQUENCY MANAGER ROUTES =======================================//

        Route::post('docketBookManager/saveDocketFrequency', 'CompanyDashboard@saveDocketFrequency')->name('store_docket_freq');
        //======================================== DOCKET BOOK MANAGER ROUTES ============================================//

        //========================================= ADDONS ===============================//
        Route::group(['prefix' => 'addons'], function () {
            Route::get('/', 'AddOnsController@index')->name('addons');

        });

        Route::get('docketBookManager/filterDocket', array('as' => 'companyDocketBookManagerFilter', 'uses' => 'CompanyDashboard@companyDocketFilter'));
        Route::post('docketBookManager/filterEmail', array('as' => 'companyDocketBookManagerFilteremail', 'uses' => 'CompanyDashboard@emailFilter'));
        Route::get('docketBookManager/filterEmail', array('as' => 'companyDocketBookManagerFilteremail', 'uses' => 'CompanyDashboard@emailFilter'));

        Route::post('invoiceManager/filterInvoice', array('as' => 'companyInvoiceFilter', 'uses' => 'CompanyDashboard@companyInvoiceFilter'));
        Route::get('invoiceManager/filterInvoice', array('as' => 'companyInvoiceFilter', 'uses' => 'CompanyDashboard@companyInvoiceFilter'));



        Route::post('docketBookManager/docket/view/approve', 'CompanyDashboard@companyDocketApprove');
        Route::get('docketBookManager/docket/approvalTypeView/{key}', 'CompanyDashboard@approvalTypeView');

        Route::post('docketBookManager/docket/reject', 'CompanyDashboard@docketReject');



        Route::get('docketBookManager/docket/docketLabel', 'CompanyDashboard@companyDocketLabel')->name('companyDocketLabel');

        Route::post('docketBookManager/saveTempDocket', 'CompanyDashboard@saveTempDocket');
        Route::post('docketBookManager/updateTempDocket', 'CompanyDashboard@updateTempDocket');
        Route::post('docketBookManager/updateTempThemeDocket', 'CompanyDashboard@updateTempThemeDocket');
        Route::post('docketBookManager/saveDocketlabel', 'CompanyDashboard@saveDocketlabel');
        Route::post('docketBookManager/updateDocketlabel', 'CompanyDashboard@updateDocketlabel');
        Route::post('docketBookManager/savemultipleDocketlabel', 'CompanyDashboard@savemultipleDocketlabel');
        Route::post('docketBookManager/docket/received/savemultipleReceivedDocketlabel', 'CompanyDashboard@savemultipleReceivedDocketlabel');
        Route::post('docketBookManager/docket/emailed/savemultipleEmailDocketlabel', 'CompanyDashboard@savemultipleEmailDocketlabel');

        Route::post('docketBookManager/designDocket/docketFieldUpdatePosition/{key}', 'CompanyDashboard@docketFieldUpdatePosition');
        Route::post('docketBookManager/designDocket/docketFieldLabelUpdate', 'CompanyDashboard@docketFieldLabelUpdate');
        Route::post('docketBookManager/designDocket/saveDocketFieldFooter', 'CompanyDashboard@saveDocketFieldFooter');
        Route::post('docketBookManager/designDocket/updatePreFiller', 'CompanyDashboard@updatePreFiller');
        Route::post('docketBookManager/designDocket/deletePreFiller/', 'CompanyDashboard@deletePreFiller');
        Route::post('docketBookManager/docket/designDocket/saveprefiller', 'CompanyDashboard@docketSavePreFiller');
        Route::post('docketBookManager/docket/designDocket/saveLinkPrefiller', 'CompanyDashboard@saveLinkPrefiller');
        Route::get('docketBookManager/designDocket/deleteAllPreFiller/{id}', 'CompanyDashboard@deleteAllPreFiller');
        Route::post('docketBookManager/docket/designDocket/addIndPrefiller', 'CompanyDashboard@addIndPrefiller');
        Route::post('docketBookManager/designDocket/updateDocketPrefix', 'CompanyDashboard@updateDocketPrefix');
        Route::post('docketBookManager/designDocket/updateDocketIdLabel', 'CompanyDashboard@updateDocketIdLabel');


        Route::post('docketBookManager/designDocket/saveTallyable/', 'CompanyDashboard@saveTallyable');
        Route::post('docketBookManager/designDocket/docketTallyableUnitRateLabelUpdate', 'CompanyDashboard@docketTallyableUnitRateLabelUpdate');

        Route::post('docketBookManager/designDocket/gridFormulaSet', 'CompanyDashboard@gridFormulaSet');
        Route::post('docketBookManager/designDocket/formulaSet', 'CompanyDashboard@formulaSet');
        Route::post('docketBookManager/designDocket/saveFormula', 'CompanyDashboard@saveFormula');

        Route::post('docketBookManager/designDocket/gridPrefillerSet', 'CompanyDashboard@gridPrefillerSet');
        Route::post('docketBookManager/designDocket/saveGridPrefiller', 'CompanyDashboard@saveGridPrefiller');
        Route::post('docketBookManager/designDocket/deleteGridPrefiller', 'CompanyDashboard@deleteGridPrefiller');
        Route::post('docketBookManager/designDocket/updateGridPreFiller', 'CompanyDashboard@updateGridPreFiller');
        Route::post('docketBookManager/designDocket/gridprefillerDefaultCheckMark', 'CompanyDashboard@gridprefillerDefaultCheckMark');
        Route::post('docketBookManager/designDocket/checkdefaultAutoFilledPrefiller', 'CompanyDashboard@checkdefaultAutoFilledPrefiller');
        Route::post('docketBookManager/designDocket/gridaddIndPrefiller', 'CompanyDashboard@gridaddIndPrefiller');
        Route::post('docketBookManager/designDocket/duplicateGrid', 'CompanyDashboard@duplicateGrid');
        Route::post('docketBookManager/designDocket/saveGridAutoCellPrefiller','CompanyDashboard@saveGridAutoCellPrefiller');
        Route::post('docketBookManager/designDocket/saveGridAutoPrefiller','CompanyDashboard@saveGridAutoPrefiller');

        Route::post('docketBookManager/designDocket/gridDynamicFilterField','CompanyDashboard@gridDynamicFilterField');
        Route::post('docketBookManager/designDocket/removeGridDynamicFilterField','CompanyDashboard@removeGridDynamicFilterField');
        Route::post('docketBookManager/designDocket/dynamicFilterField','CompanyDashboard@dynamicFilterField');
        Route::post('docketBookManager/designDocket/removeDynamicFilterField','CompanyDashboard@removeDynamicFilterField');


        Route::post('docketBookManager/designDocket/saveIsDependent', 'CompanyDashboard@saveIsDependent');
        Route::post('docketBookManager/designDocket/saveIsDependentView', 'CompanyDashboard@saveIsDependentView');


        Route::post('docketBookManager/designDocket/saveDocketPrefillerManager','CompanyDashboard@saveDocketPrefillerManager');
        Route::post('docketBookManager/designDocket/savePrefillerManagerChild','CompanyDashboard@savePrefillerManagerChild');
        Route::post('docketBookManager/designDocket/savePrefillerData','CompanyDashboard@savePrefillerData');
        Route::post('docketBookManager/designDocket/addNewParentPrefiller','CompanyDashboard@addNewParentPrefiller');
        Route::post('docketBookManager/designDocket/addNewChildPrefiller','CompanyDashboard@addNewChildPrefiller');
        Route::post('docketBookManager/designDocket/deletePrefillerLabels','CompanyDashboard@deletePrefillerLabels');
        Route::post('docketBookManager/designDocket/clearAllPrefiller','CompanyDashboard@clearAllPrefiller');
        Route::post('docketBookManager/designDocket/saveDocketFieldPrefillerManager','CompanyDashboard@saveDocketFieldPrefillerManager');
        Route::post('docketBookManager/designDocket/saveChildManagerPrefiller','CompanyDashboard@saveChildManagerPrefiller');
        Route::post('docketBookManager/designDocket/deleteprefillerManagerLabelchild','CompanyDashboard@deleteprefillerManagerLabelchild');
        Route::post('docketBookManager/designDocket/deleteprefillerManagerLabel','CompanyDashboard@deleteprefillerManagerLabel');
        Route::post('docketBookManager/designDocket/clearAllGridPrefiller','CompanyDashboard@clearAllGridPrefiller');
        Route::post('docketBookManager/designDocket/exportMapping/', 'CompanyDashboard@exportMapping');
        Route::post('docketBookManager/designDocket/updateExportMappingHeader/', 'CompanyDashboard@updateExportMappingHeader');
        Route::post('docketBookManager/designDocket/exportMappingCheckbox/', 'CompanyDashboard@exportMappingCheckbox');
        Route::post('docketBookManager/designDocket/saveExportMappingField/', 'CompanyDashboard@saveExportMappingField');
        Route::post('docketBookManager/designDocket/viewExportMappingField/', 'CompanyDashboard@viewExportMappingField');
        Route::post('docketBookManager/designDocket/saveDocketConstant/', 'CompanyDashboard@saveDocketConstant');
        Route::post('docketBookManager/designDocket/docketConstantLabelUpdate/', 'CompanyDashboard@docketConstantLabelUpdate');

        Route::post('docketBookManager/designDocket/getEcowiseData','CompanyDashboard@getEcowiseData');
        Route::post('docketBookManager/designDocket/ecowiseDataUpdateUrl','CompanyDashboard@ecowiseDataUpdateUrl');
        Route::post('docketBookManager/designDocket/saveSelectPrefilerEcowise','CompanyDashboard@saveSelectPrefilerEcowise');
        Route::post('docketBookManager/designDocket/saveGridEcowiseAutoCellPrefiller','CompanyDashboard@saveGridEcowiseAutoCellPrefiller');
        Route::post('docketBookManager/designDocket/saveGridAutoPrefillerEcowise','CompanyDashboard@saveGridAutoPrefillerEcowise');

        Route::post('docketBookManager/designDocket/getNormalEcowiseData','CompanyDashboard@getNormalEcowiseData');
        Route::post('docketBookManager/designDocket/ecowiseNormalDataUpdateUrl','CompanyDashboard@ecowiseNormalDataUpdateUrl');
        Route::post('docketBookManager/designDocket/selectNormalPrefilerEcowise','CompanyDashboard@selectNormalPrefilerEcowise');

        Route::post('docketBookManager/designDocket/linkPrefillerFilterView','CompanyDashboard@linkPrefillerFilterView');
        Route::post('docketBookManager/designDocket/updateLinkPrefillerValue','CompanyDashboard@updateLinkPrefillerValue');
        Route::post('docketBookManager/designDocket/linkGridPrefillerFilterView','CompanyDashboard@linkGridPrefillerFilterView');
        Route::post('docketBookManager/designDocket/updateLinkGridPrefillerValue','CompanyDashboard@updateLinkGridPrefillerValue');









        Route::post('docketBookManager/designDocket/addDocketField/{key}', 'CompanyDashboard@addDocketField');
        Route::post('docketBookManager/designDocket/deleteDocketField/{key}', 'CompanyDashboard@deleteDocketFields');
        Route::post('docketBookManager/designDocket/undoDocketField', 'CompanyDashboard@undoDocketField');
        Route::post('docketBookManager/designDocket/showHideDeletedDocketElement', 'CompanyDashboard@showHideDeletedDocketElement');
        Route::post('docketBookManager/designDocket/showHideDocketPrefix', 'CompanyDashboard@showHideDocketPrefix');
        Route::post('docketBookManager/designDocket/showHideDocketNumber', 'CompanyDashboard@showHideDocketNumber');





        Route::get('docketBookManager/designDocket/{key}/save', 'CompanyDashboard@saveDocket');
        Route::post('docketBookManager/designDocket/invoiceable/', 'CompanyDashboard@docketInvoaddPrefillerManagericeableUpdate');
        Route::post('docketBookManager/designDocket/timerAttached/', 'CompanyDashboard@docketTimerAttachedUpdate');
        Route::post('docketBookManager/designDocket/docketInvoiceFiled/', 'CompanyDashboard@docketInvoiceFiled');
        Route::post('docketBookManager/designDocket/docketPreviewFiled/', 'CompanyDashboard@docketPreviewFiled');
        Route::post('docketBookManager/designDocket/docketRequiredField/', 'CompanyDashboard@docketRequiredField');
        Route::post('docketBookManager/designDocket/docketSendCopy/', 'CompanyDashboard@docketSendCopy');
        Route::post('docketImageNameFieldRequired/', 'CompanyDashboard@docketImageNameFieldRequired');
        Route::post('docketDateTimeRequired/', 'CompanyDashboard@docketDateTimeRequired');


        Route::post('prefillerDefaultCheckMark/', 'CompanyDashboard@prefillerDefaultCheckMark');

        Route::post('prefillerCheckMark/', 'CompanyDashboard@prefillerCheckMark');
        Route::post('prefillerCheckMarkSingle/', 'CompanyDashboard@prefillerCheckMarkSingle');

        Route::post('checkParentPrefiller/', 'CompanyDashboard@checkParentPrefiller');

        Route::post('docketBookManager/designDocket/isEmailSubjectdDocketFieldId/', 'CompanyDashboard@isEmailSubjectdDocketFieldId');
        Route::post('docketBookManager/designDocket/isEmailSubjectdDocketGridFieldId/', 'CompanyDashboard@isEmailSubjectdDocketGridFieldId');
        Route::post('docketBookManager/designDocket/updategridTimeFormat/', 'CompanyDashboard@updategridTimeFormat');
        Route::post('docketBookManager/designDocket/updateTimeFormat/', 'CompanyDashboard@updateTimeFormat');
        Route::post('docketBookManager/designDocket/updateGridRequired/','CompanyDashboard@updateGridRequired');
        Route::post('docketBookManager/designDocket/showDefaultFolder/', 'CompanyDashboard@showDefaultFolder');
        Route::post('docketBookManager/designDocket/updateDefaultFolder/', 'CompanyDashboard@updateDefaultFolder');
        Route::post('docketBookManager/designDocket/updateGridPreview/','CompanyDashboard@updateGridPreview');
        Route::post('docketBookManager/designDocket/updateGridPdfName/','CompanyDashboard@updateGridPdfName');



        Route::post('updateDocketTotalStatus/', 'CompanyDashboard@updateDocketTotalStatus');
        Route::post('gridSendDocket/', 'CompanyDashboard@gridSendDocket');


        Route::group(['prefix' => 'docketBookManager'], function(){
            Route::group(['prefix' => 'designDocket'], function(){
                Route::post('docketFieldIsHidden', 'DocketBookManagerController@docketFieldIsHidden');
                Route::post('docketGridFieldIsHidden', 'DocketBookManagerController@docketGridFieldIsHidden');
            });

            Route::group(['prefix' => 'docket'], function(){
                Route::get('view/{key}', 'CompanyDashboard@companyDocketView');
            });

            //docket template section

            Route::get('archive', array('as' => 'companyDocketTemplatesArchive', 'uses' => 'DocketBookManagerController@companyDocketTemplatesArchive'));
            Route::post('archiveDocketTemplate', 'DocketBookManagerController@archiveDocketTemplete');
            Route::post('deleteDocketTemplate', 'DocketBookManagerController@deleteDocketTemplate');
            Route::post('docketDuplicate', 'DocketBookManagerController@docketDuplicate');

        });

        Route::post('docketBookManager/designDocket/expnanationTypeFieldId/', 'CompanyDashboard@expnanationTypeFieldId');

        Route::post('docketBookManager/designDocket/updateFieldNumber', 'CompanyDashboard@updateFieldNumber');


        //subdocket

        Route::post('docketBookManager/designDocket/docketFieldUnitFieldLabelUpdate', 'CompanyDashboard@docketFieldUnitFieldLabelUpdate');
        Route::post('docketBookManager/designDocket/docketFieldManualTimerLabelUpdate', 'CompanyDashboard@docketFieldManualTimerLabelUpdate');
        Route::post('docketBookManager/designDocket/docketFieldManualTimerBreakLabelUpdate', 'CompanyDashboard@docketFieldManualTimerBreakLabelUpdate');

        Route::post('docketBookManager/designDocket/docketYesNoFieldLabelUpdate', 'CompanyDashboard@docketYesNoFieldLabelUpdate');
        Route::post('docketBookManager/designDocket/updateYesNoFields', 'CompanyDashboard@updateYesNoFields');
        Route::post('docketBookManager/yesNoExplanation/', 'CompanyDashboard@docketYesNoExplanation');
        Route::post('docketBookManager/yesNoExplanationUncheck/', 'CompanyDashboard@yesNoExplanationUncheck');
        Route::post('docketBookManager/addSubDocketField', 'CompanyDashboard@addSubDocketField');
        Route::post('docketBookManager/subDocketFieldLabelUpdate', 'CompanyDashboard@subDocketFieldLabelUpdate');
        Route::post('docketBookManager/deleteSubDocketField/', 'CompanyDashboard@deleteSubDocketField');
        Route::post('docketBookManager/subDocketFieldUpdatePosition/', 'CompanyDashboard@subDocketFieldUpdatePosition');
        Route::post('docketBookManager/subDocketRequiredField/', 'CompanyDashboard@subDocketRequiredField');
        Route::post('docketBookManager/UpdateSubDocketColour/', 'CompanyDashboard@UpdateSubDocketColour');
        Route::post('docketBookManager/updateLabelType/', 'CompanyDashboard@updateLabelType');
        Route::post('docketBookManager/yesNoIconImage/', 'CompanyDashboard@YesNoIconImage');
        Route::post('docketBookManager/yesNoIconImageUpdate/', 'CompanyDashboard@YesNoIconImageUpdate');
        Route::post('docketBookManager/saveAdvanceHeader/', 'CompanyDashboard@saveAdvanceHeader');
        Route::post('docketBookManager/saveImageInstruction/', 'CompanyDashboard@saveImageInstruction')->name('save.image.instruction');


        Route::post('docketBookManager/clearAllExportRule','CompanyDashboard@clearAllExportRule');


        Route::post('docketBookManager/designDocket/addDocument', 'CompanyDashboard@addDocument');
        Route::post('docketBookManager/designDocket/deleteDesigneDocumentAttached', 'CompanyDashboard@deleteDesigneDocumentAttached');


        Route::get('invoiceManager/mobileView/{id}', 'CompanyDashboard@designMobileViewInvoice');
        Route::post('invoiceManager/updateInvoiceDocket', 'CompanyDashboard@updateInvoiceDocket');
        Route::post('invoiceManager/updateTempThemeInvoice', 'CompanyDashboard@updateTempThemeInvoice');

        Route::resource('invoiceLabels','InvoiceManager\InvoiceLabelController');

        Route::get('invoiceManager/invoices/invoiceLabel', 'CompanyDashboard@companyInvoiceLabel')->name('companyInvoiceLabel');
        Route::get('/companyInvoiceLabelData', 'CompanyDashboard@companyInvoiceLabelData')->name('companyInvoiceLabelData');

        Route::post('invoiceManager/invoices/invoiceLabel/saveInvoicelabel', 'CompanyDashboard@saveInvoicelabel');
        Route::post('invoiceManager/updateInvoicelabel', 'CompanyDashboard@updateInvoicelabel');
        Route::get('/deleteInvoiceLabel/{id}', 'CompanyDashboard@deleteInvoiceLabel');
        Route::post('invoiceManager/savemultipleInvoicelabel', 'CompanyDashboard@savemultipleInvoicelabel');
        Route::post('invoiceManager/invoices/deleteinvoiceAssignLabel', 'CompanyDashboard@deleteinvoiceAssignLabel');
        Route::post('invoiceManager/receivedInvoice/savemultipleReceivedInvoicelabel', 'CompanyDashboard@savemultipleReceivedInvoicelabel');
        Route::post('invoiceManager/invoices/receivedInvoice/deleteReceivedInvoiceAssignLabel', 'CompanyDashboard@deleteReceivedInvoiceAssignLabel');

        Route::post('invoiceManager/emailedInvoice/savemultipleEmailedInvoicelabel', 'CompanyDashboard@savemultipleEmailedInvoicelabel');
        Route::post('invoiceManager/invoices/emailedInvoice/deleteEmailedInvoiceAssignLabel', 'CompanyDashboard@deleteEmailedInvoiceAssignLabel');

        Route::get('docketManager/assignDocket/', array('as' => 'companyAssignDockets', 'uses' => 'CompanyDashboard@companyAssignDockets'));
        Route::get('docketManager/assignTask', array('as' => 'companyAssignDocketsCalender', 'uses' => 'CompanyDashboard@companyAssignTask'));
        Route::post('docketManager/assignDocket/', 'CompanyDashboard@storeAssignDocket');
        Route::get('docketManager/assignDocket/view/{id}', 'CompanyDashboard@viewAssignDocket')->name('assign.docket.template.view');
        Route::get('docketManager/assignDocket/search', 'CompanyDashboard@searchAssignDocket')->name('assign.docket.template.search');
        Route::post('docketManager/assignDocket/update', 'CompanyDashboard@updateAssignDocket');
        Route::delete('docketManager/assignDocket', 'CompanyDashboard@deleteAssignDocket');
        Route::post('docketBookManager/docketLabel/deleteDocketLabel/', 'CompanyDashboard@deletdocketLabel');
        Route::post('docketBookManager/docket/deleteAssignLabel', 'CompanyDashboard@deleteAssignLabel');
        Route::post('docketBookManager/docket/deleteReceivedAssignLabel', 'CompanyDashboard@deleteReceivedAssignLabel');
        Route::delete('docketBookManager/docket/deleteEmailAssignLabel/', 'CompanyDashboard@deleteEmailAssignLabel');
        Route::post('downloadJSONFile', array('as' => 'downloadJSONFile', 'uses' => 'CompanyDashboard@downloadJSONFile'));
        Route::post('uploadJSONFile', array('as' => 'uploadJSONFile', 'uses' => 'CompanyDashboard@uploadJSONFile'));

        Route::get('docketManager/documentManager/', array('as' => 'companyDocumentManager', 'uses' => 'CompanyDashboard@companyDocumentManager'));
        Route::post('docketManager/documentManager/addCompanyDocumentManager', 'CompanyDashboard@addCompanyDocumentManager');
        Route::post('docketManager/documentManager/updateCompanyDocumentManager', 'CompanyDashboard@updateCompanyDocumentManager');
        Route::post('docketManager/documentManager/deleteCompanyDocumentManager', 'CompanyDashboard@deleteCompanyDocumentManager');



        Route::prefix('/docketManager/prefillerManager')->group(base_path('routes/dashboard/company/prefillerManagerRoute.php'));
        Route::post('/docketManager/prefillerManager/prefillerDataUpdate','PrefillerManagerController@prefillerDataUpdate');
        Route::post('/docketManager/prefillerManager/uploadExcelFile','PrefillerManagerController@uploadExcelFile');


        ///---Company Document Theme
        Route::get('docketManager/documentTheme/', array('as' => 'companyDocumentTheme', 'uses' => 'CompanyDashboard@companyDocumentTheme'));
        Route::post('docketManager/documentTheme/purchaseTheme/', 'CompanyDashboard@purchaseTheme')->name('purchase.theme');
        Route::get('docketManager/documentTheme/screenshot', array('as' => 'companyDocumentThemeScreensot', 'uses' => 'CompanyDashboard@companyDocumentThemeScreensot'));
        Route::get('docketManager/documentTheme/docketThemePreview', array('as' => 'companyDocketThemePreview', 'uses' => 'CompanyDashboard@companyDocketThemePreview'));

        // company project
        Route::Resource('docketManager/project','ProjectController');
        Route::post('docketManager/project/updates','ProjectController@updates');
        Route::post('docketManager/project/closeProject','ProjectController@closeProject');


        //===========================company profile section routes==================================//
        Route::group(['prefix' => 'profile'], function () {
            Route::get('/', array('as' => 'companyProfile', 'uses' => 'CompanyProfileController@companyProfile'));
            Route::post('/', 'CompanyProfileController@companyProfileSubmit');

            Route::get('selectSubscription', 'CompanyProfileController@subscriptionSelectTrial')->name('Company.Subscription.Select');
            Route::post('selectSubscription', 'CompanyProfileController@updateSubscription');

            Route::get('continueSubscription', 'CompanyProfileController@continueSubscription')->name('Company.Subscription.Continue');
            Route::post('continueSubscription', 'CompanyProfileController@continueSubscriptionSubmit');

            Route::get('subscription', 'CompanyProfileController@subscription')->name('Company.Subscription');
            Route::get('subscription/cancel', 'CompanyProfileController@subscriptionCancel')->name('Company.Subscription.Cancel');
            Route::get('subscription/upgrade', 'CompanyProfileController@upgradeSubscription')->name('Company.Subscription.Upgrade');
            Route::post('subscription', 'CompanyProfileController@subscriptionSubmit');
            Route::post('freeSubscription', 'CompanyProfileController@freeSubscription');
            Route::get('subscription/cardDeclined', 'CompanyProfileController@cardDeclined')->name('Company.Subscription.CardDeclined');
            Route::get('subscription/canceled', 'CompanyProfileController@canceled')->name('Company.Subscription.Canceled');

            Route::get('changePassword', array('as' => 'changePassword', 'uses' => 'CompanyDashboard@changePassword'));
            Route::get('invoiceSetting', array('as' => 'invoiceSetting', 'uses' => 'CompanyDashboard@invoiceSetting'));

            Route::get('docketSetting', array('as' => 'docketSetting', 'uses' => 'CompanyDashboard@docketSetting'));
            Route::post('docketSetting/saveDocketSetting', 'CompanyDashboard@saveDocketSetting');
            Route::post('docketSetting/deleteDocketSetting', 'CompanyDashboard@deleteDocketSetting');
            Route::post('docketSetting/updateDocketSetting', 'CompanyDashboard@updateDocketSetting');
            Route::post('docketSetting/submitNumberSystem', 'CompanyDashboard@submitNumberSystem');

            Route::post('invoiceSetting', 'CompanyDashboard@invoiceSettingSubmit');
            Route::post('changePassword', 'CompanyDashboard@changePasswordSubmit');

            Route::get('updateCreditCard', 'CompanyProfileController@updateCreditCard')->name('Company.CreditCard.Update');
            Route::post('updateCreditCard', 'CompanyProfileController@creditCardStore');
            Route::get('removeCreditCard', 'CompanyProfileController@removeCreditCard')->name('Company.CreditCard.Remove');

            Route::get('stripeInvoices', 'CompanyProfileController@stripeInvoices')->name('Company.stripe.invoices');
            Route::get('xeroSetting', 'CompanyProfileController@xeroSetting')->name('Company.xero.setting');
            Route::post('updateXero', 'CompanyProfileController@updateXero');

            Route::get('xeroSetting/unlinkXero/{id}', 'CompanyProfileController@unlinkXero');

            Route::get('timezone', 'CompanyProfileController@timezone')->name('Company.timezone');
            Route::post('timezone/store', 'CompanyProfileController@storeTimeZone')->name('Company.timezone.store');

            Route::get('billingHistory', 'CompanyProfileController@billingHistory')->name('Company.billingHistory');
            Route::get('billingHistory/view/{key}', 'CompanyProfileController@billingHistoryView')->name('Company.billingHistoryView');
            Route::get('billingHistory/downloadInvoice/{key}', 'CompanyProfileController@downloadInvoice');
            //    Route::post('activateUser/{id}','CompanyProfileController@activateUser')->name('Company.ActivateUser');
        });

        //========================================= INVOICE MANAGER ROUTES ===============================================//
        Route::group(['prefix' => 'invoiceManager'], function () {
            //<>--- invoice template ---<>//
            Route::get('/', 'CompanyDashboard@companyInvoiceManager')->name('companyInvoiceManager');
            Route::post('/xero/invoiceDesignXeroSetting', 'CompanyDashboard@invoiceDesignXeroSetting');

            Route::get('designInvoice/{key}', 'CompanyDashboard@designInvoice');
            Route::post('designInvoice/', 'CompanyDashboard@cancelInvoice');


            //<>--- emailed invoices ---<>//
            Route::get('emailedInvoices/view/{key}', 'CompanyDashboard@viewEmailedInvoice');

            Route::get('invoice/view/{key}', 'CompanyDashboard@companyInvoiceView');

            //Export Invoice
            Route::get('exportInvoice', 'CompanyDashboard@exportInvoice')->name('exportInvoice');
            Route::get('exportEmailInvoice', 'CompanyDashboard@exportEmailInvoice')->name('exportEmailInvoice');
            Route::get('makePdfInvoice', 'CompanyDashboard@makePdfInvoice')->name('makePdfInvoice');
            Route::get('makePdfEmailedInvoice', 'CompanyDashboard@makePdfEmailedInvoice')->name('makePdfEmailedInvoice');

            Route::get('invoice/downloadViewInvoice/{key}', 'CompanyDashboard@downloadViewInvoice');
            Route::get('invoice/downloadViewInvoiceEmail/{key}', 'CompanyDashboard@downloadViewInvoiceEmail');
            Route::get('invoices/sendInvoice', array('as' => 'companySendInvoice', 'uses' => 'CompanyDashboard@companySendInvoice'));
            Route::post('invoices/sendInvoice/invoiceTemplate/{id}', 'CompanyDashboard@invoiceTemplate');

        });
        //========================================= INVOICE MANAGER ROUTES ===============================================//

        Route::post('invoiceManager/saveInvoice', 'CompanyDashboard@saveInvoiceTemplate');
        Route::post('invoiceManager/XeroInvoiceUpdate', 'CompanyDashboard@XeroInvoiceUpdate');

        Route::post('invoiceManager/assignInvoice/', 'CompanyDashboard@storeAssignInvoice');
        Route::post('invoiceManager/designInvoice/gst', 'CompanyDashboard@gstUpdate');
        Route::post('invoiceManager/designInvoice/invoiceGSTLabelUpdate/{key}', 'CompanyDashboard@gstUpdateValue');
        Route::post('invoiceManager/designInvoice/addInvoiceField/{key}', 'CompanyDashboard@addInvoiceField');

        Route::post('invoiceManager/designInvoice/deleteInvoiceField/{key}', 'CompanyDashboard@deleteInvoiceField');
        Route::post('invoiceManager/designInvoice/previewDescription', 'CompanyDashboard@previewDescription');
        Route::post('invoiceManager/designInvoice/showHideInvoicePrefix', 'CompanyDashboard@showHideInvoicePrefix');


        Route::post('invoiceManager/designInvoice/invoiceFieldUpdatePosition/{key}', 'CompanyDashboard@invoiceFieldUpdatePosition');
        Route::post('invoiceManager/designInvoice/invoiceFieldLabelUpdate', 'CompanyDashboard@invoiceFieldLabelUpdate');

        Route::get('invoiceManager/assignInvoice', array('as' => 'companyAssignInvoice', 'uses' => 'CompanyDashboard@companyAssignInvoice'));
        Route::delete('invoiceManager/assignInvoice/', 'CompanyDashboard@cancelAssignInvoice');
        Route::post('invoiceManager/updateInvoicePrefix/', 'CompanyDashboard@updateInvoicePrefix');

        Route::get('employee/leave', 'LeaveController@index')->name('leave_management.index');
        Route::post('employee/leave', 'LeaveController@store')->name('leave.management.store');
        Route::post('employee/leave/update', 'LeaveController@Update')->name('leave.management.update');
        Route::post('employee/leave/delete', 'LeaveController@delete')->name('leave.management.delete');
        Route::get('employee/leave/edit/{id}', 'LeaveController@edit')->name('leave.management.edit');
        Route::get('employee/leave/{id}', 'LeaveController@getEmployeeLeaveById')->name('leave.management.view');

        Route::get('machine/index', 'MachineManagementController@index')->name('machine_management.index');
        Route::post('machine/store', 'MachineManagementController@store')->name('machine_management.store');
        Route::get('machine/edit/{id}', 'MachineManagementController@edit')->name('machine_management.edit');
        Route::post('machine/delete', 'MachineManagementController@delete')->name('machine_management.delete');
        Route::post('machine/update', 'MachineManagementController@update')->name('machine_management.update');

        Route::get('machine/availability/index', 'MachineManagementController@machineAvailability')->name('machine_management.availability');

        Route::post('docketManager/assign/docket/store', 'CompanyDashboard@v2StoreAssignDocket')->name('assign.docket.store');
        Route::post('docketManager/assign/docket/update', 'CompanyDashboard@v2UpdateAssignDocket')->name('assign.docket.update');
        Route::post('docketManager/assign/docket/delete', 'CompanyDashboard@v2DeleteAssignDocket')->name('assign.docket.delete');
        Route::get('docketManager/assign/docket/dayview/{date}', 'CompanyDashboard@v2StoreAssignDocketDayView')->name('assign.docket.day.view');

        
    });
});


Route::group(['prefix'=>'folder'],function (){
    Route::get('/{token}','Auth\ShareableFolderController@index');
    Route::post('/verifyToken','Auth\ShareableFolderController@verifyToken');
    Route::post('/login','Auth\ShareableFolderController@folderLogin');
    Route::get('/docket/view/{key}', 'Auth\ShareableFolderController@companyDocketView');
    Route::get('/docket/view/emailed/{key}', 'Auth\ShareableFolderController@companyDocketViewEmailed');
    Route::get('/invoice/emailed/view/{key}', 'Auth\ShareableFolderController@viewEmailedInvoice');
    Route::get('/invoice/view/{key}', 'Auth\ShareableFolderController@companyInvoiceView');

    //download
    Route::get('/docket/download/emailed/{key}', 'Auth\ShareableFolderController@emailDocketPdfDownload');
    Route::get('/docket/download/{key}', 'Auth\ShareableFolderController@docketPdfDownload');
    Route::get('/invoice/download/emailed/{key}', 'Auth\ShareableFolderController@emailInvoicePdfDownload');
    Route::get('/invoice/download/{key}', 'Auth\ShareableFolderController@invoicePdfDownload');
    Route::post('/downloadPdf', 'Auth\ShareableFolderController@downloadPdf');


    Route::group(['middleware' => 'folder'], function () {
          Route::get('','ShareableFolderController@folderView');
          Route::post('/list','ShareableFolderController@folderlist');
          Route::post('/viewFolderData','ShareableFolderController@viewFolderData');
          Route::post('/searchFolderById','ShareableFolderController@searchFolderById');
          Route::post('/viewFolderReload','ShareableFolderController@viewFolderReload');

          Route::post('docket/view/approve', 'ShareableFolderController@companyDocketApprove');
          Route::get('/docket/approvalTypeView/{key}', 'ShareableFolderController@approvalTypeView');
          Route::post('/docket/reject', 'ShareableFolderController@docketReject');
          Route::get('/docket/emailed/{key}/{hashKey}/approve', 'ShareableFolderController@approve');
          Route::post('/docket/emailed/{key}/{hashKey}/approve', 'ShareableFolderController@approve');


    });


});










Route::get('storeImages3','APIController@storeImages3');