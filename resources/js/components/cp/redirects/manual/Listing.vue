<template>
    <div>
        <div v-if="initializing" class="card loading"><loading-graphic /></div>
        
        <data-list
            v-if="!initializing"
            ref="dataList"
            :rows="items"
            :columns="columns"
            :sort="false"
            :sort-column="sortColumn"
            :sort-direction="sortDirection"
            @visible-columns-updated="visibleColumns = $event"
        >
            <div slot-scope="{ hasSelections }">
                <div class="card overflow-hidden p-0">
                    <div class="flex flex-wrap items-center justify-between px-2 pb-2 text-sm border-b dark:border-dark-900">
                        <data-list-search class="h-8 mt-2 min-w-[240px] w-full" ref="search" v-model="searchQuery" :placeholder="searchPlaceholder" />
                    </div>
                    
                    <data-list-bulk-actions
                        :url="bulkActionsUrl"
                        @started="actionStarted"
                        @completed="actionCompleted"
                    />
                    
                    <div class="overflow-x-auto overflow-y-hidden">
                        <data-list-table :rows="items" :allow-bulk-actions="true">
                            <template slot="cell-source_url" slot-scope="{ row: redirect, index }">
                                <a :href="redirect.edit_url">{{ redirect.source_url }}</a>
                            </template>
                            <template slot="cell-is_active" slot-scope="{ row: redirect }">
                                {{ redirect.is_active ? 'Yes' : 'No' }}
                            </template>
                            
                            <template slot="actions" slot-scope="{ row: redirect }">
                                <dropdown-list>
                                    <dropdown-item :text="__('Edit')" :redirect="redirect.edit_url" />
                                    <dropdown-item
                                        :text="__('Delete')"
                                        class="warning"
                                        @click="$refs[`deleter_${redirect.id}`].confirm()"
                                    >
                                        <resource-deleter
                                            :ref="`deleter_${redirect.id}`"
                                            :resource="redirect"
                                            @deleted="removeRow(redirect)"
                                        ></resource-deleter>
                                    </dropdown-item>
                                </dropdown-list>
                            </template>
                        </data-list-table>
                    </div>
                    
                    <div v-show="items.length === 0" class="p-6 text-center text-gray-500">{{ __('No results') }}</div>
                </div>
                
                <data-list-pagination
                    class="mt-6"
                    :resource-meta="meta"
                    :per-page="perPage"
                    :show-totals="true"
                    @page-selected="selectPage"
                    @per-page-changed="changePerPage"
                />
            </div>
        </data-list>
<!--        <div v-else class="md:pt-16 max-w-2xl mx-auto">-->
<!--            <div class="w-full md:w-1/2">-->
<!--                <h1 class="mb-8">Redirects</h1>-->
<!--                <p class="text-gray-700 leading-normal mb-8 text-lg antialiased">-->
<!--                    Redirects are used to direct users to content which may have been removed or deleted.-->
<!--                </p>-->
<!--                <a :href="this.createUrl" class="btn-primary btn-lg">Create a redirect</a>-->
<!--            </div>-->
<!--        </div>-->
    </div>
</template>

<script>
export default {
    
    mixins: [Listing],
    
    props: [
        'createUrl',
        'bulkActionsUrl',
        'requestUrl',
        'site',
        'initial-redirects',
        'initial-items',
    ],
    
    data() {
        return {
            columns: this.initialColumns,
            items: this.initialItems,
            listingKey: 'redirects',
            currentSite: this.site,
            initialSite: this.site,
            pushQuery: true,
            previousFilters: null
        }
    },
    
    methods: {
        actionCompleted() {
            location.reload();
        },
    },
    
    created() {
        // Set full width class for the wrapper
        this.$config.set('wrapperClass', 'max-w-full');
    }
}
</script>

