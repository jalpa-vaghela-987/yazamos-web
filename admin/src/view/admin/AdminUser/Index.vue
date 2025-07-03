<template>
  <div class="main-block request-management">
    <Breadcrumbs />
    <div class="div col-12 p-0 d-flex align-items-start">
      <h4>{{ $t("title.CompanyAdmins") }}</h4>
    </div>
    <div class="col-12 mb-3 d-flex justify-content-end">
      <b-button variant="primary" @click="goToAddusers">
        <i class="fas fa-plus mr-1"></i> {{ $t("title.AddCompanyAdmin") }}
      </b-button>
    </div>
    <div class="col-12 row mt-3 m-0 p-0" style="min-height: 90%">
      <crud-table
        :columns="columns"
        :list-url="listUrl"
        ref="application-table"
        v-on:refresh="resetFilter"
      >
        <template #cell(actions)="row">
          <b-button
            size="sm"
            variant="outline-danger"
            @click="
              $refs['application-table'].handleDeleteShow(
                `/admin/admin-user/${row.item.id}`,
                row.item.name
              )
            "
          >
            <i class="fas fa-trash"></i>
          </b-button>

          <b-button
            size="sm"
            variant="outline-primary"
            class="ml-2"
            :to="{ name: 'AuthAdminUserIndex', query: { created_by: row.item.id } }"
          >
            <i class="fas fa-users"></i>
          </b-button>

          <b-button
            size="sm"
            variant="outline-primary"
            class="ml-2"
            :to="{ name: 'AuthAdminProjectIndex', query: { created_by: row.item.id } }"
          >
            <i class="fas fa-file"></i>
          </b-button>
        </template>
      </crud-table>
    </div>
  </div>
</template>

<script>
import { request } from "@/Util/Request";
import { mapState } from "vuex";
import { Modal } from "bootstrap";
import Breadcrumbs from "@/components/Breadcrumbs.vue";

const FILTER_FORM = {
  roles: null,
  statuses: null,
};

const COLUMN_STATE = (self) => [
  {
    key: "actions",
    label: self.$t("title.Actions"),
  },
  {
    key: "id",
    label: self.$t("title.ID"),
  },
  {
    key: "name",
    label: self.$t("title.Name"),
  },
  {
    key: "email",
    label: self.$t("title.Email"),
    sortable: true,
  },
  {
    key: "company_name",
    label: self.$t("title.CompanyName"),
    sortable: true,
  },
];

export default {
  data() {
    return {
      operation: "",
      query: {
        page: 1,
        perPage: 10,
      },
      pagination: {
        total: 0,
      },
      listUrl: "/admin/admin-user",
      filters: {
        ...FILTER_FORM,
      },
      showPopup: false,
      modalInstance: null,
      projects: [],
    };
  },
  components: { Breadcrumbs },
  methods: {
    goToProject(id) {
      this.$router.push({ name: "AdminProjectShow", params: { id } });
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
      this.$refs["application-table"].handleFilter(this.filters);
    },
    resetFilter() {
      this.filters = {
        ...FILTER_FORM,
      };
    },
    showToast(title, msg, status) {
      const h = this.$createElement;
      const vNodesMsg = h(
        "p",
        {
          class: ["text-center", "mb-0"],
        },
        [msg]
      );

      const vNodesTitle = h(
        "div",
        {
          class: ["d-flex", "flex-grow-1", "align-items-baseline", "mr-2"],
        },
        [
          h(
            "strong",
            {
              class: "mr-2",
            },
            title
          ),
        ]
      );

      this.$bvToast.toast([vNodesMsg], {
        title: [vNodesTitle],
        solid: true,
        variant: status === 1 ? "primary" : "danger",
        autoHideDelay: 5000, // ⏱ Show for 5 seconds
      });
    },
    goToAddusers() {
      this.$router.push({ name: "AdminUserCreate" });
    },
  },
  computed: {
    columns() {
      return COLUMN_STATE(this);
    },
  },
};
</script>
