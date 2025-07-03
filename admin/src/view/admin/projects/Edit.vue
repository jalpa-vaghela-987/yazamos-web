<template>
  <div>
    <Breadcrumbs />
    <b-container>
      <h2 class="mb-4">{{ $t("title.edit_project") }}</h2>
      <LoaderSpinner v-if="loader" />

      <b-form v-if="form" @submit.prevent="handleSubmit">
        <b-form-group :label="$t('title.name')">
          <b-form-input v-model="form.name" required />
        </b-form-group>

        <b-form-group :label="$t('title.description')">
          <b-form-textarea v-model="form.description" rows="3" required />
        </b-form-group>

        <b-form-group :label="$t('title.asset_type')">
          <b-form-select
            v-model="form.asset_type_id"
            :options="assetTypeOptions"
            required
          />
        </b-form-group>

        <b-form-group :label="$t('title.location')">
          <b-form-input v-model="form.location" required />
        </b-form-group>

        <b-form-group :label="$t('title.current_property_value')">
          <b-form-input v-model="form.current_property_value" type="number" required />
        </b-form-group>

        <b-form-group :label="$t('title.purchase_price')">
          <b-form-input v-model="form.purchase_price" type="number" />
        </b-form-group>

        <b-form-group :label="$t('title.renovation_cost')">
          <b-form-input v-model="form.renovation_cost" type="number" required />
        </b-form-group>

        <b-form-group :label="$t('title.start_date')">
          <b-form-input v-model="form.start_date" type="date" required />
        </b-form-group>

        <b-form-group :label="$t('title.end_date')">
          <b-form-input v-model="form.end_date" type="date" required />
        </b-form-group>

        <div v-for="flag in imageFlags" :key="flag" class="mb-4">
          <h5 class="">{{ $t("title." + flag + "Images") }}</h5>
          <div class="d-flex flex-wrap align-items-start">
            <!-- All previews -->
            <div
              v-for="(img, index) in groupedImages[flag]"
              :key="flag + '-' + index"
              class="position-relative m-2"
              style="width: 100px"
            >
              <img
                :src="img.preview || img.url"
                class="img-thumbnail"
                style="width: 100px; height: 100px; object-fit: cover"
              />
              <button
                type="button"
                class="btn btn-sm btn-danger position-absolute top-0 end-0"
                @click="removeImage(flag, index, img.isExisting)"
              >
                ×
              </button>
            </div>

            <!-- + Button (always shows) -->
            <div
              class="d-flex justify-content-center align-items-center bg-light border m-2"
              style="width: 100px; height: 100px; cursor: pointer"
              @click="openImageModal(flag)"
            >
              <span style="font-size: 32px">+</span>
            </div>
          </div>
        </div>

        <b-button type="submit" variant="primary">{{
          $t("title.update_project")
        }}</b-button>
      </b-form>

      <!-- Image Upload Modal -->
      <b-modal
        id="image-upload-modal"
        ref="imageUploadModal"
        :title="$t('title.upload_image')"
        hide-footer
        centered
        size="md"
      >
        <div
          class="border p-3 text-center bg-light"
          @drop.prevent="handleDrop($event, currentImageKey)"
          @dragover.prevent
        >
          <div v-if="currentImageKey && preview[currentImageKey]">
            <img :src="preview[currentImageKey]" class="img-thumbnail mb-2" width="100" />
          </div>
          <span>{{ $t("title.drag_or_click") }}</span>
          <input type="file" class="d-none" ref="fileInput" @change="handleFileChange" />
          <b-button
            size="sm"
            variant="secondary"
            class="mt-2"
            @click="$refs.fileInput.click()"
          >
            {{ $t("title.select") }}
          </b-button>
        </div>
      </b-modal>
    </b-container>
  </div>
</template>

<script>
import { request } from "@/Util/Request";
import Breadcrumbs from "@/components/Breadcrumbs.vue";
import { mapState } from "vuex";
import LoaderSpinner from "@/components/LoaderSpinner.vue";

export default {
  components: { Breadcrumbs, LoaderSpinner },
  computed: {
    ...mapState(["loader"]),
    groupedImages() {
      const grouped = {};

      this.imageFlags.forEach((flag) => {
        grouped[flag] = [];
      });

      this.existingImages.forEach((img) => {
        if (!grouped[img.flag]) grouped[img.flag] = [];
        grouped[img.flag].push({ ...img, isExisting: true });
      });

      this.newImages.forEach((img) => {
        if (!grouped[img.flag]) grouped[img.flag] = [];
        grouped[img.flag].push({ ...img, isExisting: false });
      });

      return grouped;
    },
  },
  data() {
    return {
      form: {
        images: [], // This will include existing + new images
      },
      currentImageKey: null,
      imageFlags: ["before", "after"], // All the flags you want

      newImages: [], // { file: File, flag: 'before' | 'after' }
      existingImages: [],
      preview: {
        before: [],
        after: [],
      },
      currentImageFlag: null,
      assetTypeOptions: [],
    };
  },
  async mounted() {
    const { id } = this.$route.params;
    try {
      const { data } = await request({ method: "get", url: `/projects/${id}` });
      this.form = {
        id: data.id,
        name: data.name,
        description: data.description,
        asset_type_id: data.asset_type_id.id,
        location: data.location,
        current_property_value: data.current_property_value,
        calculated_value: data.calculated_value,
        start_date: data.start_date,
        end_date: data.end_date,
        purchase_price: data.purchase_price,
        renovation_cost: data.renovation_cost,
        // Init image fields as null for conditional upload
        image1: null,
        image2: null,
        image3: null,
        image4: null,
      };
      this.assetTypes();

      this.form.images = data.images || [];

      // Load existing image previews from *_url
      ["before", "after"].forEach((flag) => {
        this.preview[flag] = this.form.images
          .filter((img) => img.flag === flag)
          .map((img) => ({ id: img.id, url: img.url }));
      });
      this.existingImages = data.images || [];
    } catch (error) {
      this.$bvToast.toast("Failed to fetch project data", {
        title: "Error",
        variant: "danger",
      });
    }
  },
  methods: {
    async assetTypes() {
      try {
        const response = await request({
          method: "get",
          url: `/asset-types`,
        });
        this.assetTypeOptions = response.data.map((item) => {
          return {
            value: item.id,
            text: `${item.name}`, // or just item.name if you don't need user
          };
        });
      } catch (error) {
        this.notifyError();
      }
    },
    async handleSubmit() {
      const { id } = this.$route.params;
      try {
        this.$store.dispatch("setLoader", true);
        const formData = new FormData();

        // Append regular fields
        for (const key of [
          "name",
          "description",
          "asset_type_id",
          "location",
          "renovation_cost",
          "current_property_value",
          "calculated_value",
          "start_date",
          "end_date",
          "purchase_price",
        ]) {
          formData.append(key, this.form[key]);
        }

        this.existingImages.forEach((img, index) => {
          formData.append(`images[${index}][id]`, img.id);
          formData.append(`images[${index}][flag]`, img.flag);
        });

        // Append new image files and flags
        const startIndex = this.existingImages.length;
        this.newImages.forEach((img, index) => {
          const formIndex = startIndex + index;
          formData.append(`images[${formIndex}][file]`, img.file);
          formData.append(`images[${formIndex}][flag]`, img.flag);
        });

        await request({
          method: "post",
          url: `/projects/update/${id}`,
          data: formData,
          headers: { "Content-Type": "multipart/form-data" },
        });

        this.$router.push({ name: "EntrepreneurProjectIndex" });
      } catch (error) {
        this.notifyErrormessage(error.data.message);
      } finally {
        this.$store.dispatch("setLoader", false);
      }
    },
    openImageModal(key) {
      this.currentImageFlag = key;
      this.$refs.imageUploadModal.show();
    },

    handleFileChange(e) {
      const file = e.target.files[0];

      if (!file || !this.currentImageFlag) return;
      const reader = new FileReader();
      reader.onload = (event) => {
        this.newImages.push({
          file,
          preview: event.target.result,
          flag: this.currentImageFlag,
        });

        console.log(this.newImages);
      };
      reader.readAsDataURL(file);
    },
    removeImage(flag, index, isExisting) {
      if (isExisting) {
        this.existingImages = this.existingImages.filter(
          (img, i) => !(img.flag === flag && i === index)
        );
      } else {
        this.newImages = this.newImages.filter(
          (img, i) => !(img.flag === flag && i === index)
        );
      }
    },
    setImage(key, file) {
      if (file) {
        this.form[key] = file;

        const reader = new FileReader();
        reader.onload = (e) => {
          this.$set(this.preview, key, e.target.result);
        };
        reader.readAsDataURL(file);
      }
    },
  },
};
</script>

<style scoped>
.modal-dialog {
  max-width: 400px;
}
</style>
