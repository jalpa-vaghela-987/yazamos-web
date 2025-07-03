<template>
  <div>
    <div class="row mb-3">
      <div class="col-12">
        <h4 class="fw-bold">
  {{ $t('title.Welcome') }}, {{ company_name || '' }}
</h4>

      </div>
    </div>
    <div class="container-fluid py-4">
      <!-- Assets Tabs -->
      <div class="row mx-0 mb-3">
        <div class="col-12 d-flex justify-content-between gap-2">
          <div class="d-flex gap-2 align-items-center">
            <h5 class="mb-0">{{ $t('title.Assets') }}</h5>
            <ul class="nav nav-pills ms-3 d-flex flex-nowrap overflow-auto">
              <li class="nav-item" v-for="type in assetTypes" :key="type.id">
                <button class="nav-link" :class="{ active: activeTab?.id === type.id }" @click="selectAssetTab(type)">
                  {{ type.name }}
                </button>
              </li>
            </ul>
          </div>

          <div class="text-center">
            <b-button variant="primary" @click="goToAssets">
  {{ $t('title.AssetsList') }}
</b-button>

          </div>
        </div>
      </div>

      <!-- Project Cards Horizontal Scroll -->
      <div class="project-card-wrapper px-2">
        <div class="d-flex gap-3 project-card-scroll" ref="scrollContainer">
          <div v-for="project in projects" :key="project.id"
            class="card shadow-sm border-0 rounded-4 overflow-hidden h-100 flex-shrink-0 project-card"
            @click="() => $router.push({ name: 'AdminProjectShow', params: { id: project.id } })"
            style="cursor: pointer;">
            <img :src="project.images?.[0]?.url || 'https://via.placeholder.com/280x180?text=No+Image'"
              class="card-img-top" alt="Project image" @error="handleImageError($event)"
              style="height: 180px; object-fit: cover; ">
            <div class="card-body d-flex flex-column justify-content-between">
              <h6 class="fw-bold text-primary text-truncate">{{ project.name }}</h6>
              <div class="d-flex justify-content-between text-muted small mt-auto">
                <div>
  <div>{{ $t('title.Value') }}</div>
  <strong>{{ project.current_property_value }}</strong>
</div>
<div>
  <div>{{ $t('title.Purchase') }}</div>
  <strong>{{ project.purchase_price }}</strong>
</div>
<div>
  <div>{{ $t('title.Wedge') }}</div>
  <strong>{{ project.wedge }}</strong>
</div>

              </div>
            </div>
          </div>
        </div>
      </div>

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

      <!-- Financial Chart -->
      <div class="row mx-0 mt-4">
        <div class="col-12">
          <ProjectFinancialChart />
        </div>
      </div>

      <div class="row mx-0 mt-4">
        <TransactionList/>
      </div>
    </div>
  </div>
</template>

<script>
import { request } from '@/Util/Request'
import ProjectFinancialChart from '@/components/ProjectFinancialChart.vue'
import TransactionList from '@/view/admin/transactions/index.vue'

export default {
  components: {
    ProjectFinancialChart,
    TransactionList
  },

  data() {
    return {
      user: null,
      company_name: null,
      stats: [],
      assetTypes: [],
      activeTab: null,
      projects: []
    }
  },

  methods: {
    handleImageError(event) {
      event.target.src = require('@/assets/default.png'); 
    },
    async fetchUserProfile() {
      try {
        const { data } = await request({ url: '/profile' })
        this.user = data;
        this.company_name = this.user.company_name;

      } catch (error) {
        console.error('Error fetching user profile:', error)
      }
    },
    async fetchDashboardData() {
      try {
        const { data } = await request({ url: '/admin/dashboard' })
        const dashboard = data

        // Set summary stats
        this.stats = [
  { label: this.$t('title.TotalValueOfAssets'), value: `$${dashboard.total_value_of_assets.toLocaleString()}`, textClass: 'text-primary' },
  { label: this.$t('title.TotalInvestment'), value: `$${dashboard.total_investment.toLocaleString()}`, textClass: 'text-success' },
  { label: this.$t('title.ROI'), value: `${dashboard.roi}%`, textClass: 'text-warning' },
  { label: this.$t('title.NumberOfProjects'), value: dashboard.number_of_assets, textClass: 'text-info' }

        ]

        // Extract asset types from projects
        const typeMap = {}
        dashboard.projects.forEach(project => {
          const type = project.asset_type_id // <-- this is the full object with id and name
          if (type && !typeMap[type.id]) {
            typeMap[type.id] = type
          }
        })

        this.assetTypes = Object.values(typeMap)

        // Add the "All" tab as the first tab
        this.assetTypes.unshift({ id: null, name: 'All' })

        this.projects = dashboard.projects

        // Set default tab as "All"
        this.selectAssetTab(this.assetTypes[0])
      } catch (error) {
        console.error('Error fetching dashboard data:', error)
      }
    },
    async selectAssetTab(type) {
      this.activeTab = type
      try {
        // If "All" tab is selected, show all projects
        let url = '/projects'
        let params = {}

        if (type?.id) {
          // Fetch projects based on selected asset type
          params = { asset_type_id: type.id }
        }

        const { data } = await request({
          url: url,
          params: params
        })
        this.projects = data
      } catch (error) {
        console.error('Error fetching projects for selected asset type:', error)
      }
    },
    enableDragScroll() {
      const container = this.$refs.scrollContainer
      let isDown = false
      let startX, scrollLeft

      container.addEventListener('mousedown', (e) => {
        isDown = true
        container.classList.add('active')
        startX = e.pageX - container.offsetLeft
        scrollLeft = container.scrollLeft
      })
      container.addEventListener('mouseleave', () => isDown = false)
      container.addEventListener('mouseup', () => isDown = false)
      container.addEventListener('mousemove', (e) => {
        if (!isDown) return
        const x = e.pageX - container.offsetLeft
        const walk = (x - startX) * 2
        container.scrollLeft = scrollLeft - walk
      })
    },
    goToAssets() {
      this.$router.push('/admin/asset');
    }
  },
  mounted() {
    this.fetchUserProfile()
    this.fetchDashboardData()
    this.enableDragScroll()
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
