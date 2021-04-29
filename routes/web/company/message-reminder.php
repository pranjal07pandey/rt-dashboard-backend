<?php
Route::get('/', 'MessageReminder\MessageReminderController@index')->name('message-reminder.index');
Route::post('/','MessageReminder\MessageReminderController@store')->name('message-reminder.store');
Route::post('create-group','MessageReminder\MessageReminderController@createGroup')->name('message-reminder.create-group');

Route::post('chatView','MessageReminder\MessageReminderController@chatView')->name('message-reminder.chatView');