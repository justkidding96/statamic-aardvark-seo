<?php

namespace Justkidding96\AardvarkSeo\Actions\Redirects;

use Statamic\Actions\Action;

class DeleteRedirectsAction extends Action
{
    protected $dangerous = true;

    public static function title()
    {
        return __('Delete');
    }

    public function authorize($user, $item)
    {
        return $user->can('edit aardvark redirects');
    }

    public function visibleToBulk($items)
    {
        return false;
    }

    public function confirmationText()
    {
        return 'Are you sure you want to want to delete this redirect?|Are you sure you want to delete these :count redirects?';
    }

    public function buttonText()
    {
        return 'Delete|Delete :count redirects?';
    }
}
