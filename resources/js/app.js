import MetaTitleFieldtype from './components/fieldtypes/MetaTitleFieldtype.vue';
import MetaDescriptionFieldtype from './components/fieldtypes/MetaDescriptionFieldtype.vue';
import GooglePreviewFieldtype from './components/fieldtypes/GooglePreviewFieldtype.vue';
import ManualRedirectsListing from './components/cp/redirects/manual/Listing.vue';
import RedirectsPublishForm from './components/cp/redirects/PublishForm.vue';

Statamic.booting(() => {
    // Fieldtypes
    Statamic.component('aardvark_seo_meta_title-fieldtype', MetaTitleFieldtype);
    Statamic.component('aardvark_seo_meta_description-fieldtype', MetaDescriptionFieldtype);
    Statamic.component('aardvark_seo_google_preview-fieldtype', GooglePreviewFieldtype);

    // Redirects components
    Statamic.component('aardvark-manual-redirects-listing', ManualRedirectsListing);
    Statamic.component('aardvark-redirects-publish-form', RedirectsPublishForm);
});
