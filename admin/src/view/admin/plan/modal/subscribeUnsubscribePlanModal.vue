<template>
    <div>
        <b-modal v-model="isOpenModal" id="updateStatusModal" :hide-header="true"
                 :hide-footer="true">
            
            <h6 v-if="status == 1">{{$t('title.subscribeMessage')}}</h6>
            <h6 v-if="status == 0">{{$t('title.unSubscribeMessage')}}</h6>
            <div class="modal-footer d-flex mr-2 gap-2">
                <b-button variant="primary" class="mr-2 col m-0" @click="handleSubmit">
                   {{$t('title.yes')}}
                </b-button>
                <b-button variant="primary" class="col m-0" @click="cancelModal">
                    {{$t('title.cancel')}}
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
            userActivePlan: [],
            status: 0,
        }
    },
    methods: {
        cancelModal() {
            this.isOpenModal = false
        },
        openCloseModal(activePlan, status) {
            this.status = status;
            this.userActivePlan = activePlan;
            this.isOpenModal = !this.isOpenModal;
        },
        async handleSubmit() {
            {
                try {
                    const response = await request({
                        method: 'post',
                        url: `/update-plan-status/${this.userActivePlan.id}/${this.status}`
                    });
                
                    if(response) {
                        this.cancelModal();
                        this.notifySuccess('Plan status updated successfully');
                        this.$emit('getUserActivePlan');
                    }

                } catch (error) {
                    this.notifyError();
                }
            }
        }
    }
}
</script>
