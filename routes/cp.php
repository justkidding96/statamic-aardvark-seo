<?php

use Justkidding96\AardvarkSeo\Http\Controllers\CP\DefaultsController;
use Justkidding96\AardvarkSeo\Http\Controllers\CP\GeneralController;
use Justkidding96\AardvarkSeo\Http\Controllers\CP\MarketingController;
use Justkidding96\AardvarkSeo\Http\Controllers\CP\RedirectsController;
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

        // Redirects
        Route::name('redirects.')
            ->prefix('redirects')
            ->group(function () {
                // CSV import/export
                Route::get('export', [RedirectsController::class, 'export'])
                    ->name('export');
                Route::post('import', [RedirectsController::class, 'import'])
                    ->name('import');

                // Bulk actions
                Route::get('actions', [RedirectsController::class, 'bulkActions'])
                    ->name('actions');

                Route::post('actions', [RedirectsController::class, 'runActions'])
                    ->name('run');
            });

        Route::resource('redirects', RedirectsController::class)->only([
            'index', 'create', 'edit', 'update', 'store', 'destroy',
        ]);
    });
