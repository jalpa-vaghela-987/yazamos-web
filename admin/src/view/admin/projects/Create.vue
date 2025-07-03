<template>
    <div>
        <Breadcrumbs />
        <b-container>
            <h2 class="mb-4">{{ $t("title.add_project") }}</h2>
            <LoaderSpinner v-if="loader" />

            <b-form @submit.prevent="handleSubmit">
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
                    <h5 class="">{{$t('title.' + flag +'Images')}}</h5>
                    <div class="d-flex flex-wrap align-items-start">
                        <!-- All previews -->
                        <div
                            v-for="(img, index) in groupedImages[flag]"
                            :key="flag + '-' + index"
                            class="position-relative m-2"
                            style="width: 100px;"
                        >
                            <img
                                :src="img.preview"
                                class="img-thumbnail"
                                style="width: 100px; height: 100px; object-fit: cover;"
                            />
                            <button
                                type="button"
                                class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                @click="removeImage(flag, index)"
                            >×</button>
                        </div>

                        <!-- + Button (always shows) -->
                        <div
                            class="d-flex justify-content-center align-items-center bg-light border m-2"
                            style="width: 100px; height: 100px; cursor: pointer;"
                            @click="openImageModal(flag)"
                        >
                            <span style="font-size: 32px;">+</span>
                        </div>
                    </div>
                </div>

                <b-button type="submit" variant="primary">{{ $t("title.create_project") }}</b-button>
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
        ...mapState(['loader']),
        groupedImages() {
            const grouped = {};
            this.imageFlags.forEach(flag => {
                grouped[flag] = [];
            });
            this.newImages.forEach(img => {
                if (!grouped[img.flag]) grouped[img.flag] = [];
                grouped[img.flag].push({ ...img });
            });
            return grouped;
        }
    },
    data() {
        return {
            form: {
                name: '',
                description: '',
                asset_type_id: null,
                location: '',
                current_property_value: null,
                purchase_price: null,
                renovation_cost: null,
                start_date: '',
                end_date: '',
            },
            currentImageKey: null,
            imageFlags: ['before', 'after'],
            newImages: [],
            preview: {
                before: [],
                after: []
            },
            assetTypeOptions: []
        };
    },
    async mounted() {
        await this.assetTypes();
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
                        text: `${item.name}`,
                    };
                });
            } catch (error) {
                this.$bvToast.toast("Failed to fetch asset types", {
                    title: "Error",
                    variant: "danger",
                });
            }
        },
        openImageModal(flag) {
            this.currentImageKey = flag;
            this.$refs.imageUploadModal.show();
        },
        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.newImages.push({
                    file,
                    flag: this.currentImageKey,
                    preview: URL.createObjectURL(file)
                });
            }
        },
        handleDrop(event, flag) {
            const file = event.dataTransfer.files[0];
            if (file) {
                this.newImages.push({
                    file,
                    flag,
                    preview: URL.createObjectURL(file)
                });
            }
        },
        removeImage(flag, index) {
            const imageIndex = this.newImages.findIndex(img => img.flag === flag);
            if (imageIndex !== -1) {
                this.newImages.splice(imageIndex, 1);
            }
        },
        async handleSubmit() {
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
                    "purchase_price",
                    "start_date",
                    "end_date",
                ]) {
                    formData.append(key, this.form[key]);
                }

                // Append images
                this.newImages.forEach((img, index) => {
                    formData.append(`images[${index}][file]`, img.file);
                    formData.append(`images[${index}][flag]`, img.flag);
                });

                await request({
                    method: "post",
                    url: "/projects",
                    data: formData,
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                });

                this.$router.push({ name: "AdminProjectIndex" });
            } catch (error) {
                console.error(error);
                this.$bvToast.toast("Failed to create project", {
                    title: "Error",
                    variant: "danger",
                });
            } finally {
                this.$store.dispatch("setLoader", false);
            }
        }
    }
};
</script>

<style scoped>
.border {
    border: 2px dashed #ccc;
}
</style>
