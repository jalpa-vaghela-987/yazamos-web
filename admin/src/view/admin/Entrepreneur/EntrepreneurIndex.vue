<template>
  <div>
    <Breadcrumbs />

    <CrudTable title="Entrepreneurs" fetch-url="/entrepreneur" :columns="['name', 'email', 'address', 'roles']"
      :editRouteName="editRouteName" :createRouteName="createRouteName" :delete-action="deleteEntrepreneur" />
  </div>
</template>

<script>
import CrudTable from '@/components/CrudTable.vue';
import { request } from '@/Util/Request';
import Breadcrumbs from "@/components/Breadcrumbs.vue";


export default {
  name: 'EntrepreneurIndex',
  components: { CrudTable,Breadcrumbs },
  data() {
    return {
      // Define your route names dynamically, or hardcode if preferred
      editRouteName: 'AdminEntrepreneurEdit',
      createRouteName: 'AdminEntrepreneurCreate',
    };
  },
  methods: {
    async deleteEntrepreneur(id) {
      await request({ method: 'delete', url: `/admin/entrepreneurs/${id}` });
    },
  },
};
</script>
