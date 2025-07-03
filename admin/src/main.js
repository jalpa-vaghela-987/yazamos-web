import Vue from 'vue'
import _ from 'lodash';
import store from './store';
import App from './App.vue';
import './styles/main.scss';
import { i18n } from './Localization';
import router from './router';
import 'bootstrap/dist/css/bootstrap.css';
import '@fortawesome/fontawesome-free/css/all.css';
import NotifyMixin from "@/Util/NotifyMixin";

// Correct Toast Plugin import
import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-default.css';

import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { dateFormat, handleSyncRequestLoader, hasRole } from "@/Util/auth";
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import 'vue-select/dist/vue-select.css';
import vSelect from 'vue-select';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';
import Treeselect from "@riophae/vue-treeselect";
require('./styles/bootstrap/css/bootstrap.css');
import CrudTable from "@/components/CrudTable";
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'bootstrap-icons/font/bootstrap-icons.css';

// Register components
Vue.component('v-select', vSelect);
Vue.component('treeselect', Treeselect);
Vue.component('crud-table', CrudTable);

i18n.locale = store.state.locale;

Vue.config.productionTip = false;

// Register plugins
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
const options = {
    timeout: 6000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
};
Vue.use(VueToast, options);
Vue.component('crud-table', CrudTable);

// ✅ Correct way to set default toast config for vue-toast-notification
Vue.use(VueToast, {
  position: 'top-right',
});

Vue.prototype._ = _;
Vue.prototype.$global = {
  dateFormat: (date, format = 'DD/MM/YYYY') => (date ? dateFormat(date, format) : null),
  hasRole: (role) => hasRole(role),
};

Vue.mixin(NotifyMixin);

// BootstrapVue `$bvToast` default config override
Vue.mixin({
  created() {
    if (this.$bvToast) {
      this.$bvToast.defaults = {
        autoHideDelay: 5000,
        solid: true,
        appendToast: false,
      };
    }
  }
});

// Permission directive
Vue.directive('can', {
  inserted(el, binding, vnode) {
    const permissions = vnode.context.$store.state.permissions;
    if (!permissions.includes(binding.value) && binding.value !== "no permission") {
      el.parentNode && el.parentNode.removeChild(el);
    }
  }
});

// Initialize Vue
new Vue({
  store,
  i18n,
  router,
  render: h => h(App),
  created() {
    handleSyncRequestLoader(store, process.env.VUE_APP_API_URL);
  },
}).$mount('#app');
