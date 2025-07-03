<template>
  <div>
    <CrudTable 
      title="Tenants" 
      fetch-url="/tenant" 
      :columns="['name', 'email', 'address', 'roles']"
      :editRouteName="editRouteName" 
      :createRouteName="createRouteName" 
      :delete-action="deleteTenant" 
    />
  </div>
</template>

<script>
import CrudTable from '@/components/CrudTable.vue';
import { request } from '@/Util/Request';

export default {
  name: 'TenantIndex',
  components: { CrudTable },
  data() {
    return {
      editRouteName: 'AdminTenantEdit',
      createRouteName: 'AdminTenantCreate',
    };
  },
  methods: {
    async deleteTenant(id) {
      await request({ method: 'delete', url: `/admin/tenants/${id}` });
    },
  },
};
</script>
