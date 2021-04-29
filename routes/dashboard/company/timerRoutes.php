<?php
//========================================= Timers ROUTES ===============================================//
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