<template>
  <div class="main-block request-management">
    <Breadcrumbs />
    <div class="div col-12 p-0 d-flex align-items-start">
      <h4>{{ $t("title.Project") }}</h4>
    </div>
    <div class="col-12 row mt-3 m-0 p-0" style="min-height: 90%">
      <crud-table
        :columns="columns"
        :list-url="listUrl"
        ref="application-table"
        v-on:refresh="resetFilter"
      >
          <template #cell(actions)="row">
              <div v-if="row.item.invitation_status === 'pending'">
                  <b-button
                      variant="success"
                      size="sm"
                      class="me-1"
                      @click="confirmAction('accept', row.item.id)"
                  >
                      {{ $t("title.Accept") }}
                  </b-button>
                  <b-button
                      variant="danger"
                      size="sm"
                      @click="confirmAction('reject', row.item.id)"
                  >
                      {{ $t("title.Reject") }}
                  </b-button>
              </div>
              <div v-else>
                  <b-button
                      variant="outline-primary"
                      size="sm"
                      @click="goToProject(row.item.id)"
                  >
                      <i class="fas fa-eye"></i>
                  </b-button>

                  <button
                      class="ml-2"
                      :class="{
        btn: true,
        'btn-outline-primary': true,
        rounded: true,
        'btn-sm': true,
      }"
                      v-if="row.item.role === 'entrepreneur'"
                      @click="editProject(row.item.id)"
                  >
                      <i class="fas fa-edit"></i>
                  </button>
              </div>
          </template>
      </crud-table>
    </div>

    <!-- Confirmation Modal -->
    <div
      class="modal fade"
      id="confirmationModal"
      tabindex="-1"
      aria-labelledby="confirmationModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmationModalLabel">
              {{ $t("title.ConfirmAction") }}
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            {{
              $t(
                actionType === "accept"
                  ? "title.AreYouSureAcceptProject"
                  : "title.AreYouSureRejectProject"
              )
            }}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              {{ $t("title.Cancel") }}
            </button>
            <button type="button" class="btn btn-primary" @click="executeAction">
              {{ $t("title.Confirm") }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { request } from "@/Util/Request";
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
        label: self.$t("title.ProjectName"),
        sortable: true,
    },
    {
        key: "location",
        label: self.$t("title.Location"),
        sortable: true,
    },
    {
        key: "current_property_value",
        label: self.$t("title.CurrentValue"),
        sortable: true,
    },
    {
        key: "purchase_price",
        label: self.$t("title.PurchasePrice"),
        sortable: true,
    },
    {
        key: "renovation_cost",
        label: self.$t("title.RenovationCost"),
        sortable: true,
    },
];

export default {
  data() {
    return {
      operation: "",
      projectId: null,
      query: {
        page: 1,
        perPage: 10,
      },
      pagination: {
        total: 0,
      },
      listUrl: "/project-invitations/pending",
      filters: {
        ...FILTER_FORM,
      },
      showPopup: false,
      modalInstance: null,
      projects: [],
      actionType: null, // To track the action type (accept/reject)
    };
  },
  components: { Breadcrumbs },
  methods: {
    // Method to trigger confirmation modal
    confirmAction(action, projectId) {
      this.actionType = action;
      this.projectId = projectId;
      const modalElement = document.getElementById("confirmationModal");
      this.modalInstance = new Modal(modalElement);
      this.modalInstance.show();
    },

      editProject(id) {
          this.$router.push({ name: 'ProjectEdit', params: { id } });
      },

    // Method to execute the action after confirmation
    async executeAction() {
      try {
        let response;

        if (this.actionType === "accept") {
          response = await request({
            method: "post",
            url: `/project-invitations/${this.projectId}/accept`,
          });
        } else if (this.actionType === "reject") {
          response = await request({
            method: "post",
            url: `/project-invitations/${this.projectId}/reject`,
          });
        }
        this.$refs["application-table"].refreshTableData();
        if (response.status === "success") {
          this.showToast(
            "Success",
            `${
              this.actionType.charAt(0).toUpperCase() + this.actionType.slice(1)
            } successful!`,
            1
          );

          // Refresh the table directly
        }

        this.closeModal();
      } catch (error) {
        this.notifyError(`Failed to ${this.actionType} project invitation.`);
        this.closeModal();
      }
    },

    goToProject(id) {
      this.$router.push({ name: 'ProjectShow', params: { id } });
    },

    // Close modal
    closeModal() {
      if (this.modalInstance) {
        this.modalInstance.hide();
      }
    },

    // Show toast notifications
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
      });
    },

    // Error notification
    notifyError(msg) {
      this.showToast("Error", msg, 0);
    },

    // Reset filters when refreshing the table
    resetFilter() {
      this.filters = {
        ...FILTER_FORM,
      };
      this.$refs["application-table"].refreshTableData(); // Reload data after resetting the filters
    },

    // Fetch updated data from the API
    async fetchData() {
      try {
        const response = await request({
          method: "get",
          url: this.listUrl,
          params: this.query,
        });

        if (response.status === 200) {
          this.projects = response.data.projects; // Update project list
          this.pagination.total = response.data.total; // Update pagination
        }
      } catch (error) {
        this.notifyError("Failed to load project data.");
      }
    },
  },
  computed: {
    columns() {
      return COLUMN_STATE(this);
    },
  },
  mounted() {
    this.fetchData(); // Initial data load when component is mounted
  },
};
</script>
