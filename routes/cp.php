<?php

use Justkidding96\AardvarkSeo\Http\Controllers\CP\DefaultsController;
use Justkidding96\AardvarkSeo\Http\Controllers\CP\GeneralController;
use Justkidding96\AardvarkSeo\Http\Controllers\CP\MarketingController;
use Justkidding96\AardvarkSeo\Http\Controllers\CP\Redirects\ManualRedirectsController;
use Justkidding96\AardvarkSeo\Http\Controllers\CP\SitemapController;
use Justkidding96\AardvarkSeo\Http\Controllers\CP\SocialController;

Route::prefix('aardvark-seo')
    ->name('aardvark-seo.')
    ->group(function () {
        Route::get('settings', [GeneralController::class, 'settingsRedirect'])
            ->name('settings');

        Route::prefix('settings')->group(function () {
            Route::resource('general', GeneralController::class)->only([
                'index', 'store',
            ]);

            Route::resource('sitemap', SitemapController::class)->only([
                'index', 'store',
            ]);

            Route::resource('marketing', MarketingController::class)->only([
                'index', 'store',
            ]);

            Route::resource('social', SocialController::class)->only([
                'index', 'store',
            ]);

            Route::resource('defaults', DefaultsController::class)->only([
                'index', 'edit', 'update',
            ]);
        });

        // Redirects have their own section
        Route::name('redirects.')
            ->prefix('redirects')
            ->group(function () {
                // Top level redirect
                Route::redirect('/', 'redirects/manual-redirects')->name('index');

                // Manual redirects
                Route::prefix('manual-redirects')
                    ->name('manual-redirects.')
                    ->group(function () {
                        Route::get('actions', [ManualRedirectsController::class, 'bulkActions'])
                            ->name('actions');

                        Route::post('actions', [ManualRedirectsController::class, 'runActions'])
                            ->name('run');
                    });

                Route::resource('manual-redirects', ManualRedirectsController::class)->only([
                    'index', 'create', 'edit', 'update', 'store', 'destroy',
                ]);
            });
    });
