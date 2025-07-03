<template>
    <div class="main-block request-management">
        <Breadcrumbs />
        <div class="div col-12 p-0 d-flex align-items-start justify-content-between">
            <h4 class="">{{ $t('title.Project') }}</h4>
<!--            <b-button-->
<!--                variant="primary"-->
<!--                :to="{ name: 'AdminProjectCreate' }"-->
<!--                class="d-flex align-items-center"-->
<!--                :class="{'d-none': !(isEntrepreneur || isAdmin)}"-->
<!--            >-->
<!--                <i class="fas fa-plus me-2"></i>-->
<!--                {{ $t("title.add_project") }}-->
<!--            </b-button>-->
        </div>
        <div class="col-12 row mt-3 m-0 p-0  " style="min-height: 90%">
            <crud-table :columns="columns" :list-url="listUrl" ref="application-table" v-on:refresh="resetFilter">
                <template #cell(actions)="row">
                    <b-button variant="outline-primary" size="sm" @click="goToProject(row.item.id)">
                        <i class="fas fa-eye"></i>
                    </b-button>
                    <button class="ml-2"  :class="{'btn':true, 'btn-outline-primary': true,'rounded':true,'d-none':!(isEntrepreneur|| isAdmin),'btn-sm':true}" size="sm" @click="editProject(row.item.id)">
                        <i class="fas fa-edit"></i>
                    </button>
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
import Breadcrumbs  from "@/components/Breadcrumbs.vue";

const FILTER_FORM = {
    roles: null,
    statuses: null
};

const COLUMN_STATE = (self) => [
  { key: 'actions', label: self.$t('title.Actions') },
  { key: 'id', label: self.$t('title.ID') },
  { key: 'user.name', label: self.$t('title.CreatedBy') },
  { key: 'name', label: self.$t('title.ProjectName'), sortable: true },
  { key: 'location', label: self.$t('title.Location'), sortable: true },
  { key: 'current_property_value', label: self.$t('title.CurrentValue'), sortable: true },
  { key: 'purchase_price', label: self.$t('title.PurchasePrice'), sortable: true },
  { key: 'renovation_cost', label: self.$t('title.RenovationCost'), sortable: true },
  { key: 'wedge', label: self.$t('title.Wedge'), sortable: true }
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
            listUrl: '/projects',
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
            this.$router.push({ name: this.$global.hasRole('entrepreneur')
                    ? 'ProjectShow'
                    : 'AdminProjectShow', params: { id } });
        },
        editProject(id) {
            this.$router.push({ name: this.$global.hasRole('entrepreneur')
                    ? 'ProjectEdit'
                    : 'AdminProjectEdit', params: { id } });
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
        isEntrepreneur() {
            return this.$global.hasRole('entrepreneur');
        },
        isAdmin() {
            return this.$global.hasRole('admin');
        }
    }
};
</script>
