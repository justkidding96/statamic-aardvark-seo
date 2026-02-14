<?php

namespace Justkidding96\AardvarkSeo\Events\Redirects;

use Statamic\Events\Event;
use Statamic\Contracts\Git\ProvidesCommitMessage;

class RedirectCreated extends Event implements ProvidesCommitMessage
{
    public $redirect;

    public function __construct($redirect)
    {
        $this->redirect = $redirect;
    }

    /**
     * @return string
     */
    public function commitMessage()
    {
        return 'Aardvark redirect created';
    }
}
