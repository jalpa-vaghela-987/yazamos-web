<template>
  <div>
    <Breadcrumbs />
    <b-container>
      <h2 class="mb-4">Edit Milestone</h2>
      <b-form @submit.prevent="handleSubmit">
        <b-form-group label="Title">
          <b-form-input v-model="formFields.title" />
          <b-form-invalid-feedback :state="!formErrors.has('title')">
            {{ formErrors.first('title') }}
          </b-form-invalid-feedback>
        </b-form-group>

        <b-form-group label="Description">
          <b-form-textarea v-model="formFields.description" rows="3" />
          <b-form-invalid-feedback :state="!formErrors.has('description')">
            {{ formErrors.first('description') }}
          </b-form-invalid-feedback>
        </b-form-group>

        <b-form-group label="Status">
          <b-form-select v-model="formFields.status" :options="statusOptions" />
          <b-form-invalid-feedback :state="!formErrors.has('status')">
            {{ formErrors.first('status') }}
          </b-form-invalid-feedback>
        </b-form-group>

        <b-form-group label="Due Date">
          <b-form-input v-model="formFields.due_date" type="date" />
          <b-form-invalid-feedback :state="!formErrors.has('due_date')">
            {{ formErrors.first('due_date') }}
          </b-form-invalid-feedback>
        </b-form-group>

        <b-form-group label="Start Date">
          <b-form-input v-model="formFields.start_date" type="date" />
          <b-form-invalid-feedback :state="!formErrors.has('start_date')">
            {{ formErrors.first('start_date') }}
          </b-form-invalid-feedback>
        </b-form-group>

        <b-form-group label="End Date">
          <b-form-input v-model="formFields.end_date" type="date" />
          <b-form-invalid-feedback :state="!formErrors.has('end_date')">
            {{ formErrors.first('end_date') }}
          </b-form-invalid-feedback>
        </b-form-group>

        <b-form-group label="Progress (%)">
          <b-form-input v-model="formFields.progress" type="number" min="0" max="100" />
          <b-form-invalid-feedback :state="!formErrors.has('progress')">
            {{ formErrors.first('progress') }}
          </b-form-invalid-feedback>
        </b-form-group>

        <b-form-group label="Planned Cost">
          <b-form-input v-model="formFields.planned_cost" type="number" />
          <b-form-invalid-feedback :state="!formErrors.has('planned_cost')">
            {{ formErrors.first('planned_cost') }}
          </b-form-invalid-feedback>
        </b-form-group>

        <b-form-group label="Actual Cost">
          <b-form-input v-model="formFields.actual_cost" type="number" />
          <b-form-invalid-feedback :state="!formErrors.has('actual_cost')">
            {{ formErrors.first('actual_cost') }}
          </b-form-invalid-feedback>
        </b-form-group>

        <b-form-group label="Order">
          <b-form-input v-model="formFields.order" type="number" />
          <b-form-invalid-feedback :state="!formErrors.has('order')">
            {{ formErrors.first('order') }}
          </b-form-invalid-feedback>
        </b-form-group>

        <!-- Custom Fields -->
        <b-form-group label="Supervisor">
          <b-form-input v-model="formFields.custom_fields.supervisor" />
        </b-form-group>

        <b-form-group label="Notes">
          <b-form-textarea v-model="formFields.custom_fields.notes" rows="2" />
        </b-form-group>

        <b-button type="submit" variant="primary">Submit</b-button>
      </b-form>
    </b-container>
  </div>

</template>

<script>
import { request } from '@/Util/Request';
import Error from "@/Util/Error";
import Breadcrumbs from "@/components/Breadcrumbs.vue";

const FORM_STATE = {
  title: '',
  description: '',
  status: '',
  start_date: '',
  due_date: '',
  end_date: '',
  progress: 0,
  planned_cost: null,
  actual_cost: null,
  order: 1,
  custom_fields: {
    supervisor: '',
    notes: ''
  }
};

export default {
  components: { Breadcrumbs },
  data() {
    return {
      formFields: { ...FORM_STATE },
      formErrors: new Error({}),
      statusOptions: ['planning', 'in-progress', 'completed']
    };
  },
  async mounted() {
    const { id } = this.$route.params;
    try {
      const response = await request({ method: 'get', url: `/milestones/${id}` });

      // Ensure custom_fields exist even if not returned
      this.formFields = {
        ...response.data
      };
    } catch (error) {
      console.error(error);
    }
  },
  methods: {
    async handleSubmit() {
      const { id } = this.$route.params;
      try {
        const response = await request({
          method: 'post',
          url: `/milestones/update/${id}`,
          data: this.formFields
        });

        if (response) {
          this.formFields = { ...FORM_STATE };
          this.$router.push(`/milestones/${response.data.phase_id}`);
        }
      } catch (error) {
        if (error.request && error.request.status && error.request.status === 422) {
          this.formErrors = new Error(JSON.parse(error.request.responseText).errors);
          return false;
        } else {
          this.notifyError();
        }
      }
    }
  }
};
</script>
