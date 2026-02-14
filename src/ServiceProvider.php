<?php

namespace Justkidding96\AardvarkSeo;

use Illuminate\Support\Facades\Route;
use Statamic\Facades\CP\Nav;
use Statamic\Facades\Git;
use Statamic\Facades\GraphQL;
use Statamic\Facades\Permission;
use Statamic\Events\EntryBlueprintFound;
use Statamic\Events\TermBlueprintFound;
use Statamic\GraphQL\Types\GridItemType;
use Statamic\Providers\AddonServiceProvider;
use Justkidding96\AardvarkSeo\Blueprints\CP\OnPageSeoBlueprint;
use Justkidding96\AardvarkSeo\Events\AardvarkContentDefaultsSaved;
use Justkidding96\AardvarkSeo\Events\AardvarkGlobalsUpdated;
use Justkidding96\AardvarkSeo\Events\Redirects\RedirectCreated;
use Justkidding96\AardvarkSeo\Events\Redirects\RedirectDeleted;
use Justkidding96\AardvarkSeo\Events\Redirects\RedirectSaved;
use Justkidding96\AardvarkSeo\Fieldtypes\AardvarkSeoMetaTitleFieldtype;
use Justkidding96\AardvarkSeo\Fieldtypes\AardvarkSeoMetaDescriptionFieldtype;
use Justkidding96\AardvarkSeo\Fieldtypes\AardvarkSeoGooglePreviewFieldtype;
use Justkidding96\AardvarkSeo\Listeners\AppendEntrySeoFieldsListener;
use Justkidding96\AardvarkSeo\Listeners\AppendTermSeoFieldsListener;
use Justkidding96\AardvarkSeo\Listeners\DefaultsSitemapCacheInvalidationListener;
use Justkidding96\AardvarkSeo\Listeners\Subscribers\SitemapCacheInvalidationSubscriber;
use Justkidding96\AardvarkSeo\Http\Middleware\RedirectsMiddleware;
use Justkidding96\AardvarkSeo\Modifiers\ParseLocaleModifier;
use Justkidding96\AardvarkSeo\Tags\AardvarkSeoTags;

class ServiceProvider extends AddonServiceProvider
{
    protected $fieldtypes = [
        AardvarkSeoMetaTitleFieldtype::class,
        AardvarkSeoMetaDescriptionFieldtype::class,
        AardvarkSeoGooglePreviewFieldtype::class,
    ];

    protected $listen = [
        EntryBlueprintFound::class => [
            AppendEntrySeoFieldsListener::class,
        ],
        TermBlueprintFound::class => [
            AppendTermSeoFieldsListener::class,
        ],
        AardvarkContentDefaultsSaved::class => [
            DefaultsSitemapCacheInvalidationListener::class,
        ],
    ];

    protected $middlewareGroups = [];

    protected $modifiers = [
        ParseLocaleModifier::class,
    ];

    protected $routes = [
        'cp' => __DIR__ . '/../routes/cp.php',
        'web' => __DIR__ . '/../routes/web.php',
    ];

    protected $subscribe = [
        SitemapCacheInvalidationSubscriber::class,
    ];

    protected $tags = [
        AardvarkSeoTags::class,
    ];

    protected $vite = [
        'input' => [
            'resources/js/app.js',
            'resources/css/app.css',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    public function boot()
    {
        if (! config('aardvark-seo.disable_redirects')) {
            $this->middlewareGroups = [
                'statamic.web' => [
                    RedirectsMiddleware::class,
                ],
            ];
        }

        parent::boot();

        // Set up views path
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'aardvark-seo');

        // Set up translations
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'aardvark-seo');

        // Load in custom addon config
        $this->mergeConfigFrom(__DIR__ . '/../config/aardvark-seo.php', 'aardvark-seo');
        $this->publishes([
            __DIR__ . '/../config/aardvark-seo.php' => config_path('aardvark-seo.php'),
        ], 'config');

        // Set up permissions
        $this->bootPermissions();

        // Set up navigation
        $this->bootNav();

        // Set up git integration
        $this->bootGitListener();

        // Add compatibility with GraphQL
        $this->bootGraphqlCompatibility();
    }

    /**
     * Add our custom navigation items to the CP nav
     *
     * @return void
     */
    public function bootNav()
    {
        $routeCollection = Route::getRoutes();

        // Add Aardvark SEO item to nav
        Nav::extend(function ($nav) {
            $nav->create('Aardvark SEO')
                ->can('configure aardvark settings')
                ->section('Tools')
                ->route('aardvark-seo.settings')
                ->icon('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M1.012 11.942a8.5 8.5 0 0 1 15.022-7.685M15.01 15.041l2.333 2.332M23 20.909a1.5 1.5 0 1 1-2.121 2.121l-3.889-3.889a1 1 0 0 1 0-1.414l.707-.707a1 1 0 0 1 1.414 0z"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m.5 15.53 8.793-8.793a1 1 0 0 1 1.414 0l2.586 2.586a1 1 0 0 0 1.414 0L23.5.53"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M20.5.53h3v3m-6.015 6.016a8.5 8.5 0 0 1-13.923 6.017"/></svg>')
                ->children(array_filter([
                    $nav->item(__('aardvark-seo::general.index'))
                        ->route('aardvark-seo.general.index')
                        ->can('view aardvark general settings'),
                    $nav->item(__('aardvark-seo::defaults.index'))
                        ->route('aardvark-seo.defaults.index')
                        ->can('view aardvark defaults settings'),
                    $nav->item(__('aardvark-seo::marketing.singular'))
                        ->route('aardvark-seo.marketing.index')
                        ->can('view aardvark marketing settings'),
                    $nav->item(__('aardvark-seo::social.singular'))
                        ->route('aardvark-seo.social.index')
                        ->can('view aardvark social settings'),
                    $nav->item(__('aardvark-seo::sitemap.singular'))
                        ->route('aardvark-seo.sitemap.index')
                        ->can('view aardvark sitemap settings'),
                    ! config('aardvark-seo.disable_redirects') ? $nav->item(__('aardvark-seo::redirects.plural'))
                        ->route('aardvark-seo.redirects.index')
                        ->can('view aardvark redirects') : null,
                ]));
        });
    }

    /**
     * Add permissions for AardvarkSEO settings
     *
     * @return void
     */
    public function bootPermissions()
    {
        $settings_groups = [
            [
                'value' => 'general',
                'label' => 'General',
            ],
            [
                'value' => 'marketing',
                'label' => 'Marketing',
            ],
            [
                'value' => 'social',
                'label' => 'Social',
            ],
            [
                'value' => 'sitemap',
                'label' => 'Sitemap',
            ],
            [
                'value' => 'defaults',
                'label' => 'Defaults',
            ],
        ];

        Permission::group('aardvark-seo', 'Aardvark SEO', function () use ($settings_groups) {
            Permission::register('configure aardvark settings', function ($permission) use ($settings_groups) {
                $children = collect($settings_groups)->map(function ($group) {
                    return Permission::make("view aardvark {$group['value']} settings")
                        ->label("View {$group['label']} Settings")
                        ->children([
                            Permission::make("update aardvark {$group['value']} settings")
                                ->label("Update {$group['label']} Settings"),
                        ]);
                })->all();

                $children[] = Permission::make('view aardvark redirects')
                    ->label(__('aardvark-seo::redirects.permissions.view'))
                    ->children([
                        Permission::make('edit aardvark redirects')
                            ->label(__('aardvark-seo::redirects.permissions.edit')),
                        Permission::make('create aardvark redirects')
                            ->label(__('aardvark-seo::redirects.permissions.create')),
                    ]);

                $permission->children($children);
            })->label('Configure Aardvark Settings');
        });
    }

    /**
     * Register our custom events with the Statamic git integration
     *
     * @return void
     */
    protected function bootGitListener(): void
    {
        if (config('statamic.git.enabled')) {
            $events = [
                AardvarkContentDefaultsSaved::class,
                AardvarkGlobalsUpdated::class,
                RedirectCreated::class,
                RedirectDeleted::class,
                RedirectSaved::class,
            ];

            foreach ($events as $event) {
                Git::listen($event);
            }
        }
    }

    /**
     * Register a custom graphql type for our localized_urls field
     *
     * @return void
     */
    protected function bootGraphqlCompatibility()
    {
        if (config('statamic.graphql.enabled')) {
            $blueprint = OnPageSeoBlueprint::requestBlueprint();
            GraphQL::addType(new GridItemType($blueprint->field('localized_urls')->fieldtype(), 'GridItem_LocalizedUrls'));
        }
    }
}
