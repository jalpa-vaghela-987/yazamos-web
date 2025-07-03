<template>
  <b-container>
    <h2 class="mb-4">Add Tenant</h2>
    <b-form @submit.prevent="handleSubmit" :validated="validated" novalidate>
      <!-- Name -->
      <b-form-group label="Name" :state="getValidationState('name')" :invalid-feedback="getErrorMessage('name')">
        <b-form-input v-model="form.name" @input="clearError('name')" required />
      </b-form-group>

      <!-- Email -->
      <b-form-group label="Email" :state="getValidationState('email')" :invalid-feedback="getErrorMessage('email')">
        <b-form-input v-model="form.email" type="email" @input="clearError('email')" required />
      </b-form-group>

      <!-- Address -->
      <b-form-group label="Address" :state="getValidationState('address')" :invalid-feedback="getErrorMessage('address')">
        <b-form-input v-model="form.address" @input="clearError('address')" required />
      </b-form-group>

      <!-- Phone No -->
      <b-form-group label="Phone No" :state="getValidationState('phone_no')" :invalid-feedback="getErrorMessage('phone_no')">
        <b-input-group>
          <b-form-select v-model="form.country_code" :options="countryCodes" class="country-code-select" @change="clearError('phone_no')" required />
          <b-form-input v-model="form.phone_no" type="tel" @input="clearError('phone_no')" required />
        </b-input-group>
        <small class="text-muted">Enter number without country code (6–15 digits).</small>
      </b-form-group>

      <!-- Profile Photo -->
      <b-form-group label="Profile Photo" :state="getValidationState('profile_photo')" :invalid-feedback="getErrorMessage('profile_photo')">
        <b-form-file @change="handleFileUpload" accept="image/*" browse-text="Browse" placeholder="Choose an image..." />
      </b-form-group>

      <!-- Permissions -->
      <b-form-group label="Permissions">
        <v-select v-model="form.permissions" :options="permissionsWithSelectAll" :multiple="true" label="name" track-by="id" placeholder="Select permissions" @input="handlePermissionsChange" />
        <div v-if="errors.permissions" class="invalid-feedback d-block">{{ errors.permissions }}</div>
      </b-form-group>

      <!-- Submit -->
      <b-button type="submit" variant="primary">Submit</b-button>
    </b-form>
  </b-container>
</template>

<script>
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import { request } from '@/Util/Request'
import countryCodes from '@/Util/countryCodes'

export default {
  name: 'TenantCreate',
  components: { vSelect },
  data() {
    return {
      form: {
        name: '',
        email: '',
        address: '',
        country_code: '+91',
        phone_no: '',
        permissions: [],
        profile_photo: null,
      },
      errors: {},
      validated: false,
      permissionsOptions: [],
      selectAllLabel: 'Select All',
      countryCodes,
    }
  },
  computed: {
    permissionsWithSelectAll() {
      return [
        { id: 'all', name: this.selectAllLabel },
        ...this.permissionsOptions
      ]
    }
  },
  mounted() {
    this.fetchPermissions()
  },
  methods: {
    async fetchPermissions() {
      try {
        const { data } = await request({ url: '/get-all-permission' })
        this.permissionsOptions = data.filter(p => p.id !== 'all')
      } catch (err) {
        console.error('Failed to load permissions:', err)
      }
    },
    handlePermissionsChange(selected) {
      const all = selected.some(p => p.id === 'all')
      this.form.permissions = all
        ? [...this.permissionsOptions]
        : selected.filter(p => p.id !== 'all')
    },
    handleFileUpload(e) {
      this.form.profile_photo = e.target.files[0]
      this.clearError('profile_photo')
    },
    clearError(field) {
      this.$set(this.errors, field, null)
    },
    getValidationState(field) {
      if (!this.validated) return null
      if (this.errors[field]) return false
      if (field === 'email') {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email)
      }
      if (field === 'phone_no') {
        return /^\d{6,15}$/.test(this.form.phone_no)
      }
      if (field === 'name' || field === 'address' || field === 'profile_photo') {
        return this.form[field] ? true : false
      }
      return null
    },
    getErrorMessage(field) {
      const e = this.errors[field]
      return Array.isArray(e) ? e.join(', ') : e || ''
    },
    async handleSubmit() {
      this.validated = true

      // client-side checks
      const emailOK = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email)
      const phoneOK = /^\d{6,15}$/.test(this.form.phone_no)
      if (
        !this.form.name ||
        !emailOK ||
        !this.form.address ||
        !phoneOK ||
        !this.form.profile_photo
      ) {
        if (!this.form.name) this.$set(this.errors, 'name', 'Name is required.')
        if (!emailOK) this.$set(this.errors, 'email', 'Invalid email format.')
        if (!this.form.address) this.$set(this.errors, 'address', 'Address is required.')
        if (!phoneOK) this.$set(this.errors, 'phone_no', 'Phone must be 6–15 digits.')
        if (!this.form.profile_photo) this.$set(this.errors, 'profile_photo', 'Profile photo is required.')
        return
      }

      const fd = new FormData()
      fd.append('name', this.form.name)
      fd.append('email', this.form.email)
      fd.append('address', this.form.address)
      fd.append('phone_number', `${this.form.country_code}${this.form.phone_no}`)
      fd.append('profile_photo', this.form.profile_photo)
      this.form.permissions.forEach((p, i) => fd.append(`permission[${i}]`, p.id))

      try {
        await request({
          method: 'post',
          url: '/tenant',
          data: fd,
          headers: { 'Content-Type': 'multipart/form-data' }
        })
        this.$router.push({
          name: this.$global.hasRole('super admin')
            ? 'AdminTenantIndex'
            : 'TenantIndex'
        })
      } catch (err) {
        if (err?.data?.errors) {
          const svc = err.data.errors
          const norm = {}
          for (const k in svc) {
            if (k === 'phone_number') norm.phone_no = svc[k]
            else if (k === 'permission') norm.permissions = svc[k]
            else norm[k] = svc[k]
          }
          this.errors = norm
        } else {
          this.$bvToast.toast('Something went wrong', { variant: 'danger' })
        }
      }
    }
  }
}
</script>

<style scoped>
.is-invalid {
  border-color: #dc3545 !important;
}

.invalid-feedback {
  color: #dc3545;
}

.country-code-select {
  max-width: 150px;
}
</style>
