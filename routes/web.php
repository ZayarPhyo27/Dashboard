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
    return redirect(url('/login'));
});

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Auth::routes();
Route::group(['middleware' => ['auth']], function() {
    Route::resource('article', ArticleController::class);
    Route::get('article/{id}/publish',[App\Http\Controllers\ArticleController::class, 'publish']);
    Route::get('article/{id}/active',[App\Http\Controllers\ArticleController::class, 'active']);
    Route::post('article/upload',[App\Http\Controllers\ArticleController::class,'upload'])->name('article.upload');
});


Route::group(['middleware' => ['auth','XSS']], function() {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('roles', RoleController::class);
    Route::resource('user', UserController::class);
    Route::resource('permission', PermissionController::class);
    Route::resource('notification', NotificationController::class);
    Route::resource('podcast', PodcastController::class);
    Route::resource('comic', ComicController::class);
    Route::resource('feedback', FeedbackController::class);
    Route::get('profile','UserController@profile');

    Route::get('podcast/{id}/publish', [App\Http\Controllers\PodcastController::class, 'publish']);
    Route::get('podcast/{id}/active', [App\Http\Controllers\PodcastController::class, 'active']);

    Route::get('comic/{id}/publish', [App\Http\Controllers\ComicController::class, 'publish']);
    Route::get('comic/{id}/active', [App\Http\Controllers\ComicController::class, 'active']);


    Route::get('/notification/{id}/push',[App\Http\Controllers\NotificationController::class, 'push']);

    Route::resource('video', VideoController::class);
    Route::get('video/{id}/publish',[App\Http\Controllers\VideoController::class, 'publish']);
    Route::get('video/{id}/active',[App\Http\Controllers\VideoController::class, 'active']);

    Route::get('quiz/{id}/publish', [App\Http\Controllers\QuizController::class, 'publish']);
    Route::get('quiz/{id}/active', [App\Http\Controllers\QuizController::class, 'active']);
    Route::resource('quiz', QuizController::class);

});
