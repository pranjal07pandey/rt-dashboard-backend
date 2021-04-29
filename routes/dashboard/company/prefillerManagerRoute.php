<?php
//========================================= Prefiller ROUTES ===============================================//
    Route::get('/', 'PrefillerManagerController@index')->name('companyPrefillerManager');
    Route::post('/savePrefillerManager','PrefillerManagerController@savePrefillermanager')->name('savePrefillermanager');
    Route::post('/saveParentPrefillerLabel','PrefillerManagerController@saveParentPrefillerLabel')->name('saveParentPrefillerLabel');
    Route::post('deletePrefillerManagerlabel','PrefillerManagerController@deletePrefillerManagerlabel')->name('deletePrefillerManagerlabel');
    Route::post('clearAllPrefillerManager','PrefillerManagerController@clearAllPrefillerManager')->name('clearAllPrefillerManager');
    Route::post('deletePrefillerManager','PrefillerManagerController@deletePrefillerManager')->name('deletePrefillerManager');
    Route::post('checkPrefillerManager','PrefillerManagerController@checkPrefillerManager')->name('checkPrefillerManager');
    Route::post('updatePrifillerTitle','PrefillerManagerController@updatePrifillerTitle')->name('updatePrifillerTitle');
    Route::post('updatePrifillerManagerlabel','PrefillerManagerController@updatePrifillerManagerlabel')->name('updatePrifillerManagerlabel');



//          old routes
//        Route::get('docketManager/prefillerManager/', array('as' => 'companyPrefillerManager', 'uses' => 'CompanyDashboard@companyPrefillerManager'));
//        Route::post('docketManager/prefillerManager/addPrefillerManager', 'CompanyDashboard@addPrefillerManager');
//        Route::post('docketManager/prefillerManager/updatePrefillerManager', 'CompanyDashboard@updatePrefillerManager');
//        Route::post('docketManager/prefillerManager/deletePrefillerManager', 'CompanyDashboard@deletePrefillerManager');
//        Route::post('docketManager/prefillerManager/savePrefillerLabel', 'CompanyDashboard@savePrefillerLabel');
//        Route::post('docketManager/prefillerManager/editPrefillerLabel', 'CompanyDashboard@editPrefillerLabel');
//        Route::post('docketManager/prefillerManager/deletePrefillerLabel', 'CompanyDashboard@deletePrefillerLabel');
//        Route::get('docketManager/prefillerManager/success', 'CompanyDashboard@addPrefillerManagerSuccess');

//prefillerManager New route
//            Route::post('docketManager/prefillerManager/saveParentPrefillerLabel','CompanyDashboard@saveParentPrefillerLabel');
//            Route::post('docketManager/prefillerManager/deletePrefillerManagerlabel','CompanyDashboard@deletePrefillerManagerlabel');




