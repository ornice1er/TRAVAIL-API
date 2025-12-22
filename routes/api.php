<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

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


                Route::get('/dash', 'DashController@getDash');


        Route::apiResources([
            'structures'=>'StructureController',
    'users'=>'UserController',
    'communiques'=>'CommuniqueController',
        'communique-files'=>'CommuniqueFileController',
    'actualites'=>'ActualiteController',
    'prestations'=>'PrestationController',
    'documents'=>'DocController',
    'organigrammes'=>'OrganigrammeController',
    'aofs'=>'AofController',
    'links'=>'LinkController',
    'maps'=>'MapsController',
    'docs'=>'DocsController',
    'citations'=>'CitationController',
    'categories'=>'CategoryController',
    'mots'=>'MotController',
    'sts'=>'StructureSousTutuelleController',
    'posters'=>'PosterController',
    'teams'=>'TeamController',
    'roles' => 'RoleController',
    'permissions' => 'PermissionController',
    'user-projects' => 'UserProjectController',
    'notifications' => 'NotificationController',
    'galeries'
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
 

  Route::get('/actualites/transmission/up/{id}','ActualitesController@up')->name('actualites.up');
 Route::post('/actualites/transmission/down/{id}','ActualitesController@down')->name('actualites.down');
 Route::get('/actualites/publication/up/{id}','ActualitesController@publish')->name('actualites.publish');
 Route::get('/actualites/publication/down/{id}','ActualitesController@unpublish')->name('actualites.unpublish');
 Route::get('/actualites/archivied/{id}','ActualitesController@archive')->name('actualites.archived');
 Route::get('/actualites/restored/{id}','ActualitesController@restore')->name('actualites.restored');

  Route::get('/prestations/transmission/up/{id}','PrestationsController@up')->name('prestations.up');
 Route::post('/prestations/transmission/down/{id}','PrestationsController@down')->name('prestations.down');
 Route::get('/prestations/publication/up/{id}','PrestationsController@publish')->name('prestations.publish');
 Route::get('/prestations/publication/down/{id}','PrestationsController@unpublish')->name('prestations.unpublish');
 Route::get('/prestations/archivied/{id}','PrestationsController@archive')->name('prestations.archived');
 Route::get('/prestations/restored/{id}','PrestationsController@restore')->name('prestations.restored');


 //docs actualité
 Route::get('/documents/transmission/up/{id}','DocsController@up')->name('documents.up');
 Route::post('/documents/transmission/down/{id}','DocsController@down')->name('documents.down');
 Route::get('/documents/publication/up/{id}','DocsController@publish')->name('documents.publish');
 Route::get('/documents/publication/down/{id}','DocsController@unpublish')->name('documents.unpublish');
 Route::get('/documents/archivied/{id}','DocsController@archive')->name('documents.archived');
 Route::get('/documents/restored/{id}','DocsController@restore')->name('documents.restored');
 //Route::get('/documents/delete/{id}','DocsController@delete')->name('documents.delete');


 //Organigramme actualité
 Route::get('/organigrammes/transmission/up/{id}','OrganigrammesController@up')->name('organigrammes.up');
 Route::post('/organigrammes/transmission/down/{id}','OrganigrammesController@down')->name('organigrammes.down');
 Route::get('/organigrammes/publication/up/{id}','OrganigrammesController@publish')->name('organigrammes.publish');
 Route::get('/organigrammes/publication/down/{id}','OrganigrammesController@unpublish')->name('organigrammes.unpublish');
 Route::get('/organigrammes/archivied/{id}','OrganigrammesController@archive')->name('organigrammes.archived');
 Route::get('/organigrammes/restored/{id}','OrganigrammesController@restore')->name('organigrammes.restored');
 
 //Organigramme actualité
 Route::get('/aofs/transmission/up/{id}','AofController@up')->name('aofs.up');
 Route::post('/aofs/transmission/down/{id}','AofController@down')->name('aofs.down');
 Route::get('/aofs/publication/up/{id}','AofController@publish')->name('aofs.publish');
 Route::get('/aofs/publication/down/{id}','AofController@unpublish')->name('aofs.unpublish');
 Route::get('/aofs/archivied/{id}','AofController@archive')->name('aofs.archived');
 Route::get('/aofs/restored/{id}','AofController@restore')->name('aofs.restored');

 //Stage 
 Route::get('/stages/transmission/up/{id}','StageController@up')->name('stages.up');
 Route::post('/stages/transmission/down/{id}','StageController@down')->name('stages.down');
 Route::get('/stages/publication/up/{id}','StageController@publish')->name('stages.publish');
 Route::get('/stages/publication/down/{id}','StageController@unpublish')->name('stages.unpublish');
 Route::get('/stages/archivied/{id}','StageController@archive')->name('stages.archived');
 Route::get('/stages/restored/{id}','StageController@restore')->name('stages.restored');

 //Appel d'offre 
 Route::get('/appels-offre/transmission/up/{id}','StageController@up')->name('appels_offre.up');
 Route::post('/appels-offre/transmission/down/{id}','StageController@down')->name('appels_offre.down');
 Route::get('/appels-offre/publication/up/{id}','StageController@publish')->name('appels_offre.publish');
 Route::get('/appels-offre/publication/down/{id}','StageController@unpublish')->name('appels_offre.unpublish');
 Route::get('/appels-offre/archivied/{id}','StageController@archive')->name('appels_offre.archived');
 Route::get('/appels-offre/restored/{id}','StageController@restore')->name('appels_offre.restored');

 //users actualité
 Route::get('/users/account-state/up/{id}','UserController@up')->name('users.up');
 Route::get('/users/account-state/down/{id}','UserController@down')->name('users.down');

//poster 

Route::get('/posters/publication/up/{id}','StageController@publish')->name('posters.publish');
Route::get('/posters/publication/down/{id}','StageController@unpublish')->name('posters.unpublish');
Route::get('/posters/archivied/{id}','StageController@archive')->name('posters.archived');
Route::get('/posters/restored/{id}','StageController@restore')->name('posters.restored');

// Types Structures
Route::resource('/type-structures','TypesStructuresController');
// Legendes
Route::resource('/legendes','LegendesController');


});