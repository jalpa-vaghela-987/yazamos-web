<template>
  <div class="main-block request-management">
    <!-- <Breadcrumbs /> -->
    <div class="div col-12 p-0 d-flex align-items-start">
      <h4 class="">{{ $t('title.Transactions') }}</h4>
    </div>

    <div class="col-12 row mt-3 m-0 p-0" style="min-height: 90%">
      <crud-table :columns="columns" :list-url="listUrl" ref="user-table" v-on:refresh="resetFilter">
        <template #cell(amount)="{ item }">
          {{ item.amount }} {{ item.plan.currency.code }}
        </template>
        <template #cell(created_at)="{ item }">
          {{ $global.dateFormat(item.created_at) }}
        </template>
      </crud-table>
    </div>
  </div>
</template>


<script>
import { request } from "@/Util/Request";
import { mapState, mapGetters } from "vuex";
// import Breadcrumbs from "@/components/Breadcrumbs.vue";

const FILTER_FORM = {
  
};

const COLUMN_STATE = (self) => [
  { key: 'transaction_id', label: self.$t('title.TransactionId'), sortable: true },
  { key: 'plan.title', label: self.$t('title.PlanTitle')},
  { key: 'plan.duration', label: self.$t('title.PlanDuration') },
  { key: 'amount', label: self.$t('title.Amount'), sortable: true },
  { key: 'message', label: self.$t('title.Message') },
  { key: 'status', label: self.$t('title.Status'), sortable: true },
  { key: 'created_at', label: self.$t('title.TransactionDate') },
];

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
      listUrl: "/transactions",
      filters: { ...FILTER_FORM }
    };
  },
  components: {
    // Breadcrumbs,
  },
  methods: {
    handleFilter() {
      // Passing filters to the table component
      this.$refs["user-table"].handleFilter(this.filters);
    },
    refreshTable() {
      this.$refs["user-table"].handleRefreshList();
    },
    resetFilter() {
      this.filters = { ...FILTER_FORM };
    }
  },
  computed: {
    columns() {
      return COLUMN_STATE(this);
    },
  }
};
</script>
