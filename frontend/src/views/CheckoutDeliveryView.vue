<template>
  <div class="page-checkout-delivery-bg">
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

      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">
        Spôsob doručenia
      </h1>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Delivery Options -->
          <div
            class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700"
          >
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
              Spôsob doručenia
            </h2>

            <div class="space-y-4">
              <label
                v-for="option in deliveryOptions"
                :key="option.id"
                class="flex items-start p-4 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:border-blue-400 dark:hover:border-blue-500 transition"
                :class="
                  selectedDelivery === option.id
                    ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20'
                    : ''
                "
              >
                <input
                  type="radio"
                  v-model="selectedDelivery"
                  :value="option.id"
                  class="mt-1"
                />
                <div class="ml-4 flex-1">
                  <h3 class="font-semibold text-gray-900 dark:text-white">
                    {{ option.name }}
                  </h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ option.description }}
                  </p>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white mt-2">
                    {{ option.price }} €
                  </p>
                  <p
                    v-if="option.deliveryTime"
                    class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                  >
                    {{ option.deliveryTime }}
                  </p>
                </div>
              </label>
            </div>
          </div>
          <!-- Order Items List -->
          <div
            class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700"
          >
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
              Položky v objednávke
            </h2>

            <div v-if="cartStore.cartItems.length === 0" class="text-center py-8">
              <p class="text-gray-500 dark:text-gray-400">Váš košík je prázdny</p>
            </div>

            <div v-else class="space-y-4">
              <div
                v-for="item in cartStore.cartItems"
                :key="item.id"
                class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg"
              >
                <div class="flex-1">
                  <h3 class="font-semibold text-gray-900 dark:text-white">
                    {{ item.title }}
                  </h3>
                  <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ parseFloat(item.price).toFixed(2) }} € za kus
                  </p>
                </div>
                <div class="text-right">
                  <p class="font-semibold text-gray-900 dark:text-white">
                    {{ item.quantity }}x
                  </p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ (parseFloat(item.price) * item.quantity).toFixed(2) }} €
                  </p>
                </div>
              </div>
            </div>
          </div>
          <!-- Shipping Address -->
          <div
            class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700"
          >
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
              Adresa doručenia
            </h2>

            <div class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >Meno a priezvisko</label
                  >
                  <input
                    v-model="address.fullName"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600"
                    placeholder="Ján Varga"
                    maxlength="255"
                  />
                </div>
                <div>
                  <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >Telefón</label
                  >
                  <div class="flex gap-2">
                    <select
                      v-model="phoneCountryCode"
                      class="px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm"
                    >
                      <option value="+421">+421 SK</option>
                      <option value="+420">+420 CZ</option>
                      <option value="+43">+43 AT</option>
                      <option value="+48">+48 PL</option>
                      <option value="+36">+36 HU</option>
                    </select>
                    <input
                      v-model="phoneNumber"
                      type="tel"
                      @input="formatPhoneInput"
                      class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600"
                      placeholder="256 256 262"
                      maxlength="11"
                    />
                  </div>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ phoneDigits }}/9 číslic
                  </p>
                </div>
              </div>

              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                  >Email</label
                >
                <input
                  v-model="address.email"
                  type="email"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600"
                  placeholder="email@example.com"
                />
              </div>

              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                  >Adresa</label
                >
                <input
                  v-model="address.street"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600"
                  placeholder="Ulica a číslo popisné"
                />
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >PSČ</label
                  >
                  <input
                    v-model="address.zip"
                    type="text"
                    @input="formatZip"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600"
                    placeholder="82101"
                    maxlength="6"
                  />
                </div>
                <div>
                  <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >Mesto</label
                  >
                  <input
                    v-model="address.city"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600"
                    placeholder="Bratislava"
                  />
                </div>
              </div>

              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                  >Krajina</label
                >
                <select
                  v-model="address.country"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600"
                >
                  <option value="SK">Slovensko</option>
                  <option value="CZ">Česká republika</option>
                  <option value="HU">Maďarsko</option>
                  <option value="PL">Poľsko</option>
                  <option value="AT">Rakúsko</option>
                </select>
              </div>

              <div>
                <label class="flex items-center mt-4">
                  <input
                    v-model="address.useAsDefault"
                    type="checkbox"
                    class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-600"
                  />
                  <span class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                    >Uložiť ako predvolenú adresu</span
                  >
                </label>
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

            <div
              class="space-y-3 mb-6 border-b border-gray-200 dark:border-gray-700 pb-6"
            >
              <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>Spolu za položky</span>
                <span>{{ cartStore.subtotal.toFixed(2) }} €</span>
              </div>
              <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>Doručenie</span>
                <span>{{ selectedDeliveryPrice }} €</span>
              </div>
              <div
                class="flex justify-between text-lg font-bold text-gray-900 dark:text-white"
              >
                <span>Celkom</span>
                <span>{{ total.toFixed(2) }} €</span>
              </div>
            </div>

            <div class="space-y-3 mb-6">
              <h3 class="font-semibold text-gray-900 dark:text-white">
                Položky v košíku:
              </h3>
              <div
                v-for="item in cartStore.cartItems"
                :key="item.id"
                class="flex justify-between text-sm text-gray-600 dark:text-gray-400"
              >
                <span>{{ item.title }} x{{ item.quantity }}</span>
                <span>{{ (item.price * item.quantity).toFixed(2) }} €</span>
              </div>
            </div>

            <button
              @click="proceedToPayment"
              class="w-full btn-primary-lg"
            >
              Pokračovať na platbu
            </button>

            <button
              @click="goBack"
              class="w-full mt-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-semibold py-3 rounded-lg transition-colors"
            >
              Späť na košík
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useRouter } from "vue-router";
import { useCartStore } from "../stores/cartStore";
import api from "../api";

export default {
  name: "CheckoutDeliveryView",
  setup() {
    const router = useRouter();
    const cartStore = useCartStore();
    return { router, cartStore };
  },
  data() {
    return {
      steps: [
        { label: "Košík", active: false },
        { label: "Doručenie", active: true },
        { label: "Platba", active: false },
        { label: "Potvrdenie", active: false },
      ],
      selectedDelivery: "standard",
      deliveryOptions: [
        {
          id: "standard",
          name: "Štandardná doprava",
          description: "Doručenie kuriérom na adresu",
          price: 4.99,
          deliveryTime: "do 3 pracovných dní",
        },
        {
          id: "express",
          name: "Expresná doprava",
          description: "Rýchle doručenie v rámci Bratislavy",
          price: 9.99,
          deliveryTime: "do 24 hodín",
        },
        {
          id: "pickup",
          name: "Osobný odber",
          description: "Odber na pobočke alebo pick-up pointu",
          price: 0,
          deliveryTime: "od nasledujúceho pracovného dňa",
        },
      ],
      address: {
        fullName: "",
        phone: "",
        email: "",
        street: "",
        zip: "",
        city: "",
        country: "SK",
        useAsDefault: false,
      },
      loading: false,
      phoneNumber: "",
      phoneCountryCode: "+421",
    };
  },
  computed: {
    selectedDeliveryPrice() {
      const option = this.deliveryOptions.find((o) => o.id === this.selectedDelivery);
      return option ? option.price : 0;
    },
    total() {
      return this.cartStore.subtotal + this.selectedDeliveryPrice;
    },
    phoneDigits() {
      return this.phoneNumber.replace(/\D/g, "").length;
    },
  },
  methods: {
    proceedToPayment() {
      // Validate address
      if (
        !this.address.fullName ||
        !this.address.street ||
        !this.address.city ||
        !this.address.zip
      ) {
        alert("Prosím vyplňte všetky povinné polia");
        return;
      }

      // Validate email
      if (!this.isValidEmail(this.address.email)) {
        alert("Prosím zadajte platnú emailovú adresu");
        return;
      }

      // Validate phone (9 digits)
      if (this.phoneNumber && this.phoneDigits !== 9) {
        alert("Telefón musí mať presne 9 číslic");
        return;
      }

      // Validate zip format
      if (!this.isValidZip(this.address.zip)) {
        alert("PSČ musí mať presne 5 číslic");
        return;
      }

      // Store delivery data
      sessionStorage.setItem(
        "checkoutDelivery",
        JSON.stringify({
          method: this.selectedDelivery,
          address: this.address,
          phone: `${this.phoneCountryCode}${this.phoneNumber.replace(/\D/g, "")}`,
        })
      );
      this.router.push("/checkout/payment");
    },
    goBack() {
      this.router.push("/cart");
    },
    parsePhoneNumber(phone) {
      if (!phone) {
        this.phoneCountryCode = "+421";
        this.phoneNumber = "";
        return;
      }

      const match = phone.match(/^(\+\d{2,3})(.*)$/);
      if (match) {
        this.phoneCountryCode = match[1];
        this.phoneNumber = match[2].replace(/\D/g, "");
        this.formatPhoneInput();
      } else {
        this.phoneCountryCode = "+421";
        this.phoneNumber = phone.replace(/\D/g, "");
        this.formatPhoneInput();
      }
    },
    formatPhoneInput() {
      let num = this.phoneNumber.replace(/\D/g, "").substring(0, 9);
      let parts = [];
      if (num.length > 0) parts.push(num.substring(0, 3));
      if (num.length > 3) parts.push(num.substring(3, 6));
      if (num.length > 6) parts.push(num.substring(6, 9));
      this.phoneNumber = parts.join(" ");
    },
    formatZip() {
      let num = this.address.zip.replace(/\D/g, "").substring(0, 5);
      if (num.length > 3) {
        num = num.slice(0, 3) + " " + num.slice(3);
      }
      this.address.zip = num;
    },
    isValidEmail(email) {
      if (!email || typeof email !== "string") return false;
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
      return re.test(email);
    },
    isValidZip(zip) {
      const digits = zip.replace(/\D/g, "");
      return digits.length === 5;
    },
    async loadUserProfile() {
      if (!this.cartStore.isLoggedIn) {
        return;
      }

      try {
        this.loading = true;
        const response = await api.get("/api/user");
        const user = response.data;

        // Pre-fill form with user data
        this.address.fullName = user.name || "";
        this.address.phone = user.phone || "";
        this.address.email = user.email || "";
        this.address.street = user.address || "";
        this.address.city = user.city || "";
        this.address.zip = user.zip || "";

        // Parse phone number with country code
        this.parsePhoneNumber(this.address.phone);
      } catch (error) {
        console.error("Failed to load user profile:", error);
      } finally {
        this.loading = false;
      }
    },
  },
  async mounted() {
    // Ensure auth is initialized before loading user data
    if (!this.cartStore.isLoggedIn && this.cartStore.cartItems.length === 0) {
      await this.cartStore.initializeAuth();
    }
    // Reload cart from database/localStorage
    await this.cartStore.loadCart();
    // Load user profile data
    await this.loadUserProfile();
  },
};
</script>
