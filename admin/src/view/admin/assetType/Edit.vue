<template>
    <div>
        <Breadcrumbs />
        <b-container>
            <LoaderSpinner v-if="loader" />
            <!--        <h2 class="mb-4">Edit Project</h2>-->
            <b-form v-if="form" @submit.prevent="handleSubmit">
                <b-form-group label="Name">
                    <b-form-input v-model="form.name" required />
                </b-form-group>

                <b-button type="submit" variant="primary" class="m-2">Update Project Category</b-button>
                <b-button variant="secondary" @click="cancel" class="m-2">Cancel</b-button>
            </b-form>
        </b-container>
    </div>

</template>

<script>
import { request } from '@/Util/Request';
import Breadcrumbs from "@/components/Breadcrumbs.vue";
import { mapState } from "vuex";
import LoaderSpinner from '@/components/LoaderSpinner.vue';

export default {
    components: { Breadcrumbs,LoaderSpinner },
    data() {
        return {
            form: null,
        };
    },
    async mounted() {
        const { id } = this.$route.params;
        try {
            const response = await request({ method: 'get', url: `/asset-types/${id}` });
            this.form = response.data;
        } catch (error) {
            console.error(error);
            this.$bvToast.toast('Failed to fetch project data', {
                title: 'Error',
                variant: 'danger'
            });
        }
    },
    computed: {
        ...mapState(['loader']),
    },
    methods: {
        cancel() {
            this.$router.push({
                name: 'CategoryIndex'
            })
        },
        async handleSubmit() {
            const { id } = this.$route.params;
            try {
                this.$store.dispatch('setLoader', true);
                await request({
                    method: 'put',
                    url: `/asset-types/${id}`,
                    data: this.form
                });
                this.$router.push({ name: 'CategoryIndex' });
            } catch (error) {
                console.error(error);
                this.$bvToast.toast('Failed to update project', {
                    title: 'Update Error',
                    variant: 'danger'
                });
            } finally {
                this.$store.dispatch('setLoader', false);
            }
        }
    }
};
</script>
