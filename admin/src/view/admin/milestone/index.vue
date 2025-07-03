<template>
    <div>
        <Breadcrumbs />

        <b-button  variant="primary" class="mb-3"
            :to="{ name: ($global.hasRole('super admin')) ? 'AdminMilestoneCreate' : 'MilestoneCreate', params: { phase_id } }">
            Add New Milestone
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
                <b-button size="sm" variant="warning"
                    :to="{ name: ($global.hasRole('super admin')) ? 'AdminMilestoneEdit' : 'MilestoneEdit', params: { id: row.item.id } }">
                    Edit
                </b-button>
                <b-button  size="sm" variant="danger" class="ml-2"
                    @click="deleteMilestone(row.item.id)">
                    Delete
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
        phase_id: {
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
                { key: 'due_date', label: 'Due Date' },
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
        this.getMilestones();
    },
    methods: {
        async getMilestones() {
            try {
                const { data } = await request({
                    url: '/milestones',
                    params: { phase_id: this.phase_id }
                });
                this.phases = data;
            } catch (error) {
                console.error(error);
                this.notifyError();
            }
        },
        async deleteMilestone(id) {
            try {
                await request({ method: 'delete', url: `/milestones/${id}` });
                this.getMilestones();
            } catch (error) {
                console.error(error);
                this.notifyError();
            }
        }
    }
};
</script>
