<template>
    <div class="main-block request-management">
        <Breadcrumbs />
        <div class="div col-12 p-0 d-flex align-items-start">
            <h4>{{ $t('title.InvitationLogs') }}</h4>
        </div>
        <div class="col-12 row mt-3 m-0 p-0  " style="min-height: 90%">
            <crud-table :columns="columns" :list-url="listUrl" ref="application-table" v-on:refresh="resetFilter">
                <template #cell(message)="row">
                    <span v-b-tooltip.hover :title="row.item.message">
                        {{ row.item.message.slice(0, 50) }}{{ row.item.message.length > 50 ? '...' : '' }}
                    </span>
                </template>
                <template #cell(actions)="row">
                    <b-button variant="outline-secondary" size="sm" @click="$bvModal.msgBoxOk(row.item.message)">
                        <i class="fas fa-envelope"></i>
                    </b-button>
                </template>

            </crud-table>
        </div>
    </div>

</template>

<script>
import {
    request
} from "@/Util/Request";
import {
    mapState
} from "vuex";
import {
    Modal
} from "bootstrap";
import Breadcrumbs from "@/components/Breadcrumbs.vue";

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
        label: self.$t('title.CreatedBy'),
        sortable: true
    },
    {
        key: 'phone_number',
        label: self.$t('title.PhoneNumber'),
        sortable: true
    },
    {
        key: 'message',
        label: self.$t('title.Message')
    },
    {
        key: 'status',
        label: self.$t('title.Status'),
        sortable: true
    },
    {
        key: 'sent_at',
        label: self.$t('title.SentAt'),
        sortable: true
    },
    {
        key: 'created_at',
        label: self.$t('title.CreatedAt'),
        sortable: true
    }
];




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
            listUrl: '/invitation-logs',
            preprocessData: (data) => {
                return data.map(item => ({
                    ...item,
                    'user.name': item.user?.name || 'N/A'
                }));
            },
            filters: {
                ...FILTER_FORM
            },
            showPopup: false,
            modalInstance: null,
            projects: []
        };
    },
    components: { Breadcrumbs },
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
        }
    }
};
</script>
