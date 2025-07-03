<template>
  <div class="container my-5">
    <h1 class="text-center mb-5">{{$t('title.chooseYourPlan')}}</h1>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <div class="col" v-for="plan in plans" :key="plan.id">
        <div class="card h-100 text-center shadow-sm">
          <div class="card-header bg-primary text-white">
            <h4 class="my-0">{{ plan.title }}</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
              {{ plan.currency.symbol }}{{ plan.amount }} <small class="text-muted">/{{ plan.duration.slice(0, 2) }}</small>
            </h1>
            <ul class="list-unstyled mt-3 mb-4">
              <li v-html="plan.notes"></li>
            </ul>
            <button v-if="userActivePlan && (userActivePlan?.plan?.id == plan.id && userActivePlan?.is_subscribe == 1)" class="btn btn-success" @click="openCardDetailModal">
              {{$t('title.active')}}
            </button>
            <button v-if="userActivePlan.length == 0" class="btn btn-outline-primary" @click="selectPlan(plan)">
              {{$t('title.choose')}}
            </button>

            <!-- subscribe and unsubscribe user plan -->
            <button v-if="userActivePlan && (userActivePlan?.plan?.id == plan.id && userActivePlan?.is_subscribe == 1)" class="btn btn-danger ml-2"  @click="updatePlanStatus(userActivePlan, 0)">
              {{$t('title.unSubscribe')}}
            </button>
            <button v-if="userActivePlan && (userActivePlan?.plan?.id == plan.id && userActivePlan?.is_subscribe == 0)" class="btn btn-warning ml-2"  @click="updatePlanStatus(userActivePlan, 1)">
              {{$t('title.subscribe')}}
            </button>

            <p class="mt-3 text-info" v-if="userActivePlan && (userActivePlan?.plan?.id == plan.id && userActivePlan?.is_subscribe == 1 && userActivePlan?.expired_at)">
              {{$t('title.autorenewMessage')}} <strong>{{ formattedExpiryDate(userActivePlan.expired_at) }}</strong>
            </p>

            <p class="mt-3 text-danger" v-if="(userActivePlan.length == 0) && (expiredPlan) && (expiredPlan?.plan?.id == plan.id && expiredPlan?.expired_at)">
              {{$t('title.expiredPlanMessage')}} <strong>{{ formattedExpiryDate(expiredPlan.expired_at) }}</strong>
            </p>
          </div>
        </div>
      </div>
    </div>
    <Payment ref="payment"/>
    <CardDetailModal ref="cardDetailModal"/>
    <SubscribeUnsubscribePlanModal ref="updateStatusModal" @getUserActivePlan="getUserActivePlan"/>
  </div>
</template>

<script>
import { request } from "@/Util/Request";
import Payment from "../payment/index";
import CardDetailModal from "../payment/modal/cardDetailModal";
import SubscribeUnsubscribePlanModal from "./modal/subscribeUnsubscribePlanModal";
import { mapGetters } from "vuex";
export default {
  name: 'PlanPage',
  data() {
    return {
      plans: [],
      expiredPlan: []
    };
  },
  components: {
    Payment,
    CardDetailModal,
    SubscribeUnsubscribePlanModal
  },
  mounted() {
    this.getPlans();
    this.getUserActivePlan();
    this.getUserExpiredPlan();
  },
  computed: {
    ...mapGetters(['userActivePlan'])
  },
  methods: {
    formattedExpiryDate(expiredDate) {      
      const date = new Date(expiredDate)
      const options = { day: 'numeric', month: 'long', year: 'numeric' }
      return date.toLocaleDateString('en-GB', options)
    },
    async getUserActivePlan() {
      try {
        const { data } = await request({ url: '/user-active-plan' });
        this.$store.dispatch('setUserActivePlan', data);
      } catch (err) {
        console.error('Failed to load active plan:', err);
      }
    },
    async getPlans() {
        try {
            const { data } = await request({
                url: '/plans'
            });
            this.plans = data;
        } catch (error) {
            console.error(error);
            this.notifyError();
        }
    },
    async getUserExpiredPlan() {
        try {
            const { data } = await request({
                url: '/user-expired-plan'
            });
            this.expiredPlan = data;
        } catch (error) {
            console.error(error);
            this.notifyError();
        }
    },
    selectPlan(plan) {
        this.$refs['payment'].openCloseModal(plan);
    },
    openCardDetailModal() {
      let cardDetail = this.userActivePlan?.card_detail;
      this.$refs['cardDetailModal'].openCloseModal(cardDetail);
    },
    async updatePlanStatus(activePlan, status) {
        this.$refs['updateStatusModal'].openCloseModal(activePlan, status);
    }
  },
};
</script>
