<?php

namespace WithCandour\AardvarkSeo;

use Illuminate\Support\Facades\Route;
use Statamic\Facades\CP\Nav;
use Statamic\Facades\Git;
use Statamic\Facades\GraphQL;
use Statamic\Facades\Permission;
use Statamic\Events\EntryBlueprintFound;
use Statamic\Events\TermBlueprintFound;
use Statamic\GraphQL\Types\GridItemType;
use Statamic\Providers\AddonServiceProvider;
use WithCandour\AardvarkSeo\Blueprints\CP\OnPageSeoBlueprint;
use WithCandour\AardvarkSeo\Events\AardvarkContentDefaultsSaved;
use WithCandour\AardvarkSeo\Events\AardvarkGlobalsUpdated;
use WithCandour\AardvarkSeo\Events\Redirects\ManualRedirectCreated;
use WithCandour\AardvarkSeo\Events\Redirects\ManualRedirectDeleted;
use WithCandour\AardvarkSeo\Events\Redirects\ManualRedirectSaved;
use WithCandour\AardvarkSeo\Fieldtypes\AardvarkSeoMetaTitleFieldtype;
use WithCandour\AardvarkSeo\Fieldtypes\AardvarkSeoMetaDescriptionFieldtype;
use WithCandour\AardvarkSeo\Fieldtypes\AardvarkSeoGooglePreviewFieldtype;
use WithCandour\AardvarkSeo\Listeners\AppendEntrySeoFieldsListener;
use WithCandour\AardvarkSeo\Listeners\AppendTermSeoFieldsListener;
use WithCandour\AardvarkSeo\Listeners\DefaultsSitemapCacheInvalidationListener;
use WithCandour\AardvarkSeo\Listeners\Subscribers\SitemapCacheInvalidationSubscriber;
use WithCandour\AardvarkSeo\Http\Middleware\RedirectsMiddleware;
use WithCandour\AardvarkSeo\Modifiers\ParseLocaleModifier;
use WithCandour\AardvarkSeo\Tags\AardvarkSeoTags;

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

    protected $middlewareGroups = [
        'statamic.web' => [
            RedirectsMiddleware::class,
        ],
    ];

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

        // Add SEO item to nav
        Nav::extend(function ($nav) {
            // Top level SEO item
            $nav->create('SEO')
                ->can('configure aardvark settings')
                ->section('Tools')
                ->route('aardvark-seo.settings')
                ->icon('seo-search-graph')
                ->children([
                    // Settings categories
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
                ]);

            $nav->create(__('aardvark-seo::redirects.plural'))
                ->can('view aardvark redirects')
                ->section('Tools')
                ->route('aardvark-seo.redirects.index')
                ->icon('<svg width="20" height="20" xmlns="http://www.w3.org/2000/svg"><path d="M16 8.093V4a1 1 0 00-1-1H2a1 1 0 00-1 1v10a1 1 0 001 1h7.129m6.458-9.337h-14.5m2.595-2.198v2m2.55-2.079v2M12.01 19.16v-3.35a3.878 3.878 0 013.878-3.879h1.566m-1.439-1.688l2.043 1.637m-.01.063l-2.043 1.637" stroke="currentColor" stroke-width=".75" fill="none" fill-rule="evenodd" stroke-linecap="round"/></svg>')
                ->children([
                    $nav->item(__('aardvark-seo::redirects.manual.plural'))
                        ->can('view aardvark redirects')
                        ->route('aardvark-seo.redirects.manual-redirects.index'),
                ]);
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
                $permission->children([
                    Permission::make('view aardvark {settings_group} settings')
                        ->replacements('settings_group', function () use ($settings_groups) {
                            return collect($settings_groups)->map(function ($group) {
                                return [
                                    'value' => $group['value'],
                                    'label' => $group['label'],
                                ];
                            });
                        })
                        ->label('View :settings_group Settings')
                        ->children([
                            Permission::make('update aardvark {settings_group} settings')
                                ->label('Update :settings_group Settings'),
                        ]),
                    Permission::make('view aardvark redirects')
                        ->label(__('aardvark-seo::redirects.permissions.view'))
                        ->children([
                            Permission::make('edit aardvark redirects')
                                ->label(__('aardvark-seo::redirects.permissions.edit')),
                            Permission::make('create aardvark redirects')
                                ->label(__('aardvark-seo::redirects.permissions.create')),
                        ]),
                ]);
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
                ManualRedirectCreated::class,
                ManualRedirectDeleted::class,
                ManualRedirectSaved::class,
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
