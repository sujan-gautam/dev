<?php

//use App\Http\Controllers\ProfileController;
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

/*
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

*/




//Admin routes
Route::group([
    'prefix'     => 'admin',
    'middleware' => ['eco.web','throttle:60,1'],
    'namespace'  => 'App\Http\Controllers\Admin',
], function () {

    Route::get('login', 'AdminController@showLogin')->name('admin.login');

});

Route::group([
    'prefix'     => 'admin',
    'middleware' => ['eco.admin','throttle:60,1'],
    'namespace'  => 'App\Http\Controllers\Admin',
], function () {

    Route::get('clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        return redirect()->back()->withSuccess('Cache cleaned successfully');
    });

    Route::get('dashboard', 'AdminController@index')->name('dashboard');

//Pages
    Route::get('pages', 'PageController@index')->name('pages');
    Route::get('pages/create', 'PageController@create')->name('pages');
    Route::post('pages/create', 'PageController@store');
    Route::get('pages/{id}/edit', 'PageController@edit')->name('pages');
    Route::post('pages/{id}/edit', 'PageController@update');
    Route::get('pages/{id}/delete', 'PageController@destroy');
    Route::get('pages/get-pages', 'PageController@get')->name('pages.get');

//Users
    Route::get('users', 'UserController@index')->name('users');
    Route::get('users/get', 'UserController@get')->name('users.get');
    Route::get('users/{id}/delete', 'UserController@destroy')->name('users.delete');
    Route::get('users/{id}/delete-pastes', 'UserController@destroyPastes')->name('users.delete_pastes');
    Route::get('users/{id}/edit', 'UserController@edit')->name('users.edit');
    Route::post('users/{id}/edit', 'UserController@update');
    Route::get('users/create', 'UserController@create')->name('users.create');
    Route::post('users/create', 'UserController@store');

//Syntax languages
    Route::get('syntax-languages', 'SyntaxController@index')->name('syntax_languages');
    Route::get('syntax-languages/get', 'SyntaxController@get')->name('syntax_languages.get');
    Route::get('syntax-languages/{id}/delete', 'SyntaxController@destroy')->name('syntax_languages.delete');
    Route::get('syntax-languages/{id}/edit', 'SyntaxController@edit');
    Route::post('syntax-languages/{id}/edit', 'SyntaxController@update');
    Route::get('syntax-languages/create', 'SyntaxController@create');
    Route::post('syntax-languages/create', 'SyntaxController@store');

//Site languages
    Route::get('site-languages', 'LanguageController@index')->name('site_languages');
    Route::get('site-languages/get', 'LanguageController@get')->name('site_languages.get');
    Route::get('site-languages/{id}/delete', 'LanguageController@destroy')->name('site_languages.delete');
    Route::get('site-languages/{id}/edit', 'LanguageController@edit');
    Route::post('site-languages/{id}/edit', 'LanguageController@update');
    Route::get('site-languages/create', 'LanguageController@create');
    Route::post('site-languages/create', 'LanguageController@store');
    Route::get('site-languages/{id}/translations', 'LanguageController@translationsEdit');
    Route::post('site-languages/{id}/translations', 'LanguageController@translationsUpdate');

//Pastes
    Route::get('pastes', 'PasteController@index')->name('pastes');
    Route::get('pastes/get', 'PasteController@get')->name('pastes.get');
    Route::get('pastes/{id}/delete', 'PasteController@destroy')->name('pastes.delete');
    Route::get('pastes/{id}/edit', 'PasteController@edit')->name('pastes.edit');
    Route::post('pastes/{id}/edit', 'PasteController@update');
    Route::get('pastes/create', 'PasteController@create')->name('pastes.create');
    Route::post('pastes/create', 'PasteController@store');
    Route::post('pastes/delete-selected', 'PasteController@deleteSelected');

//Reported Pastes
    Route::get('reported-pastes', 'ReportController@index')->name('reports');
    Route::get('reported-pastes/get', 'ReportController@get')->name('reports.get');
    Route::get('reported-pastes/{id}/delete', 'ReportController@destroy')->name('reports.delete');
    Route::post('reported-pastes/delete-selected', 'ReportController@deleteSelected');

//Settings
    Route::get('settings', 'SettingController@edit')->name('settings');
    Route::post('settings', 'SettingController@update');

//Folders
    Route::get('folders', 'FolderController@index')->name('folders');
    Route::get('folders/get', 'FolderController@get')->name('folders.get');
    Route::get('folders/{id}/delete', 'FolderController@destroy')->name('folders.delete');
    Route::get('folders/{id}/edit', 'FolderController@edit')->name('folders.edit');
    Route::post('folders/{id}/edit', 'FolderController@update');
    Route::get('folders/create', 'FolderController@create')->name('folders.create');
    Route::post('folders/create', 'FolderController@store');

});

//Auth routes
Route::group([
    'middleware' => ['eco.auth','throttle:60,1'],
    'namespace'  => 'App\Http\Controllers',
], function () {

    Route::get('paste/{slug}/edit', 'PasteController@edit')->name('paste.edit');
    Route::post('paste/{slug}/edit', 'PasteController@update');
    Route::get('paste/{slug}/delete', 'PasteController@destroy')->name('paste.delete');

    Route::post('report-issue', 'PasteController@report')->name('paste.report');

    Route::get('dashboard', 'UserController@index')->name('user.dashboard');

    Route::get('my-pastes', 'UserController@pastes')->name('user.pastes');

    Route::get('profile', 'UserController@edit')->name('profile.edit');
    Route::post('profile', 'UserController@update');

    Route::get('profile/delete', 'UserController@destroyShow')->name('profile.delete');
    Route::post('profile/delete', 'UserController@destroy');

    Route::get('backup','UserController@backupShow')->name('user.backup');
    Route::post('backup','UserController@backup');

    Route::get('paste-settings','UserController@pasteSettingsShow')->name('user.paste.settings');
    Route::post('paste-settings','UserController@pasteSettings');

    Route::get('folders/create', 'FolderController@create')->name('folder.create');
    Route::post('folders/create', 'FolderController@store');    
    Route::get('folders/{id}/delete', 'FolderController@destroy')->name('folder.delete');
    Route::get('folders/{id}/edit', 'FolderController@edit')->name('folder.edit');
    Route::post('folders/{id}/edit', 'FolderController@update');

    Route::get('my-folders', 'UserController@folders')->name('user.folders');

});

//Non Auth Routes
Route::group([
    'middleware' => ['eco.web','throttle:60,1'],
    'namespace'  => 'App\Http\Controllers',    
], function () {

    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('redirect', 'PageController@redirect')->name('redirect');

    require __DIR__.'/auth.php';

    Route::get('auth/{provider}', 'Auth\RegisteredUserController@redirectToSocialProvider')->name('social.login');
    Route::get('auth/{provider}/callback', 'Auth\RegisteredUserController@handleSocialProviderCallback')->name('social.callback');

    //Route::post('login', 'Auth\LoginController@authenticate')->name('user.login');

    Route::get('/', 'HomeController@index')->name('home');
    Route::get('search', 'PasteController@search')->name('search');
    Route::post('paste/create', 'PasteController@store')->name('paste.store');

    Route::get('archive', 'PasteController@archiveList')->name('archive.list');
    Route::get('archive/{slug}', 'PasteController@archive')->name('archive');

    Route::get('contact', 'PageController@contact')->name('contact');
    Route::post('contact', 'PageController@contactPost');

    Route::get('pages/{slug}', 'PageController@show')->name('page.show');

    Route::get('raw/{slug}', 'PasteController@raw')->name('raw');
    Route::get('download/{slug}', 'PasteController@download')->name('download');
    Route::get('clone/{slug}', 'PasteController@clone')->name('clone');
    Route::get('embed/{slug}', 'PasteController@embed')->name('embed');

    Route::get('sitemap-main.xml','PageController@sitemapMain')->name('sitemap.main');
    Route::get('sitemap.xml','PageController@sitemaps')->name('sitemap');
    Route::get('sitemap-{date}.xml','PageController@sitemap')->name('sitemap.show');

    Route::post('get-paste', 'PasteController@getPaste')->name('paste.get');

    Route::get('trending', 'PasteController@trending')->name('trending');

    Route::get('u/{name}', 'UserController@profile')->name('user.profile');
    Route::post('u/{name}/contact', 'UserController@contact')->name('user.contact');

    Route::get('folders/{slug}', 'FolderController@show')->name('folder.show');

    Route::get('{slug}', 'PasteController@show')->name('paste.show');

});
