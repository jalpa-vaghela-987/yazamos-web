<template>
    <div>
        <Breadcrumbs />

        <b-button variant="primary" class="mb-3" @click="$router.push({ name: 'CategoryCreate' })">
            Add New Asset Type
        </b-button>

        <b-table :items="categories" :fields="fields" bordered hover responsive>
            <template #cell(actions)="row">
                <b-button size="sm" variant="warning" @click="editCategory(row.item.id)">
                    Edit
                </b-button>
            </template>
        </b-table>
    </div>
</template>

<script>
import { request } from '@/Util/Request';
import Breadcrumbs from "@/components/Breadcrumbs.vue";

export default {
    components: { Breadcrumbs },
    data() {
        return {
            categories: [],
            fields: [
                { key: 'name', label: 'Name' },
                { key: 'actions', label: 'Actions' }
            ]
        };
    },
    mounted() {
        this.getCategories();
    },
    methods: {
        async getCategories() {
            try {
                const { data } = await request({ url: '/asset-types' });
                this.categories = data;
            } catch (error) {
                console.error(error);
                this.$bvToast.toast('Failed to load Category', {
                    variant: 'danger',
                    title: 'Error'
                });
            }
        },
        editCategory(id) {
            this.$router.push({ name: 'CategoryEdit', params: { id } });
        },
        async deleteCategory(id) {
            try {
                await request({ method: 'delete', url: `/asset-types/${id}` });
                this.getCategories();
            } catch (error) {
                console.error(error);
                this.$bvToast.toast('Delete failed', {
                    variant: 'danger',
                    title: 'Error'
                });
            }
        }
    }
};
</script>
