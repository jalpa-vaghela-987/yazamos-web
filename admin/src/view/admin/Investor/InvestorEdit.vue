<template>
  <b-container>
    <h2 class="mb-4">Update Investor</h2>
    <BasePersonForm
      :permissionsOptions="permissionsOptions"
      :submitUrl="'investor/' + investorId"
      :serverErrors="errors"
      :existingData="existingData"
      @submit="handleSubmit"
    />
  </b-container>
</template>

<script>
import BasePersonForm from '@/components/BasePersonForm.vue';
import { request } from '@/Util/Request';

export default {
  name: 'InvestorUpdate',
  components: { BasePersonForm },
  data() {
    return {
      permissionsOptions: [],
      errors: {},
      investorId: this.$route.params.id, // assuming id is passed in route params
      existingData: null
    };
  },
  mounted() {
    this.fetchPermissions();
    this.fetchInvestor();
  },
  methods: {
    async fetchPermissions() {
      try {
        const { data } = await request({ url: '/get-all-permission' });
        this.permissionsOptions = data.filter(p => p.id !== 'all');
      } catch (err) {
        console.error('Failed to load permissions:', err);
      }
    },
    async fetchInvestor() {
      try {
        const { data } = await request({ url: `/investor/${this.investorId}` });
        this.existingData = data; // Populate the form with existing data
      } catch (err) {
        console.error('Failed to fetch investor data:', err);
      }
    },
    async handleSubmit(formData) {
      try {
        await request({
          method: 'post',
          url: `/investor/${this.investorId}`,
          data: formData,
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        this.$router.push({
          name: 'AdminInvestorIndex'
        });
      } catch (err) {
        if (err?.data?.errors) {
          const svc = err.data.errors;
          const norm = {};
          for (const k in svc) {
            if (k === 'phone_number') norm.phone_no = svc[k];
            else if (k === 'permission') norm.permissions = svc[k];
            else norm[k] = svc[k];
          }
          this.errors = norm;
        } else {
          this.$bvToast.toast('Something went wrong', { variant: 'danger' });
        }
      }
    }
  }
};
</script>
