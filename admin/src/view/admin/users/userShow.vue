<template>
  <div class="main-block user-subscription-list">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4>{{ $t("title.UsersWithPlans") }}</h4>
    </div>

    <div v-if="users.length">
      <div v-for="user in users" :key="user.id" class="user-block mb-5 p-6">
        <div class="row g-3">
          <div
            class="col-md-4"
            v-for="(value, label) in userCardFields(user)"
            :key="label"
          >
            <div class="field-card p-3 border rounded h-100">
              <strong>{{ label }}</strong>
              <div>{{ value }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="text-center text-muted">
      {{ $t("title.NoUsersFound") }}
    </div>
    <div class="text-center mt-4">
      <button @click="goBack" class="btn btn-secondary">
        {{ $t("title.GoBack") }}
      </button>
    </div>
  </div>
</template>

<script>
import { request } from "@/Util/Request";

export default {
  name: "UserSubscriptionList",
  data() {
    return {
      users: [],
    };
  },
  mounted() {
    this.fetchUsers();
  },
  methods: {
    async fetchUsers() {
      try {
        const userId = this.$route.params.id || null;
        const url = userId ? `/users-info?user_id=${userId}` : `/users-info`;
        const response = await request({ url });
        this.users = response.data;
      } catch (error) {
        console.error("Failed to fetch users:", error);
      }
    },
    userCardFields(user) {
      const subscription = user.subscription || {};
      const plan = subscription.plan || {};

      return {
        [this.$t("title.Name")]: user.name || "-",
        [this.$t("title.Email")]: user.email || "-",
        [this.$t("title.Phone")]: user.phone_number
          ? `${user.country_code || ""}${user.phone_number}`
          : "-",
        [this.$t("title.Company")]: user.company_name || "-",
        [this.$t("title.Address")]: user.address || "-",
        [this.$t("title.EmailVerified")]: user.email_verified_at
          ? new Date(user.email_verified_at).toLocaleString()
          : "-",
        [this.$t(
          "title.Projects"
        )]: `${user.owned_projects_count} owned, ${user.assigned_projects_count} assigned`,
        [this.$t("title.TransactionId")]: subscription.transaction_id || "-",
        [this.$t("title.Amount")]: subscription.amount || "-",
        [this.$t("title.Status")]: subscription.status || "-",
        [this.$t("title.Plan")]: plan.name
          ? `${plan.name} (${plan.duration})`
          : plan.duration
          ? `(${plan.duration})`
          : "-",
        [this.$t("title.PlanDescription")]: plan.description || "-",
        [this.$t("title.Renew")]: subscription.is_renew === 1 ? "Yes" : "No",
        [this.$t("title.Subscribe")]: subscription.is_subscribe === 1 ? "Yes" : "No",
        [this.$t("title.Message")]: subscription.message || "-",
      };
    },

    goBack() {
      this.$router.go(-1);
    },
  },
};
</script>

<style scoped>
.user-block {
  background: #f8f9fa;
  border-radius: 8px;
}

.field-card {
  background-color: #fff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  min-height: 150px; /* Increase height */
  min-width: 100%; /* Make card fill the column */
  display: flex;
  flex-direction: column;
  justify-content: center;
  font-size: 16px;
}
</style>
