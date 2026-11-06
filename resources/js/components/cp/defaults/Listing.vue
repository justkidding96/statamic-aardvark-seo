<template>
    <ui-listing
        :url="requestUrl"
        :columns="initialColumns"
        :allow-bulk-actions="false"
        :allow-presets="false"
        :allow-customizing-columns="false"
    >
        <template #cell-title="{ value, row }">
            <a :href="row.edit_url">{{ value }}</a>
        </template>
        <template #prepended-row-actions="{ row }">
            <ui-dropdown-item :text="__('Edit')" :href="row.edit_url" />
        </template>
    </ui-listing>
</template>

<script>
export default {
    props: [
        'requestUrl',
        'initial-columns',
    ],
}
</script>
