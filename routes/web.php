<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();


Route::get('/redirect', 'SocialAuthController@redirect');
Route::get('/callback', 'SocialAuthController@callback');

Route::get('/home', 'HomeController@index');

Route::get('post/{offset?}/{srcword?}','PostController@index');


Route::get('/checkupdate', function() {
    return response(file_get_contents(storage_path()."/app/proc/".\Auth::id().".json"));
});


