<template>
  <div class="d-flex justify-content-center align-items-center min-vh-100">
    <b-container>
      <b-card title="Sign Up" class="mx-auto" style="max-width: 500px; width: 100%">
        <b-form
          @submit.prevent="handleSubmit"
          :validated="validated"
          novalidate
          class="m-5"
        >
          <b-form-group label="Full Name">
            <b-form-input
              v-model="formFields.name"
              @input="removeError('name')"
              required
            />
            <b-form-invalid-feedback :state="!formErrors.has('name')">
              {{ formErrors.first("name") }}
            </b-form-invalid-feedback>
          </b-form-group>

          <b-form-group label="Email">
            <b-form-input
              v-model="formFields.email"
              type="email"
              @input="removeError('email')"
              required
            />
            <b-form-invalid-feedback :state="!formErrors.has('email')">
              {{ formErrors.first("email") }}
            </b-form-invalid-feedback>
          </b-form-group>

          <b-form-group label="Phone Number">
            <vue-tel-input
              :class="formErrors.has('phone_number') ? `error-input` : ``"
              v-model="formFields.phone_number"
              v-bind="phone.options"
              @input="onPhoneInput"
              @focus="resetError"
              @keydown.native="checkEnterToSendOtp"
              required
            />
            <b-form-invalid-feedback :state="!formErrors.has('phone_number')">
              {{ formErrors.first("phone_number") }}
            </b-form-invalid-feedback>

            <b-button
              v-if="!otpSent"
              @click="sendOtp"
              variant="primary"
              block
              class="mt-3"
              >Send OTP</b-button
            >

            <!-- Display OTP input group only if OTP has been sent -->
            <b-form-group v-if="otpSent" label="OTP">
              <b-form-input
                v-model="formFields.otp"
                @input="removeError('otp')"
                required
                class="mb-3"
              />
              <b-form-invalid-feedback :state="!formErrors.has('otp')">
                {{ formErrors.first("otp") }}
              </b-form-invalid-feedback>
              <b-button @click="verifyOtp" variant="info" block class="mt-3"
                >Verify OTP</b-button
              >
            </b-form-group>
          </b-form-group>
          <b-button v-if="otpVerified" type="submit" variant="primary" block class="mt-3"
            >Sign Up</b-button
          >
        </b-form>
      </b-card>
    </b-container>
  </div>
</template>

<script>
import countryCodes from "@/Util/countryCodes";
import Error from "@/Util/Error";
import { request } from "@/Util/Request";
import { VueTelInput } from "vue-tel-input";
import "vue-tel-input/dist/vue-tel-input.css";
import { reactive } from "vue";
import { setStorage } from "@/Util/auth";
import commonMixin from "@/Util/commonMixin";

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
  name: "SignupForm",
  components: {
    VueTelInput,
  },
  data() {
    return {
      formFields: {
        name: "",
        email: "",
        phone_number: "",
        otp: "",
        role: "",
      },
      phone: phone,
      formErrors: new Error({}),
      validated: false,
      otpSent: false,
      otpVerified: false,
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
      console.log("country_Coder", rawCountryCodeParam);

      const cleanedCountryCodeParam = rawCountryCodeParam.replace(/\s+/g, ""); // Remove all spaces
      const formattedCountryCode = cleanedCountryCodeParam.startsWith("+")
        ? cleanedCountryCodeParam
        : `+${cleanedCountryCodeParam}`;

      if (phoneNumberParam && formattedCountryCode) {
        // Combine country code and phone number for vue-tel-input
        const fullPhoneNumber = `${formattedCountryCode}${phoneNumberParam}`;
        this.formFields.phone_number = `${fullPhoneNumber}`;
      }
    },
    onPhoneInput(formattedNumber, input) {
      this.phone.inputValue = input;
      if (input && input.nationalNumber) {
        console.log(formattedNumber, input);
        setTimeout(() => {
          this.formFields.phone_number = input.nationalNumber;
        }, 0);
      }
    },
    resetError() {
      this.formErrors = new Error({});
    },
    async checkEnterToSendOtp(event) {
      if (event.keyCode == 13 || event.key === "Enter") {
        this.sendOtp();
      }
    },
    handleInput(codeField, event) {
      const value = event.target.value;
      if (!/^\d$/.test(value)) {
        this.formFields.otp[codeField] = "";
        return;
      }
      this.formFields.otp[codeField] = value;

      const nextInput = event.target.nextElementSibling;
      if (nextInput && nextInput.tagName === "INPUT") {
        nextInput.focus();
      }
    },
    handleKeyDown(codeField, event) {
      const target = event.target;
      if (event.key == "Enter" || event.keyCode == 13) {
        this.$refs.submitBtn.focus();
        return;
      }

      if (event.key === "Backspace" && !this.formFields.otp[codeField]) {
        const previousInput = target.previousElementSibling;
        if (previousInput && previousInput.tagName === "INPUT") {
          previousInput.focus();
        }
      }
    },
    async sendOtp() {
      if (!this.phone.inputValue.valid) {
        this.formErrors = new Error({
          phone_number: ["Please enter a valid phone number"],
        });
        return;
      }

      try {
        const response = await request({
          method: "post",
          url: "/send-otp",
          data: {
            country_code: `+${this.phone.inputValue.countryCallingCode}`,
            phone_number: this.phone.inputValue.nationalNumber,
          },
        });
        if (response.status_code === 200) {
          this.otpSent = true;
          this.notifySuccess("OTP sent to your phone number.");
        }
      } catch (error) {
        console.error("Error sending OTP:", error);
        this.notifyError("Error sending OTP.");
      }
    },
    async verifyOtp() {
      try {
        const response = await request({
          method: "post",
          url: "signup/verify-otp",
          data: {
            country_code: `+${this.phone.inputValue.countryCallingCode}`,
            phone_number: this.phone.inputValue.nationalNumber,
            otp: this.formFields.otp,
          },
        });

        if (response.status_code === 200) {
          this.otpVerified = true;
        } else {
          this.notifyError("Invalid OTP. Please try again.");
        }
        this.notifySuccess("OTP verified successfully!");
      } catch (error) {
        this.notifyError("Error verifying OTP.");
      }
    },
    async handleSubmit() {
      try {
        if (!this.otpVerified) {
          this.notifyError("Please verify OTP before signing up.");
          return;
        }

        const urlParams = new URLSearchParams(window.location.search);
        const role = urlParams.get("role") || "admin";

        const response = await request({
          method: "post",
          url: `/signup/${role}`,
          data: {
            ...this.formFields,
            country_code: `+${this.phone.inputValue.countryCallingCode}`,
            phone_number: this.phone.inputValue.nationalNumber,
          },
        });

        this.notifySuccess("Signup successful!");
        // if (role === "entrepreneur") {
        //   this.$router.push({ name: "SignupSuccess" });
        // } else {
        const { data } = response;
        setStorage("auth", data);
        await this.redirectingUserPage(data);
        // }
      } catch (error) {
        if (error.request && error.request.status === 422) {
          this.formErrors = new Error(JSON.parse(error.request.responseText).errors);
        } else {
          this.notifyError();
        }
      }
    },
    removeError(key) {
      if (typeof key === `object`) {
        for (let i = 0; i < key.length; i++) {
          this.formErrors.remove(key[i]);
        }
      } else {
        this.formErrors.remove(key);
      }
    },
  },
};
</script>

<style scoped>
.country-code-select {
  max-width: 120px;
}
</style>
