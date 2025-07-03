<template>
  <div class="container mt-4" v-if="user">
    <b-card :title="$t('title.UserProfile')" class="mb-4">
      <div class="d-flex align-items-center">
        <div>
          <h5>{{ user?.name }}</h5>
          <p class="mb-0 text-muted">{{ user?.email }}</p>
           <p class="mb-0 text-muted">{{ user?.address }}</p>
        </div>
      </div>
    </b-card>

    <b-card>
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h6>{{ $t('title.TwoFactorAuthentication') }}</h6>
          <p class="mb-0" v-if="user.two_factor_enabled">
  {{ $t('title.twoFactorEnabled') }}
</p>

<p class="mb-0" v-else>
  {{ $t('title.twoFactorDisabled') }}
</p>

        </div>

        <b-button
          :variant="user.two_factor_enabled ? 'danger' : 'success'"
          @click="toggle2FA"
        >
          {{ user.two_factor_enabled ? 'Disable 2FA' : 'Enable 2FA' }}
        </b-button>
      </div>
    </b-card>
    <b-card v-if="user.two_factor_enabled">
      <div class="text-center">
    <h5>{{ $t('title.scanQR') }}</h5>
    <img v-if="qr_code_url" :src="qr_code_url" alt="2FA QR Code" class="img-fluid my-3" />
    <p class="text-muted">{{ $t('title.secretKey') }} {{ secret }}</p>
</div>

    </b-card>

      <ChangePassword/>
  </div>
</template>

<script>
import {mapGetters, mapState} from "vuex";
import { request } from "@/Util/Request";
import Error from "@/Util/Error";
import { reactive } from "vue";
import ChangePassword from "../settings/changePassword.vue"
import QRCode from 'qrcode';

export default {
    components: {
        ChangePassword
    },
  data() {
    return {
        qr_code_url: null,
        secret: null,
        user: null
    };
  },
  mounted() {
    this.getProfile();
  },
  methods: {
    async getProfile() {
        try {
            const response = await request({
                method: 'get',
                url: `profile`,
            });
            let {data} = response;
            this.user = data;
            if(this.user?.two_factor_enabled) {
                this.generateQrCode();
            }
        } catch (error) {
            console.log(error);
        }
    },
    async generateQrCode() {
        this.secret = this.user.google2fa_secret;
        const otpauth = `otpauth://totp/${encodeURIComponent(process.env.VUE_APP_TITLE)} (${this.user.email})?secret=${this.secret}&issuer=${process.env.VUE_APP_TITLE}&algorithm=SHA1&digits=6&period=30`;
        try {
            this.qr_code_url = await QRCode.toDataURL(otpauth);
        } catch (err) {
            console.error('QR Code generation failed:', err);
        }
    },
    async toggle2FA() {
      try {
        let url = (this.user.two_factor_enabled) ? `2fa/disable` : `2fa/enable`;
        const response = await request({
            method: 'post',
            url: url,
        });
        let {data} = response;
        if(data) {
            if(data.qr_code_url) {
                this.qr_code_url = await QRCode.toDataURL(data.qr_code_url);
                this.secret = data.manual_key;
                this.user.two_factor_enabled = true;
            } else {
                this.user.two_factor_enabled = false;
            }
        }
        this.notifySuccess(`2FA ${this.user.two_factor_enabled ? 'enabled' : 'disabled'} successfully.`);
      } catch (error) {
        this.notifyError();
      }
    },
  },
};
</script>

<style scoped>
h5 {
  margin-bottom: 0;
}
</style>
