<template>
    <b-modal v-model="isOpenModal" id="card-modal" :title="$t('title.card')" @ok="handleSubmit" @close="cancelModal" size="lg"
                 :hide-footer="true"
                 :visible="showModal" :no-close-on-esc="true" :no-close-on-backdrop="true"
        >
                <b-form @submit.stop.prevent="handleSubmit" novalidate>
                    <div class="row">
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
                        <hr />
                        <div class="col-12 d-flex justify-content-end gap-4 mb-3">
                            <b-button :disabled="loader" variant="primary" class="btn main-btn col fs-20 h-44 save-button" id="saveBtn"
                                    type="submit">
                                <i class="fa fa-spinner fa-spin" v-if="loader"></i>
                            {{$t('title.submit')}}
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
};

export default {
    name: 'Payment',
    data() {
        return {
            icons: icons,
            showModal: false,
            formFields: { ...DEFAULT_FORM_STATE },
            formErrors: new Error({}),
            isEditing: false,
            creditCardInput: '',
            isOpenModal: false,
            cardDetails: [],
            cardId: null
        };
    },
    mounted() {
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
        cancelModal() {
            this.isOpenModal = false;
        },
        resetCardDetail() {
            this.formFields = { ...DEFAULT_FORM_STATE };
            this.cardId = null;
            this.creditCardInput = null;
            this.formErrors = new Error({});
        },
        openCloseModal(cardDetail) {
           this.resetCardDetail();

            if(cardDetail) {
                this.formFields = { ...cardDetail };
                this.formFields.expiry_year = cardDetail.expiry_year.toString().slice(-2);
                this.cardId = cardDetail.id;
                this.creditCardInput = this.maskedCardNumber;
            }
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
        async handleSubmit() {
            try {
                const response = await request({
                    method: (this.cardId) ? 'put' : 'post',
                    url: (this.cardId) ? `/card-details/${this.cardId}` : `/card-details`,
                    data: {
                        ...this.formFields
                    }
                });

                let { data } = response;

                if (data && response.status === 'success') {
                    if(this.cardId) {
                         this.notifySuccess('Card detail updated successfully');
                    } else {
                         this.notifySuccess('Card detail added successfully');
                    }
                    this.isOpenModal = false;
                    this.$emit('cardDetails');
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
