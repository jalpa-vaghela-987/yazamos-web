<template>
    <div ref="ganttContainer" style="width: 100%; height: 700px;"></div>
  </template>
  
  <script setup>
  import { onMounted, onBeforeUnmount, ref } from 'vue'
  import { request } from '@/Util/Request'
  import 'dhtmlx-gantt/codebase/dhtmlxgantt.css'
  import gantt from 'dhtmlx-gantt'
  
  const ganttContainer = ref(null)
  
  onMounted(async () => {
    try {
      const res = await request({ url: '/gantt-data' })
      const items = res.data ?? []
  
      const tasks = {
        data: items.map(item => ({
          id: item.id,
          text: item.label,
          start_date: item.start,
          end_date: item.end,
          parent: item.parent || 0,
          progress: 1,
        })),
      }
  
      gantt.config.readonly = true
  
      gantt.config.date_format = "%Y-%m-%d"
      gantt.init(ganttContainer.value)
      gantt.parse(tasks)
    } catch (err) {
      console.error("Error loading Gantt data:", err)
    }
  })
  
  onBeforeUnmount(() => {
    gantt.clearAll()
  })
  </script>
  