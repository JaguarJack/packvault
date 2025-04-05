<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\LicenseUserController;

Route::middleware('auth')->group(function () {
    Route::controller(LicenseController::class)
        ->group(function (){
           Route::get('license', 'index')->name('license.index');
           Route::get('license/create', 'create')->name('license.create');
           Route::post('license', 'store')->name('license.store');
           Route::get('license/{id}/edit', 'edit')->name('license.edit');
           Route::put('license/{id}', 'update')->name('license.update');
           Route::delete('license/{id}', 'destroy')->name('license.delete');
           Route::put('license/{id}/activate', 'activate')->name('license.activate');
        });

    Route::controller(LicenseUserController::class)
        ->group(function (){
            Route::get('users', 'index')->name('license.user.index');
            Route::get('license-user/create', 'create')->name('license.user.create');
            Route::post('license-user', 'store')->name('license.user.store');
            Route::get('license-user/{id}/edit', 'edit')->name('license.user.edit');
            Route::put('license-user/{id}', 'update')->name('license.user.update');
            Route::delete('license-user/{id}', 'destroy')->name('license.user.delete');
            Route::put('license-user/{id}/activate', 'activate')->name('license.user.activate');
        });
});
