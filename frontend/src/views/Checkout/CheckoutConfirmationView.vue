<template>
  <div class="page-checkout-confirmation-bg">
    <div class="max-w-7xl mx-auto px-6 py-12">
      <!-- Progress Steps -->
      <div class="mb-12">
        <div class="flex items-center justify-center">
          <div v-for="(step, index) in steps" :key="index" class="flex items-center">
            <div class="flex flex-col items-center">
              <div
                class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-colors"
                :class="
                  step.active
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400'
                "
              >
                {{ index + 1 }}
              </div>
              <span
                class="mt-2 text-sm font-medium whitespace-nowrap"
                :class="
                  step.active
                    ? 'text-blue-600 dark:text-blue-400'
                    : 'text-gray-500 dark:text-gray-400'
                "
              >
                {{ step.label }}
              </span>
            </div>
            <div
              v-if="index < steps.length - 1"
              class="w-16 h-1 mx-4 mb-6"
              :class="
                steps[index + 1].active ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'
              "
            ></div>
          </div>
        </div>
      </div>

      <!-- Success message -->
      <div class="text-center mb-12 max-w-2xl mx-auto">
        <div
          class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full mb-4"
        >
          <i class="fas fa-check-circle text-3xl text-green-600 dark:text-green-400"></i>
        </div>
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
          {{ $t('pages.checkout.confirmation.title') }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400">{{ $t('pages.checkout.confirmation.thank_you') }}</p>
      </div>

      <!-- Order reference -->
      <div
        class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 mb-8 border border-blue-200 dark:border-blue-800"
      >
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $t('pages.checkout.confirmation.order_reference') }}</p>
        <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
          {{ order?.reference }}
        </p>
      </div>

      <!-- Order details -->
      <div v-if="order" class="space-y-8">
        <!-- Delivery info -->
        <div
          class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700"
        >
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
            {{ $t('pages.checkout.confirmation.delivery') }}
          </h2>

          <div class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $t('pages.checkout.confirmation.delivery_method') }}</p>
                <p class="font-medium text-gray-900 dark:text-white">
                  {{ getDeliveryMethodLabel(order.delivery?.method) }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ $t('pages.checkout.confirmation.delivery_fee') }}
                </p>
                <p class="font-medium text-gray-900 dark:text-white">
                  {{ getDeliveryFee(order.delivery?.method).toFixed(2) }} €
                </p>
              </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700" />

            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                {{ $t('pages.checkout.confirmation.shipping_address') }}
              </p>
              <div class="space-y-1 text-gray-900 dark:text-white">
                <p class="font-medium">{{ order.delivery?.address?.fullName }}</p>
                <p>{{ order.delivery?.address?.street }}</p>
                <p>
                  {{ order.delivery?.address?.zip }} {{ order.delivery?.address?.city }}
                </p>
                <p>{{ order.delivery?.address?.country }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                  {{ order.delivery?.phone }}
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ order.delivery?.address?.email }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment info -->
        <div
          class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700"
        >
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
            {{ $t('pages.checkout.confirmation.payment') }}
          </h2>

          <div class="space-y-2">
            <div class="flex items-center gap-3">
              <i
                :class="getPaymentMethodIcon(order.payment?.method)"
                class="text-2xl"
              ></i>
              <div>
                <p class="font-medium text-gray-900 dark:text-white">
                  {{ getPaymentMethodLabel(order.payment?.method) }}
                </p>
                <p
                  class="text-sm text-gray-600 dark:text-gray-400"
                  v-if="order.payment?.method === 'card'"
                >
                  {{ $t('pages.checkout.confirmation.card_ending', { last4: order.payment?.card?.last4 }) }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Order items -->
        <div
          class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700"
        >
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
            {{ $t('pages.checkout.confirmation.order_items') }}
          </h2>

          <div class="space-y-4">
            <div
              v-for="item in order.items"
              :key="item.id"
              class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0 last:pb-0"
            >
              <div class="flex-1">
                <p class="font-medium text-gray-900 dark:text-white">{{ item.title }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ $t('pages.checkout.confirmation.quantity') }} {{ item.quantity }}
                </p>
              </div>
              <div class="text-right">
                <p class="font-semibold text-gray-900 dark:text-white">
                  {{ (parseFloat(String(item.price)) * item.quantity).toFixed(2) }} €
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ parseFloat(String(item.price)).toFixed(2) }} {{ $t('pages.checkout.delivery.per_unit') }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Order summary -->
        <div
          class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700"
        >
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
            {{ $t('pages.checkout.payment.order_summary') }}
          </h2>

          <div class="space-y-3 mb-6 border-b border-gray-200 dark:border-gray-700 pb-6">
            <div class="flex justify-between text-gray-600 dark:text-gray-400">
              <span>{{ $t('pages.checkout.confirmation.items_total_label') }}</span>
              <span
                >{{
                  (order.total - getDeliveryFee(order.delivery?.method)).toFixed(2)
                }}
                €</span
              >
            </div>
            <div class="flex justify-between text-gray-600 dark:text-gray-400">
              <span>{{ $t('pages.checkout.payment.shipping') }}</span>
              <span>{{ getDeliveryFee(order.delivery?.method).toFixed(2) }} €</span>
            </div>
            <div
              class="flex justify-between text-lg font-bold text-gray-900 dark:text-white"
            >
              <span>{{ $t('pages.checkout.confirmation.total_due') }}</span>
              <span>{{ parseFloat(order.total).toFixed(2) }} €</span>
            </div>
          </div>

          <div class="text-sm text-gray-600 dark:text-gray-400">
            <p>{{ $t('pages.checkout.confirmation.created_at') }}: {{ formatDate(order.created_at) }}</p>
            <p>
              {{ $t('pages.checkout.confirmation.status') }}: <span class="font-medium">{{ getOrderStatus(order.status || 'pending') }}</span>
            </p>
          </div>
        </div>

        <!-- Next steps -->
        <div
          class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6 border border-green-200 dark:border-green-800"
        >
          <h3 class="font-semibold text-gray-900 dark:text-white mb-3">{{ $t('pages.checkout.confirmation.next_steps') }}</h3>
          <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
            <li class="flex items-start gap-2">
              <i
                class="fas fa-check-circle text-green-600 dark:text-green-400 mt-0.5"
              ></i>
              <span>{{ $t('pages.checkout.confirmation.email_sent_to', { email: order.delivery?.address?.email }) }}</span>
            </li>
            <li class="flex items-start gap-2">
              <i
                class="fas fa-check-circle text-green-600 dark:text-green-400 mt-0.5"
              ></i>
              <span>{{ $t('pages.checkout.confirmation.order_in_processing') }}</span>
            </li>
            <li class="flex items-start gap-2">
              <i
                class="fas fa-check-circle text-green-600 dark:text-green-400 mt-0.5"
              ></i>
              <span>{{ $t('pages.checkout.confirmation.will_receive_notification') }}</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Action buttons -->
      <div class="flex gap-4 mt-12">
        <router-link to="/" class="flex-1 text-center btn-primary-lg">
          {{ $t('pages.checkout.confirmation.continue_shopping') }}
        </router-link>
        <router-link
          v-if="isAuthenticated"
          to="/orders"
          class="flex-1 text-center bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-semibold py-3 rounded-lg"
        >
          {{ $t('pages.checkout.confirmation.my_orders') }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { useRouter } from "vue-router";
import { useCartStore } from '../../stores/cartStore';

export default {
  name: "CheckoutConfirmationView",
  setup() {
    const router = useRouter();
    const cartStore = useCartStore();
    return { router, cartStore };
  },
  data() {
    return {
      order: null,
      isAuthenticated: false,
      currentStep: 3,
    };
  },
  computed: {
    steps() {
      const labels = [
        this.$t('cart.steps.cart'),
        this.$t('pages.checkout.delivery.step'),
        this.$t('cart.steps.payment'),
        this.$t('cart.steps.confirmation'),
      ];
      return labels.map((label, idx) => ({ label, active: idx === this.currentStep }));
    },
  },
  methods: {
    loadOrder() {
      const raw = localStorage.getItem("checkoutOrder");
      console.log("🔍 Loading order from localStorage:", raw ? "FOUND" : "NOT FOUND");
      if (!raw) {
        // redirect to home if no order
        console.warn("⚠️ No order data - redirecting to home");
        this.router.push("/");
        return;
      }
      try {
        this.order = JSON.parse(raw);
        console.log("✅ Order loaded:", this.order.reference);
        
        // Try to send confirmation email if user is logged in
        this.sendConfirmationEmail();
      } catch (e) {
        console.error("Invalid checkoutOrder in localStorage", e);
        this.router.push("/");
      }
    },

    async sendConfirmationEmail() {
      try {
        const token = localStorage.getItem("token");
        if (!token || !this.order?.id) {
          console.log("ℹ️ Skipping email: not authenticated or no order ID");
          return;
        }

        console.log("📧 Sending confirmation email...");
        const response = await fetch(`/api/orders/${this.order.id}/send-confirmation`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        });

        if (response.ok) {
          console.log("✅ Confirmation email sent successfully");
        } else {
          console.warn("⚠️ Failed to send confirmation email:", response.status);
        }
      } catch (error) {
        console.error("❌ Error sending confirmation email:", error);
      }
    },

    getDeliveryMethodLabel(method) {
      const map = { standard: 'method1', express: 'method2', pickup: 'method3' };
      const key = map[method] || null;
      if (!key) return method;
      const name = this.$t(`pages.delivery.delivery_methods.${key}.name`);
      const price = this.$t(`pages.delivery.delivery_methods.${key}.price`);
      return `${name} ${price}`;
    },

    getDeliveryFee(method) {
      if (method === "express") return 9.99;
      if (method === "pickup") return 0;
      return 4.99;
    },

    getPaymentMethodLabel(method) {
      const mapping = {
        card: 'payment_method_card',
        googlepay: 'payment_method_googlepay',
        paypal: 'payment_method_paypal',
      };
      const key = mapping[method];
      if (!key) return method;
      return this.$t(`pages.checkout.payment.${key}`);
    },

    getPaymentMethodIcon(method) {
      if (method === "card") return "fas fa-credit-card text-blue-600";
      if (method === "googlepay") return "fab fa-google text-blue-600";
      if (method === "paypal") return "fab fa-paypal text-blue-600";
      return "fas fa-wallet";
    },

    getOrderStatus(status) {
      const statuses = {
        pending: "Čakajúce spracovanie",
        paid: "Zaplatené",
        cancelled: "Zrušené",
        shipped: "Odoslané",
        delivered: "Doručené",
      };
      return statuses[status] || status;
    },

    formatDate(dateString) {
      try {
        if (!dateString) return "Neznámy dátum";
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return "Neznámy dátum";
        return date.toLocaleDateString("sk-SK", {
          year: "numeric",
          month: "long",
          day: "numeric",
          hour: "2-digit",
          minute: "2-digit",
        });
      } catch {
        return "Neznámy dátum";
      }
    },

  },
  mounted() {
    this.loadOrder();
  },
  unmounted() {
    // Clear localStorage when leaving confirmation page
    localStorage.removeItem("checkoutOrder");
    localStorage.removeItem("checkoutDelivery");
  },
};
</script>
