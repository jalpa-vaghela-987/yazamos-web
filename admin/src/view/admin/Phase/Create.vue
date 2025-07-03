<template>
  <div>
    <Breadcrumbs />
    <b-container>
      <h2 class="mb-4">Add New Phase</h2>
      <b-form @submit.prevent="handleSubmit">
        <b-form-group label="Title">
          <b-form-input v-model="form.title" required />
        </b-form-group>

        <b-form-group label="Description">
          <b-form-textarea v-model="form.description" rows="3" required />
        </b-form-group>

        <b-form-group label="Status">
          <b-form-select v-model="form.status" :options="statusOptions" required />
        </b-form-group>

        <b-form-group label="Start Date">
          <b-form-input v-model="form.start_date" type="date" required />
        </b-form-group>

        <b-form-group label="End Date">
          <b-form-input v-model="form.end_date" type="date" required />
        </b-form-group>

        <b-form-group label="Progress (%)">
          <b-form-input v-model="form.progress" type="number" min="0" max="100" required />
        </b-form-group>

        <b-form-group label="Planned Cost">
          <b-form-input v-model="form.planned_cost" type="number" required />
        </b-form-group>

        <b-form-group label="Actual Cost">
          <b-form-input v-model="form.actual_cost" type="number" required />
        </b-form-group>

        <b-form-group label="Order">
          <b-form-input v-model="form.order" type="number" required />
        </b-form-group>

        <b-form-group label="Extra Fields">
          <div v-for="(field, index) in form.extra" :key="index" class="mb-2 d-flex align-items-center">
            <b-form-input v-model="field.key" placeholder="Key" class="mr-2" />
            <b-form-input v-model="field.value" placeholder="Value" class="mr-2" />
            <b-button size="sm" variant="danger" @click="removeExtra(index)">Remove</b-button>
          </div>
          <b-button size="sm" variant="success" @click="addExtra">Add Extra Field</b-button>
        </b-form-group>
        <!-- Custom Fields -->
        <b-form-group label="Supervisor">
          <b-form-input v-model="form.custom_fields.supervisor" />
        </b-form-group>

        <b-form-group label="Notes">
          <b-form-textarea v-model="form.custom_fields.notes" rows="2" />
        </b-form-group>

        <b-button type="submit" variant="primary">Submit</b-button>
      </b-form>
    </b-container>
  </div>
</template>

<script>
import { request } from '@/Util/Request';
import Breadcrumbs from "@/components/Breadcrumbs.vue";

export default {
  props: ['project_id'],
  components: { Breadcrumbs },

  data() {
    return {
      form: {
        project_id: this.project_id,
        title: '',
        description: '',
        status: '',
        start_date: '',
        end_date: '',
        progress: 0,
        planned_cost: null,
        actual_cost: null,
        order: 1,
        extra: [],
        custom_fields: {
          supervisor: '',
          notes: ''
        }
      },
      statusOptions: ['planning', 'in-progress', 'completed']
    };
  },
  methods: {
    addExtra() {
      this.form.extra.push({ key: '', value: '' });
    },
    removeExtra(index) {
      this.form.extra.splice(index, 1);
    },
    async handleSubmit() {
      try {
        await request({
          method: 'post',
          url: '/phases',
          data: this.form
        });
        this.$router.push(`/phases/${this.project_id}`);
      } catch (error) {
        console.error(error);
        this.$bvToast.toast('Failed to create phase', {
          variant: 'danger',
          title: 'Error'
        });
      }
    }
  }
};
</script>
