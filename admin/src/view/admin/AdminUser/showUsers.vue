<template>
  <div class="main-block request-management">
    <Breadcrumbs />
    <div class="div col-12 p-0 d-flex align-items-start">
      <h4 class="">{{ $t("title.Users") }}</h4>
    </div>
    <div class="col-12 row mt-3 m-0 p-0" style="min-height: 90%">
      <crud-table
        :columns="columns"
        :list-url="computedListUrl"
        ref="user-table"
        v-on:refresh="resetFilter"
      >
        <template #cell(phone_number)="{ item }">
          {{ item.country_code }}{{ item.phone_number }}
        </template>
        <template #cell(actions)="{ item }">
          <b-button variant="outline-primary" size="sm" @click="goToUserDetails(item.id)">
            <i class="fas fa-eye"></i>
          </b-button>
        </template>
      </crud-table>
    </div>

    <UserDeleteConfirmationModal
      ref="userDeleteConfirmationModal"
      @refreshTable="refreshTable"
    />
  </div>
</template>

<script>
import { request } from "@/Util/Request";
import { mapState } from "vuex";
import { Modal } from "bootstrap";
import Breadcrumbs from "@/components/Breadcrumbs.vue";
import UserDeleteConfirmationModal from "../users/modal/deleteConfirmationModal";

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
    key: "role",
    label: self.$t("title.Role"),
    sortable: true,
  },
  {
    key: "name",
    label: self.$t("title.Name"),
    sortable: true,
  },
  {
    key: "email",
    label: self.$t("title.Email"),
  },
  {
    key: "phone_number",
    label: self.$t("title.Phone"),
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
      listUrl: "/get-users",
      filters: {
        ...FILTER_FORM,
      },
      showPopup: false,
      modalInstance: null,
      projects: [],
    };
  },
  components: {
    Breadcrumbs,
    UserDeleteConfirmationModal,
  },
  methods: {
    openDeleteModal(id) {
      this.$refs["userDeleteConfirmationModal"].openCloseModal(id);
    },
    goToUserDetails(id) {
      this.$router.push({ name: "UsersInfo", params: { id } });
    },
    handleFilter() {
      this.$refs["user-table"].handleFilter(this.filters);
    },
    refreshTable() {
      this.$refs["user-table"].handleRefreshList();
    },
    resetFilter() {
      this.filters = {
        ...FILTER_FORM,
      };
    },
  },
  computed: {
    columns() {
      return COLUMN_STATE(this);
    },
    computedListUrl() {
      let url = "/get-users";
      if (this.$route.query.created_by) {
        url += `?created_by=${this.$route.query.created_by}`;
      }
      return url;
    },
  },
};
</script>
