import MetaTitleFieldtype from './components/fieldtypes/MetaTitleFieldtype.vue';
import MetaDescriptionFieldtype from './components/fieldtypes/MetaDescriptionFieldtype.vue';
import GooglePreviewFieldtype from './components/fieldtypes/GooglePreviewFieldtype.vue';
import RedirectsListing from './components/cp/redirects/Listing.vue';
import DefaultsListing from './components/cp/defaults/Listing.vue';

Statamic.booting(() => {
    Statamic.$components.register('aardvark_seo_meta_title-fieldtype', MetaTitleFieldtype);
    Statamic.$components.register('aardvark_seo_meta_description-fieldtype', MetaDescriptionFieldtype);
    Statamic.$components.register('aardvark_seo_google_preview-fieldtype', GooglePreviewFieldtype);
    Statamic.$components.register('aardvark-redirects-listing', RedirectsListing);
    Statamic.$components.register('aardvark-defaults-listing', DefaultsListing);
});
