<?php

namespace Justkidding96\AardvarkSeo\Listeners\Contracts;

interface SeoFieldsListener
{
    /**
     * Check the content type we're working with to ensure
     * SEO fields are intended to appear in the CMS
     */
    public function check_content_type(string $content_type);
}
