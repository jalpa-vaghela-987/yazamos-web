<template>
    <div class="mt-4">
        <b-card :title="$t('title.changePassword')" class="mb-4">
            <b-form @submit.prevent="changePassword">
                <b-form-group :label="$t('title.currentPassword')">
                    <b-input-group>
                        <b-form-input
                            :type="showCurrent ? 'text' : 'password'"
                            v-model="formFields.old_password"
                            @input="removeError('old_password')"
                        />
                        <b-input-group-append>
                            <b-button @click="showCurrent = !showCurrent" variant="outline-secondary">
                                <i :class="showCurrent ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </b-button>
                        </b-input-group-append>
                    </b-input-group>
                    <b-form-invalid-feedback
                        :state="!formErrors.has('old_password')">
                        {{ formErrors.first('old_password') }}
                    </b-form-invalid-feedback>
                </b-form-group>
  
                <b-form-group :label="$t('title.newPassword')">
                    <b-input-group>
                        <b-form-input
                            :type="showNew ? 'text' : 'password'"
                            v-model="formFields.password"
                            @input="removeError('password')"
                        />
                        <b-input-group-append>
                            <b-button @click="showNew = !showNew" variant="outline-secondary">
                                <i :class="showNew ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </b-button>
                        </b-input-group-append>
                    </b-input-group>
                    <b-form-invalid-feedback
                        :state="!formErrors.has('password')">
                        {{ formErrors.first('password') }}
                    </b-form-invalid-feedback>
                </b-form-group>
  
                <b-form-group :label="$t('title.confirmNewPassword')">
                    <b-input-group>
                        <b-form-input
                            :type="showConfirm ? 'text' : 'password'"
                            v-model="formFields.password_confirmation"
                            @input="removeError('password_confirmation')"
                        />
                        <b-input-group-append>
                            <b-button @click="showConfirm = !showConfirm" variant="outline-secondary">
                                <i :class="showConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </b-button>
                        </b-input-group-append>
                    </b-input-group>
                    <b-form-invalid-feedback
                        :state="!formErrors.has('password_confirmation')">
                        {{ formErrors.first('password_confirmation') }}
                    </b-form-invalid-feedback>
                </b-form-group>
  
                <b-button type="submit" variant="primary" :disabled="loading">
                    {{ loading ? $t('title.saving') : $t('title.changePasswordButton') }}
                </b-button>
            </b-form>
        </b-card>
    </div>
  </template>
  

<script>
import { request } from "@/Util/Request";
import Error from "@/Util/Error";

const FORM_STATE = {
    old_password: null,
    password: null,
    password_confirmation: null
};

export default {
  data() {
    return {
      formFields: { ...FORM_STATE },
      formErrors: new Error({}),
      loading: false,
      showCurrent: false,
      showNew: false,
      showConfirm: false,
    }
  },
  methods: {
     async removeError(key) {
        if ( typeof key === `object` ) {
            for (let i = 0; i < key.length; i++) {
                this.formErrors.remove(key[i]);
            }
        } else {
            this.formErrors.remove(key);
        }
    },
    async changePassword() {
      try {
            this.loading = true;
            const response = await request({
                method: 'post',
                url: `/update-password`,
                data: this.formFields
            });

            let {data} = response;

            if(data) {
                this.formFields = { ...FORM_STATE };
                this.notifySuccess('Password changed successfully.');
                this.loading = false;
            }
            console.log(data);

      } catch (error) {
            this.loading = false;
            if ( error.request && error.request.status && error.request.status === 422 ) {
                this.formErrors = new Error(JSON.parse(error.request.responseText).errors);
                return false;
            } else {
                this.notifyError();
            }
      }
    }
  }
}
</script>
