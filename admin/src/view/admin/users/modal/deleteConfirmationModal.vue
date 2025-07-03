<template>
    <div>
        <b-modal v-model="isOpenModal" id="deleteUser" :hide-header="true"
                 :hide-footer="true">
            <h6>Are you sure you want to delete this users? </h6>
            <div v-if="formFields.role == 'entrepreneur' && formFields.entrepreneur_total_projects != 0">
                <p>Reassign this entrepreneur's projects to another entrepreneur before delete</p>
                <br>
                <a :href="`${$route.path}/edit/${id}`">Reassign entrepreneur</a>
            </div>
            <div class="modal-footer d-flex mr-2 gap-2">
                <b-button variant="primary" class="mr-2 col m-0" @click="handleSubmit">
                   Yes
                </b-button>
                <b-button variant="primary" class="col m-0" @click="cancelModal">
                    No
                </b-button>
            </div>
        </b-modal>
    </div>
</template>


<script>
import {mapState, mapGetters} from "vuex";
import {request} from "@/Util/Request";
import Error from "@/Util/Error";
import {ASYNC_SEARCH} from '@riophae/vue-treeselect';

const FORM_STATE = {
    user_id: null,
    role: null,
    entrepreneur_total_projects: 0
};

export default {
    data() {
        return {
            id: null,
            isOpenModal: false,
            formFields: {...FORM_STATE},
            formErrors: new Error({}),
            dropdowns: {
                users: []
            },
        }
    },
    methods: {
        async entrepreneurLists() {
            try {
                    const response = await request({
                        method: 'get',
                        url: `user/dropdowns?role=entrepreneur&user_id=${this.id}`,
                    });

                    const {data} = response;
                    this.dropdowns.users = data;
                } catch (error) {
                    this.notifyError()
                }
        },
        cancelModal() {
            this.isOpenModal = false
        },
        openCloseModal(user=null) {
            this.formFields = {...FORM_STATE};
            this.id = user?.id;
            this.formFields.role = user?.role;
            this.formFields.entrepreneur_total_projects = (user?.entrepreneur_projects) ? user.entrepreneur_projects.length : 0;
            this.isOpenModal = !this.isOpenModal;
            this.entrepreneurLists();
        },
        async removeError(key) {
            if ( typeof key === `object` ) {
                for (let i = 0; i < key.length; i++) {
                    this.formErrors.remove(key[i]);
                }
            } else {
                this.formErrors.remove(key);
            }
        },
        async handleSubmit() {
            {
                try {
                    const response = await request({
                        method: 'delete',
                        url: `users/${this.id}`,
                        data: this.formFields
                    });

                    if ( response ) {
                        this.notifySuccess(`User deleted successfully`);
                        this.cancelModal()
                        this.$emit('refreshTable');
                        this.formFields = {...FORM_STATE};
                    }
                    this.formErrors = new Error({});

                } catch (error) {
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
}
</script>
