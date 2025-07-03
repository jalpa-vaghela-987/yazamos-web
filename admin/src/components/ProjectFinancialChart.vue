<template>
    <div class="bg-white rounded-4 shadow-sm p-4">
      <apexchart type="bar" :options="chartOptions" :series="series" height="350" />
    </div>
  </template>
  
  <script>
  import VueApexCharts from 'vue-apexcharts'
  import { request } from '@/Util/Request'
  
  export default {
    components: {
      apexchart: VueApexCharts
    },
    data() {
      return {
        series: [],
        chartOptions: {
          chart: {
            type: 'bar',
            height: 350
          },
          xaxis: {
            categories: [],  // Project IDs will be added here
            title: {
              text: 'Project ID'
            }
          },
          title: {
            text: 'Project Financial Overview',
            align: 'left'
          },
          colors: ['#008FFB', '#00E396', '#FEB019'],
          dataLabels: {
            enabled: false
          },
          tooltip: {
            y: {
              formatter: (value, { seriesIndex, dataPointIndex }) => {
                const project = this.projects[dataPointIndex]
                return `
                  <strong>Name:</strong> ${project.name}<br>
                `
              }
            }
          }
        },
        projects: [] // To store project data
      }
    },
    async mounted() {
      try {
        const data = await request({ url: '/admin/project-financial-chart' })
        console.log('Project financial chart data:', data) // Log the response data for debugging
  
        if (data && Array.isArray(data.projects)) {
          console.log('Projects:', data.projects) // Log the projects to inspect
  
          const projects = data.projects
          this.projects = projects // Store the projects for use in tooltips
  
          this.chartOptions.xaxis.categories = projects.map(p => p.id) // Use project ID on the x-axis
  
          this.series = [
            {
              name: 'Estimated Budget',
              data: projects.map(p => p.estimated_budget || 0) // Replace null with 0
            },
            {
              name: 'Purchase Price',
              data: projects.map(p => p.purchase_price || 0) // Replace null with 0
            },
            {
              name: 'Current Value',
              data: projects.map(p => p.current_property_value || 0) // Replace null with 0
            }
          ]
        } else {
          console.error('No projects data or incorrect format:', data)
        }
      } catch (error) {
        console.error('Failed to fetch project financial chart data:', error)
      }
    }
  }
  </script>
  