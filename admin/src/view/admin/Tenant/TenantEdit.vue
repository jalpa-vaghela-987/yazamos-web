<template>
  <b-container>
    <h2 class="mb-4">Update Tenant</h2>
    <BasePersonForm
      :permissionsOptions="permissionsOptions"
      :submitUrl="'tenant/' + tenantId"
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
  name: 'TenantUpdate',
  components: { BasePersonForm },
  data() {
    return {
      permissionsOptions: [],
      errors: {},
      tenantId: this.$route.params.id, // assuming id is passed in route params
      existingData: null
    };
  },
  mounted() {
    this.fetchPermissions();
    this.fetchTenant();
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
    async fetchTenant() {
      try {
        const { data } = await request({ url: `/tenant/${this.tenantId}` });
        console.log('Tenant data:', data);
        this.existingData = data; // Populate the form with existing data
      } catch (err) {
        console.error('Failed to fetch tenant data:', err);
      }
    },
    async handleSubmit(formData) {
      try {
        await request({
          method: 'post',
          url: `/tenant/${this.tenantId}`,
          data: formData,
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        this.$router.push({
          name: 'AdminTenantIndex'
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
