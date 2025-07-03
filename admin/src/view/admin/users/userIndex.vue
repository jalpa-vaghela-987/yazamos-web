<template>
  <div class="main-block request-management">
    <Breadcrumbs />
    <div class="div col-12 p-0 d-flex align-items-start">
      <h4 class="">{{ $t('title.User') }}</h4>
    </div>

    <!-- Filters for Roles and Projects -->
    <div class="col-12 d-flex justify-content-end mt-3">
      <div class="col-md-3 p-0">
        <b-form-select v-model="filters.roles" :options="roleOptions" @change="handleFilter" class="w-100" />
      </div>

      <!-- Add Project filter -->
      <div class="col-md-3 p-0 ml-2">
        <b-form-select v-model="filters.project_id" :options="projectOptions" @change="handleFilter" class="w-100" />
      </div>
    </div>

    <div class="col-12 row mt-3 m-0 p-0" style="min-height: 90%">
      <template v-if="userActivePlan.length == 0 || userActivePlan.is_subscribe == 0">
        <div class="alert alert-danger" role="alert">
          {{$t('title.planErrorMessage')}}
        </div>
      </template>
      <crud-table :columns="columns" :list-url="listUrl" ref="user-table" v-on:refresh="resetFilter">
        <template>
          <b-button variant="outline-primary" :to="{ name: (activePlan && activePlan.id && activePlan.is_subscribe == 1) ? 'AdminChildUserCreate' : 'AdminPlan' }">
            <b-icon icon="plus"></b-icon> {{ $t('title.AddUser') }}
          </b-button>
        </template>

        <template #cell(phone_number)="{ item }">
          {{ item.country_code }}{{ item.phone_number }}
        </template>
        <template #cell(entrepreneur_projects)="{ item }">
          {{ item.entrepreneur_projects.length }}
        </template>
        <template #cell(assigned_projects)="{ item }">
          {{ item.assigned_projects.length }}
        </template>
        <template #cell(actions)="row">
          <b-button variant="primary" class="mr-2" size="sm"
            :to="{ name: 'AdminChildUserEdit', params: { id: row.item.id } }">
            <i class="fas fa-edit"></i>
          </b-button>
          <b-button variant="primary" size="sm" @click="openDeleteModal(row.item)">
            <i class="fas fa-trash"></i>
          </b-button>
        </template>
        <template #cell(signup_status)="{ item }">
          <span :class="item.email ? 'text-success' : 'text-danger'">
            {{ item.email ? 'Signed Up' : 'Not Signed Up' }}
          </span>
        </template>
      </crud-table>
    </div>
    <UserDeleteConfirmationModal ref="userDeleteConfirmationModal" @refreshTable="refreshTable" />
  </div>
</template>


<script>
import { request } from "@/Util/Request";
import { mapState, mapGetters } from "vuex";
import Breadcrumbs from "@/components/Breadcrumbs.vue";
import UserDeleteConfirmationModal from "../users/modal/deleteConfirmationModal";

const FILTER_FORM = {
  roles: null,
  project_id: null, // Added project filter
};

const COLUMN_STATE = (self) => [
  { key: 'role', label: self.$t('title.Role'), sortable: true },
  { key: 'name', label: self.$t('title.Name'), sortable: true },
  { key: 'email', label: self.$t('title.Email') },
  { key: 'phone_number', label: self.$t('title.Phone'), sortable: true },
  { key: 'entrepreneur_projects', label: self.$t('title.EntrepreneurTotalProjects'), sortable: true },
  { key: 'assigned_projects', label: self.$t('title.AssignedTotalProjects'), sortable: true },
  { key: 'signup_status', label: self.$t('title.SignupStatus'), sortable: false },
  { key: 'actions', label: self.$t('title.Actions') }
]
;

export default {
  data() {
    return {
      operation: "",
      query: {
        page: 1,
        perPage: 10
      },
      pagination: {
        total: 0
      },
      listUrl: "/users",
      filters: { ...FILTER_FORM },
      roleOptions: [
        { value: null, text: "Select Role" },
        { value: "entrepreneur", text: "Entrepreneur" },
        { value: "investor", text: "Investor" },
        { value: "tenant", text: "Tenant" }
      ],
      projectOptions: [], // Options for projects
      showPopup: false,
      modalInstance: null,
      projects: [],
      activePlan: []
    };
  },
  components: {
    Breadcrumbs,
    UserDeleteConfirmationModal
  },
  mounted() {
    this.getUserActivePlan();
  },
  created() {
    this.fetchProjects(); // Fetch projects on component mount
  },
  methods: {
    async getUserActivePlan() {
      try {
        const { data } = await request({ url: '/user-active-plan' });
        this.activePlan = data;
        this.$store.dispatch('setUserActivePlan', this.activePlan);
      } catch (err) {
        console.error('Failed to load active plan:', err);
      }
    },
    openDeleteModal(user) {
      this.$refs["userDeleteConfirmationModal"].openCloseModal(user);
    },
    handleFilter() {
      // Passing filters to the table component
      this.$refs["user-table"].handleFilter(this.filters);
    },
    refreshTable() {
      this.$refs["user-table"].handleRefreshList();
    },
    resetFilter() {
      this.filters = { ...FILTER_FORM };
    },
    async fetchProjects() {
      try {
        const response = await request("/projects"); // Fetch projects from the API
        this.projectOptions = [
          { value: null, text: "Select Project" },
          ...response.data.map(project => ({
            value: project.id,
            text: project.name
          }))
        ];
      } catch (error) {
        console.error("Error fetching projects", error);
      }
    }
  },
  computed: {
    columns() {
      return COLUMN_STATE(this);
    },
    ...mapGetters(['userActivePlan'])
  }
};
</script>
