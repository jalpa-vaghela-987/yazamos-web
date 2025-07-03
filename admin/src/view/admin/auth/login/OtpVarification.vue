<template>
  <b-container fluid class="login-wrapper">
    <b-card :title="$t('login.title')" class="login-card">
      <!-- QR Code Section -->
      <div v-if="qrCodeUrl" class="mb-4 text-center">
        <img :src="qrCodeUrl" :alt="$t('login.scanQr')" class="qr-code mb-3" />
        <div class="manual-key-box">
          <strong>{{ $t("login.manualKey") }}:</strong>
          <span class="text-muted">{{ manualKey }}</span>
        </div>
      </div>

      <!-- OTP Input Fields -->
      <div class="d-flex justify-content-center mb-3" dir="ltr">
        <input
          v-for="(digit, index) in otpDigits"
          :key="index"
          :ref="(el) => (otpRefs[index] = el)"
          v-model="otpDigits[index]"
          type="text"
          inputmode="numeric"
          maxlength="1"
          class="otp-input mx-1"
          @input="onOtpInput(index)"
          @keydown.backspace="onOtpBackspace(index, $event)"
          @paste="onOtpPaste"
          :autofocus="index === 0"
        />
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="text-danger text-center mb-2">
        {{ errorMessage }}
      </div>

      <!-- Recovery Link -->
      <div class="text-center mb-3" v-if="!qrCodeUrl">
        <b-form-checkbox v-if="!showRecoveryLink" v-model="showRecoveryLink">
          {{ $t("login.lostAccess") }}
        </b-form-checkbox>
        <b-link
          v-if="showRecoveryLink"
          @click="sendQrCodeEmail"
          :class="{ 'd-none': isDisplayQrDisabled }"
          class="recovery-link"
        >
          {{ $t("login.sendQrToEmail") }}
        </b-link>
      </div>

      <!-- Verify Button -->
      <b-button
        variant="primary"
        block
        :disabled="isSubmitting"
        @click="verify2FA"
        class="auth-button"
      >
        <span v-if="isSubmitting">
          <b-spinner small></b-spinner> {{ $t("login.verifying") }}
        </span>
        <span v-else>{{ $t("login.verify") }}</span>
      </b-button>
    </b-card>
  </b-container>
</template>

<script>
import QRCode from "qrcode";
import { request } from "@/Util/Request";
import { setStorage } from "@/Util/auth";
import commonMixin from "@/Util/commonMixin";

export default {
  data() {
    return {
      otpDigits: Array(6).fill(""),
      otpRefs: [],
      errorMessage: "",
      isSubmitting: false,
      id: null,
      qrCodeUrl: null,
      manualKey: null,
      showRecoveryLink: false,
      isDisplayQrDisabled: false,
    };
  },
  mixins: [commonMixin],
  computed: {
    otp() {
      return this.otpDigits.join("");
    },
  },
  async created() {
    this.id = this.$route.query.id;
    this.manualKey = this.$route.query.manual;
    const rawQr = this.$route.query.qr;
    if (rawQr) {
      this.qrCodeUrl = await QRCode.toDataURL(rawQr);
    }
  },
  mounted() {
    this.focusOtp(0);
  },
  methods: {
    async sendQrCodeEmail() {
      try {
        // Example: Sending request to API endpoint to send QR via email
        await request({
          method: "post",
          url: `send-2fa-qr`,
          data: { id: this.id },
        });
        this.notifySuccess("QR code sent to your email.");
      } catch (error) {
        this.notifyError("Failed to send QR code. Please try again.");
      }
    },
    async verify2FA() {
      if (this.otp.length !== 6 || this.isSubmitting) return;
      this.isSubmitting = true;
      this.errorMessage = "";

      try {
        const { data } = await request({
          method: "post",
          url: `2fa/verify`,
          data: { id: this.id, otp: this.otp },
        });

        if (data) {
          setStorage("auth", data);
          await this.redirectingUserPage(data);
          this.notifySuccess("Login successfully.");
        }
      } catch (error) {
        if (error?.response?.status === 422 && error.response.data.errors) {
          this.errorMessage = Object.values(error.response.data.errors)[0][0];
        } else {
          this.errorMessage = this.$t("login.invalidOtp") || "Invalid OTP.";
        }
      } finally {
        this.isSubmitting = false;
      }
    },

    onOtpInput(index) {
      const val = this.otpDigits[index];

      if (/^\d$/.test(val)) {
        if (index < 5) this.focusOtp(index + 1);
      } else {
        this.otpDigits[index] = ""; // clear non-digit
      }

      // Auto-submit if last digit is entered and all fields are filled
      if (this.otp.length === 6 && this.otpDigits.every((d) => d !== "")) {
        this.verify2FA();
      }
    },

    onOtpBackspace(index, event) {
      if (!this.otpDigits[index] && event.key === "Backspace" && index > 0) {
        this.focusOtp(index - 1);
      }
    },

    onOtpPaste(event) {
      event.preventDefault();
      const paste = (event.clipboardData || window.clipboardData).getData("text");
      const digits = paste.replace(/\D/g, "").slice(0, 6).split("");

      digits.forEach((digit, i) => {
        if (i < 6) this.$set(this.otpDigits, i, digit);
      });

      this.$nextTick(() => {
        const nextIndex = Math.min(digits.length, 5);
        this.focusOtp(nextIndex);

        if (digits.length === 6) {
          this.verify2FA();
        }
      });
    },

    focusOtp(index) {
      const ref = this.otpRefs[index];
      if (ref && typeof ref.focus === "function") {
        ref.focus();
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

.otp-input {
  width: 40px;
  height: 50px;
  font-size: 24px;
  text-align: center;
  border: 2px solid #ccc;
  border-radius: 8px;
  outline: none;
  background-color: #fff;
}
.otp-input:focus {
  border-color: #007bff;
  box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
}
.text-danger {
  font-size: 14px;
}
</style>
