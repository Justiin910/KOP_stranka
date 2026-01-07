<template>
  <div class="page-cart-bg">
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
                class="mt-2 text-sm font-medium"
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
              class="w-24 h-1 mx-4 mb-6"
              :class="
                steps[index + 1].active ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'
              "
            ></div>
          </div>
        </div>
      </div>

      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Nákupný košík</h1>

      <!-- Empty Cart -->
      <div
        v-if="(cartStore?.cartItems?.length ?? 0) === 0"
        class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 text-center"
      >
        <p class="text-gray-600 dark:text-gray-400">Váš košík je prázdny.</p>
      </div>

      <!-- Cart with Items -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2 space-y-4">
          <div
            v-for="item in (cartStore?.cartItems || [])"
            :key="item.id"
            class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700"
          >
            <div class="flex gap-4">
              <img
                :src="item.image"
                :alt="item.title"
                class="w-24 h-24 object-cover rounded"
              />
              <div class="flex-1">
                <h3 class="font-semibold text-gray-900 dark:text-white">
                  {{ item.title }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                  {{ item.description }}
                </p>

                <div class="flex items-center justify-between mt-4">
                  <div class="flex items-center gap-3">
                    <button
                      @click="decreaseQuantity(item.id)"
                      class="w-8 h-8 rounded border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center"
                    >
                      -
                    </button>
                    <span class="font-medium text-gray-900 dark:text-white">{{
                      item.quantity
                    }}</span>
                    <button
                      @click="increaseQuantity(item.id)"
                      class="w-8 h-8 rounded border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center"
                    >
                      +
                    </button>
                  </div>
                  <button
                    @click="removeItem(item.id)"
                    class="text-red-600 hover:text-red-700 text-sm font-medium"
                  >
                    Odstrániť
                  </button>
                </div>
              </div>
              <div class="text-right">
                <p class="text-xl font-bold text-gray-900 dark:text-white">
                  {{ formatPrice(item.price * item.quantity) }} €
                </p>
                <p
                  v-if="item.quantity > 1"
                  class="text-sm text-gray-500 dark:text-gray-400"
                >
                  {{ formatPrice(item.price) }} € / ks
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
          <div
            class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700 sticky top-6"
          >
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
              Súhrn objednávky
            </h2>

              <div class="space-y-3 mb-6">
              <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>Medzisúčet</span>
                <span>{{ formatPrice(cartStore?.subtotal ?? 0) }} €</span>
              </div>
              <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>Doprava</span>
                <span>{{ formatPrice(shipping) }} €</span>
              </div>
              <div
                class="border-t border-gray-200 dark:border-gray-700 pt-3 flex justify-between text-lg font-bold text-gray-900 dark:text-white"
              >
                <span>Celkom</span>
                <span>{{ formatPrice(total) }} €</span>
              </div>
            </div>

            <button
              @click="proceedToCheckout"
              class="w-full btn-primary-lg py-3 rounded-lg"
            >
              Pokračovať
            </button>

            <button
              @click="continueShopping"
              class="w-full mt-3 btn-secondary py-3 rounded-lg"
            >
              Pokračovať v nákupe
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { useRouter } from "vue-router";
import { useCartStore } from "../stores/cartStore";

interface Step {
  label: string;
  active: boolean;
}

export default defineComponent({
  name: "CartView",
  setup() {
    const router = useRouter();
    const cartStore = useCartStore();
    return { router, cartStore };
  },
  data(): {
    shipping: number;
    steps: Step[];
  } {
    return {
      shipping: 4.99,
      steps: [
        { label: "Košík", active: true },
        { label: "Doručenie", active: false },
        { label: "Platba", active: false },
        { label: "Potvrdenie", active: false },
      ],
    };
  },
  computed: {
    total(): number {
      return (this.cartStore?.subtotal ?? 0) + this.shipping;
    },
  },
  methods: {
    formatPrice(price: number | string): string {
      return parseFloat(String(price)).toFixed(2);
    },
    increaseQuantity(itemId: number): void {
      this.cartStore.increaseQuantity(itemId);
    },
    decreaseQuantity(itemId: number): void {
      this.cartStore.decreaseQuantity(itemId);
    },
    removeItem(itemId: number): void {
      this.cartStore.removeItem(itemId);
    },
    proceedToCheckout(): void {
      this.router.push("/checkout/delivery");
    },
    continueShopping(): void {
      this.router.push("/");
    },
  },
  async mounted() {
    await this.cartStore.initializeAuth();
    await this.cartStore.loadCart();
  },
});
</script>
