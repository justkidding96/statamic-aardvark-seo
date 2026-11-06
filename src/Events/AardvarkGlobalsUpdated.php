<?php

namespace Justkidding96\AardvarkSeo\Events;

use Statamic\Events\Event;
use Statamic\Contracts\Git\ProvidesCommitMessage;

class AardvarkGlobalsUpdated extends Event implements ProvidesCommitMessage
{
    /**
     * @var string
     */
    public $handle;

    public function __construct(string $handle)
    {
        $this->handle = $handle;
    }

    /**
     * @return string
     */
    public function commitMessage()
    {
        return 'Aardvark globals saved';
    }
}
