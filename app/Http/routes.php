<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/viz/module-downloads', '\App\DrupalStats\Controllers\Visualizations\ProjectPageController@moduleDownloads');
Route::get('/data/module-downloads', '\App\DrupalStats\Controllers\Data\ModuleDownloadsDataController@moduleDownloads');

Route::get('/viz/ci-jobs', '\App\DrupalStats\Controllers\Visualizations\CiJobPageController@cijobStatus');
Route::get('/viz/ci-jobs-reasons', '\App\DrupalStats\Controllers\Visualizations\CiJobPageController@cijobReason');
Route::get('/data/ci-jobs', '\App\DrupalStats\Controllers\Data\CiJobsDataController@cijobsBranchStatus');
Route::get('/data/ci-jobs-reasons', '\App\DrupalStats\Controllers\Data\CiJobsDataController@cijobsBranchReason');
Route::get('/data/ci-jobs/refresh', '\App\DrupalStats\Controllers\Data\CiJobsDataController@cijobsRefresh');

Route::get('/viz/user-languages', '\App\DrupalStats\Controllers\Visualizations\UserPageController@userLanguages');
Route::get('/viz/user-expertise', '\App\DrupalStats\Controllers\Visualizations\UserPageController@userExpertise');
Route::get('/data/user-languages', '\App\DrupalStats\Controllers\Data\UserDataController@userLanguages');
Route::get('/data/user-expertise', '\App\DrupalStats\Controllers\Data\UserDataController@userExpertise');
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});
