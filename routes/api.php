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
            'structures'=>'StructuresController',
    'users'=>'UserController',
    'communiques'=>'CommuniqueController',
    'actualites'=>'ActualiteController',
    'prestations'=>'PrestationController',
    'documents'=>'DocController',
    'organigrammes'=>'OrganigrammeController',
    'aofs'=>'AofController',
    'links'=>'LinkController',
    'maps'=>'MapsController',
  //  'docs'=>'DocsController',
    'citations'=>'CitationController',
    'mots'=>'MotController',
    'sts'=>'StructureSousTutuelleController',
    'posters'=>'PosterController',
    'teams'=>'TeamController',
            'roles' => 'RoleController',
            'permissions' => 'PermissionController',
            'user-projects' => 'UserProjectController',
            'notifications' => 'NotificationController',
        ]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// NewsLetter
Route::post('/subscribe','NewslettersController@subscribe')->name('subscribe');
Route::get('/historique/{type}','MediaController@index')->name('medias.index');
Route::get('/historique-show/{type}/{id}','MediaController@show')->name('medias.show');
Route::get('/journals','MediaController@index2')->name('medias.index2');


//poster 

Route::get('/posters/publication/up/{id}','StageController@publish')->name('posters.publish');
Route::get('/posters/publication/down/{id}','StageController@unpublish')->name('posters.unpublish');
Route::get('/posters/archivied/{id}','StageController@archive')->name('posters.archived');
Route::get('/posters/restored/{id}','StageController@restore')->name('posters.restored');

        Route::get('users/{id}/state/{state}', 'UserController@changeState');
        Route::post('users-search', 'UserController@search');

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
Route::get('public/communiques', 'PublicController@getCommuniques')->name('communiques');
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





 Route::get('/communiques/transmission/up/{id}','CommuniqueController@up')->name('communiques.up');
 Route::post('/communiques/transmission/down/{id}','CommuniqueController@down')->name('communiques.down');
 Route::get('/communiques/publication/up/{id}','CommuniqueController@publish')->name('communiques.publish');
 Route::get('/communiques/publication/down/{id}','CommuniqueController@unpublish')->name('communiques.unpublish');
 Route::get('/communiques/archivied/{id}','CommuniqueController@archive')->name('communiques.archived');
 Route::get('/communiques/restored/{id}','CommuniqueController@restore')->name('communiques.restored');
 

});
