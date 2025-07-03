<template>
    <b-container class="mt-5">
      <b-card title="Login" class="mx-auto" style="max-width: 400px;">
        <b-form v-if="!show2FA" @submit.prevent="login">
          <b-form-group label="Email" label-for="email-input">
            <b-form-input id="email-input" type="email" v-model="formFields.email" placeholder="Enter email"
              @input="removeError('email')"></b-form-input>
          </b-form-group>
          <b-form-invalid-feedback :state="!formErrors.has('email')">
            {{ formErrors.first('email') }}
          </b-form-invalid-feedback>
  
          <b-form-group label="Password" label-for="password-input">
            <b-form-input id="password-input" type="password" v-model="formFields.password" placeholder="Enter password"
              @input="removeError('password')"></b-form-input>
          </b-form-group>
          <b-form-invalid-feedback :state="!formErrors.has('password')">
            {{ formErrors.first('password') }}
          </b-form-invalid-feedback>
          <div class="d-flex justify-content-end mt-1">
            <b-link @click="goToResetPassword" class="small">Forgot Password?</b-link>
          </div>
          <b-button type="submit" class="mt-2" variant="primary" block>Login</b-button>
        </b-form>
  
        <b-form v-else @submit.prevent="verify2FA">
          <b-form-group label="Enter 2FA Code">
            <b-form-input v-model="twoFAFormFields.otp" @input="removeError('otp')"></b-form-input>
          </b-form-group>
          <b-form-invalid-feedback :state="!formErrors.has('otp')">
            {{ formErrors.first('otp') }}
          </b-form-invalid-feedback>
  
          <b-button type="submit" class="mt-2" variant="success">Verify</b-button>
        </b-form>
      </b-card>
    </b-container>
  </template>
  
  <script>
  import { getAuthUser, setStorage } from "@/Util/auth";
  import { mapGetters } from "vuex";
  import { request } from "@/Util/Request";
  import Error from "@/Util/Error";
  import commonMixin from "@/Util/commonMixin";
  import { reactive } from "vue";
  
  const FORM_STATE = {
    email: null,
    password: null
  };
  
  const TWOFA_FORM_STATE = {
    otp: null,
    id: null
  };
  
  export default {
    data() {
      return {
        isSubmitLogin: false,
        show2FA: false,
        formFields: { ...FORM_STATE },
        twoFAFormFields: { ...TWOFA_FORM_STATE },
        formErrors: new Error({}),
        twoFACode: null
      }
    },
    mixins: [commonMixin],
    methods: {
      goToResetPassword() {
        this.$router.push({ name: 'ResetPassword' }); 
      },
      async removeError(key) {
        if (typeof key === `object`) {
          for (let i = 0; i < key.length; i++) {
            this.formErrors.remove(key[i]);
          }
        } else {
          this.formErrors.remove(key);
        }
      },
      async login() {
        try {
          this.isSubmitLogin = true;
          const response = await request({
            method: 'post',
            url: `/admin/login`,
            data: this.formFields
          });
  
          let { data } = response;
          if (data.user.two_factor_enabled) {
            this.show2FA = true;
            this.twoFAFormFields.id = data.user.id;
          } else {
            await this.redirectingUserPage(data);
          }
          this.notifySuccess('Login successfully.');
          this.isSubmitLogin = false;
        } catch (error) {
          this.isSubmitLogin = false;
          if (error.request && error.request.status && error.request.status === 422) {
            this.formErrors = new Error(JSON.parse(error.request.responseText).errors);
            return false;
          } else {
            this.notifyError();
          }
        }
      },
      async verify2FA() {
        try {
          this.isSubmitLogin = true;
          const response = await request({
            method: 'post',
            url: `2fa/verify`,
            data: this.twoFAFormFields
          });
  
          let { data } = response;
          if (data) {
            await this.redirectingUserPage(data);
          }
          this.isSubmitLogin = false;
          this.notifySuccess('Login successfully.');
        } catch (error) {
          this.isSubmitLogin = false;
          if (error.request && error.request.status && error.request.status === 422) {
            this.formErrors = new Error(JSON.parse(error.request.responseText).errors);
            return false;
          } else {
            this.notifyError();
          }
        }
      }
    }
  }
  </script>
  