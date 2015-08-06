<?php

// Pages
Route::get('/', 'PagesController@home');
Route::get('/leaderboard', 'PagesController@leaderboard');
Route::get('/privacy', 'PagesController@privacy');

// Uber OAuth
Route::post('/oauth/uber', 'OAuthController@uber');

// Ajax
Route::post('/ajax/pubpriv', 'AjaxController@pubpriv');