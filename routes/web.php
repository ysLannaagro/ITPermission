<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupMailController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\GroupFolderController;

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

Route::get('/', [UserController::class, 'index'])->name('home.index');
Route::group(['middleware' => ['guest']], function() {
    /**
     * Register Routes
     */
    Route::get('/register', [UserController::class, 'show_register'])->name('register.show');
    Route::post('/register', [UserController::class, 'register'])->name('register.perform');

    /**
     * Login Routes
     */
    Route::get('/login', [UserController::class, 'show_login'])->name('login.show');
    Route::post('/login', [UserController::class, 'login'])->name('login.perform');

});

Route::group(['middleware' => ['auth']], function() {
    /**
     * Logout Routes
     */
    Route::get('/logout', [UserController::class, 'perform'])->name('logout.perform');
});

Route::get('/group_mail/index_1', [GroupMailController::class, 'index_1'])->name('group_mail.index_1');
Route::get('/group_mail/importExportView', [GroupMailController::class, 'importExportView'])->name('group_mail_import');
Route::post('/group_mail/import', [GroupMailController::class, 'import'])->name('gm_import');
Route::get('/group_mail/destroy/{id}', [GroupMailController::class, 'destroy'])->name('gm_del');
Route::get('/group_mail/to_use/{id}', [GroupMailController::class, 'to_use'])->name('group_mail.to_use');
Route::get('/group_mail/to_manage/{id}', [GroupMailController::class, 'to_manage'])->name('group_mail.to_manage');
Route::post('/group_mail/manage/{id}', [GroupMailController::class, 'manage'])->name('group_mail.manage');
Route::get('/group_mail/manage_del/{id}', [GroupMailController::class, 'manage_del'])->name('group_mail.manage_del');
Route::get('/group_mail/to_mail/{id}', [GroupMailController::class, 'to_mail'])->name('group_mail.to_mail');
Route::post('/group_mail/mail/{id}', [GroupMailController::class, 'mail'])->name('group_mail.mail');
Route::get('/group_mail/mail_del/{id}', [GroupMailController::class, 'mail_del'])->name('group_mail.mail_del');
Route::get('/group_mail/chk_public', [GroupMailController::class, 'chk_public'])->name('group_mail.chk_public');
Route::get('/group_mail/to_folder/{id}', [GroupMailController::class, 'to_folder'])->name('group_mail.to_folder');
Route::post('/group_mail/folder/{id}', [GroupMailController::class, 'folder'])->name('group_mail.folder');
Route::get('/group_mail/folder_del/{id}', [GroupMailController::class, 'folder_del'])->name('group_mail.folder_del');
Route::get('/group_mail/chk_folder', [GroupMailController::class, 'chk_folder'])->name('group_mail.chk_folder');
Route::resource('group_mail', GroupMailController::class);

Route::get('/mail/importExportView', [MailController::class, 'importExportView'])->name('mail_import_view');
Route::post('/mail/import', [MailController::class, 'import'])->name('mail_import');
Route::get('/mail/destroy/{id}', [MailController::class, 'destroy'])->name('mail_del');
Route::get('/mail/to_group/{id}', [MailController::class, 'to_group'])->name('mail.to_group');
Route::post('/mail/group/{id}', [MailController::class, 'group'])->name('mail.group');
Route::get('/mail/group_del/{id}', [MailController::class, 'group_del'])->name('mail.group_del');
Route::resource('mail', MailController::class);

Route::get('/report/to_report', [ReportController::class, 'report'])->name('report.to_report');
Route::resource('report', ReportController::class);

Route::get('/folder/importExportView', [FolderController::class, 'importExportView'])->name('folder_import');
Route::post('/folder/import', [FolderController::class, 'import'])->name('folder_to_import');
Route::get('/folder/to_group/{id}', [FolderController::class, 'to_group'])->name('folder.to_group');
Route::get('/folder/folder_del/{id}', [FolderController::class, 'destroy'])->name('folder_del');
Route::get('/folder/to_group/{id}', [FolderController::class, 'to_group'])->name('folder.to_group');
Route::post('/folder/group/{id}', [FolderController::class, 'group'])->name('folder.group');
Route::get('/folder/group_del/{id}', [FolderController::class, 'group_del'])->name('folder.group_del');
Route::resource('folder', FolderController::class);
