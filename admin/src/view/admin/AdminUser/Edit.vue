<template>
    <div>
        <Breadcrumbs />
        <b-container>
            <h2 class="mb-4">Update Company Admin</h2>
            <LoaderSpinner v-if="loader" />
            <BasePersonForm :permissionsOptions="permissionsOptions" :submitUrl="'/admin/admin-user/' + adminId"
                :serverErrors="errors" :existingData="existingData" @submit="handleSubmit" />
        </b-container>
    </div>
</template>

<script>
import BasePersonForm from '@/components/BasePersonForm.vue';
import { request } from '@/Util/Request';
import Breadcrumbs from "@/components/Breadcrumbs.vue";
import { mapState } from "vuex";
import LoaderSpinner from '@/components/LoaderSpinner.vue';

export default {
    name: 'Edit',
    components: { BasePersonForm, Breadcrumbs,LoaderSpinner },


    data() {
        return {
            permissionsOptions: [],
            errors: {},
            adminId: this.$route.params.id, // assuming id is passed in route params
            existingData: null
        };
    },
    mounted() {
        this.fetchPermissions();
        this.fetchAdmin();
    },
    computed: {
        ...mapState(['loader']),
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
        async fetchAdmin() {
            try {
                const { data } = await request({ url: `/admin/admin-user/${this.adminId}` });
                this.existingData = data; // Populate the form with existing data
            } catch (err) {
                console.error('Failed to fetch admin data:', err);
            }
        },
        async handleSubmit(formData) {
            try {
                this.$store.dispatch('setLoader', true);

                await request({
                    method: 'post',
                    url: `/admin/admin-user/${this.adminId}`,
                    data: formData,
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                this.$router.push({
                    name: 'AdminUserIndex'
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
