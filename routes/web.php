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

use App\Jobs\TestJob;
use Illuminate\Support\Facades\Auth;

if (app()->environment('local')) {
    Auth::loginUsingId(1);
}

// Dynamic Dockerfiles and entrypoints for Cloud Build
Route::get('/build/{type}/{file}', 'BuildInstructionsController@show')->name('build-instructions');

// Incoming GitHub webhooks
Route::post('/hooks/github', 'GitHubHookController');

// TODO: Remove
Route::get('/test', function () {
    TestJob::dispatch(12);
});

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    // Dashboard
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    // Google Projects
    Route::resource('google-projects', 'GoogleProjectController');

    // Projects
    Route::resource('projects', 'ProjectController');

    // Environments
    Route::resource('projects.environments', 'EnvironmentController');

    // Environment Databases
    Route::resource('projects.environments.database', 'EnvironmentDatabaseController')
        ->only(['index', 'store', 'destroy']);

    // Environment Settings
    Route::resource('projects.environments.settings', 'EnvironmentSettingsController')
        ->only(['index', 'store']);

    // Deployments
    Route::resource('projects.environments.deployments', 'DeploymentController');
    Route::post('projects/{project}/environments/{environment}/deployments/{deployment}/redeploy', 'RedeployDeploymentController')
        ->name('projects.environments.deployments.redeploy');

    // Logs
    Route::get('projects/{project}/environments/{environment}/logs', 'EnvironmentLogController')
        ->name('projects.environments.logs');

    // Domains
    Route::get('projects/{project}/environments/{environment}/domains', 'EnvironmentDomainsController')
        ->name('projects.environments.domains');

    // Commands
    Route::get('projects/{project}/environments/{environment}/commands', 'CommandController@index')
        ->name('projects.environments.commands.index');
    Route::get('projects/{project}/environments/{environment}/commands/{command}', 'CommandController@show')
        ->name('projects.environments.commands.show');

    // Database Instances
    Route::resource('database-instances', 'DatabaseInstanceController');

    // Inbound Source Provider Authorizations
    Route::get('auth/github', function () {
        return view('popup-callback');
    });
});
