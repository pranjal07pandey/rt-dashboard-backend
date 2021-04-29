<?php
Route::get('/', 'ClientManagement\ClientManagementController@index')->name('clientManagement.index');

Route::group(['prefix' => 'clients'], function(){
    Route::get('search/{key}', 'ClientManagement\ClientManagementController@search');

    Route::get('request','ClientManagement\ClientRequestController@index')->name('clients.request');
    Route::post('request', 'ClientManagement\ClientManagementController@request');
    Route::get('request/{action}/{key}', 'ClientManagement\ClientRequestController@action')->name('clients.request.action');

    Route::get('unapproved', 'ClientManagement\ClientRequestController@unapproved')->name('clients.request.unapproved');
    Route::delete('destroy', 'ClientManagement\ClientManagementController@destroy')->name('clients.destroy');

    Route::group(['prefix' => 'emails'], function(){
        Route::get('/', 'ClientManagement\EmailClientController@index')->name('clients.emails.index');
        Route::post('/', 'ClientManagement\EmailClientController@store')->name('clients.emails.store');
        Route::post('update', 'ClientManagement\EmailClientController@update')->name('clients.emails.update');
        Route::delete('destroy', 'ClientManagement\EmailClientController@destroy')->name('clients.emails.destroy');
    });
});