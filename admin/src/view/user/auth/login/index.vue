<template>
  <b-container fluid class="login-wrapper">
    <b-card :title="$t('login.title')" class="login-card">
      <b-form @submit.prevent="submitForm">
        <!-- Phone Number Input -->
        <template v-if="loginType === 'phone'">
          <b-form-group
            :label="$t('login.phoneNumber')"
            label-for="phone-input"
          >
            <vue-tel-input
              :class="formErrors.has('phone') ? 'error-input' : ''"
              v-model="formFields.phone"
              mode="international"
              v-bind="phone.options"
              :autoFormat="false"
              validCharactersOnly
              @focus="resetError"
              @input="onPhoneInput"
              @keydown.native="checkEnterToSubmit"
            />
            <b-form-invalid-feedback :state="!formErrors.has('phone')">
              {{ formErrors.first("phone") }}
            </b-form-invalid-feedback>
          </b-form-group>
          <div class="text-center mt-2">
            <a href="#" @click.prevent="loginType = 'email'" class="switch-login-type">
              {{ $t('login.switchToEmail') }}
            </a>
          </div>
        </template>

        <!-- Email Input -->
        <template v-else>
          <b-form-group
            :label="$t('login.email')"
            label-for="email-input"
          >
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
          <div class="text-center mt-2">
            <a href="#" @click.prevent="loginType = 'phone'" class="switch-login-type">
              {{ $t('login.switchToPhone') }}
            </a>
          </div>
        </template>

        <b-button
          type="submit"
          class="mt-3"
          variant="primary"
          block
          :disabled="loading || (loginType === 'phone' && !phone.inputValue.valid)"
        >
          <b-spinner small label="Sending..." class="mr-2" v-if="loading"></b-spinner>
          {{ loading ? $t("login.sendingOtp") : $t("login.sendOtp") }}
        </b-button>
      </b-form>
    </b-card>
  </b-container>
</template>

<script>
import { request } from "@/Util/Request";
import Error from "@/Util/Error";
import commonMixin from "@/Util/commonMixin";
import { VueTelInput } from 'vue-tel-input';
import 'vue-tel-input/dist/vue-tel-input.css';
import { reactive } from 'vue';

const phone = reactive({
    value: "",
    isBlurred: false,
    inputValue: {
        formatted: "",
        valid: false,
    },
    options: {
        autoFormat: true,
        inputOptions: {
            showDialCode: false,
            showDialCodeInList: true,
        },
        mode: "international",
        validCharactersOnly: true,
    },
});

export default {
  components: {
    VueTelInput
  },
  data() {
    return {
      loginType: 'phone', // Default to phone login
      formFields: {
        email: "",
        phone: ""
      },
      phone: phone,
      formErrors: new Error({}),
      loading: false
    };
  },
  mounted() {
    this.autoFillCountryCodeAndPhone();
  },
  mixins: [commonMixin],
  methods: {
    autoFillCountryCodeAndPhone() {
      // Check if URL contains countryCode and phoneNumber params
      const urlParams = new URLSearchParams(window.location.search);
      const phoneNumberParam = urlParams.get("phone");
      const rawCountryCodeParam = urlParams.get("country_code") || "";

      const cleanedCountryCodeParam = rawCountryCodeParam.replace(/\s+/g, ""); // Remove all spaces
      const formattedCountryCode = cleanedCountryCodeParam.startsWith("+")
        ? cleanedCountryCodeParam
        : `+${cleanedCountryCodeParam}`;

      if (phoneNumberParam && formattedCountryCode) {
        // Combine country code and phone number for vue-tel-input
        const fullPhoneNumber = `${formattedCountryCode}${phoneNumberParam}`;
        this.formFields.phone = `${phoneNumberParam}`;

        // Set the phone input value
        this.phone.value = fullPhoneNumber;
        this.phone.inputValue = {
          formatted: fullPhoneNumber,
          valid: true,
          nationalNumber: phoneNumberParam,
          countryCallingCode: formattedCountryCode.replace('+', ''),
        };
      }
    },
    onPhoneInput(formattedNumber, input) {
      this.phone.inputValue = input;
      if (input && input.nationalNumber) {
        setTimeout(() => {
          if(formattedNumber.startsWith('+61')) {
            input.countryCallingCode = 61;
            input.countryCode = 'AU';
          }
          this.formFields.phone = input.nationalNumber;
        }, 0);
      }
    },
    resetError() {
      this.formErrors = new Error({});
    },
    checkEnterToSubmit(event) {
      if (event.keyCode == 13 || event.key === "Enter") {
        this.submitForm();
      }
    },
    removeError(key) {
      this.formErrors.remove(key);
    },
    async submitForm() {
      if (this.loginType === 'phone' && !this.phone.inputValue.valid) {
        this.formErrors = new Error({
          phone: ['Please enter a valid phone number']
        });
        return;
      }

      this.loading = true;
      try {
        const data = this.loginType === 'phone'
          ? {
              phone: this.phone.inputValue.nationalNumber,
              country_code: `+${this.phone.inputValue.countryCallingCode}`
            }
          : { email: this.formFields.email };

        const response = await request({
          method: "post",
          url: `/login`,
          data
        });

        const userId = response.user_id;
        this.notifySuccess("OTP sent to your email.");

        this.$router.push({
          name: "OtpVerify",
          query: {
            userId,
            [this.loginType]: this.loginType === 'phone' ? this.phone.inputValue.nationalNumber : this.formFields.email,
            ...(this.loginType === 'phone' && { country_code: `+${this.phone.inputValue.countryCallingCode}` })
          },
        });
      } catch (error) {
        if (error.request && error.request.status === 422) {
          this.formErrors = new Error(JSON.parse(error.request.responseText).errors);
        } else {
          this.notifyError();
        }
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.otp-input {
  width: 48px;
  height: 58px;
  font-size: 26px;
  text-align: center;
  border: 2px solid #dcdcdc;
  border-radius: 8px;
  transition: border-color 0.3s, box-shadow 0.3s;
  outline: none;
  background-color: #fff;
}

.otp-input:focus {
  border-color: #007bff;
  box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
}

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

.b-modal .modal-content {
  border-radius: 10px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

/* Add these new styles for the switch link */
.switch-login-type {
  color: #007bff;
  text-decoration: none;
  font-size: 0.9rem;
}

.switch-login-type:hover {
  color: #0056b3;
  text-decoration: underline;
}

/* Add these new styles for vue-tel-input */
:deep(.vue-tel-input) {
  border-radius: 4px;
  border: 1px solid #ced4da;
}

:deep(.vue-tel-input.error-input) {
  border-color: #dc3545;
}

:deep(.vue-tel-input:focus-within) {
  border-color: #80bdff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

:deep(.vti__dropdown) {
  border-radius: 4px 0 0 4px;
}

:deep(.vti__input) {
  border-radius: 0 4px 4px 0;
  border: none;
  padding: 0.375rem 0.75rem;
}

:deep(.vti__dropdown-list) {
  max-height: 200px;
  overflow-y: auto;
}
</style>
