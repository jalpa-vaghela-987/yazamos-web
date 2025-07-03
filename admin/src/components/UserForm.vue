<template>
  <div>
    <b-form @submit.prevent="handleSubmit" :validated="validated" novalidate>
      <b-form-group label="Roles" label-for="roles">
        <treeselect
          placeholder="select"
          v-model="formFields.role_id"
          class="form-input"
          :class="formErrors.has('role_id') ? `error-input` : ``"
          :options="dropdowns.roles"
          required
          @select="removeError('role_id')"
        />
        <b-form-invalid-feedback :state="!formErrors.has('role_id')">
          {{ formErrors.first("role_id") }}
        </b-form-invalid-feedback>
      </b-form-group>

      <b-form-group label="Name">
        <b-form-input v-model="formFields.name" @input="removeError('name')" required />
        <b-form-invalid-feedback :state="!formErrors.has('name')">
          {{ formErrors.first("name") }}
        </b-form-invalid-feedback>
      </b-form-group>

      <!-- Address -->
      <!-- <b-form-group label="Address">
              <b-form-input v-model="formFields.address" @input="removeError('address')" required />
              <b-form-invalid-feedback
                    :state="!formErrors.has('address')">
                    {{ formErrors.first('address') }}
                </b-form-invalid-feedback>
            </b-form-group> -->

      <!-- Phone -->
      <b-form-group label="Phone No">
        <vue-tel-input
          :class="formErrors.has('phone_number') ? `error-input` : ``"
          v-model="formFields.phone_number"
          @focus="resetError"
          @input="onPhoneInput"
          @keydown.native="checkEnterToSendOtp"
        />

        <b-form-invalid-feedback :state="!formErrors.has('phone_number')">
          {{ formErrors.first("phone_number") }}
        </b-form-invalid-feedback>
      </b-form-group>

      <b-form-group label="Projects" label-for="projects">
        <treeselect
          multiple
          placeholder="select"
          v-model="formFields.project_ids"
          class="form-input"
          :class="formErrors.has('project_ids') ? `error-input` : ``"
          :options="dropdowns.projects"
          required
          @input="removeError('project_ids')"
        />
        <b-form-invalid-feedback :state="!formErrors.has('project_ids')">
          {{ formErrors.first("project_ids") }}
        </b-form-invalid-feedback>
      </b-form-group>

      <b-form-group>
        <div v-if="selectedRoleName == 'entrepreneur' && userProjects != 0">
          <b-form-group v-if="selectedRoleName == 'entrepreneur'">
            <!-- Assets Tabs -->
            <div class="row mx-0 mb-3">
              <div class="col-12 d-flex justify-content-between gap-2">
                <div class="d-flex gap-2 align-items-center">
                  <h5 class="mb-0">Assets</h5>
                  <ul class="nav nav-pills ms-3 d-flex flex-nowrap overflow-auto">
                    <li class="nav-item" v-for="type in assetTypes" :key="type.id">
                      <b-button
                        variant="secondary"
                        class="btn-active-nav-link nav-link m-1"
                        :class="{
                          active: activeTab?.id === type.id,
                          deactivated: activeTab?.id !== type.id,
                        }"
                        @click="selectAssetTab(type)"
                      >
                        {{ type.name }}
                      </b-button>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Project Cards Horizontal Scroll -->
            <div class="project-card-wrapper px-2">
              <div class="d-flex gap-3 project-card-scroll" ref="scrollContainer">
                <div
                  class="position-relative"
                  v-for="project in filteredProjectsByType"
                  :key="project.id"
                  style="position: relative"
                >
                  <!-- Checkbox overlay -->
                  <input
                    type="checkbox"
                    class="project-checkbox"
                    :value="project.id"
                    v-model="selectedProjectIds"
                    @click.stop
                    style="
                      position: absolute;
                      top: 10px;
                      right: 10px;
                      z-index: 2;
                      width: 20px;
                      height: 20px;
                    "
                  />

                  <!-- Project card -->
                  <div
                    class="card shadow-sm border-0 rounded-4 overflow-hidden h-100 flex-shrink-0 project-card"
                    :class="{ 'border-primary': selectedProjectIds.includes(project.id) }"
                    style="cursor: pointer"
                    @click="toggleProjectSelection(project.id)"
                  >
                    <img
                      :src="
                        project.images?.[0]?.url ||
                        'https://via.placeholder.com/280x180?text=No+Image'
                      "
                      class="card-img-top"
                      alt="Project image"
                      @error="handleImageError($event)"
                      style="height: 180px; object-fit: cover"
                    />

                    <div class="card-body d-flex flex-column justify-content-between">
                      <h6 class="fw-bold text-primary text-truncate">
                        {{ project.name }}
                      </h6>
                      <div
                        class="d-flex justify-content-between text-muted small mt-auto"
                      >
                        <div>
                          <div>Value</div>
                          <strong>{{ project.current_property_value }}</strong>
                        </div>
                        <div>
                          <div>Purchase</div>
                          <strong>{{ project.purchase_price }}</strong>
                        </div>
                        <div>
                          <div>Wedge</div>
                          <strong>{{ project.wedge }}</strong>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </b-form-group>
          <!-- <b-form @submit.prevent="handleSubmit" novalidate> -->

          <b-form-group label="Reassign Entrepreneurs" label-for="users">
            <treeselect
              placeholder="select"
              v-model="formFields.user_id"
              class="form-input"
              :class="formErrors.has('user_id') ? `error-input` : ``"
              :options="dropdowns.users"
              @input="removeError('user_id')"
            />
            <b-form-invalid-feedback :state="!formErrors.has('user_id')">
              {{ formErrors.first("user_id") }}
            </b-form-invalid-feedback>
          </b-form-group>
          <!-- </b-form> -->
        </div>
      </b-form-group>

      <b-button type="submit" variant="primary">Submit</b-button>
      <b-button variant="secondary" @click="cancel" class="m-2">Cancel</b-button>
    </b-form>
  </div>
</template>

<script>
import Error from "@/Util/Error";
import countryCodes from "@/Util/countryCodes";
import { request } from "@/Util/Request";
import { ASYNC_SEARCH } from "@riophae/vue-treeselect";
import { VueTelInput } from "vue-tel-input";
import "vue-tel-input/dist/vue-tel-input.css";

const FORM_STATE = {
  role_id: null,
  user_id: null,
  name: "",
  phone_number: "",
  country_code: "",
  project_ids: [],
  reassign_projects: [],
};

export default {
  name: "UserForm",
  components: {
    VueTelInput,
  },
  data() {
    return {
      formFields: { ...FORM_STATE },
      formErrors: new Error({}),
      validated: false,
      errors: {},
      countryCodes,
      dropdowns: {
        roles: [],
        projects: [],
        users: [],
      },
      stats: [],
      assetTypes: [],
      activeTab: null,
      userProjects: [],
      filteredProjectsByType: [],
      phone: {
        inputValue: "",
      },
      selectedProjectIds: [],
    };
  },
  mounted() {
    this.roles();
    this.projects();

    if (this.$route.params && this.$route.params.id) {
      this.getDetail(this.$route.params.id).then(() => {
        this.fetchDashboardData();
        this.entrepreneurLists();
      });
    }
  },
  computed: {
    selectedRoleName() {
      const role = this.dropdowns.roles.find((r) => r.id === this.formFields.role_id);
      return role ? role.name : "";
    },
  },
  methods: {
    resetError() {
      this.formErrors = new Error({});
    },
    onPhoneInput(formattedNumber, input) {
      this.phone.inputValue = input?.nationalNumber || "";
      if (input && input.nationalNumber) {
        console.log(formattedNumber, input);
        setTimeout(() => {
          if (formattedNumber.startsWith("+61")) {
            input.countryCallingCode = 61;
            input.countryCode = "AU";
          }
          this.formFields.phone_number = input?.nationalNumber || "";
          this.formFields.country_code = `+${input.countryCallingCode}`;
        }, 0);
      }
    },
    async checkEnterToSendOtp(event) {
      if (event.keyCode == 13 || event.key === "Enter") {
        this.sendOtp();
      }
    },
    handleImageError(event) {
      event.target.src = require("@/assets/default.png");
    },
    cancel() {
      this.$router.back();
    },
    async getDetail(id) {
      try {
        const response = await request({
          method: "get",
          url: `/users/${id}`,
        });

        const { data } = response;
        this.formFields = data;
        this.formFields.phone_number = `${data.country_code}${data.phone_number}`;
        this.userProjects = data["projects"];
      } catch (error) {
        this.notifyError();
      }
    },
    async entrepreneurLists() {
      try {
        const response = await request({
          method: "get",
          url: `user/dropdowns?role=entrepreneur&user_id=${this.$route.params.id}`,
        });

        const { data } = response;
        this.dropdowns.users = data;
      } catch (error) {
        this.notifyError();
      }
    },
    async roles() {
      try {
        const response = await request({
          method: "get",
          url: `/user/roles`,
        });
        const { data } = response;
        this.dropdowns.roles = data;
      } catch (error) {
        this.notifyError();
      }
    },
    async projects() {
      try {
        const response = await request({
          method: "get",
          url: `/admin/projects`,
        });
        const { data } = response;
        this.dropdowns.projects = data;
      } catch (error) {
        this.notifyError();
      }
    },
    async handleSubmit() {
      try {
        let url = `users`;
        let method = "post";

        if (this.$route.params.id) {
          url = `users/${this.$route.params.id}`;
          method = "PUT";
        }

        this.formFields.reassign_projects = this.selectedProjectIds;

        const response = await request({
          method: method,
          url: url,
          data: this.formFields,
        });

        this.resetForm();
        if (this.$route.params.id) {
          this.notifySuccess("User updated successfully");
        } else {
          this.notifySuccess("User created successfully");
        }
      } catch (error) {
        if (error.request && error.request.status && error.request.status === 422) {
          this.formErrors = new Error(JSON.parse(error.request.responseText).errors);
          return false;
        } else {
          this.notifyError();
        }
      }
    },
    async resetForm() {
      this.formFields = { ...FORM_STATE };
      this.formErrors = new Error({});
    },
    isNumber(event) {
      const charCode = event.which ? event.which : event.keyCode;

      if (charCode < 48 || charCode > 57) {
        event.preventDefault();
        return;
      }

      if (this.formFields.phone_number && this.formFields.phone_number.length >= 15) {
        event.preventDefault();
      }
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

    toggleProjectSelection(id) {
      const index = this.selectedProjectIds.indexOf(id);
      if (index === -1) {
        this.selectedProjectIds.push(id);
      } else {
        this.selectedProjectIds.splice(index, 1);
      }
    },

    async filterProjectsByAssetType(selectedAssetType) {
      try {
        // Get the projects from formFields
        const projects = this.userProjects;

        // If "All" is selected, return all projects
        if (!selectedAssetType || selectedAssetType === "All") {
          this.filteredProjectsByType = projects;
          return;
        }

        // Filter the projects by the selected asset type
        const filteredProjects = projects.filter(
          (project) => project.asset_type === selectedAssetType
        );

        // Set the filtered projects
        this.filteredProjectsByType = filteredProjects;
      } catch (error) {
        console.error("Error filtering projects by asset type:", error);
      }
    },
    async selectAssetTab(type) {
      this.activeTab = type;

      try {
        const selectedAssetType = type?.name; // Assuming `name` is the selected asset type
        if (selectedAssetType == "All") {
          // If no specific type is selected, show all projects
          this.filteredProjectsByType = this.userProjects;
        } else {
          // Filter projects based on selected asset type
          await this.filterProjectsByAssetType(selectedAssetType);
        }
      } catch (error) {
        console.error("Error selecting asset tab:", error);
      }
    },
    async fetchDashboardData() {
      try {
        // Extract asset types from projects
        const typeMap = {};
        this.userProjects.forEach((project) => {
          const type = project.asset_type_id; // <-- this is the full object with id and name
          if (type && !typeMap[type.id]) {
            typeMap[type.id] = type;
          }
        });

        this.assetTypes = Object.values(typeMap);

        // Add the "All" tab as the first tab
        this.assetTypes.unshift({ id: null, name: "All" });

        this.filteredProjectsByType = this.userProjects;

        // Set default tab as "All"
        this.selectAssetTab(this.assetTypes[0]);
      } catch (error) {
        console.error("Error fetching dashboard data:", error);
      }
    },
  },
};
</script>

<style scoped>
.country-code-select {
  max-width: 150px;
}

.btn-active-nav-link {
  color: #fff !important;
  background-color: #1f0f7b !important;
  border-color: #1f0f7b !important;
}

.deactivated {
  color: #0b0909 !important;
  background-color: #d5dfe7 !important;
  border-color: #1f0f7b;
}

.project-checkbox {
  accent-color: #1f0f7b;
  /* Optional for checkbox styling */
}
</style>
