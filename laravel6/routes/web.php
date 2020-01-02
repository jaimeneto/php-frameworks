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

Auth::routes(['verify' => true]);

Route::get('/', 'HomeController@index')->name('home');
Route::redirect('/home', '/');

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', 'PostController@index')->name('admin');
    Route::get('/user', 'UserController@index')->name('user.index');
    Route::delete('/user/{id}/{force?}', 'UserController@destroy')->name('user.destroy');
    Route::put('/user/{id}/turnIntoAdmin', 'UserController@turnIntoAdmin')->name('user.turnIntoAdmin');
    Route::put('/user/{id}/restore', 'UserController@restore')->name('user.restore');

    Route::resource('/post', 'PostController')->except(['show']);

    Route::get('/comment', 'CommentController@index')->name('comment.index');
    Route::put('/comment/{id}', 'CommentController@approve')->name('comment.approve');
    Route::delete('/comment/{id}', 'CommentController@destroy')->name('comment.destroy');
});

Route::get('/post/{id}', 'PostController@show')->name('post.show');
Route::post('/comment', 'CommentController@store')->name('comment.store');