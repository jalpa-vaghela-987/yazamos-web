<template>
    <div>
    <Breadcrumbs />
    <div class="main-block request-management">
        <div class="div col-12 p-0 d-flex align-items-start">
            <h4 class="">{{ $t('title.Project') }}</h4>
        </div>
        <div class="col-12 row mt-3 m-0 p-0  " style="min-height: 90%">
            <crud-table :columns="columns" :list-url="computedListUrl" ref="application-table" v-on:refresh="resetFilter">
                <template #cell(actions)="row">
                    <b-button variant="outline-primary" size="sm" @click="goToProject(row.item.id)">
                        <i class="fas fa-eye"></i>
                    </b-button>
                </template>

            </crud-table>
        </div>
    </div>
    </div>

</template>

<script>

import {
    Modal
} from "bootstrap";
import Breadcrumbs  from "@/components/Breadcrumbs.vue";

const FILTER_FORM = {
    roles: null,
    statuses: null
};

const COLUMN_STATE = (self) => [
    {
        key: 'actions',
        label: self.$t('title.Actions')
    },
    {
        key: 'id',
        label: self.$t('title.ID')
    },
    {
        key: 'user.name',
        label: self.$t('title.CreatedBy')
    },
    {
        key: 'name',
        label: self.$t('title.ProjectName'),
        sortable: true
    },
    {
        key: 'location',
        label: self.$t('title.Location'),
        sortable: true
    },
    {
        key: 'current_property_value',
        label: self.$t('title.CurrentValue'),
        sortable: true
    },
    {
        key: 'purchase_price',
        label: self.$t('title.PurchasePrice'),
        sortable: true
    },
    {
        key: 'renovation_cost',
        label: self.$t('title.RenovationCost'),
        sortable: true
    },
    {
        key: 'wedge',
        label: self.$t('title.Wedge'),
        sortable: true
    }
]



export default {
    data() {
        return {
            operation: '',
            query: {
                page: 1,
                perPage: 10,
            },
            pagination: {
                total: 0,
            },
            listUrl: '/get-projects',
            filters: {
                ...FILTER_FORM
            },
            showPopup: false,
            modalInstance: null,
            projects: []
        };
    },
    components: {Breadcrumbs},
    methods: {
        goToProject(id) {
            this.$router.push({ name: 'AdminProjectShow', params: { id } });
        },
        openModal() {
            const modalElement = document.getElementById("deleteModal");
            this.modalInstance = new Modal(modalElement); // Initialize modal
            this.modalInstance.show();
        },
        closeModal() {
            if (this.modalInstance) {
                this.modalInstance.hide();
            }
        },

        handleFilter() {
            this.$refs['application-table'].handleFilter(this.filters);
        },
        resetFilter() {
            this.filters = {
                ...FILTER_FORM
            };
        },
        showToast(title, msg, status) {
            const h = this.$createElement;
            const vNodesMsg = h('p', {
                class: ['text-center', 'mb-0']
            }, [msg]);
            const vNodesTitle = h('div', {
                    class: ['d-flex', 'flex-grow-1', 'align-items-baseline', 'mr-2']
                },
                [h('strong', {
                    class: 'mr-2'
                }, title)]
            );

            this.$bvToast.toast([vNodesMsg], {
                title: [vNodesTitle],
                solid: true,
                variant: status === 1 ? 'primary' : 'danger',
            });
        }

    },
    computed: {
        columns() {
            return COLUMN_STATE(this);
        },
        computedListUrl() {
            let url = '/get-projects';
            if (this.$route.query.created_by) {
                url += `?created_by=${this.$route.query.created_by}`;
            }
            return url;
        }
    }
};
</script>
