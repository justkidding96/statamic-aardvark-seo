<template>
    <div class="meta-field-validator__outer">
        <div class="meta-field-validator__field-container">
            <textarea :value="value" @input="update($event.target.value)" @keyup="handleKeyUp" :name="name" :id="id" :placeholder="generatePlaceholder()" class="input-text" style="overflow-x:hidden;overflow-wrap:break-word;resize:none"></textarea>
            <progress max="300" :value="contentLength" :class="'meta-field-validator__progress meta-field-validator__progress--' + validation.step" />
        </div>
        <span class="meta-field-validator__caption" v-html="validation.caption"></span>
    </div>
</template>

<script>
    import MetaDataAnalyser from './mixins/MetaDataAnalyser.vue';

    export default {
        mixins: [Fieldtype, MetaDataAnalyser],

        methods: {
            generatePlaceholder() {
                return this.config.placeholder || "No meta description has been set for this page, search engines will use a relevent body of text from the page content instead.";
            },
            validateMeta(length) {
                let validation;
                switch (true) {
                    case length === 0:
                    validation = {
                        step: "valid",
                        caption: "You have not set a meta description for this page."
                    };
                    break;
                    case length < 50:
                    validation = {
                        step: "warn",
                        caption: "Your meta description could be longer."
                    };
                    break;
                    case length >= 20 && length <= 300:
                    validation = { step: "valid", caption: "Your meta description is a good length." };
                    break;
                    case length > 300:
                    validation = {
                        step: "err",
                        caption:
                        "Your meta description is too long, <strong>the ideal length is between 50 and 300 characters.</strong>"
                    };
                    break;
                }
                return validation;
            }
        }
    }
</script>
