<template>
    <div class="google-styles google-preview__preview z-depth-1">
        <div>
            <span class="google-styles__title">{{ this.previewParts.title }}</span>
            <span class="google-styles__link">{{ this.previewParts.url }}</span>
            <p class="google-styles__description">{{ this.previewParts.description }}</p>
        </div>
    </div>
</template>

<script>
    export default {
        mixins: [Fieldtype],

        inject: ['storeName'],

        computed: {
            previewParts() {

                const state = this.$store.state.publish[this.storeName];

                const {
                    meta_title,
                    meta_description,
                    slug,
                    title,
                } = state.values;

                const {
                    site_name,
                    site_url,
                    title_separator,
                    default_locale,
                } = this.meta;

                // Initialise pageTitle with meta_title if available, otherwise combine title with site name and separator.
                let pageTitle = meta_title || `${title}${site_name ? ` ${title_separator} ${site_name}` : ''}`;

                // Override pageTitle for non-default locales without a localised meta_title, using title and optionally site name and separator.
                if (state && state.localizedFields && default_locale !== state.site && !state.localizedFields.includes('meta_title')) {
                    pageTitle = `${title}${site_name ? ` ${title_separator} ${site_name}` : ''}`;
                }

                return {
                    title: pageTitle,
                    url: `${site_url}/${slug}`,
                    description: meta_description
                }
            }
        }
    }
</script>
