<?php

use App\Http\Controllers\OAuthController;
use App\Http\Controllers\PackageJobController;
use App\Http\Controllers\RepositoryController;
use App\Http\Controllers\VcsPlatformController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    # 团队相关
    Route::controller(VcsPlatformController::class)->group(function (){
        Route::get('connect/vcs', 'connectVcs')->name('connect.vcs');
        Route::get('disconnect/vcs/{vcs}', 'disconnectVcs')->name('disconnect.vcs');
        Route::post('set/accessToken', 'storePersonalToken')->name('vcs.set.accessToken');
    });

    Route::controller(RepositoryController::class)->prefix('repository')->group(function () {
        Route::get('', 'index')->name('repository');
        Route::get('create', 'create')->name('repository.create');
        Route::post('store', 'store')->name('repository.store');
        Route::get('{id}', 'show')->name('repository.show');
        Route::put('{id}', 'update')->name('repository.update');
        Route::delete('destroy/{id}', 'destroy')->name('repository.delete');
        Route::delete('tag/{id}', 'destroyTag')->name('repository.delete.tag');
        Route::get('download/{id}', 'download')->name('repository.download');
    });

    Route::controller(PackageJobController::class)->prefix('job')->group(function () {
        Route::get('', 'index')->name('package.job');
        Route::delete('{id}', 'destroy')->name('package.job.delete');
    });
});



# oauth 授权
Route::controller(OAuthController::class)->prefix('oauth')->group(function () {
    Route::prefix('github')->group(function (){
        Route::get('redirect', 'githubRedirect')->name('github.redirect');
        Route::get('callback', 'githubCallback')->name('github.callback');
    });

    Route::prefix('gitee')->group(function (){
        Route::get('redirect', 'giteeRedirect')->name('gitee.redirect');
        Route::get('callback', 'giteeCallback')->name('gitee.callback');
    });
});
