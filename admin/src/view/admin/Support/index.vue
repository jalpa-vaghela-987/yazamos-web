<template>
  <b-card>
    <h4>{{ $t("title.ContactSupport") }}</h4>
    <b-form @submit.prevent="submitForm">
      <b-form-group :label="$t('title.YourName')">
        <b-form-input v-model="form.name" required></b-form-input>
      </b-form-group>
      <b-form-group :label="$t('title.YourEmail')">
        <b-form-input type="email" v-model="form.email" required></b-form-input>
      </b-form-group>
      <b-form-group :label="$t('title.YourMessage')">
        <b-form-textarea v-model="form.message" rows="4" required></b-form-textarea>
      </b-form-group>

      <div class="d-flex justify-content-between">
        <b-button type="submit" variant="primary" :disabled="loading">
          {{ loading ? $t("title.Sending") : $t("title.SendMessage") }}
        </b-button>
        <span v-if="success" class="text-success">{{ $t("title.MessageSent") }}</span>
      </div>
    </b-form>
  </b-card>
</template>

<script>
import { request } from "@/Util/Request";

export default {
  name: "SupportForm",
  data() {
    return {
      form: {
        name: "",
        email: "",
        message: "",
      },
      loading: false,
      success: false,
      errors: {},
    };
  },
  methods: {
    async submitForm() {
      this.loading = true;
      this.errors = {};
      try {
        await request({
          method: "post",
          url: "/support/message",
          data: this.form,
        });

        this.success = true;
        this.form = { name: "", email: "", message: "" };
        this.$bvToast.toast("Message sent successfully", {
          variant: "success",
          solid: true,
        });
      } catch (err) {
        if (err?.data?.errors) {
          this.errors = err.data.errors;
        }

        this.$bvToast.toast(err?.data?.message || "Something went wrong", {
          variant: "danger",
          solid: true,
        });
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
