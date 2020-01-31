<?php

Route::get('/student/{card_uid}', 'ApiController@getStudent')->name('getstudent');
Route::get('/students', 'ApiController@getAllStudents')->name('getallstudents');
Route::get('/groups', 'ApiController@getAllGroups')->name('getallgroups');
Route::get('/groupsbycohort/{cohort}', 'ApiController@getGroupsByCohort')->name('getgroupsbycohort');
Route::post('/register', 'ApiController@registerInOut')->name('register');
Route::post('/savecards', 'ApiController@saveCards')->name('savecards');
Route::get('/sendreport/{daypart}/{date}', 'ApiController@sendReport')->name('sendreport');
Route::post('/savemanualregistration', 'ApiController@saveManualRegistration');
Route::get('/studentregistration/{date}/{id}', 'ApiController@getRegistrationOfStudent');
