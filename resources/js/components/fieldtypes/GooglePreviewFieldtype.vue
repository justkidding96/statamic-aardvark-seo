<template>
    <div class="max-w-[600px] rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-900">
        <div class="flex items-center gap-3">
            <div class="flex h-7 w-7 shrink-0 items-center justify-center overflow-hidden rounded">
                <img v-if="meta.favicon_url" :src="meta.favicon_url" class="h-7 w-7 object-contain" alt="" />
                <svg v-else class="h-5 w-5 text-gray-400" viewBox="0 0 16 16" fill="currentColor"><path d="M8 0a8 8 0 100 16A8 8 0 008 0zm0 2a6 6 0 110 12A6 6 0 018 2z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="truncate text-sm leading-tight text-gray-800 dark:text-gray-200">{{ meta.site_name }}</div>
                <div class="truncate text-xs leading-tight text-gray-600 dark:text-gray-400">{{ previewParts.breadcrumb }}</div>
            </div>
        </div>
        <div class="mt-1.5 text-xl leading-snug text-[#1a0dab] dark:text-blue-400">{{ previewParts.title }}</div>
        <p v-if="previewParts.description" class="mt-0.5 text-sm leading-normal text-gray-600 dark:text-gray-400">{{ previewParts.description }}</p>
    </div>
</template>

<script>
import Fieldtype from './mixins/Fieldtype.js';

export default {
    mixins: [Fieldtype],

    computed: {
        previewParts() {
            if (!this.publishContainer) return { title: '', breadcrumb: '', description: '' };

            const { meta_title, meta_description, slug, title } = this.publishContainer.values;
            const { site_name, site_url, title_separator, title_order } = this.meta;

            const url = new URL(site_url || 'https://example.com');
            const breadcrumb = `${url.origin} › ${slug || ''}`;

            const baseTitle = meta_title || title;
            const composedTitle = title_order === 'site_first'
                ? `${site_name} ${title_separator} ${baseTitle}`
                : `${baseTitle} ${title_separator} ${site_name}`;

            return {
                title: composedTitle,
                breadcrumb,
                description: meta_description,
            };
        },
    },
};
</script>
