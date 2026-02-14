<template>
    <div>
        <ui-textarea
            :model-value="value"
            @update:model-value="update"
            :placeholder="placeholder"
            :id="id"
            :read-only="isReadOnly"
            :rows="3"
            resize="none"
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
        contentLength() {
            return typeof this.value === 'string' ? this.value.length : 0;
        },

        placeholder() {
            return this.config.placeholder || 'No meta description has been set for this page, search engines will use relevant body text instead.';
        },

        validation() {
            const length = this.contentLength;

            if (length === 0) return { step: 'valid', caption: 'No meta description set for this page.' };
            if (length < 50) return { step: 'warn', caption: 'Your meta description could be longer.' };
            if (length <= this.meta.description_max_length) return { step: 'valid', caption: 'Your meta description is a good length.' };
            return { step: 'err', caption: `Your meta description is too long. <strong>Ideal length is 50–${this.meta.description_max_length} characters.</strong>` };
        },

        progressWidth() {
            if (!this.meta.description_max_length || this.contentLength === 0) return '0%';
            return Math.min((this.contentLength / this.meta.description_max_length) * 100, 100) + '%';
        },

        progressBarClass() {
            return { valid: 'bg-green-500', warn: 'bg-orange-400', err: 'bg-red-500' }[this.validation.step];
        },

        remaining() {
            return (this.meta.description_max_length || 0) - this.contentLength;
        },

        remainingClass() {
            if (this.remaining < 0) return 'text-red-500';
            if (this.remaining < 10) return 'text-orange-400';
            return 'text-gray-500 dark:text-gray-400';
        },
    },
};
</script>
