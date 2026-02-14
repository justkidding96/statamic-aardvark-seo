import { isRef } from 'vue';

/**
 * Fieldtype mixin for Statamic v6 (Vue 3).
 *
 * In v5, Statamic exposed `window.Fieldtype` as a global mixin.
 * In v6, this global was removed. This is a local replacement
 * that provides the same props, methods, and computed properties.
 */
export default {
    inject: {
        injectedPublishContainer: {
            from: 'PublishContainerContext',
            default: null,
        },
    },

    props: {
        id: String,
        value: { required: true },
        config: { type: Object, default: () => ({}) },
        handle: { type: String, required: true },
        meta: { type: Object, default: () => ({}) },
        readOnly: { type: Boolean, default: false },
        showFieldPreviews: { type: Boolean, default: false },
        namePrefix: String,
        fieldPathPrefix: String,
    },

    methods: {
        update(value) {
            this.$emit('update:value', value);
        },

        updateMeta(value) {
            this.$emit('update:meta', value);
        },
    },

    computed: {
        publishContainer() {
            if (!this.injectedPublishContainer) return null;

            return Object.fromEntries(
                Object.entries(this.injectedPublishContainer).map(([key, value]) => [
                    key,
                    isRef(value) ? value.value : value,
                ])
            );
        },

        name() {
            if (this.namePrefix) {
                return `${this.namePrefix}[${this.handle}]`;
            }

            return this.handle;
        },

        isReadOnly() {
            return this.readOnly
                || this.config.visibility === 'read_only'
                || this.config.visibility === 'computed'
                || false;
        },

        replicatorPreview() {
            if (!this.showFieldPreviews) return;

            return this.value;
        },
    },
};
