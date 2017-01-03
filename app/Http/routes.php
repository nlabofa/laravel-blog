<?php


Route::get('auth/login', ['as' => 'login', 'uses' => 'Auth\AuthController@getLogin']);
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', ['as' => 'logout', 'uses' => 'Auth\AuthController@getLogout']);

Route::get('auth/register', ['as' => 'register', 'uses' => 'Auth\AuthController@getRegister']);
Route::post('auth/register', 'Auth\AuthController@postRegister');


 
//This is a post action that allows  email to be sent to the user which contains special token to reset password
Route::post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
//this is the view where the user enters the email he uses to register for account
Route::get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
//this is the view where the user will reset and enter new password after he has clicked on the confirmation link
//this triggers the post action to reset the form
Route::post('password/reset', 'Auth\PasswordController@reset');

Route::resource('categories', 'CategoryController', ['except' => ['create']]);
Route::resource('tags', 'TagController', ['except' => ['create']]);


Route::get('blog/{slug}', [ 'as' => 'blog.single', 'uses' => 'BlogController@getSingle'])->where('slug', '[\w\d\-\_]+');
Route::get('blog',[ 'uses' => 'BlogController@getIndex', 'as' => 'blog.index']);

Route::post('comments/{post_id}', ['uses' => 'CommentsController@store', 'as' => 'comments.store']);
Route::get('comments/{id}/edit', ['uses' => 'CommentsController@edit', 'as' => 'comments.edit']);
Route::put('comments/{id}', ['uses' => 'CommentsController@update', 'as' => 'comments.update']);
Route::delete('comments/{id}', ['uses' => 'CommentsController@destroy', 'as' => 'comments.destroy']);
Route::get('comments/{id}/delete', ['uses' => 'CommentsController@delete', 'as' => 'comments.delete']);

//pages controller routes that handles the home ,contact and about page(static pages)
Route::get('contact', 'PagesController@getContact');
Route::post('contact', 'PagesController@postContact');
Route::get('about', 'PagesController@getAbout');
Route::get('/', 'PagesController@getIndex');

//CRUD route
Route::resource('posts', 'PostController');

