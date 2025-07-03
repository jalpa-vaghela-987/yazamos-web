<template>
  <div>
    <b-modal
     v-model="isOpenModal"
      id="credit-card-modal"
      ref="cardDetailModal"
      :title="$t('title.cardDetail')"
      hide-footer
      centered
    >
      <b-card
            class="text-white p-4"
            style="background: linear-gradient(135deg, #4b6cb7, #182848); border-radius: 1rem; box-shadow: 0 0 15px rgba(0,0,0,0.3);"
        >
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="card-brand">Visa</div>
            </div>

            <div class="d-flex justify-content-between mb-3">
              <div>
                  <small class="text-uppercase">{{$t('title.cardNumber')}}</small>
                  <h5 class="mb-0 font-weight-bold">{{ maskedCardNumber }}</h5>
              </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
              <div>
                  <small class="text-uppercase">{{$t('title.cardholder')}}</small>
                  <p class="mb-0">{{ selectedCard.card_holder_name  }}</p>
              </div>
              <div>
                  <small class="text-uppercase">{{$t('title.expires')}}</small>
                  <p class="mb-0">{{ formattedExpiry }}</p>
              </div>
            </div>
        </b-card>
    </b-modal>
  </div>
</template>

<script>
export default {
  data() {
    return {
      selectedCard: {},
      isOpenModal: false
    }
  },
  computed: {
    maskedCardNumber() {
      if (!this.selectedCard.card_no) return ''
      return this.selectedCard.card_no.replace(/\d{12}(\d{4})/, '**** **** **** $1')
    },
    formattedExpiry() {
        if (!this.selectedCard.expiry_month) return '';
        if (this.selectedCard.expiry_month && this.selectedCard.expiry_year) {
            const m = this.selectedCard.expiry_month.toString().padStart(2, '0'); // ensure two digits month
            const y = this.selectedCard.expiry_year.toString().slice(-2);
            return `${m}/${y}`;
        }
        return '';
    }
  },
  methods: {
     openCloseModal(card=null) {
        this.selectedCard = card
        this.isOpenModal = !this.isOpenModal;
    }
  }
}
</script>

<style scoped>
.card-font {
  /* font-family: 'Courier New', Courier, monospace; */
  letter-spacing: 2px;
}

.card-brand {
  font-weight: bold;
  font-size: 1.2rem;
}
</style>
