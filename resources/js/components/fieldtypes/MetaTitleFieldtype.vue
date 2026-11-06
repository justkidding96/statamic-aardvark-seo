<template>
    <div>
        <ui-input
            :model-value="editableValue"
            @update:model-value="update"
            :placeholder="entryTitle"
            :id="id"
            :read-only="isReadOnly"
            :prepend="showSiteName && isSiteFirst ? sitePrefix.trim() : null"
            :append="showSiteName && !isSiteFirst ? siteSuffix.trim() : null"
        />
        <div class="mt-2 flex items-center gap-2">
            <div class="h-1 flex-1 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                <div
                    class="h-full rounded-full transition-all duration-200"
                    :class="progressBarClass"
                    :style="{ width: progressWidth }"
                />
            </div>
            <span class="shrink-0 text-xs tabular-nums" :class="remainingClass">{{ remaining }}</span>
        </div>
        <p class="mt-1 text-xs text-gray-600 dark:text-gray-400" v-html="validation.caption" />
    </div>
</template>

<script>
import Fieldtype from './mixins/Fieldtype.js';

export default {
    mixins: [Fieldtype],

    computed: {
        isSiteFirst() {
            return this.meta.title_order === 'site_first';
        },

        separator() {
            return this.meta.title_separator || '|';
        },

        sitePrefix() {
            return `${this.meta.site_name} ${this.separator} `;
        },

        siteSuffix() {
            return ` ${this.separator} ${this.meta.site_name}`;
        },

        entryTitle() {
            return this.publishContainer?.values?.title || '';
        },

        editableValue() {
            return (typeof this.value === 'string' && this.value) ? this.value : '';
        },

        // Mirrors PageDataParser::generatePageTitle: a custom meta title is used as-is
        // unless the "append site name" setting is on; the entry title fallback always gets it.
        showSiteName() {
            return !this.editableValue || this.meta.append_site_name;
        },

        fullTitle() {
            const title = this.editableValue || this.entryTitle;
            if (!title) return '';
            if (!this.showSiteName) return title;
            return this.isSiteFirst
                ? this.sitePrefix + title
                : title + this.siteSuffix;
        },

        fullTitleLength() {
            return this.fullTitle.length;
        },

        validation() {
            const length = this.fullTitleLength;

            if (length === 0) return { step: 'valid', caption: 'No meta title set — the page title will be used.' };
            if (length < 20) return { step: 'warn', caption: 'Your meta title could be longer.' };
            if (length <= this.meta.title_max_length) return { step: 'valid', caption: 'Your meta title is a good length.' };
            return { step: 'err', caption: `Your meta title is too long. <strong>Ideal length is 20–${this.meta.title_max_length} characters.</strong>` };
        },

        progressWidth() {
            if (!this.meta.title_max_length || this.fullTitleLength === 0) return '0%';
            return Math.min((this.fullTitleLength / this.meta.title_max_length) * 100, 100) + '%';
        },

        progressBarClass() {
            return { valid: 'bg-green-500', warn: 'bg-orange-400', err: 'bg-red-500' }[this.validation.step];
        },

        remaining() {
            return (this.meta.title_max_length || 0) - this.fullTitleLength;
        },

        remainingClass() {
            if (this.remaining < 0) return 'text-red-500';
            if (this.remaining < 10) return 'text-orange-400';
            return 'text-gray-500 dark:text-gray-400';
        },
    },
};
</script>
