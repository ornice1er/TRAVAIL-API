<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['namespace' => 'App\Http\Controllers'], function () {

    Route::post('/login', 'UserAuthController@login');
    Route::post('/forgot-password', 'UserAuthController@sendResetPasswordLink');
    Route::post('/recovery-password', 'UserAuthController@recoveryPassword');
    // Route::post('/register', ["UserAuthController@ 'register']);
    Route::post('/verify-otp', 'OtpController@verifyOTP');

    Route::post('/user-notify', 'UserAuthController@notify');
    Route::get('/users-notified', 'UserController@getNotified');
    Route::get('settings', 'SettingController@index');
    Route::post('settings', 'SettingController@update');

    Route::middleware('basic.auth')->group(function () {

        Route::apiResources([
            'users' => 'UserController',
            'municipalities' => 'MunicipalityController',
        ]);
        Route::get('municipalities-ghm', 'MunicipalityController@indexGhm');
    });

    Route::middleware('auth:api')->group(function () {
        Route::get('/user', 'UserAuthController@user');
        Route::get('/logout', 'UserAuthController@logout');
        Route::post('/change-password', 'UserAuthController@changePassword');
        Route::post('/change-first-password', 'UserAuthController@ChangeFirstPassword');
        Route::post('/reset-password', 'UserAuthController@resetPassword');
        Route::post('/user-update', 'UserAuthController@update');
        Route::post('/user-permissions', 'UserAuthController@userPermission');

        Route::post('/user-push-token', 'UserAuthController@addPushToken');
        Route::get('/user-push-token-delete/{id}', 'UserAuthController@deletePushToken');

        Route::post('/upload-file', 'UserAuthController@uploadFile');
        Route::get('/delete-file', 'UserAuthController@deleteFile');

        Route::apiResources([
            'corps' => 'CorpsController',
            'agents' => 'AgentController',
            'statuts' => 'StatutController',
            'primes' => 'PrimeController',
            'fonctions' => 'FonctionController',
            'hsups' => 'HsupController',
            'grades' => 'GradeController',
            'periodes' => 'PeriodeController',
            'retenues' => 'RetenueController',
            'typeactes' => 'TypeacteController',
            'typeprimes' => 'TypeprimeController',
            'primestatuts' => 'PrimestatutController',
            'joursferies' => 'JoursferieController',
            'notationagents' => 'NotationagentController',
            'uas' => 'UAController',
            'roles' => 'RoleController',
            'permissions' => 'PermissionController',
            'user-projects' => 'UserProjectController',
            'notifications' => 'NotificationController',
        ]);

        Route::get('/logs', 'LogController@index');


        Route::get('notifications/{id}/state/{state}', 'NotificationController@changeState');
        Route::post('notifications-search', 'NotificationController@search');

        Route::get('corps/{id}/state/{state}', 'CorpsController@changeState');
        Route::post('corps-search', 'CorpsController@search');

        Route::get('statuts/{id}/state/{state}', 'StatutController@changeState');
        Route::post('statuts-search', 'StatutController@search');
        
        Route::get('primes/{id}/state/{state}', 'PrimeController@changeState');
        Route::post('primes-search', 'PrimeController@search');

        Route::get('fonctions/{id}/state/{state}', 'FonctionController@changeState');
        Route::post('fonctions-search', 'FonctionController@search');

        Route::get('grades/{id}/state/{state}', 'GradeController@changeState');
        Route::post('grades-search', 'GradeController@search');

        Route::get('periodes/{id}/state/{state}', 'PeriodeController@changeState');
        Route::post('periodes-search', 'PeriodeController@search');

        Route::get('uas/{id}/state/{state}', 'UAController@changeState');
        Route::post('uas-search', 'UAController@search');

        Route::get('typeactes/{id}/state/{state}', 'TypeacteController@changeState');
        Route::post('typeactes-search', 'TypeacteController@search');

        Route::get('typeprimes/{id}/state/{state}', 'TypeprimeController@changeState');
        Route::post('typeprimes-search', 'TypeprimeController@search');

        Route::get('primestatuts/{id}/state/{state}', 'PrimestatutController@changeState');
        Route::post('primestatuts-search', 'PrimestatutController@search');

        Route::get('retenues/{id}/state/{state}', 'RetenueController@changeState');
        Route::post('retenues-search', 'RetenueController@search');

        Route::get('agents/{id}/state/{state}', 'AgentController@changeState');
        Route::post('agents-search', 'AgentController@search');

        Route::get('joursferies/{id}/state/{state}', 'JoursferieController@changeState');
        Route::post('joursferies-search', 'JoursferieController@search');

        Route::get('notationagents/{id}/state/{state}', 'NotationagentController@changeState');
        Route::post('notationagents-search', 'NotationagentController@search');

        Route::get('hsups/{id}/state/{state}', 'HsupController@changeState');
        Route::post('hsups-search', 'HsupController@search');

        Route::post('roles-search', 'RoleController@search');
        Route::post('permissions-search', 'PermissionController@search');

        Route::get('user-settings', 'UserSettingController@index');
        Route::put('user-settings', 'UserSettingController@update');

        Route::post('projects-search', 'ProjectController@search');
        Route::get('projects/{id}/state/{state}', 'ProjectController@changeState');

        Route::get('users/{id}/state/{state}', 'UserController@changeState');
        Route::post('users-search', 'UserController@search');

        Route::get('users-rh', 'UserController@indexRH');
        Route::get('users-rh/{id}', 'UserController@showRH');
    });




Route::get('accueil', 'PublicController@index')->name('accueil');
Route::get('public/actualites', 'PublicController@getActualites')->name('dgt');
Route::get('public/services', 'PublicController@getServices');

Route::get('accueil-dgrce', 'PublicController@index')->name('dgrce');
Route::get('accueil-dgfp', 'PublicController@index')->name('dgfp');
Route::get('cookie-checker', 'PublicController@setCookie')->name('cookie');


Route::get('anciens-ministres', 'PublicController@index')->name('anciens');
Route::get('suivi-des-reformes', 'PublicController@index')->name('reformes');
Route::get('igsep', 'PublicController@index')->name('igsep');
Route::get('structures-sous-tutelles', 'PublicController@index')->name('st');
Route::get('directions', 'PublicController@index')->name('directions');
Route::get('organigramme', 'PublicController@index')->name('organigramme');
Route::get('vision', 'PublicController@index')->name('vision');
Route::get('sgm', 'PublicController@index')->name('sgm');
Route::get('dd', 'PublicController@index')->name('dd');
Route::get('aof-igsep', 'PublicController@index')->name('aof.igsep');
Route::get('aof/{id?}', 'PublicController@index')->name('aof');
Route::get('eservices', 'PublicController@index')->name('eservices');
Route::get('ministre/{category?}', 'PublicController@index')->name('ministre');
Route::get('public/documents', 'PublicController@getDocuments')->name('document');
Route::get('communiques', 'PublicController@index')->name('communiques');
Route::get('recrutements', 'PublicController@index')->name('recrutements');
Route::get('opp-stages', 'PublicController@index')->name('stages');
Route::get('formations', 'PublicController@index')->name('formations');
Route::get('appel-d-offres', 'PublicController@index')->name('offres');
Route::get('actualites/{category?}', 'PublicController@index')->name('actualites');
Route::get('sanctions', 'PublicController@index')->name('sanctions');


Route::get('/page/communiques/{slug}', 'PublicController@getCommuniquePage')->name('page.communique');
Route::get('/page/actualites/{slug}', 'PublicController@getActualitePage')->name('page.actualite');
Route::get('/page/galleries/{slug}', 'PublicController@index')->name('page.galleries');


Route::get('structure/presentation/{st}', 'PublicController@index')->name('structure');

Route::post('send-contact-form', 'PublicController@sendContactForm');


});
