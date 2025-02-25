<template>
    <div class="meta-field-validator__outer">
        <div class="meta-field-validator__field-container">
            <div class="input-group">
                <input @focus="toggleFocus(true)" @blur="toggleFocus(false)" :value="generateTitle(value)" @input="update($event.target.value)" @keyup="handleKeyUp" type="text" :name="name" :id="id" :placeholder="generatePlaceholder()" class="input-text" />
            </div>
            <progress max="70" :value="contentLength" :class="'meta-field-validator__progress meta-field-validator__progress--' + validation.step" />
        </div>
        <span class="meta-field-validator__caption" v-html="validation.caption"></span>
    </div>
</template>

<script>
    import MetaDataAnalyser from './mixins/MetaDataAnalyser.vue';

    export default {

        mixins: [Fieldtype, MetaDataAnalyser],

        inject: ['storeName'],

        data() {
            return {
                isFocused: false,
                hasSyncedJustChanged: false,
            };
        },

        computed: {
            /**
             * Computes the synchronization state based on whether the current handle
             * is included in the localizedFields array from the Vuex store state.
             * @returns {Boolean} The sync state.
             */
            isSynced() {
                const state = this.$store.state.publish[this.storeName];
                if (state && state.localizedFields) {
                    return !state.localizedFields.includes(this.config.handle);
                }
                return false;
            },
        },

        watch: {
            /**
             * Watches for changes in the `isSynced` computed property to perform
             * actions or log its changes.
             */
            isSynced(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.hasSyncedJustChanged = true;
                }
            },
        },

        methods: {
            toggleFocus(focusState) {
                this.isFocused = focusState;
            },
            generatePlaceholder() {
                const state = this.$store.state.publish[this.storeName];
                return this.meta.site_name
                    ? `${state.values.title || ''} ${this.meta.title_separator} ${this.meta.site_name}`
                    : state.values.title || '';
            },
            /**
             * Generates the title based on localisation fields.
             * If the current site is not the default locale and the 'meta_title' is not localised,
             * it returns an empty string; otherwise, it returns the provided value. It also handles the sync logic by updating
             * the meta title based on the fields current sync state.
             *
             * @param {string} value - The original title value to potentially return.
             * @return {string} - The localised title based on the current site's locale and field's sync state, or an empty string.
             */
            generateTitle(value) {
                // Access the publish state from the Vuex store
                const state = this.$store.state.publish[this.storeName];

                // Check if state is defined, and if the current site has localizedFields and is not the default locale.
                if(state && state.localizedFields && this.meta.default_locale !== state.site) {

                    // If the field is not synced and has just been changed and is not currently focused,
                    // reset the hasSyncedJustChanged flag and update the meta title to match the page title.
                    if (!this.isSynced && this.hasSyncedJustChanged && !this.isFocused) {
                        this.hasSyncedJustChanged = false;
                        // ToDo: Best practice: dispatch an action or commit a mutation instead
                        state.values.meta_title = state.values.title;
                        return state.values.title;
                    }

                    // If the field is synced and has just been changed and is not currently focused, clear the meta title.
                    if(this.isSynced && this.hasSyncedJustChanged && !this.isFocused) {
                        // ToDo: Best practice: dispatch an action or commit a mutation instead
                        state.values.meta_title = '';
                    }

                    // If the field is synced and has not just changed, ensure the meta title remains cleared.
                    if(this.isSynced && !this.hasSyncedJustChanged) {
                        // ToDo: Best practice: dispatch an action or commit a mutation instead
                        state.values.meta_title = '';
                    }

                    // Return the value only if 'meta_title' is a localised field
                    return state.localizedFields.includes('meta_title') ? value : '';
                }

                // Return the provided value as default
                return value;
            },

            validateMeta(length) {
                let validation;
                switch (true) {
                    case length === 0:
                        validation = {
                            step: "valid",
                            caption:
                            "You have not set a meta title, the value for the page title will be used."
                        };
                        break;
                    case length < 20:
                        validation = {
                            step: "warn",
                            caption: "Your meta title could be longer."
                        };
                        break;
                    case length >= 20 && length <= 70:
                        validation = { step: "valid", caption: "Your meta title is a good length." };
                    break;
                    case length > 70:
                        validation = {
                            step: "err",
                            caption:
                            "Your meta title is too long, <strong>the ideal length is between 20 and 70 characters.</strong>"
                        };
                    break;
                }
                return validation;
            }
        }
    }
</script>
