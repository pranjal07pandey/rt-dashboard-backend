<?php
Route::get('/','EmployeeManagement\EmployeeManagementController@index')->name('employeeManagement.index');

Route::group(['prefix' => 'employees'], function(){
    Route::post('/', 'EmployeeManagement\EmployeeController@store')->name('employees.store');
    Route::get('create', 'EmployeeManagement\EmployeeController@create')->name('employees.create');
    Route::get('{employee}/edit', 'EmployeeManagement\EmployeeController@edit')->name('employees.edit');
    Route::patch('{employee}','EmployeeManagement\EmployeeController@update')->name('employees.update');

    Route::get('{userId}/editAdmin', 'EmployeeManagement\EmployeeController@editAdmin')->name('employees.admin.edit');
    Route::patch('{userId}/updateAdmin', 'EmployeeManagement\EmployeeController@updateAdmin')->name('employees.admin.update');

    Route::post('receiveDocketCopy', 'EmployeeManagement\EmployeeController@receiveDocketCopy')->name('employees.receiveDocketCopy');
    Route::post('activate', 'EmployeeManagement\EmployeeController@activate')->name('employees.activate');
});