<template>
  <div v-if="!isUnAuthorized">
    <!-- Top Navbar -->
    <b-navbar
      toggleable="lg"
      type="light"
      variant="white"
      class="shadow-sm fixed-top px-4"
    >
      <b-navbar-brand
        :to="{
          name:
            $global.hasRole('super admin') || $global.hasRole('admin')
              ? 'AdminDashboard'
              : 'Dashboard',
        }"
        class="d-flex align-items-center"
      >
        <img
          src="../assets/image.png"
          alt="Logo"
          class="img-fluid"
          style="max-height: 30px; width: auto; max-width: 100%"
        />
      </b-navbar-brand>

      <!-- Add mobile toggle button -->
      <b-button class="d-lg-none" variant="link" @click="toggleSidebar">
        <b-icon :icon="isSidebarOpen ? 'x' : 'list'" font-scale="1.5"></b-icon>
      </b-button>

      <!-- Desktop Logout Button and Language Switcher -->
      <b-navbar-nav class="d-none d-lg-flex align-items-center px-3 py-2">
        <!-- Language Switcher -->
        <b-dropdown right class="mx-2" no-caret toggle-class="p-0 border-0 bg-gray">
          <template #button-content>
            <div
              class="box image rounded-circle d-flex justify-content-center align-items-center"
              style="font-size: 18px"
            >
              <b-icon icon="translate"></b-icon>
            </div>
          </template>

          <b-dropdown-item
            class="notify-item"
            v-for="(entry, i) in languages"
            :key="`Lang${i}`"
            :value="entry"
            @click="setLanguage(entry.language, entry.title)"
          >
            <span class="align-middle">{{ entry.title }}</span>
          </b-dropdown-item>
        </b-dropdown>

        <!-- Logout Button -->
        <b-button
          class="text-white bg-gray border-0 px-4 py-2 rounded ml-2"
          @click="openLogoutModal"
        >
          <b-icon icon="box-arrow-right" class="mr-2"></b-icon>
        </b-button>
      </b-navbar-nav>
    </b-navbar>

    <!-- Sidebar and Main Content Wrapper -->
    <div class="d-flex" style="padding-top: 60px">
      <!-- Sidebar Backdrop -->
      <div v-if="isSidebarOpen" class="sidebar-backdrop" @click="toggleSidebar"></div>

      <!-- Sidebar -->
      <div id="sidebar" class="sidebar bg-white shadow-sm " :class="{ show: isSidebarOpen }">
        <div class="nav flex-column p-3 d-flex flex-column h-100">
          <!-- Navigation Links -->
          <div class="flex-grow-1">
            <div v-for="(link, index) in filteredNavLinks" :key="'navlink-' + index">
              <!-- Dropdown Menu -->
              <div v-if="link.children && link.children.length" class="nav-item mb-2">
                <b-button
                  v-b-toggle="'collapse-' + index"
                  class="w-100 text-left bg-white border-0 shadow-none d-flex align-items-center justify-content-between"
                  variant="light"
                >
                  <div class="d-flex align-items-center">
                    <b-icon :icon="link.icon" font-scale="1.2" class="mr-2"></b-icon>
                    {{ $t(link.name) }}
                  </div>
                  <i class="fa fa-angle-down"></i>
                </b-button>

                <b-collapse :id="'collapse-' + index" class="bg-white w-100">
                  <div class="p-2">
                    <router-link
                      v-for="(child, cIndex) in link.children"
                      :key="'child-' + index + '-' + cIndex"
                      :to="child.route"
                      class="nav-link d-block text-dark py-2 px-3"
                      @click.native="closeSidebarOnMobile"
                    >
                      {{ $t(child.name) }}
                    </router-link>
                  </div>
                </b-collapse>
              </div>

              <!-- Single Menu Item -->
              <router-link
                v-else
                :to="link.route"
                class="nav-link d-flex align-items-center text-dark mb-2"
                :key="'item-' + index"
                @click.native="closeSidebarOnMobile"
              >
                <b-icon :icon="link.icon" font-scale="1.2" class="mr-2"></b-icon>
                <span>{{ $t(link.name) }}</span>
              </router-link>
            </div>
          </div>

          <!-- Mobile Logout Button -->
          <div class="mt-auto pt-3 border-top d-lg-none">
            <b-dropdown right class="mx-2" no-caret
              toggle-class="p-0 border-0 bg-gray">
              <template #button-content>
                <div
                  class="box image rounded-circle d-flex justify-content-center align-items-center"
                  style="font-size: 18px"
                >
                  <b-icon icon="translate"></b-icon>
                </div>
              </template>

              <b-dropdown-item
                class="notify-item"
                v-for="(entry, i) in languages"
                :key="`Lang${i}`"
                :value="entry"
                @click="setLanguage(entry.language, entry.title)"
              >
                <span class="align-middle">{{ entry.title }}</span>
              </b-dropdown-item>
            </b-dropdown>
            <b-button
              class="w-100 text-white bg-gray border-0 mt-3 py-2 rounded d-flex align-items-center justify-content-center"
              @click="openLogoutModal"
            >
              <b-icon icon="box-arrow-right" class="mr-2"></b-icon>
            </b-button>
          </div>
        </div>
      </div>
    </div>

    <b-modal
      id="logout-confirm-modal"
      ref="logoutConfirmModal"
      :title="this.$t('title.ConfirmLogout')"
      hide-footer
    >
      <div class="d-flex flex-column align-items-center text-center">
        <p>{{ this.$t("title.AreYouSureYouWantToLogout") }}</p>
        <div class="mt-3 d-flex gap-2 justify-content-center">
          <b-button variant="danger" @click="confirmLogout">{{
            this.$t("title.YesLogout")
          }}</b-button>
          <b-button variant="secondary" @click="cancelLogout">{{
            this.$t("title.Cancel")
          }}</b-button>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>
import {
  getAuthUser,
  getStorage,
  hasRole,
  refresh,
  removeStorage,
  setStorage,
} from "@/Util/auth";
import rawNavLinks from "@/config/route";
import { mapState, mapGetters } from "vuex";
import { request } from "@/Util/Request";

export default {
  name: "AppHeader",
  data() {
    return {
      isSidebarOpen: false,
      languages: [
        {
          language: "en-US",
          title: "English",
        },
        {
          language: "he-IL",
          title: "Hebrew",
        },
      ],
      windowWidth: window.innerWidth,
    };
  },
  computed: {
    ...mapState(["isUnAuthorized", "setIsRTL"]),
    ...mapGetters(["user"]),

    userRole() {
      return this.user?.role ?? "guest";
    },

    filteredNavLinks() {
      return this.filterMenuByRole(rawNavLinks);
    },
  },

  methods: {
    filterMenuByRole(items) {
      return items
        .filter((item) => item.roles.includes(this.userRole))
        .map((item) => {
          if (item.children && item.children.length) {
            const allowedChildren = item.children.filter((child) =>
              child.roles.includes(this.userRole)
            );
            return { ...item, children: allowedChildren };
          }
          return item;
        })
        .filter((item) => !item.children || item.children.length);
    },

    openLogoutModal() {
      this.$refs.logoutConfirmModal.show(); // Open the confirmation modal
    },

    cancelLogout() {
      this.$refs.logoutConfirmModal.hide(); // Close the modal without logout
    },

    async confirmLogout() {
      try {
        await request({
          method: "post",
          url: "/logout",
        });
      } catch (error) {
        // Log the error but continue with cleanup
        console.error("Logout API failed:", error?.response?.status, error?.message);
      } finally {
        // Always perform cleanup regardless of API response
        const role = this.user?.role;
        removeStorage("auth");
        await this.$store.dispatch("user", null);
        await this.$store.dispatch("setUnAuthorized", false); // Set to false since we're intentionally logging out
        
        // Redirect based on role
        if (role === "admin" || role === "super admin") {
          await this.$router.push({ name: "AdminLogin" });
        } else {
          await this.$router.push({ name: "Login" });
        }
        
        // Close the modal
        this.$refs.logoutConfirmModal.hide();
      }
    },

    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen;
      document.body.style.overflow = this.isSidebarOpen ? "hidden" : "";
    },

    closeSidebarOnMobile() {
        this.$nextTick(() => {
            if (window.innerWidth < 992) {
                this.isSidebarOpen = false;
                document.body.style.overflow = "";
            }
        });
    },

    setLanguage(locale, country) {
      this.$i18n.locale = locale; // Important to update the i18n instance
      this.text = country;
      this.$store.commit("CHANGE_SETTING", locale);
      const isRTL = locale === "he-IL";
      this.$store.dispatch("setIsRTL", isRTL);
      setStorage(`locale`, locale);
        document.documentElement.setAttribute("dir", isRTL ? "rtl" : "ltr");
        location.reload(); // Full page reload to apply language everywhere
    },
  },

  mounted() {
    window.addEventListener("resize", this.updateWindowWidth);
  },

  beforeDestroy() {
    window.removeEventListener("resize", this.updateWindowWidth);
  },
};
</script>

<style scoped>
.fixed-top {
  z-index: 1030;
  height: 60px;
}

.sidebar {
  position: fixed;
  left: 0;
  top: 60px;
  width: 250px;
  height: calc(100vh - 60px);
  z-index: 1020;
  overflow-y: auto;
  border-right: 1px solid #eee;
  transition: transform 0.3s ease-in-out;
  display: flex;
  flex-direction: column;
}

@media (min-width: 992px){
    [dir="rtl"] .sidebar {
        right:0 !important;
    }
}

[dir="rtl"] .sidebar {
    text-align: right;
}

.sidebar-backdrop {
  position: fixed;
  top: 60px;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1015;
}

/* Add styles to adjust the container-fluid in parent */
:deep(.container-fluid) {
  padding-left: 265px !important;
  padding-top: 75px !important;
  min-height: calc(100vh - 60px);
  background: #f8f9fa;
}

:deep(.row) {
  margin-right: 0;
  margin-left: 0;
}

.nav-link {
  color: #333;
  padding: 0.75rem 1rem;
  transition: all 0.3s;
}

.nav-link:hover {
  background-color: #f8f9fa;
  border-radius: 4px;
  color: #007bff;
}

.bg-gray {
  background-color: #6c757d;
}

.bg-gray:hover {
  background-color: #5a6268;
}

@media (max-width: 991.98px) {
  .sidebar {
    transform: translateX(-100%);
  }

  .sidebar.show {
    transform: translateX(0);
  }

  :deep(.container-fluid) {
    padding-left: 15px !important;
    padding-right: 15px !important;
  }
}

.nav-item .btn {
  padding: 0.75rem 1rem;
  color: #333;
  transition: all 0.3s;
}

.nav-item .btn:hover {
  background-color: #f8f9fa !important;
  color: #007bff;
}

.nav-item .btn:focus {
  box-shadow: none !important;
}

.nav-link {
  text-decoration: none;
  transition: all 0.3s;
}

.nav-link:hover {
  background-color: #f8f9fa;
  color: #007bff !important;
}

.bg-white {
  background-color: white !important;
}
</style>
