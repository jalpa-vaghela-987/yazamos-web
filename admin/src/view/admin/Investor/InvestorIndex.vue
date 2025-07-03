<template>
  <div>
    <CrudTable 
      title="Investors" 
      fetch-url="/investor" 
      :columns="['name', 'email', 'address', 'roles']"
      :editRouteName="editRouteName" 
      :createRouteName="createRouteName" 
      :delete-action="deleteInvestor" 
    />
  </div>
</template>

<script>
import CrudTable from '@/components/CrudTable.vue';
import { request } from '@/Util/Request';

export default {
  name: 'InvestorIndex',
  components: { CrudTable },
  data() {
    return {
      editRouteName: 'AdminInvestorEdit',
      createRouteName: 'AdminInvestorCreate',
    };
  },
  methods: {
    async deleteInvestor(id) {
      await request({ method: 'delete', url: `/admin/investors/${id}` });
    },
  },
};
</script>
