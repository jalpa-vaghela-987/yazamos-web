<template>
  <b-container fluid class="login-wrapper">
    <b-card title="Enter OTP" class="login-card">
      <div class="d-flex justify-content-center">
        <input
          v-for="(digit, index) in otpDigits"
          :key="index"
          ref="otpInput"
          v-model="otpDigits[index]"
          type="text"
          maxlength="1"
          class="otp-input mx-1"
          @input="onOtpInput(index)"
          @keydown.backspace="onOtpBackspace(index, $event)"
          @paste="onOtpPaste($event)"
        />
      </div>

      <!-- Show error message -->
      <div v-if="errorMessage" class="text-danger text-center mt-2">
        {{ errorMessage }}
      </div>

      <b-button
        variant="primary"
        class="mt-3"
        block
        :disabled="isVerifying"
        @click="submitOtp"
      >
        <b-spinner small label="Verifying..." class="mr-2" v-if="isVerifying" />
        {{ isVerifying ? "Verifying OTP..." : $t("login.verifyOtp") }}
      </b-button>
      <!-- Resend OTP Button -->
      <b-button
        variant="link"
        class="mt-2 p-0"
        block
        :disabled="isResending"
        @click="resendOtp"
      >
        <b-spinner small label="Resending..." class="mr-2" v-if="isResending" />
        {{ isResending ? "Resending..." : "Resend OTP" }}
      </b-button>
    </b-card>
  </b-container>
</template>

<script>
import { request } from "@/Util/Request";
import { setStorage } from "@/Util/auth";
import commonMixin from "@/Util/commonMixin";

export default {
  data() {
    return {
      isVerifying: false,
      isResending: false,
      userId: null,
      otpDigits: Array(6).fill(""),
      errorMessage: "",
    };
  },
  mixins: [commonMixin],
  computed: {
    otp() {
      return this.otpDigits.join("");
    },
  },
  mounted() {
    this.userId = this.$route.query.userId;
    if (!this.userId) {
      this.$router.push({ name: "Login" });
    }
    this.$nextTick(() => this.$refs.otpInput[0].focus());
  },
  methods: {
    async submitOtp() {
      if (this.isVerifying) return;

      this.isVerifying = true;
      this.errorMessage = "";

      try {
        const response = await request({
          method: "post",
          url: `/verify-otp`,
          data: {
            user_id: this.userId,
            otp: this.otp,
          },
        });

        const { data } = response;
        setStorage("auth", data);
        await this.redirectingUserPage(data);
        this.notifySuccess("Login successful.");
      } catch (error) {
        let message = "Something went wrong. Please try again.";

        if (error?.response?.status === 422 && error.response.data.errors) {
          const errors = error.response.data.errors;
          const firstField = Object.keys(errors)[0];
          message = errors[firstField][0];
        } else if (error.response?.data?.message) {
          message = error.response.data.message;
        }

        this.errorMessage = message;
      } finally {
        this.isVerifying = false;
      }
    },

    async resendOtp() {
      if (this.isResending) return;

      this.isResending = true;
      this.errorMessage = "";

      try {
        const response = await request({
          method: "post",
          url: `/resend-otp`,
          data: {
            user_id: this.userId,
          },
        });

        this.notifySuccess("OTP resent successfully.");
        // Optional for dev/test
        console.log("New OTP:", response.data.otp);
      } catch (error) {

        if (error?.response?.status === 422 && error.response.data.errors) {
          const errors = error.response.data.errors;
          const firstField = Object.keys(errors)[0];
          message = errors[firstField][0];
        } else if (error.response?.data?.message) {
          message = error.response.data.message;
        }

        // **Make sure the user sees this error**
        this.errorMessage = message;
      } finally {
        this.isResending = false;
      }
    },

    onOtpPaste(event) {
      const paste = (event.clipboardData || window.clipboardData).getData("text");
      const digits = paste.replace(/\D/g, "").slice(0, 6).split("");
      this.otpDigits = Array(6).fill("");

      digits.forEach((digit, i) => {
        if (i < 6) this.$set(this.otpDigits, i, digit);
      });

      this.$nextTick(() => {
        const nextIndex = Math.min(digits.length, 5);
        if (this.$refs.otpInput[nextIndex]) {
          this.$refs.otpInput[nextIndex].focus();
        }

        if (digits.length === 6) {
          this.submitOtp();
        }
      });

      event.preventDefault();
    },

    onOtpInput(index) {
      const val = this.otpDigits[index];
      if (val && index < 5) {
        this.$refs.otpInput[index + 1].focus();
      }

      if (this.otpDigits.every((digit) => digit.length === 1)) {
        this.submitOtp();
      }
    },

    onOtpBackspace(index, event) {
      if (!this.otpDigits[index] && index > 0 && event.key === "Backspace") {
        this.$refs.otpInput[index - 1].focus();
      }
    },
  },
};
</script>

<style scoped>
.text-danger {
  font-size: 14px;
}
.otp-input {
  width: 40px;
  height: 50px;
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
</style>
