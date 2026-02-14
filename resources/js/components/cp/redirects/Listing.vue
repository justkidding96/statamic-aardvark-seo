<template>
    <ui-listing
        ref="listing"
        :url="requestUrl"
        :columns="initialColumns"
        :allow-bulk-actions="false"
        :allow-presets="false"
        :allow-customizing-columns="false"
    >
        <template #cell-source_url="{ value, row }">
            <a :href="row.edit_url">{{ value }}</a>
        </template>
        <template #cell-is_active="{ value }">
            {{ value ? 'Yes' : 'No' }}
        </template>
        <template #prepended-row-actions="{ row }">
            <ui-dropdown-item :text="__('Edit')" :href="row.edit_url" />
            <ui-dropdown-item :text="__('Delete')" variant="destructive" @click="confirmDelete(row)" />
        </template>
    </ui-listing>

    <ui-confirmation-modal
        v-model:open="isConfirming"
        :title="__('Delete Redirect')"
        :body-text="__('Are you sure you want to delete this redirect?')"
        :button-text="__('Delete')"
        :danger="true"
        :busy="isDeleting"
        @confirm="deleteRedirect"
    />
</template>

<script>
export default {
    props: [
        'createUrl',
        'bulkActionsUrl',
        'requestUrl',
        'site',
        'initial-columns',
        'initial-items',
    ],

    data() {
        return {
            isConfirming: false,
            isDeleting: false,
            deletingRedirect: null,
        };
    },

    methods: {
        confirmDelete(redirect) {
            this.deletingRedirect = redirect;
            this.isConfirming = true;
        },

        deleteRedirect() {
            this.isDeleting = true;

            this.$axios.delete(this.deletingRedirect.delete_url).then(() => {
                this.isDeleting = false;
                this.isConfirming = false;
                this.$refs.listing.refresh();
            });
        },
    },
}
</script>
