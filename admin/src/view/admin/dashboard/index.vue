<template>
  <div>
    <component :is="componentToRender" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import AdminView from './admin.vue'
import SuperAdminView from './superadmin.vue'

export default {
  components: {
    AdminView,
    SuperAdminView
  },
  computed: {
    ...mapGetters(['user']),
    componentToRender() {
      console.log('User role:', this.user?.role)
      if (this.user?.role === 'super admin') {
        return 'SuperAdminView'
      } else {
        return 'AdminView'
      }
    }
  }
}
</script>
