<?php

namespace Justkidding96\AardvarkSeo\Events\Redirects;

use Statamic\Events\Event;
use Statamic\Contracts\Git\ProvidesCommitMessage;

class RedirectDeleted extends Event implements ProvidesCommitMessage
{
    public function __construct()
    {
        // No op
    }

    /**
     * @return string
     */
    public function commitMessage()
    {
        return 'Aardvark redirect deleted';
    }
}
