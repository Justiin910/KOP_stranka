<template>
  <div class="page-checkout-confirmation-bg">
    <div class="max-w-2xl mx-auto px-6 py-12">
      <!-- Success message -->
      <div class="text-center mb-12">
        <div
          class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full mb-4"
        >
          <i class="fas fa-check-circle text-3xl text-green-600 dark:text-green-400"></i>
        </div>
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
          Objednávka potvrdená!
        </h1>
        <p class="text-gray-600 dark:text-gray-400">Ďakujeme za vašu objednávku</p>
      </div>

      <!-- Order reference -->
      <div
        class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 mb-8 border border-blue-200 dark:border-blue-800"
      >
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Číslo objednávky</p>
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
            Doručenie
          </h2>

          <div class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Spôsob doručenia</p>
                <p class="font-medium text-gray-900 dark:text-white">
                  {{ getDeliveryMethodLabel(order.delivery?.method) }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  Poplatek za doručenie
                </p>
                <p class="font-medium text-gray-900 dark:text-white">
                  {{ getDeliveryFee(order.delivery?.method).toFixed(2) }} €
                </p>
              </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700" />

            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                Doručovacia adresa
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
            Spôsob platby
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
                  Karta končiaca na {{ order.payment?.card?.last4 }}
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
            Položky v objednávke
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
                  Množstvo: {{ item.quantity }}
                </p>
              </div>
              <div class="text-right">
                <p class="font-semibold text-gray-900 dark:text-white">
                  {{ (parseFloat(String(item.price)) * item.quantity).toFixed(2) }} €
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ parseFloat(String(item.price)).toFixed(2) }} € / kus
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
            Súhrn objednávky
          </h2>

          <div class="space-y-3 mb-6 border-b border-gray-200 dark:border-gray-700 pb-6">
            <div class="flex justify-between text-gray-600 dark:text-gray-400">
              <span>Spolu za položky</span>
              <span
                >{{
                  (order.total - getDeliveryFee(order.delivery?.method)).toFixed(2)
                }}
                €</span
              >
            </div>
            <div class="flex justify-between text-gray-600 dark:text-gray-400">
              <span>Doručenie</span>
              <span>{{ getDeliveryFee(order.delivery?.method).toFixed(2) }} €</span>
            </div>
            <div
              class="flex justify-between text-lg font-bold text-gray-900 dark:text-white"
            >
              <span>Celkom k úhradě</span>
              <span>{{ parseFloat(order.total).toFixed(2) }} €</span>
            </div>
          </div>

          <div class="text-sm text-gray-600 dark:text-gray-400">
            <p>Vytvoreno: {{ formatDate(order.created_at) }}</p>
            <p>
              Stav: <span class="font-medium">{{ getOrderStatus(order.status) }}</span>
            </p>
          </div>
        </div>

        <!-- Next steps -->
        <div
          class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6 border border-green-200 dark:border-green-800"
        >
          <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Ďalšie kroky</h3>
          <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
            <li class="flex items-start gap-2">
              <i
                class="fas fa-check-circle text-green-600 dark:text-green-400 mt-0.5"
              ></i>
              <span
                >Potvrdzovací email bol odoslaný na
                {{ order.delivery?.address?.email }}</span
              >
            </li>
            <li class="flex items-start gap-2">
              <i
                class="fas fa-check-circle text-green-600 dark:text-green-400 mt-0.5"
              ></i>
              <span>Vaša objednávka je v procese spracovania</span>
            </li>
            <li class="flex items-start gap-2">
              <i
                class="fas fa-check-circle text-green-600 dark:text-green-400 mt-0.5"
              ></i>
              <span>Dostanete notifikáciu keď bude objednávka odoslaná</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Action buttons -->
      <div class="flex gap-4 mt-12">
        <router-link
          to="/"
          class="flex-1 text-center btn-primary-lg"
        >
          Pokračovať v nákupoch
        </router-link>
        <router-link
          v-if="isAuthenticated"
          to="/orders"
          class="flex-1 text-center bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-semibold py-3 rounded-lg"
        >
          Moje objednávky
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { useRouter } from "vue-router";
import { useCartStore } from "../stores/cartStore";

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
    };
  },
  methods: {
    loadOrder() {
      const raw = sessionStorage.getItem("checkoutOrder");
      if (!raw) {
        // redirect to home if no order
        this.router.push("/");
        return;
      }
      try {
        this.order = JSON.parse(raw);
      } catch (e) {
        console.error("Invalid checkoutOrder in sessionStorage", e);
        this.router.push("/");
      }
    },

    getDeliveryMethodLabel(method) {
      const labels = {
        standard: "Štandardné doručenie (4.99 €)",
        express: "Expresné doručenie (9.99 €)",
        pickup: "Vyzdvihnutie v pobočke (ZDARMA)",
      };
      return labels[method] || method;
    },

    getDeliveryFee(method) {
      if (method === "express") return 9.99;
      if (method === "pickup") return 0;
      return 4.99;
    },

    getPaymentMethodLabel(method) {
      const labels = {
        card: "Platobná karta",
        googlepay: "Google Pay",
        paypal: "PayPal",
      };
      return labels[method] || method;
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
        const date = new Date(dateString);
        return date.toLocaleDateString("sk-SK", {
          year: "numeric",
          month: "long",
          day: "numeric",
          hour: "2-digit",
          minute: "2-digit",
        });
      } catch {
        return dateString;
      }
    },

    async checkAuthStatus() {
      try {
        const response = await fetch("/api/user", {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("auth_token") || ""}`,
          },
        });
        this.isAuthenticated = response.ok;
      } catch (e) {
        this.isAuthenticated = false;
      }
    },
  },
  mounted() {
    this.loadOrder();
    this.checkAuthStatus();
  },
};
</script>
