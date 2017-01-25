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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', '\App\DrupalStats\Controllers\MarkdownPageController@aboutPage');

Route::get('/viz/module-downloads', '\App\DrupalStats\Controllers\Visualizations\ProjectPageController@moduleDownloads');
Route::get('/viz/project-downloads', '\App\DrupalStats\Controllers\Visualizations\ProjectPageController@projectDownloads');
Route::get('/viz/projects-growth', '\App\DrupalStats\Controllers\Visualizations\ProjectPageController@projectsGrowth');
Route::get('/data/module-downloads', '\App\DrupalStats\Controllers\Data\ProjectDataController@moduleDownloads');
Route::get('/data/project-downloads', '\App\DrupalStats\Controllers\Data\ProjectDataController@projectDownloads');
Route::get('/data/projects-growth', '\App\DrupalStats\Controllers\Data\ProjectDataController@projectsGrowth');

Route::get('/viz/ci-jobs', '\App\DrupalStats\Controllers\Visualizations\CiJobPageController@cijobStatus');
Route::get('/viz/ci-jobs-reasons', '\App\DrupalStats\Controllers\Visualizations\CiJobPageController@cijobReason');
Route::get('/data/ci-jobs', '\App\DrupalStats\Controllers\Data\CiJobsDataController@cijobsBranchStatus');
Route::get('/data/ci-jobs-reasons', '\App\DrupalStats\Controllers\Data\CiJobsDataController@cijobsBranchReason');
Route::get('/data/ci-jobs/refresh', '\App\DrupalStats\Controllers\Data\CiJobsDataController@cijobsRefresh');

Route::get('/viz/user-languages', '\App\DrupalStats\Controllers\Visualizations\UserPageController@userLanguages');
Route::get('/viz/user-expertise', '\App\DrupalStats\Controllers\Visualizations\UserPageController@userExpertise');
Route::get('/viz/user-countries', '\App\DrupalStats\Controllers\Visualizations\UserPageController@userCountries');
Route::get('/viz/user-growth', '\App\DrupalStats\Controllers\Visualizations\UserPageController@userGrowth');
Route::get('/viz/user-gender-growth', '\App\DrupalStats\Controllers\Visualizations\UserPageController@userGenderGrowth');
Route::get('/viz/user-country-growth', '\App\DrupalStats\Controllers\Visualizations\UserPageController@userCountryGrowth');
Route::get('/data/user-languages', '\App\DrupalStats\Controllers\Data\UserDataController@userLanguages');
Route::get('/data/user-expertise', '\App\DrupalStats\Controllers\Data\UserDataController@userExpertise');
Route::get('/data/user-countries', '\App\DrupalStats\Controllers\Data\UserDataController@userCountries');
Route::get('/data/user-growth', '\App\DrupalStats\Controllers\Data\UserDataController@userGrowth');
Route::get('/data/user-gender-growth', '\App\DrupalStats\Controllers\Data\UserDataController@userGenderGrowth');
Route::get('/data/user-country-growth', '\App\DrupalStats\Controllers\Data\UserDataController@userCountryGrowth');

Route::get('/viz/issues/{name?}', '\App\DrupalStats\Controllers\Visualizations\ProjectIssuePageController@projectIssueBreakup');
Route::get('/viz/project-issue-count', '\App\DrupalStats\Controllers\Visualizations\ProjectIssuePageController@projectIssueCount');
Route::get('/data/issues/{name?}', '\App\DrupalStats\Controllers\Data\ProjectIssueDataController@projectIssueBreakup');
Route::get('/data/project-issue-count', '\App\DrupalStats\Controllers\Data\ProjectIssueDataController@projectIssueCount');

