# Aardvark SEO Documentation

## Installation

#### Install via composer:
```
composer require justkidding96/aardvark-seo
```
Then publish the publishables from the service provider:
```
php artisan vendor:publish --provider="Justkidding96\AardvarkSeo\ServiceProvider"
```

#### Install via CP
Or alternatively search for us in the `Tools > Addons` section of the Statamic control panel.

### Config
After installing, a config file will be created at `config/aardvark-seo.php`. This will give you control over a number of config options:

| Setting | Type       | Description                                                 |
| --------- | ---------- | ----------------------------------------------------------- |
| `asset_container`      | String  | The asset container to store images in            |
| `asset_folder`         | String  | The folder inside the container to use            |
| `custom_socials`       | Array   | An array of custom socials to add to our selector |
| `excluded_collections` | Array   | An array of collections to exclude from adding the SEO tab |
| `excluded_taxonomies`  | Array   | An array of taxonomies to exclude from adding the SEO tab |
| `title_max_length`     | Integer | Maximum character length for meta titles (default: `60`) |
| `description_max_length` | Integer | Maximum character length for meta descriptions (default: `160`) |
| `disable_favicons`     | Boolean | Disable favicon output in the head tag (default: `false`) |
| `disable_redirects`    | Boolean | Disable the redirects module entirely (default: `false`) |
| `disable_default_schema` | Boolean | Disable the default schema graph output (default: `false`) |

### Tags

Getting your site's SEO data onto the page relies on a few tags being present in your theme templates:

- `{{ aardvark-seo:head }}` - Contains meta tags and other information, this tag should be inside of the `<head>` element (in place of any title or meta tags)
- `{{ aardvark-seo:body }}` - Contains scripts that need to be inside of the `<body>` element, it should be placed after the opening `<body>` tag.
- `{{ aardvark-seo:footer }}` - Contains any scripts that need to be included at the end of the page, it should be placed towards the end of page along with any other scripts you have in the footer.

### Git integration
Aardvark SEO integrates with the [Statamic Git functionality](https://statamic.dev/git-automation) meaning that any changes you make to the site settings, content defaults or redirects will get committed to your git repo automatically. The following steps are required to enable the git integration for Aardvark SEO:
1. Add the Aardvark storage directory to the `paths` array in `config/statamic/git.php`.
```
    base_path('content'),
    base_path('users'),
    ...
    storage_path('statamic/addons/aardvark-seo'),
```
2. Ensure that the Aardvark storage directory is not ignored in `storage/statamic/.gitignore`. This can be done by replacing the contents of that file with the following:
```
/*
!.gitignore
!/addons/aardvark-seo
```

## Upgrading

### Upgrading to 6.0.0

Version 6.0.0 requires **Statamic v6** (`statamic/cms: ^6.0`).

#### Breaking Changes

- Redirect events have been renamed: `ManualRedirectCreated`, `ManualRedirectSaved`, and `ManualRedirectDeleted` are now `RedirectCreated`, `RedirectSaved`, and `RedirectDeleted`.

#### Upgrade Steps

1. Update your `composer.json` to require `^6.0` of the addon
2. Run `composer update justkidding96/aardvark-seo`
3. Publish assets: `php artisan vendor:publish --force --tag=aardvark-seo`
4. If you were listening to `ManualRedirectCreated`, `ManualRedirectSaved`, or `ManualRedirectDeleted` events, update your references to `RedirectCreated`, `RedirectSaved`, and `RedirectDeleted`
5. Review the new config options in `config/aardvark-seo.php` and publish them if needed
6. Clear your caches:
```bash
php artisan optimize:clear
```

### Upgrading to 5.1.0

Version 5.1.0 introduces breaking changes due to a namespace change.

#### Breaking Changes

The package namespace has changed from `WithCandour\AardvarkSeo` to `Justkidding96\AardvarkSeo`.

#### Upgrade Steps

1. Update your `composer.json` to require the new package:
```bash
composer remove withcandour/aardvark-seo
composer require justkidding96/aardvark-seo
```

2. Update any references to the old namespace in your code:
```
# Old
WithCandour\AardvarkSeo\...

# New
Justkidding96\AardvarkSeo\...
```

3. If you have published the service provider, update the reference:
```php
# Old
WithCandour\AardvarkSeo\ServiceProvider

# New
Justkidding96\AardvarkSeo\ServiceProvider
```

4. Clear your caches:
```bash
php artisan optimize:clear
```

## Permissions

Aardvark SEO now has a set of permissions which can be applied to user roles, head to the permissions section of the control panel to take a look, non-super users will now need permission to view and update the global settings. There are additional permissions for creating and updating redirects.

## Sitemaps

XML Sitemaps will get automatically generated for your site, the default url for the sitemap is `<your-site-address>/sitemap.xml`, however you are welcome to turn this off by heading to SEO > Sitemap and toggling "Enable Sitemap?" off.

The priority and change frequency can be configured on a per-page basis under the 'SEO' section.

Individual collections / taxonomies can be excluded from the sitemap with the settings under SEO > Sitemap.

## Redirects

You can manage the list of redirects for your site from within the control panel, the Redirects item in the Tools section of the control panel is the place to go for this.

Redirects support both 301 (permanent) and 302 (temporary) status codes, and trailing slashes are handled automatically.

### CSV Import/Export

You can export all redirects as a CSV file and import redirects from a CSV file. The CSV file should have the following columns: `source_url`, `target_url`, and optionally `status_code`.

### Disabling Redirects

The redirects module can be disabled entirely by setting `disable_redirects` to `true` in the config file. This will remove the middleware and the navigation item from the control panel.

> Redirects are relative to the site URL, so subfolder multisite installations will not need the site root prepended to the redirect URLs e.g. `/redirect` rather than `/en/redirect` for a site installed at `example.com/en`

## Marketing tools

Google tag manager can be enabled and managed through the Aardvark addon, additionally there is functionality to add verification codes for the major webmaster tools under the SEO > Marketing page.

## On-page SEO

A new 'SEO' section will be added to the editor screen for any Pages, Collection entries or Taxonomy terms from which the SEO and share data will be managed.

Special fields for the meta title and description include progress bars and character counters to help you optimize your metadata for search engines. A Google search preview, designed to match the look of actual search results, will help to visualise how your page will appear in search.

### Title Order

You can configure whether the page title appears before or after the site name (e.g. "Page Title | Site Name" vs "Site Name | Page Title") under SEO > General.

### Disable
You can prevent the SEO tab from appearing by adding the handle of the collection/term to the `excluded_collections` or `excluded_taxonomies` array in the Aardvark config file.

## Indexing

Site indexing can be controlled either at the site-level (crawlers will not index any page) or on a per-page basis. On every page there is a toggle, when enabled the page will no longer get indexed. In addition there is a separate option for controlling whether on-page links should get followed by crawlers.

## Schema

A schema graph will be generated for each page which will pull data from the Aardvark global settings including things like the Organization and social media profiles linked to the website, additionally WebSite and WebPage schema will be generated automatically.

The default schema output can be disabled by setting `disable_default_schema` to `true` in the config file. Custom schema can still be added per-page using the schema code editor field.

### Breadcrumbs

Another schema feature which will also be generated is the breadcrumb trail, Google provides [documentation](https://developers.google.com/search/docs/data-types/breadcrumb) about the specifics and how it is used. Aardvark automatically adds breadcrumbs for all pages on your site.

## Social media

Aardvark provides you with full control over how your site looks when shared on social media through generating data and filling opengraph and twitter meta tags. In addition you can set links to each of your social media profiles in the SEO > Social menu.

We have a list of default social types but you may define your own in the addon settings, use the grid to add items to the 'Social Icon' dropdown.

The social media data can be accessed on the frontend through the `{{ aardvark-seo:socials }}` tag, use it to loop through the provided social media links.

### Socials tag example

```html
<ul>
    {{ aardvark-seo:socials }}
        <li><a href="{{ url }}">{{ social_icon }}</a></li>
    {{ /aardvark-seo:socials }}
</ul>
```

## Multisite and Localization

Aardvark will provide full SEO functionality for Statamic instances running multisite as well as providing useful information for multisite instances running over multiple locales.

### Hreflang
Aardvark SEO will automatically generate a list of `<link rel="alternate" hreflang="x">` tags for Statamic instances running multiple sites where content is shared across locales. Additionally you can manually configure alternate urls using the 'Alternate URLs' table in the on-page SEO settings.

## Content defaults
You can set default SEO options on a per-content option. For example, SEO options can be set at the collection level, allowing for section-specific values for fields like the OpenGraph share image etc. To control the defaults head to SEO > Content Defaults in the menu and click through to each collection / taxonomy individually.
