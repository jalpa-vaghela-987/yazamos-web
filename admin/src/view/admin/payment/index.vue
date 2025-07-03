<template>
    <b-modal v-model="isOpenModal" id="card-modal" :title="$t('title.pay')" @ok="handleSubmit" @close="cancelModal" size="lg"
                 :hide-footer="true"
                 :visible="showModal" :no-close-on-esc="true" :no-close-on-backdrop="true"
        >
            
                <b-form @submit.stop.prevent="handleSubmit" novalidate>
                    <div class="row">
                        <div class="mb-4" v-if="cardDetails.length > 0">
                            <label class="form-label mb-2">{{$t('title.selectAnExistingCard')}}</label>
                            <div :class="formErrors.first('card_id')?`is-invalid`:``" v-for="card in cardDetails" :key="card.id" class="form-check mb-2">
                                <input type="radio" :value="card.id" v-model="formFields.card_id" class="form-check-input" :id="'card-' + card.id" @focus="removeError('card_id')">
                                <div class="card-info-box p-3 rounded" style="background-color: #f7f5ff;">
                                    <div class="d-flex flex-column">
                                        <div class="fw-bold fs-5">
                                            {{ cardDetailMaskedCardNumber(card.card_no) }}
                                        </div>
                                        <div class="text-muted mt-1">
                                        {{ card.card_holder_name }} {{$t('title.expires')}} {{ card.expiry_year }}/{{ card.expiry_month }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-check mt-2">
                                <input type="radio" value="new" v-model="formFields.card_id" class="form-check-input" id="newCardOption">
                                <label for="newCardOption" class="form-check-label">
                                    {{$t('title.useANewCard')}}
                                </label>
                            </div>
                            <div class="invalid-feedback mt-3" v-if="formErrors.first('card_id')">
                                <span v-for="(error, index) in formErrors.get('card_id')"
                                    :key="index">{{
                                        error
                                    }}</span>
                            </div>
                        </div>
                        

                        <div class="row" v-if="formFields.card_id === 'new'">
                            <div class="col-12 mx-0 mb-3">
                                <label for="cardHolderName"
                                    class="text-secondary form-label mb-3">{{ this.$t('title.holdersName') }}</label>
                                <div
                                    :class="formErrors.first('card_holder_name')?`input-group is-invalid`:`input-group`">
                                    <input type="text" id="cardHolderName"
                                        class="form-control input-grey fs-14 default-input mini-input"
                                        v-model="formFields.card_holder_name"
                                        @keypress="inputCardHolderName"
                                        @focus="removeError('card_holder_name')"
                                    />
                                </div>
                                <div class="invalid-feedback" v-if="formErrors.first('card_holder_name')">
                                            <span v-for="(error, index) in formErrors.get('card_holder_name')"
                                                :key="index">{{
                                                    error
                                                }}</span>
                                </div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <label for="cardNumber"
                                    class="text-secondary form-label mb-3">{{ this.$t('title.creditCardNumber') }}</label>
                                <div :class="formErrors.first('card_no')?`input-group is-invalid`:`input-group`">
                                    <input
                                        type="text"
                                        id="cardNumber"
                                        class="form-control input-grey fs-14 default-input mini-input"
                                        v-model="creditCardInput"
                                        @input="validateCardNo"
                                        @focus="showFullCardNumber"
                                        @blur="maskCardNumber"
                                    />
                                    <span class="payment-card">
                                        <svg width="35" height="24" class="icon icon-Mastercard">
                                            <use :href="icons + '#icon-Mastercard'"></use>
                                        </svg>
                                        <svg width="35" height="24" class="icon icon-Visa">
                                            <use :href="icons + '#icon-Visa'"></use>
                                        </svg>
                                        <svg width="35" height="24" class="icon icon-DinersClub">
                                            <use :href="icons + '#icon-DinersClub'"></use>
                                        </svg>
                                    </span>
                                </div>
                                <div class="invalid-feedback" v-if="formErrors.first('card_no')">
                                    <span v-for="(error, index) in formErrors.get('card_no')" :key="index">
                                        {{ error }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6 mx-0 mb-3">
                                <label for="expiryMonthYear"
                                    class="text-secondary form-label mb-3">{{ this.$t('title.expiration') }}</label>
                                <div
                                    :class="formErrors.first('expiry_month')?`input-group is-invalid`:formErrors.first('expiry_year')?`input-group is-invalid`:formErrors.first('expiry')?`input-group is-invalid`:`input-group`">
                                    <input type="text" id="expiryMonthYear"
                                        class="form-control input-grey fs-14 default-input mini-input"
                                        placeholder="MM/YY"
                                        v-model="expiryMonthYear"
                                        @input="validateExpiry"
                                        @paste="validateExpiry"
                                        @focus="removeError(['expiry_month','expiry_year', 'expiry'])"
                                    />
                                </div>
                                <div class="invalid-feedback" v-if="formErrors.first('expiry_month')">
                                    <span v-for="(error, index) in formErrors.get('expiry_month')" :key="index">
                                        {{ error }}
                                    </span>
                                </div>
                                <div class="invalid-feedback" v-if="formErrors.first('expiry_year')">
                                    <span v-for="(error, index) in formErrors.get('expiry_year')" :key="index">
                                        {{ error }}
                                    </span>
                                </div>
                                <div class="invalid-feedback" v-if="formErrors.first('expiry')">
                                    <span v-for="(error, index) in formErrors.get('expiry')" :key="index">
                                        {{ error }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6 mx-0 mb-3">
                                <label for="cvv" class="text-secondary form-label mb-3">CVC</label>
                                <div :class="formErrors.first('cvv')?`input-group is-invalid`:`input-group`">
                                    <!-- ADD HERE CLASS WAS-VALIDATED OR IS-INVALID  -->
                                    <input type="password"
                                        id="cvv"
                                        class="form-control input-grey fs-14 default-input mini-input"
                                        placeholder="CVC"
                                        v-model="formFields.cvv"
                                        @input="validateCVV"
                                        @focus="removeError('cvv')"
                                    />
                                    <svg width="16" height="16" class="icon icon-CVC">
                                        <use :href="icons + '#icon-CVC'"></use>
                                    </svg>
                                </div>
                                <div class="invalid-feedback" v-if="formErrors.first('cvv')">
                                            <span v-for="(error, index) in formErrors.get('cvv')" :key="index">{{
                                                    error
                                                }}</span>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="col-12 d-flex justify-content-end gap-4 mb-3">
                            <b-button :disabled="loader" variant="primary" class="btn main-btn col fs-20 h-44 save-button" id="saveBtn"
                                    type="submit">
                                <i class="fa fa-spinner fa-spin" v-if="loader"></i>
                            {{$t('title.pay')}} {{plan?.currency?.symbol}}{{plan.amount}}
                            </b-button>
                        </div>
                    </div>
                </b-form>
           
        </b-modal>
</template>

<script>
import { mapGetters } from "vuex";
import Error from "@/Util/Error";
import icons from '@/assets/icons.svg';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';
import { request } from "@/Util/Request";

const DEFAULT_FORM_STATE = {
    card_holder_name: null,
    card_no: null,
    expiry_month: null,
    expiry_year: null,
    cvv: null,
    card_id: 'new'
};

export default {
    name: 'Payment',
    data() {
        return {
            icons: icons,
            showModal: false,
            formFields: { ...DEFAULT_FORM_STATE },
            formErrors: new Error({}),
            dropdowns: {
                countries: [],
            },
            isSubscribedPlan: false,
            isEditing: false,
            creditCardInput: '',
            isOpenModal: false,
            plan: [],
            selectedCardId: null,
            cardDetails: []
        };
    },
    mounted() {
        this.getCardDetails();

        window.addEventListener("keydown", (event) => {
            if ( document.activeElement.className == 'vue-treeselect__input' && event.key === 'Tab' && !event.shiftKey ) {
                document.getElementById('cancelBtn').classList.add('active');
            }

            if ( document.activeElement == document.getElementById('cancelBtn') && event.key === 'Tab' && !event.shiftKey ) {
                document.getElementById('cancelBtn').classList.remove('active');
                document.getElementById('saveBtn').classList.add('active');
            }

            if ( document.activeElement == document.getElementById('saveBtn') && event.key === 'Tab' && !event.shiftKey ) {
                document.getElementById('saveBtn').classList.remove('active');
            }
        });
    },
    methods: {
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
        cancelModal() {
            this.isOpenModal = false
        },
        openCloseModal(plan) {
            this.plan = plan;
            this.isOpenModal = !this.isOpenModal;
        },
        showFullCardNumber() {
            this.isEditing = true;
            this.creditCardInput = this.formFields.card_no;
        },
        maskCardNumber() {
            this.isEditing = false;
            this.formFields.card_no = this.creditCardInput;
            this.creditCardInput = this.maskedCardNumber;
        },
        inputCardHolderName(evt) {
            evt = evt || window.event;
            const charCode = evt.which ? evt.which : evt.keyCode;
            const value = evt.target.value;

            if ( value.length >= 26 ) {
                evt.preventDefault();
            } else {
                this.formFields.card_holder_name = `${ value.toString().slice(0, 25) }`;
                return true;
            }
        },
        closeCreditCardModal() {
            this.showModal = false;
            this.formErrors = new Error({});
        },
        async getDropdowns() {
            try {
                const response = await request({
                    method: 'get',
                    url: `/dropdowns/countries`
                });

                const { data } = response;
                this.dropdowns.countries = data;
            } catch (error) {
                if ( error.request && error.request.status && error.request.status !== 401 ) {
                    this.notifyError();
                }
            }
        },
        async handleSubmit() {
            try {
                this.isSubscribedPlan = true;

                const response = await request({
                    method: 'post',
                    url: `/payment`,
                    data: {
                        ...this.formFields,
                        amount: (this.plan || {}).amount,
                        plan_id: (this.plan || {}).id,
                        user_id: this.user.id // Pass the user ID from route query
                    }
                });

                let { data } = response;

                if (data && data.status === 'complete') {
                    this.notifySuccess('Payment processed successfully');
                    this.isOpenModal = false;
                    this.$router.push({ name: 'AdminChildUserIndex' }); // Redirect to user list after successful payment
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
                    if ( error.data && error.data.message && error.request.status == 500 ) {
                        console.log(error.data.message);
                        this.notifyErrormessage(error.data.message);
                    } else {
                        this.notifyError();
                    }
                }
                
            } finally {
                this.isSubscribedPlan = false;
            }
        },
        validateExpiry(event) {
            const cursorPosition = event.target.selectionStart;
            const isDelete = ( event.inputType === "deleteContentForward" || event.inputType === "deleteContentBackward" );
            const newCursortPosition = isDelete ? cursorPosition : cursorPosition - 1;

            const value = event.target.value.replace(/[^0-9]/g, '');
            if ( value.length >= 3 ) {
                event.target.value = `${ value.toString().slice(0, 2) }/${ value.toString().slice(2, 4) }`;
            } else {
                event.target.value = value;
            }
            if ( isDelete ) {
                event.target.setSelectionRange(newCursortPosition, newCursortPosition);
            }
        },
        validateCVV(event) {
            const value = event.target.value.replace(/[^0-9]/g, '');
            if ( value.length > 0 && value.length <= 3 ) {
                this.formFields.cvv = value;
            } else {
                this.formFields.cvv = `${ value.toString().slice(0, 3) }`;

            }
        },
        validateCardNo(event) {
            let charCode = event.which ? event.which : event.keyCode;
            if (
                ( charCode > 31 && ( charCode < 48 || charCode > 57 ) && charCode !== 46 ) ||
                event.target.value + String.fromCharCode(charCode) > 60
            ) {
                event.preventDefault();
            } else {
                const value = event.target.value.replace(/[^0-9]/g, '');
                this.formFields.card_no = `${ value.toString().slice(0, 16) }`;
                this.creditCardInput = this.formFields.card_no;
                return true;
            }
        },
        removeError(key) {
            if ( typeof key === `object` ) {
                for (let i = 0; i < key.length; i++) {
                    this.formErrors.remove(key[i]);
                }
            } else {
                this.formErrors.remove(key);
            }
        },
        openModal() {
            if ( _.isEmpty(this.cardDetail) ) {
                this.showModal = true;
            } else {
                this.handleSubmit();
            }
        },
        editCardModal() {
            this.formFields = { ...this.cardDetail };
            this.creditCardInput = this.maskedCardNumber;
            this.formFields.card = 'edit';
            this.showModal = true;
        },
        openStatusConfirmationModal() {
            this.$refs.planStatusUpdateModal.handleToggleModal();
        },
        reload() {
            this.$emit('reload');
        }
    },
    computed: {
        ...mapGetters(['loader', 'user']),
        maskedCardNumber() {
            if (this.isEditing) {
                return this.formFields.card_no;
            }

            const length = this.formFields.card_no.length;
            console.log(length)
            let cardNumber = this.formFields.card_no;

            // Initialize the formatted number
            let formattedNumber = '';

            // If there are no digits, return empty
            if (length === 0) {
                return '';
            }

            // Loop through the card number and format it
            for (let i = 0; i < length; i++) {
                // Add a dash after every 4 digits, unless it's the last group of digits
                if (i > 0 && i % 4 === 0) {
                    formattedNumber += '-';
                }

                // Mask digits with 'X' until the last 4 digits
                if (length > 12) {
                    formattedNumber += (i < 12) ? 'X' : cardNumber[i];
                } else {
                    formattedNumber += 'X';
                }
            }
            return formattedNumber;
        },
        countryId: {
            get() {
                return this.dropdowns.countries.find(option => option.id === this.formFields.country_id) ?? null;
            },
            set(value) {
                this.formFields.country_id = value ? value.id : null;
            }
        },
        expiryMonthYear: {
            get() {
                let monthYear = null;
                if ( this.formFields.expiry_month ) {
                    monthYear = this.formFields.expiry_month;
                    if ( monthYear > 1 && monthYear <= 9 && monthYear.toString().length == 1 ) {
                        monthYear = `0${ this.formFields.expiry_month }`;
                    } else {
                        monthYear = this.formFields.expiry_month;
                    }
                }
                if ( this.formFields.expiry_year ) {
                    monthYear += '/' + this.formFields.expiry_year;
                }
                return monthYear;
            },
            set(value) {
                const currentYear = new Date().getFullYear().toString();
                const currentMonth = new Date().getMonth().toString();
                const actualVal = value.replace(/\D/g, '');
                const month = `${ actualVal.slice(0, 2) }`;
                const year = `${ actualVal.slice(2, 4) }`;
                if ( month >= 1 && month <= 12 ) {
                    if ( month > 1 && month <= 9 && month.length == 1 ) {
                        this.formFields.expiry_month = `0${ month }`;
                    } else {
                        this.formFields.expiry_month = month;
                    }
                } else {
                    if ( month > 12 ) {
                        this.formFields.expiry_month = 12;
                    } else {
                        this.formFields.expiry_month = month;
                    }
                }

                this.formFields.expiry_year = year;

                if ( year >= currentYear.slice(2, 4) || ( year == currentYear.slice(2, 4) && month >= parseInt(currentMonth) ) ) {
                    this.formFields.expiry_year = year;
                }

                if ( year < 1 ) {
                    this.formFields.expiry_year = '';
                }
            }
        }
    },
    watch: {
        'cardDetail': function (newVal) {
            if ( !_.isEmpty(newVal) ) {
                this.formFields = {
                    ...newVal
                };
            } else {
                this.formFields.card = 1;
            }
        }
    }
};
</script>

<style>
.save-button.active {
    background-color: var(--sec-color) !important;
}

.credit-card .mr-4 {
    margin-right: 2px;
}

.payment-card {
    position: absolute;
    right: 0px;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    gap: 4px;
    z-index: 10
}

.icon-CVC {
    position: absolute;
    right: 11px;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    gap: 4px;
    z-index: 10
}

.card-info-box {
  font-family: 'Arial', sans-serif;
  /* width: fit-content; */
  background-color: #f7f5ff;
  box-shadow: 0 0 6px rgba(0, 0, 0, 0.05);
}

</style>
