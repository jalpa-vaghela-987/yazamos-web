<template>
    <div>
      <Breadcrumbs />
  <b-container>
    <h2 class="mb-4">Update User</h2>
    <LoaderSpinner v-if="loader" />
    <UserForm
      :permissionsOptions="permissionsOptions"
      :submitUrl="'users'"
      :serverErrors="errors"
      @submit="handleSubmit"
    />
  </b-container>
  </div>
</template>

<script>
import UserForm from '@/components/UserForm.vue';
import { request } from '@/Util/Request';
import Breadcrumbs  from "@/components/Breadcrumbs.vue";
import { mapState } from "vuex";
import LoaderSpinner from '@/components/LoaderSpinner.vue';

export default {
  name: 'UserUpdate',
  components: { UserForm,Breadcrumbs,LoaderSpinner  },
  computed: {
        ...mapState(['loader']),
    },
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
        this.existingData = data; // Populate the form with existing data
      } catch (err) {
        console.error('Failed to fetch entrepreneur data:', err);
      }
    },
    async handleSubmit(formData) {
      try {
        this.$store.dispatch('setLoader', true);
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
      }finally {
        this.$store.dispatch('setLoader', false);
      }
    }
  }
};
</script>
