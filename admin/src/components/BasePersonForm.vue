<template>
  <b-form @submit.prevent="handleSubmit" :validated="validated" novalidate>
    <!-- Name -->
    <b-form-group
      label="Name"
      :state="getValidationState('name')"
      :invalid-feedback="getErrorMessage('name')"
    >
      <b-form-input v-model="form.name" @input="clearError('name')" required />
    </b-form-group>

    <!-- Email -->
    <b-form-group
      label="Email"
      :state="getValidationState('email')"
      :invalid-feedback="getErrorMessage('email')"
    >
      <b-form-input
        v-model="form.email"
        type="email"
        @input="clearError('email')"
        required
      />
    </b-form-group>

    <!-- Address -->
    <b-form-group
      label="Address"
      :state="getValidationState('address')"
      :invalid-feedback="getErrorMessage('address')"
    >
      <b-form-input v-model="form.address" @input="clearError('address')" required />
    </b-form-group>

    <!-- Phone -->
    <b-form-group label="Phone No" :state="getValidationState('phone_no')">
      <b-input-group>
        <b-form-select
          v-model="form.country_code"
          :options="countryCodes"
          class="country-code-select"
          @change="clearError('phone_no')"
        />
        <b-form-input
          v-model="form.phone_no"
          type="tel"
          required
          @input="clearError('phone_no')"
          :state="getValidationState('phone_no')"
        />
      </b-input-group>
      <small class="text-muted">{{ $t('title.EnterNumberWithoutCountryCode') }}</small>
      <div v-if="errors.phone_no" class="invalid-feedback d-block">{{ getErrorMessage('phone_no') }}</div>
    </b-form-group>

    <b-button type="submit" variant="primary">Submit</b-button>
  </b-form>
</template>

<script>
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import countryCodes from '@/Util/countryCodes';

export default {
  name: 'BasePersonForm',
  props: {
    submitUrl: String,
    serverErrors: Object,
    existingData: Object,
  },
  data() {
    return {
      form: {
        name: '',
        email: '',
        address: '',
        phone_no: '',
        country_code: '+91',
      },
      validated: false,
      errors: {},
      countryCodes,
    };
  },
  computed: {
  },
  watch: {
    existingData: {
      immediate: true,
      handler(data) {
        if (!data) return;

        this.form.name = data.name || '';
        this.form.email = data.email || '';
        this.form.address = data.address || '';

        const fullPhone = data.phone_number || '';
        for (const code of this.countryCodes.map(c => c.value).sort((a, b) => b.length - a.length)) {
          if (fullPhone.startsWith(code)) {
            this.form.country_code = code;
            this.form.phone_no = fullPhone.slice(code.length);
            break;
          }
        }

      },
    },
    serverErrors: {
      immediate: true,
      handler(errors) {
        this.errors = { ...this.errors, ...errors };
      },
    },
  },
  methods: {
    clearError(field) {
      this.errors[field] = '';
    },
    getValidationState(field) {
      if (!this.validated) return null;
      if (this.errors[field]) return false;

      switch (field) {
        case 'email':
          return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email);
        case 'phone_no':
          return /^\d{6,15}$/.test(this.form.phone_no);
        default:
          return this.form[field] ? true : false;
      }
    },
    getErrorMessage(field) {
      const err = this.errors[field];
      return Array.isArray(err) ? err.join(', ') : err || '';
    },
    async handleSubmit() {
      this.validated = true;
      this.errors = {};

      const isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email);
      const isPhoneValid = /^\d{6,15}$/.test(this.form.phone_no);

      if (!this.form.name) this.errors.name = 'Name is required.';
      if (!isEmailValid) this.errors.email = 'Invalid email format.';
      if (!this.form.address) this.errors.address = 'Address is required.';
      if (!isPhoneValid) this.errors.phone_no = 'Phone number must be 6–15 digits.';

      if (Object.keys(this.errors).length > 0) return;

      try {
        const formData = new FormData();
        if (this.existingData?.id) formData.append('_method', 'PUT');

        formData.append('name', this.form.name);
        formData.append('email', this.form.email);
        formData.append('address', this.form.address);
        formData.append('phone_number', `${this.form.country_code}${this.form.phone_no}`);


        this.$emit('submit', formData);
      } catch (error) {
        this.handleServerErrors(error);
      }
    },
    handleServerErrors(error) {
      const rawErrors = error?.response?.data?.errors || error.data?.errors || {};
      const mappedErrors = {};

      for (const key in rawErrors) {
        switch (key) {
          case 'phone_number':
            mappedErrors.phone_no = rawErrors[key];
            break;
          default:
            mappedErrors[key] = rawErrors[key];
        }
      }

      this.errors = mappedErrors;
    },
  },
};
</script>

<style scoped>
.country-code-select {
  max-width: 150px;
}
</style>
