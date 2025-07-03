<template>
    <div>
        <Breadcrumbs />
        <b-container>
            <h2 class="mb-4">Add New Asset Type</h2>
            <LoaderSpinner v-if="loader" />
            <b-form @submit.prevent="handleSubmit">
                <b-form-group label="Name">
                    <b-form-input v-model="form.name" required />
                </b-form-group>

                <b-button type="submit" variant="primary" class="m-2">Submit</b-button>
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
    components: { Breadcrumbs, LoaderSpinner },
    data() {
        return {
            form: {
                name: '',
            },
        };
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
            try {
                this.$store.dispatch('setLoader', true);
                const response = await request({
                    method: 'post',
                    url: '/asset-types',
                    data: this.form
                });
                this.$router.push({ name: 'CategoryIndex' });
            } catch (error) {
                console.error(error);
                this.$bvToast.toast('Something went wrong', { variant: 'danger' });
            }finally {
                this.$store.dispatch('setLoader', false);
            }
        }
    }
};
</script>
