<template>
    <div>
      <h4><i>{{ $t('title.WelcomeBack', { role: 'super admin' }) }}</i></h4>
      <div class="container-fluid py-4">

        <!-- Summary Stats -->
        <div class="row text-center mx-0 justify-content-center mt-4">
          <div class="col-6 col-xxs-12 col-md-3 mb-4" v-for="(item, index) in stats" :key="index">
            <div
              class="bg-white border rounded-4 shadow-sm p-4 h-100 d-flex flex-column justify-content-center align-items-center"
              style="min-height: 160px;">
              <h6 class="mb-2">{{ item.label }}</h6>
              <h5 :class="[item.textClass, 'mb-0']">{{ item.value }}</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>

<script>
import { request } from '@/Util/Request'

export default {

  data() {
    return {
      stats: [],
      assetTypes: [],
      activeTab: null,
    }
  },
  methods: {
    async fetchDashboardData() {
      try {
        const { data } = await request({ url: '/superadmin/dashboard' })
        const dashboard = data

        // Set summary stats
        this.stats = [
  { label: this.$t('title.TotalValueOfAssets'), value: `$${dashboard.total_value_of_assets.toLocaleString()}`, textClass: 'text-primary' },
  { label: this.$t('title.TotalInvestment'), value: `$${dashboard.total_investment.toLocaleString()}`, textClass: 'text-success' },
  { label: this.$t('title.ROI'), value: `${dashboard.roi}%`, textClass: 'text-warning' },
  { label: this.$t('title.NumberOfProjects'), value: dashboard.number_of_assets, textClass: 'text-info' },
  { label: this.$t('title.TotalCompanyAdmins'), value: dashboard.adminCount, textClass: 'text-primary' }
]


        // Set default tab as "All"
      } catch (error) {
        console.error('Error fetching dashboard data:', error)
      }
    },
  },
  mounted() {
    this.fetchDashboardData()
  }
}
</script>


<style scoped>
@media (max-width: 350px) {
  .col-xxs-12 {
    flex: 0 0 100%;
    max-width: 100%;
  }
}

.project-card-wrapper {
  overflow-x: auto;
  white-space: nowrap;
}

.project-card-scroll {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  cursor: grab;
}

.project-card-scroll.active {
  cursor: grabbing;
}

.project-card-scroll::-webkit-scrollbar {
  display: none;
}

.project-card {
  width: 280px;
}
</style>
