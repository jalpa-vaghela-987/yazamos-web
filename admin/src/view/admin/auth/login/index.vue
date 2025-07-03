<template>
  <b-container fluid class="login-wrapper">
    <b-card :title="$t('login.title')" class="login-card">
      <b-form @submit.prevent="login">
        <b-form-group :label="$t('login.email')" label-for="email-input">
          <b-form-input
            id="email-input"
            type="email"
            v-model="formFields.email"
            :placeholder="$t('login.emailPlaceholder')"
            @input="removeError('email')"
          />
          <b-form-invalid-feedback :state="!formErrors.has('email')">
            {{ formErrors.first("email") }}
          </b-form-invalid-feedback>
        </b-form-group>

        <b-button
          type="submit"
          variant="primary"
          class="mt-2"
          :disabled="isSubmitLogin"
          block
        >
          <span v-if="isSubmitLogin">
            <b-spinner small></b-spinner> {{ $t("login.signingIn") }}
          </span>
          <span v-else>{{ $t("login.login") }}</span>
        </b-button>
      </b-form>
    </b-card>
  </b-container>
</template>

<script>
import { request } from "@/Util/Request";
import Error from "@/Util/Error";
import commonMixin from "@/Util/commonMixin";

export default {
  data() {
    return {
      formFields: { email: null },
      formErrors: new Error({}),
      isSubmitLogin: false,
    };
  },
  mixins: [commonMixin],
  methods: {
    removeError(key) {
      this.formErrors.remove(key);
    },
    async login() {
      try {
        this.isSubmitLogin = true;
        const { data } = await request({
          method: "post",
          url: `/admin/login`,
          data: this.formFields,
        });

        if (data.user.two_factor_enabled) {
          this.$router.push({
            name: "login-2fa",
            query: {
              id: data.user.id,
              qr: data.qr_code_url,
              manual: data.manual_key,
            },
          });
        } else {
          await this.redirectingUserPage(data);
        }
      } catch (error) {
        this.isSubmitLogin = false;
        if (error.request && error.request.status === 422) {
          this.formErrors = new Error(JSON.parse(error.request.responseText).errors);
        } else {
          this.notifyError();
        }
      }
    },
  },
};
</script>

<style scoped>
.login-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
}

.login-card {
  width: 100%;
  max-width: 400px;
  border-radius: 10px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  padding: 20px;
}
</style>
