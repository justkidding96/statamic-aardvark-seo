<?php

namespace Justkidding96\AardvarkSeo\Http\Middleware;

use Statamic\Facades\Site;
use Statamic\Support\Str;
use Statamic\Facades\Config;
use Statamic\Facades\URL;
use Justkidding96\AardvarkSeo\Redirects\Repositories\RedirectsRepository;

class RedirectsMiddleware
{
    public function handle($request, $next)
    {
        // If there is a 404 search our redirects and stuff
        $response = $next($request);

        if ($response->getStatusCode() === 404) {
            if ($redirect = $this->getRedirectResponse($request)) {
                return $redirect;
            }
        }

        return $response;
    }

    /**
     * Return a redirect response when the request matches an active redirect
     */
    private function getRedirectResponse($request)
    {
        // Get the current site root
        $site_root = Url::makeRelative(Url::makeAbsolute(Config::getSiteUrl()));

        $repository = $this->getRedirectsRepository();

        foreach ($this->getSourceUrls($request, $site_root) as $source_url) {
            if (! $repository->sourceExists($source_url)) {
                continue;
            }

            $redirect = $repository->getBySource($source_url);

            if (! $redirect['is_active']) {
                continue;
            }

            $target = $redirect['target_url'];

            // If the target is relative - prepend the site root
            if (Str::startsWith($target, '/')) {
                $target = Str::ensureLeft($target, $site_root);
            }

            return redirect($target, $redirect['status_code']);
        }

        return null;
    }

    /**
     * Build the list of source URLs a request could have been stored as
     */
    private function getSourceUrls($request, $site_root)
    {
        // Ensure we have a leading slash
        $request_path = Str::ensureLeft($request->path(), '/');

        // Remove the current site root from the request
        $path = Str::ensureLeft(Str::removeLeft($request_path, $site_root), '/');

        // Redirects may be stored with or without the site root and trailing slash
        return array_unique([
            $path,
            rtrim($path, '/') ?: '/',
            $request_path,
            rtrim($request_path, '/') ?: '/',
        ]);
    }

    private function getRedirectsRepository()
    {
        return new RedirectsRepository('redirects/manual', Site::current());
    }
}
