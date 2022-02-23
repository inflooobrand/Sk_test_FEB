<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::post('login', 'App\Http\Controllers\AuthController@login');
    Route::post('register', 'App\Http\Controllers\AuthController@register');
    
});

Route::prefix('v1')->middleware(['auth:api'])->group(function () {
    Route::post('blog/create', 'App\Http\Controllers\BlogPostsController@blogCreate');
    Route::get('user/profile', 'App\Http\Controllers\AuthController@show');
    Route::put('user/update', 'App\Http\Controllers\AuthController@update');
    Route::get('user/list', 'App\Http\Controllers\AuthController@list');
    Route::get('blog/fetch', 'App\Http\Controllers\BlogPostsController@show');
    Route::get('blog/list', 'App\Http\Controllers\BlogPostsController@list');
    Route::post('blog/comments', 'App\Http\Controllers\BlogPostsController@blogComments');
    Route::put('blog/update', 'App\Http\Controllers\BlogPostsController@update');
    Route::post('blog/delete', 'App\Http\Controllers\BlogPostsController@delete');
    Route::get('blog/comments/fetch', 'App\Http\Controllers\BlogPostsController@blogCommentShow');
    Route::put('blog/comments/update', 'App\Http\Controllers\BlogPostsController@blogCommentupdate');
    Route::get('blog/comments/list', 'App\Http\Controllers\BlogPostsController@blogCommentList');
    Route::post('blog/comments/delete', 'App\Http\Controllers\BlogPostsController@blogCommentDelete');
    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
});