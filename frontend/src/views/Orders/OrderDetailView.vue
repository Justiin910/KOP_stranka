<template>
  <div class="min-h-screen bg-white dark:bg-gray-900">
    <div class="max-w-4xl mx-auto px-6 py-12">
      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="text-center">
          <svg
            class="animate-spin h-12 w-12 mx-auto text-indigo-600"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
            ></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
          <p class="mt-4 text-gray-600 dark:text-gray-400">Načítavanie objednávky...</p>
        </div>
      </div>

      <!-- Error State -->
      <div
        v-else-if="error"
        class="bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 rounded-lg p-6 text-center"
      >
        <p class="text-red-700 dark:text-red-400 mb-4">{{ error }}</p>
        <button
          @click="$router.back()"
          class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors"
        >
          Späť
        </button>
      </div>

      <!-- Order Details -->
      <div v-else-if="order">
        <!-- Header with Back Button -->
        <div class="flex items-center justify-between mb-8">
          <div>
            <button
              @click="$router.back()"
              class="mb-4 text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium flex items-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 19l-7-7 7-7"
                ></path>
              </svg>
              Späť
            </button>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
              Objednávka #{{ order.id }}
            </h1>
          </div>
          <span
            class="px-4 py-2 rounded-lg text-sm font-semibold"
            :class="getStatusClass(order.status)"
          >
            {{ order.status }}
          </span>
        </div>

        <!-- Order Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <!-- Order Date -->
          <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
          >
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Dátum objednávky</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">
              {{ formatDate(order.created_at) }}
            </p>
          </div>

          <!-- Total Amount -->
          <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
          >
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Celková suma</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">
              {{ formatPrice(order.total) }} €
            </p>
          </div>

          <!-- Order Reference -->
          <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
          >
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Referenčné číslo</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white font-mono">
              {{ order.reference }}
            </p>
          </div>
        </div>

        <!-- Order Items -->
        <div
          class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8"
        >
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
            Položky objednávky
          </h2>

          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th
                    class="px-4 py-3 text-left text-gray-600 dark:text-gray-400 font-medium"
                  >
                    Produkt
                  </th>
                  <th
                    class="px-4 py-3 text-center text-gray-600 dark:text-gray-400 font-medium"
                  >
                    Množstvo
                  </th>
                  <th
                    class="px-4 py-3 text-right text-gray-600 dark:text-gray-400 font-medium"
                  >
                    Jednotková cena
                  </th>
                  <th
                    class="px-4 py-3 text-right text-gray-600 dark:text-gray-400 font-medium"
                  >
                    Spolu
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr
                  v-for="item in order.items"
                  :key="item.id"
                  class="hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                  <td class="px-4 py-4 text-gray-900 dark:text-white font-medium">
                    {{ item.product_name }}
                  </td>
                  <td class="px-4 py-4 text-center text-gray-900 dark:text-white">
                    {{ item.quantity }}
                  </td>
                  <td class="px-4 py-4 text-right text-gray-900 dark:text-white">
                    {{ formatPrice(item.price) }} €
                  </td>
                  <td
                    class="px-4 py-4 text-right text-gray-900 dark:text-white font-semibold"
                  >
                    {{ formatPrice(item.quantity * item.price) }} €
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Order Total -->
          <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
            <div class="flex justify-end items-center gap-4">
              <span class="text-lg font-bold text-gray-900 dark:text-white"
                >Celková suma:</span
              >
              <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                {{ formatPrice(order.total) }} €
              </span>
            </div>
          </div>
        </div>

        <!-- Delivery & Payment Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <!-- Delivery Info -->
          <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
          >
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
              Doručovacia adresa
            </h3>
            <div class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
              <p v-if="address.fullName">
                <strong>{{ address.fullName }}</strong>
              </p>
              <p v-if="address.street">{{ address.street }}</p>
              <p v-if="address.city || address.zip">
                {{ address.zip }} {{ address.city }}
              </p>
              <p v-if="address.country">{{ address.country }}</p>
              <p v-if="address.phone" class="mt-3">
                <strong>Telefón:</strong> {{ address.phone }}
              </p>
              <p v-if="address.email" class="mt-1">
                <strong>Email:</strong> {{ address.email }}
              </p>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
              <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">
                Metóda doručenia:
              </p>
              <p class="text-sm font-medium text-gray-900 dark:text-white">
                {{ order.delivery_method }}
              </p>
            </div>
          </div>

          <!-- Payment Info -->
          <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
          >
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
              Informácie o platbe
            </h3>
            <div class="text-sm text-gray-700 dark:text-gray-300 space-y-3">
              <div>
                <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">
                  Metóda platby:
                </p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ order.payment_method }}
                </p>
              </div>
              <div>
                <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">
                  Stav objednávky:
                </p>
                <p class="text-sm font-medium">
                  <span
                    class="px-2 py-1 rounded text-xs font-semibold"
                    :class="getStatusClass(order.status)"
                  >
                    {{ order.status }}
                  </span>
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-3">
          <button
            @click="$router.back()"
            class="px-6 py-2 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded-lg font-medium transition-colors"
          >
            Späť na zoznam
          </button>
          <button
            v-if="order.status === 'Doručené'"
            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors"
          >
            Stiahnuť faktúru
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from "@/api.ts";

export default {
  name: "OrderDetailView",
  data() {
    return {
      order: null,
      loading: true,
      error: null,
      address: {},
    };
  },
  computed: {
    orderId() {
      return this.$route.params.id;
    },
  },
  mounted() {
    this.fetchOrder();
  },
  methods: {
    async fetchOrder() {
      this.loading = true;
      this.error = null;

      try {
        const response = await api.get(`/api/orders/${this.orderId}`);

        if (response.data && response.data.success) {
          this.order = response.data.order;

          // Parse address if it's a JSON string
          if (typeof this.order.address === "string") {
            this.address = JSON.parse(this.order.address);
          } else {
            this.address = this.order.address || {};
          }
        } else if (Array.isArray(response.data)) {
          // Fallback if the API returns different format
          this.order = response.data;
          this.address = this.order?.address || {};
        } else {
          this.error = "Chyba pri načítaní objednávky";
        }
      } catch (err) {
        console.error("Error fetching order:", err);
        if (err.response?.status === 403) {
          this.error = "Nemáte prístup k tejto objednávke";
        } else if (err.response?.status === 404) {
          this.error = "Objednávka nebola nájdená";
        } else {
          this.error = "Chyba pri načítaní objednávky";
        }
      } finally {
        this.loading = false;
      }
    },
    formatDate(date) {
      if (!date) return "";
      const d = new Date(date);
      return d.toLocaleDateString("sk-SK", {
        year: "numeric",
        month: "long",
        day: "numeric",
      });
    },
    formatPrice(price) {
      return parseFloat(price).toFixed(2);
    },
    getStatusClass(status) {
      const classes = {
        Doručené: "bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200",
        "V preprave": "bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200",
        "Spracováva sa":
          "bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200",
        čakajúce: "bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200",
        Zrušené: "bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200",
      };
      return (
        classes[status] || "bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200"
      );
    },
  },
};
</script>
