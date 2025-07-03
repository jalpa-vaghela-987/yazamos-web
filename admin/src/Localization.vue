<template>
  <div class="app-background">
    <div v-if="authChecked">
      <div v-if="!isUnAuthorized && !shouldHideHeader">
        <AppHeader />
        <div class="main-content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <router-view :locale="locale" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="col-12">
        <router-view :locale="locale" />
      </div>
    </div>
  </div>
</template>

<script>
import Vue from "vue";
import VueI18n from "vue-i18n";
import english from "./locales/en-US";
import Hebrew from "./locales/he-IL";
import AppHeader from "./components/AppHeader.vue";
import { mapState } from "vuex";

const locales = {
  "en-US": english,
  "he-IL": Hebrew,
};

Vue.use(VueI18n);

const rtlLanguages = ["he-IL"];

export const i18n = new VueI18n({
  locale: "en-US",
  fallbackLocale: "en-US",
  silentTranslationWarn: true,
  messages: {
    "en-US": locales["en-US"].messages,
    "he-IL": locales["he-IL"].messages,
  },
});

export default {
  name: "Localization",
  components: {
    AppHeader,
  },
  data() {
    return {
      authChecked: false,
    };
  },
  computed: {
    ...mapState(["locale", "isUnAuthorized"]),
    shouldHideHeader() {
      const routesWithoutHeader = [
        "AdminLogin",
        "register",
        "forgot-password",
        "Login",
        "Signup",
        'OtpVerify',
        'login-2fa',
        "SignupSuccess",
      ];
      return routesWithoutHeader.includes(this.$route.name);
    },
  },
  mounted() {
    const isRtl = rtlLanguages.includes(this.locale);
    document.documentElement.setAttribute("dir", isRtl ? "rtl" : "ltr");
    document.documentElement.setAttribute("lang", this.locale);
    setTimeout(() => {
      this.authChecked = true;
    }, 100);
  },
  watch: {
    locale(newLocale) {
      const isRtl = rtlLanguages.includes(newLocale);
      document.documentElement.setAttribute("dir", isRtl ? "rtl" : "ltr");
      document.documentElement.setAttribute("lang", newLocale);
    },
  },
};
</script>

<style scoped>
.app-background {
  background-image: url("@/assets/background.jpg");
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: fixed;
  min-height: 100vh;
  width: 100%;
}
.main-content {
  margin-left: 250px;
  background-color: rgb(253, 253, 253);
  min-height: calc(100vh - 60px);
  transition: margin-left 0.3s ease-in-out;
}

.container-fluid {
  padding: 1rem;
}
@media (min-width: 992px) {
    [dir="rtl"] .main-content {
        margin-right: 250px;
        margin-left: 0;
    }
}
[dir="rtl"] .container-fluid {
    text-align: right;
}

@media (max-width: 991.98px) {
  .main-content {
    margin-left: 0;
  }
}
</style>
