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
            // Get the current site root
            $site_root = Url::makeRelative(Url::makeAbsolute(Config::getSiteUrl()));

            // Remove the current site root from the request
            $path = Str::removeLeft(Str::ensureLeft($request->path(), '/'), $site_root);

            // Ensure we have a leading slash
            $source_url = Str::ensureLeft($path, '/');

            // First check the manual redirects
            $manual_repository = $this->getManualRedirectsRepository();
            if ($manual_repository->sourceExists($source_url)) {
                $redirect = $manual_repository->getBySource($source_url);

                $target = $redirect['target_url'];

                // If the target is relative - prepend the site root
                if (Str::startsWith($target, '/')) {
                    $target = Str::ensureLeft($target, $site_root);
                }

                $status = $redirect['status_code'];
                $is_active = $redirect['is_active'];

                if ($is_active) {
                    return redirect($target, $status);
                }
            }
        }

        return $response;
    }

    private function getManualRedirectsRepository()
    {
        return new RedirectsRepository('redirects/manual', Site::current());
    }
}
