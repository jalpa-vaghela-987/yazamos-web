<!--do not modify this file until not required-->
<template>
  <b-container fluid class="p-2 overflow-hidden">
    <b-row class="row p-0 d-flex flex-wrap align-items-end mb-3">
      <b-col lg="4" v-if="shouldShowSearchBox">
        <b-form-group label-for="filter-input" class="mb-0">
          <b-input-group>
            <b-form-input
              id="filter-input"
              :disabled="loader"
              v-model="query.search"
              type="search"
              placeholder="Search..."
            ></b-form-input>
            <b-input-group-append>
              <b-button
                variant="primary"
                :disabled="loader || !query.search"
                @click="handleSearch"
              >
                Search
              </b-button>
            </b-input-group-append>
          </b-input-group>
        </b-form-group>
      </b-col>

      <!-- slot for filter button-->
      <slot name="filter-slot"></slot>
    </b-row>

    <div class="row">
      <div class="col-12 text-center py-2" v-if="loader">
        <i class="fa fa-spin fa-spinner"></i>
      </div>
      <div class="col-8">
        <div class="d-flex gap-2 justify-content-start mb-3">
          <slot></slot>
          <!-- slot for add new button-->
          <button
            :disabled="loader"
            class="btn btn-primary"
            title="Refresh"
            @click="refreshTableData"
          >
            <b-icon icon="arrow-clockwise"></b-icon>
          </button>
        </div>
      </div>
      <div class="col-4">
        <div class="d-flex gap-2 justify-content-end mb-3"></div>
      </div>
    </div>

    <b-table
      :items="dataSources"
      :fields="columns"
      v-if="columns.length"
      label-sort-asc="↑"
      label-sort-desc="↓"
      label-sort-clear=""
      empty-text="No Result Found..."
      :sort-by.sync="query.orderBy"
      :sort-desc.sync="sortBy"
      stacked="md"
      show-empty
      small
      responsive
      table-class="default-table"
    >
      <template #row-details="row">
        <b-card>
          <b-table
            striped
            hover
            small
            :items="row.item.sub_items"
            :fields="subItemFields"
          ></b-table>
        </b-card>
      </template>
      <template #cell(expand)="row">
        <b-button
          v-if="row.item.sub_items.length > 0"
          size="sm"
          @click="row.toggleDetails"
          variant="outline-primary"
          class="mr-2"
        >
          {{ row.detailsShowing ? "Hide" : "Show" }}
        </b-button>
      </template>
      <slot v-for="slot in Object.keys($slots)" :name="slot" :slot="slot" />
      <template v-for="slot in Object.keys($scopedSlots)" :slot="slot" slot-scope="scope">
        <slot :name="slot" v-bind="scope"></slot>
      </template>
    </b-table>

    <b-row class="p-0 align-items-end">
      <b-col class="my-1 col col-lg-3 ms-md-auto">
        <b-form-group
          label="Per Page"
          label-for="per-page-select"
          label-cols-sm="6"
          label-align-sm="right"
          label-size="sm"
          class="mb-0"
        >
          <b-form-select
            id="per-page-select"
            v-model="query.perPage"
            :options="pageSelection"
            :disable="loader"
          ></b-form-select>
        </b-form-group>
      </b-col>
      <b-col class="my-1 col-auto table-pagination">
        <b-pagination
          v-if="pagination"
          v-model="query.page"
          :total-rows="pagination.total"
          :per-page="query.perPage"
          prev-class="previous-arrow"
          next-class="next-arrow"
          first-number
          last-number
          align="fill"
          size="md"
          class="my-0"
        ></b-pagination>
      </b-col>
    </b-row>
    <b-modal
      id="deleteModal"
      v-model="deleteModal"
      :title="$t('title.ConfirmDelete')"
      @ok="handleDelete"
    >
      <p>{{ $t("title.AreYouSureYouWantToDelete", { item: tableName }) }}</p>
    </b-modal>
  </b-container>
</template>

<style>
.table.b-table > thead > tr > [aria-sort],
.table.b-table > tfoot > tr > [aria-sort] {
  background-size: 1em 1.5em;
}
</style>
<script>
import ListingMixin from "@/Util/ListingMixin";
import "bootstrap-vue/dist/bootstrap-vue.css";
import { request } from "@/Util/Request";
import { mapGetters, mapState } from "vuex";

export default {
  mixins: [ListingMixin],
  data() {
    return {
      modal: null,
      deleteUrl: null,
      deleteModal: false,
      tableName: null,
    };
  },
  methods: {
    async handleDelete() {
      if (this.deleteUrl) {
        try {
          const response = await request({
            method: "delete",
            url: this.deleteUrl,
          });
          this.notifySuccess(`${this.tableName} deleted successfully.`);
          this.handleRefreshList();
        } catch (error) {
          if (error.request && error.request.status && error.request.status === 422) {
            this.formErrors = new Error(JSON.parse(error.request.responseText).errors);
            return false;
          }
        }
      }
    },
    handleDeleteShow(url, name) {
      this.deleteUrl = url;
      this.deleteModal = true;
      this.tableName = name;
    },
    handleFilter(filter) {
      this.getList(filter);
    },
    handleSearch() {
      this.query.page = 1; // Reset pagination
      this.getList(); // Call the API using your ListingMixin
    },
    filterTable() {
      // Ensure this method triggers data filtering (modify as per your logic)
      this.getList();
    },
    handleCustomSearch(search) {
      this.query.search = search;
      this.handleSearch();
    },
  },
  computed: {
    ...mapState(["loader", "user"]),
  },
  watch: {
    "query.search"(newVal, oldVal) {
      if (oldVal && newVal === "") {
        // User cleared the input (clicked native X or removed text)
        this.refreshTableData(); // or this.getList() depending on your logic
      }
    },
  },

  props: {
    shouldShowSearchBox: {
      type: Boolean,
      default: true,
    },
  },
};
</script>

<style>
.b-sidebar-footer {
  padding: 0;
}

.default-table {
  border-collapse: separate !important;
  border-spacing: 0 !important;
  border-radius: 10px !important;
  overflow: hidden;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
}

.default-table th,
.default-table td {
  border: 1px solid #ddd !important; /* Light border for table cells */
  padding: 12px;
}

.default-table thead {
  background-color: #007bff; /* Blue background for the header */
  color: white;
  border-radius: 10px 10px 0 0; /* Rounded top corners */
}

.default-table tbody tr {
  background-color: white;
  transition: background-color 0.3s ease;
}

.default-table tbody tr:nth-child(even) {
  background-color: #f8f9fa; /* Light gray for even rows */
}

.default-table tbody tr:nth-child(odd) {
  background-color: #ffffff; /* White background for odd rows */
}

.default-table tbody tr:hover {
  background-color: #ffebee; /* Red background on hover */
  border-color: #d32f2f; /* Red border on hover */
}

.default-table tbody tr:first-child {
  border-top-left-radius: 10px; /* Rounded top-left corner */
  border-top-right-radius: 10px; /* Rounded top-right corner */
}

.default-table tbody tr:last-child {
  border-bottom-left-radius: 10px; /* Rounded bottom-left corner */
  border-bottom-right-radius: 10px; /* Rounded bottom-right corner */
}

/* Additional Styling for the headers */
.default-table th {
  font-weight: bold;
  text-align: left;
  padding: 16px;
}

.default-table th:hover {
  background-color: #0056b3; /* Darker blue on header hover */
  cursor: pointer; /* Change the cursor to indicate interactivity */
}

/* Optional: Add a style for the table's pagination and footer */
.table-pagination {
  margin-top: 20px;
}

.table-pagination .pagination {
  justify-content: center;
}

.table-pagination .pagination .page-item {
  background-color: #f8f9fa;
  border: 1px solid #ddd;
  margin: 0 2px;
  border-radius: 5px;
}

.table-pagination .pagination .page-item:hover {
  background-color: #007bff;
  color: white;
}
</style>
