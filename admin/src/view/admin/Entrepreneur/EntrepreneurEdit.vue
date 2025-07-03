<template>
  <div>
    <Breadcrumbs />
    <b-container>
      <h2 class="mb-4">Update Entrepreneur</h2>
      <BasePersonForm :permissionsOptions="permissionsOptions" :submitUrl="'entrepreneur/' + entrepreneurId"
        :serverErrors="errors" :existingData="existingData" @submit="handleSubmit" />
    </b-container>
  </div>

</template>

<script>
import BasePersonForm from '@/components/BasePersonForm.vue';
import { request } from '@/Util/Request';
import Breadcrumbs from "@/components/Breadcrumbs.vue";

export default {
  name: 'EntrepreneurUpdate',
  components: { BasePersonForm,Breadcrumbs },
  data() {
    return {
      permissionsOptions: [],
      errors: {},
      entrepreneurId: this.$route.params.id, // assuming id is passed in route params
      existingData: null
    };
  },
  mounted() {
    this.fetchPermissions();
    this.fetchEntrepreneur();
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
    async fetchEntrepreneur() {
      try {
        const { data } = await request({ url: `/entrepreneur/${this.entrepreneurId}` });
        console.log('Entrepreneur data:', data);
        this.existingData = data; // Populate the form with existing data
      } catch (err) {
        console.error('Failed to fetch entrepreneur data:', err);
      }
    },
    async handleSubmit(formData) {
      try {
        await request({
          method: 'post',
          url: `/entrepreneur/${this.entrepreneurId}`,
          data: formData,
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        this.$router.push({
          name: 'AdminEntrepreneurIndex'
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
