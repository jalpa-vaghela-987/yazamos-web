<template>
  <div>
    <Breadcrumbs />
    <b-container>
      <h2 class="mb-4">Edit Phase</h2>
      <b-form v-if="form" @submit.prevent="handleSubmit">
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

        <!-- Custom Fields -->
        <b-form-group label="Supervisor">
          <b-form-input v-model="form.custom_fields.supervisor" />
        </b-form-group>

        <b-form-group label="Notes">
          <b-form-textarea v-model="form.custom_fields.notes" rows="2" />
        </b-form-group>

        <b-button type="submit" variant="primary">Update Phase</b-button>
      </b-form>
    </b-container>
  </div>

</template>

<script>
import { request } from '@/Util/Request';
import Breadcrumbs from "@/components/Breadcrumbs.vue";

export default {
  components: { Breadcrumbs },

  data() {
    return {
      form: null,
      statusOptions: ['planning', 'in-progress', 'completed']
    };
  },
  async mounted() {
    const { id } = this.$route.params;
    try {
      const response = await request({ method: 'get', url: `/phases/${id}` });

      // Ensure custom_fields exist even if not returned
      this.form = {
        ...response.data,
        custom_fields: {
          supervisor: response.data.custom_fields?.supervisor || '',
          notes: response.data.custom_fields?.notes || ''
        }
      };
    } catch (error) {
      console.error(error);
      this.$bvToast.toast('Failed to fetch phase data', {
        title: 'Error',
        variant: 'danger'
      });
    }
  },
  methods: {
    async handleSubmit() {
      const { id } = this.$route.params;
      try {
        await request({
          method: 'post',
          url: `/phases/update/${id}`,
          data: this.form
        });

        this.$router.push(`/phases/${this.form.project_id}`);
      } catch (error) {
        console.error(error);
        this.$bvToast.toast('Failed to update phase', {
          title: 'Update Error',
          variant: 'danger'
        });
      }
    }
  }
};
</script>
