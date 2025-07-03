<template>
  <b-container>
    <h2 class="mb-4">Add Investor</h2>
    <BasePersonForm
      :permissionsOptions="permissionsOptions"
      :submitUrl="'investor'"
      :serverErrors="errors"
      @submit="handleSubmit"
    />
  </b-container>
</template>

<script>
import BasePersonForm from '@/components/BasePersonForm.vue';
import { request } from '@/Util/Request';
import countryCodes from '@/Util/countryCodes';

export default {
  name: 'InvestorCreate',
  components: { BasePersonForm },
  data() {
    return {
      permissionsOptions: [],
      errors: {},
      countryCodes,
    };
  },
  mounted() {
    this.fetchPermissions();
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
    async handleSubmit(formData) {
      try {
        await request({
          method: 'post',
          url: '/investor',
          data: formData,
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        this.$router.push({
          name: this.$global.hasRole('super admin') ? 'AdminInvestorIndex' : 'InvestorIndex'
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

<style scoped>
.is-invalid {
  border-color: #dc3545 !important;
}
.invalid-feedback {
  color: #dc3545;
}
</style>
