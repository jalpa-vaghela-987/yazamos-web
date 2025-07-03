<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>{{$t('title.yourCreditCards')}}</h3>
      <b-button v-if="cardDetails.length > 0" variant="primary" @click="openModal()">+ {{$t('title.addCard')}}</b-button>
    </div>
    <template v-if="cardDetails.length > 0">
        <div class="alert alert-warning" role="alert">
          {{$t('title.cardMessage')}}
        </div>
      </template>
    <b-row>
      <b-col
        v-for="card in cardDetails"
        :key="card.id"
        cols="12"
        md="6"
        lg="4"
      >
        <b-card no-body class="credit-card mb-3" :class="{ active: card.is_active }">
          <div class="p-3 text-white">
            <div class="d-flex justify-content-between align-items-center">
              <div class="card-brand">Visa</div>
              <b-badge v-if="card.is_active" variant="light">{{$t('title.active')}}</b-badge>
            </div>

            <div class="card-number mt-4 mb-2">{{ cardDetailMaskedCardNumber(card.card_no) }}</div>

            <div class="d-flex justify-content-between small">
              <div>{{ card.card_holder_name }}</div>
              <div>{{$t('title.expires')}}: {{ formatExpiry(card.expiry_month, card.expiry_year) }}</div>
            </div>

            <div class="mt-3 d-flex justify-content-end">
              <b-button size="sm" variant="light" class="mr-2" @click="openModal(card)">{{$t('title.edit')}}</b-button>
              <b-button
                size="sm"
                variant="outline-light"
                :disabled="(card.is_active)? true : false"
                @click="setActiveCard(card.id)"
              >
                {{$t('title.setActive')}}
              </b-button>
            </div>
          </div>
        </b-card>
      </b-col>
    </b-row>

   <AddEditCardModal ref="cardModal" @cardDetails="getCardDetails"/>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import Error from "@/Util/Error";
import icons from '@/assets/icons.svg';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';
import { request } from "@/Util/Request";
import AddEditCardModal from "./modal/addEditCardModal"

export default {
  data() {
    return {
      showModal: false,
      editId: null,
      cardDetails: [],
    }
  },
  components: {
    AddEditCardModal
  },
  mounted() {
    this.getCardDetails();
  },
  methods: {
    formatExpiry(month, year) {
        // month can be string or number
        const m = month.toString().padStart(2, '0'); // ensure two digits month
        const y = year.toString().slice(-2);
        return `${m}/${y}`;
    },
    cardDetailMaskedCardNumber(cardNo) {
        return cardNo.replace(/\d{12}(\d{4})/, '**** **** **** $1')
    },
    async getCardDetails() {
        try {
            const { data } = await request({
                url: '/card-details'
            });
            this.cardDetails = data;
            console.log(data);
        } catch (error) {
            console.error(error);
            this.notifyError();
        }
    },
    openModal(card = null) {
      this.$refs['cardModal'].openCloseModal(card);
    },
    async setActiveCard(cardId) {
       try {
                const response = await request({
                    method: 'post',
                    url: `/active-card-details/${cardId}`
                });

                let { data } = response;

                if (data && response.status === 'success') {
                    this.notifySuccess('Card detail activated successfully');
                    this.getCardDetails();
                }

            } catch (error) {
                
                if ( error.data && error.data.error && error.request.status == 400 ) {
                    this.notifyError();
                }

                if ( error.request && error.request.status && error.request.status === 422 ) {
                    this.formErrors = new Error(JSON.parse(error.request.responseText).errors);
                    return false;
                }
                
                if ( error.data && error.data.message) {
                    this.notifyError();
                }
                
            } 
    },
  },
}
</script>

<style scoped>
.credit-card {
  background: linear-gradient(135deg, #4b6cb7, #182848);
  color: #fff;
  border-radius: 15px;
  min-height: 180px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  transition: transform 0.2s ease;
}
.credit-card:hover {
  transform: scale(1.02);
}
.credit-card.active {
  border: 2px solid #0d6efd;
}
.card-number {
  font-size: 1.4rem;
  letter-spacing: 2px;
}
.card-brand {
  font-weight: bold;
  font-size: 1.2rem;
}
</style>
