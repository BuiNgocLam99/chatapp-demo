<?php

use App\Events\WebsocketDemoEvent;
use App\Http\Controllers\ChatsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    broadcast(new WebsocketDemoEvent('some data'));

    return view('welcome');
});

Route::get('/chats', [ChatsController::class, 'index']);
Route::get('/messages', [ChatsController::class, 'fetchMessages']);
Route::post('/message', [ChatsController::class, 'sendMessage']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
