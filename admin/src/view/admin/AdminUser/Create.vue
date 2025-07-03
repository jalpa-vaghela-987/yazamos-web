<template>
  <div>
    <Breadcrumbs />
    <b-container>
      <h2 class="mb-4">{{ $t("title.add_company_admin") }}</h2>
      <LoaderSpinner v-if="loader" />
      <b-form @submit.prevent="handleSubmit" :validated="validated" novalidate>
        <!-- Company Name -->
        <b-form-group
          :label="$t('title.company_name')"
          :state="getValidationState('company_name')"
          :invalid-feedback="getErrorMessage('company_name')"
        >
          <b-form-input
            v-model="form.company_name"
            @input="clearError('company_name')"
            required
          />
        </b-form-group>

        <!-- Profile Photo -->
        <b-form-group
          :label="$t('title.profile_photo')"
          :state="getValidationState('profile_photo')"
          :invalid-feedback="getErrorMessage('profile_photo')"
        >
          <b-form-file
            @change="handleFileUpload"
            accept="image/*"
            :browse-text="$t('title.browse')"
            :placeholder="$t('title.choose_image')"
          />
        </b-form-group>

        <!-- Name -->
        <b-form-group
          :label="$t('title.name')"
          :state="getValidationState('name')"
          :invalid-feedback="getErrorMessage('name')"
        >
          <b-form-input v-model="form.name" @input="clearError('name')" required />
        </b-form-group>

        <!-- Email -->
        <b-form-group
          :label="$t('title.email')"
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

        <!-- Phone Number -->
        <b-form-group
          :label="$t('title.PhoneNumber')"
          :state="getValidationState('phone_number')"
          :invalid-feedback="getErrorMessage('phone_number')"
        >
          <vue-tel-input
            :class="errors.phone_number ? 'error-input' : ''"
            v-model="phoneInput"
            mode="international"
            :autoFormat="false"
            valid-characters-only
            @input="onPhoneInput"
            @blur="validatePhone"
          />
        </b-form-group>

        <!-- Buttons -->
        <b-button type="submit" variant="primary" class="m-2">
          {{ $t("title.submit") }}
        </b-button>
        <b-button variant="secondary" @click="cancel" class="m-2">
          {{ $t("title.cancel") }}
        </b-button>
      </b-form>
    </b-container>
  </div>
</template>

<script>
import { request } from "@/Util/Request";
import countryCodes from "@/Util/countryCodes";
import Breadcrumbs from "@/components/Breadcrumbs.vue";
import { mapState } from "vuex";
import LoaderSpinner from "@/components/LoaderSpinner.vue";
import { VueTelInput } from "vue-tel-input";
import "vue-tel-input/dist/vue-tel-input.css";

export default {
  name: "Create",
  components: {
    Breadcrumbs,
    LoaderSpinner,
    VueTelInput,
  },
  data() {
    return {
      form: {
        name: "",
        email: "",
        country_code: "",
        phone_number: "",
        profile_photo: null,
        company_name: "",
      },
      errors: {},
      phoneInput: "",
      validated: false,
      phone: {
        options: {
          mode: "international",
          preferredCountries: ["il", "us", "gb"],
        },
      },
      countryCodes,
    };
  },
  computed: {
    ...mapState(["loader"]),
  },
  methods: {
    validatePhone() {
      if (!this.form.phone_number) {
        this.$set(this.errors, "phone_number", "Phone number is required.");
      }
    },
    onPhoneInput(value, phoneData) {
      console.log("onPhoneInput", phoneData);
      if (phoneData && phoneData.nationalNumber) {
        this.form.phone_number = phoneData.nationalNumber || phoneData.number;
        this.form.country_code = `+${phoneData.countryCallingCode}`;
      } else {
        this.form.phone_number = "";
        this.form.country_code = "";
      }
    },
    handlePhoneInput(phoneData) {
      console.log("phoneData", phoneData);
      this.form.phone_number = phoneData.nationalNumber || phoneData.number;
      this.form.country_code = phoneData.countryCallingCode || "";
      console.log("Updated phone number:", this.form.phone_number);
      console.log("Updated country code:", this.form.country_code);
      this.clearError("phone_number");
    },
    cancel() {
      this.$router.push({
        name: "AdminUserIndex",
      });
    },
    handleFileUpload(e) {
      this.form.profile_photo = e.target.files[0];
      this.clearError("profile_photo");
    },
    clearError(field) {
      this.$set(this.errors, field, null);
    },
    getValidationState(field) {
      if (!this.validated) return null;
      if (this.errors[field]) return false;
      if (field === "email") {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email);
      }
      if (["name", "company_name", "phone_number"].includes(field)) {
        return this.form[field] ? true : false;
      }
      if (field === "profile_photo") return true;
      return null;
    },
    getErrorMessage(field) {
      const e = this.errors[field];
      return Array.isArray(e) ? e.join(", ") : e || "";
    },
    async handleSubmit() {
      this.validated = true;
      console.log("Submitting form:", this.form);
      const emailOK = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email);
      if (
        !this.form.name ||
        !emailOK ||
        !this.form.company_name ||
        !this.form.profile_photo ||
        !this.form.phone_number
      ) {
        if (!this.form.name) this.$set(this.errors, "name", "Name is required.");
        if (!emailOK) this.$set(this.errors, "email", "Invalid email format.");
        if (!this.form.company_name)
          this.$set(this.errors, "company_name", "Company Name is required.");
        if (!this.form.profile_photo)
          this.$set(this.errors, "profile_photo", "Profile photo is required.");
        if (!this.form.phone_number)
          this.$set(this.errors, "phone_number", "Phone number is required.");
        return;
      }

      const fd = new FormData();
      fd.append("name", this.form.name);
      fd.append("email", this.form.email);
      fd.append("company_name", this.form.company_name);
      fd.append("country_code", this.form.country_code);
      fd.append("phone_number", this.form.phone_number);
      if (this.form.profile_photo) {
        fd.append("profile_photo", this.form.profile_photo);
      }

      try {
        this.$store.dispatch("setLoader", true);
        await request({
          method: "post",
          url: "/admin/admin-user",
          data: fd,
          headers: { "Content-Type": "multipart/form-data" },
        });
        this.$router.push({
          name: "AdminUserIndex",
        });
      } catch (err) {
        if (err?.data?.errors) {
          const norm = {};
          for (const k in err.data.errors) {
            norm[k] = err.data.errors[k];
          }
          this.errors = norm;
        } else {
          this.$bvToast.toast("Something went wrong", { variant: "danger" });
        }
      } finally {
        this.$store.dispatch("setLoader", false);
      }
    },
  },
};
</script>

<style>
.country-code-select {
  max-width: 150px;
}
.error-input {
  border-color: #dc3545;
}
</style>
