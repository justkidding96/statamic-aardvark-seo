<?php

namespace WithCandour\AardvarkSeo\Rules;

use Illuminate\Contracts\Validation\Rule;
use Statamic\Facades\Site;
use Illuminate\Support\Facades\Request;
use WithCandour\AardvarkSeo\Redirects\Repositories\RedirectsRepository;

class UniqueUrl implements Rule
{
    private $redirects = [];

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        $repository = new RedirectsRepository('redirects/manual', Site::selected());
        $this->redirects = $repository->all();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Resolve the id from the url
        $id = Request::route()->parameter('manual_redirect') ?? null;

        // If the id is null, we are creating a new redirect otherwise we are updating an existing redirect
        return !$this->redirects
            ->reject(fn ($redirect) => $redirect['id'] === $id)
            ->contains($attribute, $value);
    }

    /**
     * Get the validation error message.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function message()
    {
        return 'The :attribute must be an unique url.';
    }
}