<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware'=>'auth'],function(){

    Route::get('notifications','App\Http\Controllers\NotificationController@index')->name('notification.index');
    Route::get('notifications/{notification}','App\Http\Controllers\NotificationController@show')->name('notification.show');
    Route::put('notifications/mark-as-read-all','App\Http\Controllers\NotificationController@markAllAsRead')->name('notification.markAsRead.all');
    Route::delete('notifications/clear-all','App\Http\Controllers\NotificationController@destroy')->name('notification.destory');
    Route::put('notification/{notification}/mark-as-read','App\Http\Controllers\NotificationController@markAsRead')->name('notification.markAsRead.individual');
    Route::get('users/{user}','App\Http\Controllers\HomeController@user')->name('user.show');

});
