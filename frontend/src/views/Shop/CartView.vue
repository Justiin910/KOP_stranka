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

      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">{{ $t('cart.title') }}</h1>

      <!-- Empty Cart -->
      <div
        v-if="(cartStore?.cartItems?.length ?? 0) === 0"
        class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 text-center"
      >
        <p class="text-gray-600 dark:text-gray-400">{{ $t('cart.empty') }}</p>
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

                <!-- Display Variant Options -->
                <div v-if="item.variant_options && Object.keys(item.variant_options).length > 0" class="mt-3 space-y-1">
                  <div v-for="(value, key) in item.variant_options" :key="key" class="text-sm text-gray-700 dark:text-gray-300">
                    <span class="font-medium capitalize">{{ key }}:</span> {{ value }}
                  </div>
                  <button 
                    @click="openEditVariantes(item)"
                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium mt-2"
                  >
                    {{ $t('cart.items.edit_options') || 'Edit Options' }}
                  </button>
                </div>

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
                    {{ $t('cart.items.remove') }}
                  </button>
                </div>
              </div>
              <div class="text-right">
                <p class="text-xl font-bold text-gray-900 dark:text-white">
                  {{ formatPrice((item.variant_price || item.price) * item.quantity) }} €
                </p>
                <p
                  v-if="item.quantity > 1"
                  class="text-sm text-gray-500 dark:text-gray-400"
                >
                  {{ formatPrice(item.variant_price || item.price) }} {{ $t('cart.items.per_unit') }}
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
              {{ $t('cart.summary.title') }}
            </h2>

              <div class="space-y-3 mb-6">
              <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>{{ $t('cart.summary.subtotal') }}</span>
                <span>{{ formatPrice(cartStore?.subtotal ?? 0) }} €</span>
              </div>
              <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>{{ $t('cart.summary.shipping') }}</span>
                <span>{{ formatPrice(shipping) }} €</span>
              </div>
              <div
                class="border-t border-gray-200 dark:border-gray-700 pt-3 flex justify-between text-lg font-bold text-gray-900 dark:text-white"
              >
                <span>{{ $t('cart.summary.total') }}</span>
                <span>{{ formatPrice(total) }} €</span>
              </div>
            </div>

            <button
              @click="proceedToCheckout"
              class="w-full btn-primary-lg py-3 rounded-lg"
            >
              {{ $t('cart.summary.checkout') }}
            </button>

            <button
              @click="continueShopping"
              class="w-full mt-3 btn-secondary py-3 rounded-lg"
            >
              {{ $t('cart.summary.continue_shopping') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Variant Edit Modal -->
    <div v-if="editingItem" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
          {{ $t('cart.items.edit_options') || 'Edit Options' }}
        </h3>
        
        <div v-if="editingItem.variant_options && Object.keys(editingItem.variant_options).length > 0" class="space-y-4 mb-6">
          <div v-for="(value, key) in editingItem.variant_options" :key="key">
            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2 capitalize">
              {{ key }}
            </label>
            <select 
              v-model="editingVariants[key]"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500"
            >
              <option v-for="option in editingItem.variants?.[key] || []" :key="option" :value="option">
                {{ option }}
              </option>
            </select>
          </div>
        </div>

        <div class="flex gap-3">
          <button 
            @click="cancelEditVariantes"
            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition"
          >
            {{ $t('common.cancel') || 'Cancel' }}
          </button>
          <button 
            @click="saveEditVariantes"
            class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition"
          >
            {{ $t('common.save') || 'Save' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { useRouter } from "vue-router";
import { useCartStore } from "../../stores/cartStore";
import api from "../../api";

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
    currentStep: number;
    editingItem: any;
    editingVariants: Record<string, string>;
  } {
    return {
      shipping: 4.99,
      currentStep: 0,
      editingItem: null,
      editingVariants: {},
    };
  },
  computed: {
    steps(): Step[] {
      const labels = [
        this.$t('cart.steps.cart'),
        this.$t('cart.steps.delivery'),
        this.$t('cart.steps.payment'),
        this.$t('cart.steps.confirmation'),
      ];
      return labels.map((label, idx) => ({ label, active: idx === this.currentStep }));
    },
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
    openEditVariantes(item: any): void {
      this.editingItem = item;
      this.editingVariants = { ...item.variant_options };
      
      // If variants are missing, fetch from product API
      if (!item.variants && item.product_id) {
        api.get(`/api/products/${item.product_id}`).then((response) => {
          const product = response.data;
          if (product.variants) {
            // Parse variants if string
            let parsedVariants = product.variants;
            if (typeof parsedVariants === 'string') {
              try {
                parsedVariants = JSON.parse(parsedVariants);
              } catch (e) {
                console.warn('Failed to parse variants');
                return;
              }
            }
            // Update both the editing item and the actual item in cart
            this.editingItem.variants = parsedVariants;
            item.variants = parsedVariants;
          }
        }).catch((error) => {
          console.error('Failed to fetch product variants:', error);
        });
      }
    },
    cancelEditVariantes(): void {
      this.editingItem = null;
      this.editingVariants = {};
    },
    async saveEditVariantes(): Promise<void> {
      if (!this.editingItem) return;
      
      try {
        await this.cartStore.updateCartItemVariants(
          this.editingItem.id,
          this.editingVariants
        );
        this.editingItem = null;
        this.editingVariants = {};
      } catch (error) {
        console.error('Failed to update variants:', error);
        alert('Chyba pri aktualizácii variantov');
      }
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
