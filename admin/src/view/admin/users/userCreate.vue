<template>
  <div>
      <Breadcrumbs />
  <b-container>

    <h2 class="mb-4">Add User</h2>
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
import countryCodes from '@/Util/countryCodes';
import Breadcrumbs  from "@/components/Breadcrumbs.vue";
import { mapState } from "vuex";
import LoaderSpinner from '@/components/LoaderSpinner.vue';

export default {
  name: 'UserCreate',
  components: { UserForm,Breadcrumbs,LoaderSpinner },
  computed: {
        ...mapState(['loader']),
    }, 
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
        this.$store.dispatch('setLoader', true);
        await request({
          method: 'post',
          url: '/entrepreneur',
          data: formData,
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        this.$router.push({
          name: this.$global.hasRole('super admin') ? 'AdminEntrepreneurIndex' : 'EntrepreneurIndex'
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

<style scoped>
.is-invalid {
  border-color: #dc3545 !important;
}
.invalid-feedback {
  color: #dc3545;
}
</style>
