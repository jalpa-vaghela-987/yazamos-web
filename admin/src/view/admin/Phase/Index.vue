<template>
    <div>
        <Breadcrumbs />

        <b-button variant="primary" class="mb-3"
            @click="$router.push({ name: ($global.hasRole('super admin')) ? 'AdminPhaseCreate' : 'PhaseCreate', params: { project_id } })">
            Add New Phase
        </b-button>

        <b-table :items="phases" :fields="fields" bordered hover responsive>
            <template #cell(custom_fields)="row">
                <div v-if="row.item.custom_fields">
                    <div v-for="(value, key) in row.item.custom_fields" :key="key">
                        <strong>{{ key }}:</strong> {{ value }}
                    </div>
                </div>
                <div v-else>-</div>
            </template>


            <template #cell(actions)="row">
                <b-button size="sm" variant="warning" @click="editPhase(row.item.id)">
                    Edit
                </b-button>
                <b-button  size="sm" variant="danger" class="ml-2"
                    @click="deletePhase(row.item.id)">
                    Delete
                </b-button>
                <b-button  size="sm" variant="info" class="ml-2"
                    :to="{ name: ($global.hasRole('super admin')) ? 'AdminMilestoneIndex' : 'MilestoneIndex', params: { phase_id: row.item.id } }">
                    Milestone
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
    name: 'PhaseIndex',
    props: {
        project_id: {
            type: [Number, String],
            required: true
        }
    },
    data() {
        return {
            phases: [],
            fields: [
                { key: 'title', label: 'Title' },
                { key: 'description', label: 'Description' },
                { key: 'status', label: 'Status' },
                { key: 'start_date', label: 'Start Date' },
                { key: 'end_date', label: 'End Date' },
                { key: 'progress', label: 'Progress %' },
                { key: 'planned_cost', label: 'Planned Cost' },
                { key: 'actual_cost', label: 'Actual Cost' },
                { key: 'custom_fields', label: 'Custom Fields' },
                { key: 'actions', label: 'Actions' }
            ]
        };
    },
    mounted() {
        this.getPhases();
    },
    methods: {
        async getPhases() {
            try {
                const { data } = await request({
                    url: '/phases',
                    params: { project_id: this.project_id }
                });
                this.phases = data;
            } catch (error) {
                console.error(error);
                this.$bvToast.toast('Failed to load phases', {
                    variant: 'danger',
                    title: 'Error'
                });
            }
        },
        editPhase(id) {
            this.$router.push({ name: (this.$global.hasRole('super admin')) ? 'AdminPhaseEdit' : 'PhaseEdit', params: { id } });
        },
        async deletePhase(id) {
            try {
                await request({ method: 'delete', url: `/phases/${id}` });
                this.getPhases();
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
