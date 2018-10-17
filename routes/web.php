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

Route::get('/', 'PagesController@index');

Route::get('/about', 'PagesController@about');

Route::get('/services', 'PagesController@services');

Route::get('/home/users', 'PagesController@userlist');

Route::get('/home/fav', 'FavController@showFavourite');

Route::post('/', 'FavController@favourite');

Route::resource('posts', 'PostsController');

Route::get('search', 'SearchController@scopeSearch');



   //     Route::get('/users/{id}', function($id) {
    //        return 'This is user ' .$id;
      //  });
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');






