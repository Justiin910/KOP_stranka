<template>
  <div class="flex bg-white dark:bg-gray-900 min-h-screen">
    <!-- SIDEBAR -->
    <aside
      class="w-64 h-screen bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800"
    >
      <SideBarComponent />
    </aside>

    <!-- PRODUCT DETAIL -->
    <section class="flex-1 px-10 py-12">
      <!-- BREADCRUMBS -->
      <nav
        class="text-sm text-gray-600 dark:text-gray-300 mb-2 flex items-center gap-2 flex-wrap"
      >
        <router-link to="/" class="hover:underline">Domov</router-link>
        <span>/</span>

        <router-link
          :to="`/category/${product.category}`"
          class="hover:underline capitalize"
        >
          {{ product.category }}
        </router-link>
        <span>/</span>

        <span class="text-gray-900 dark:text-gray-100 font-semibold">
          {{ product.title }}
        </span>
      </nav>
      <!-- DIVIDER -->
      <div
        class="border-t border-gray-200 dark:border-gray-800 mt-2 mb-4 text-center"
      ></div>

      <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- IMG -->
        <div class="flex flex-col items-center">
          <img
            :src="product.image"
            :alt="product.title"
            class="w-full max-w-md rounded-lg shadow-md"
          />
          <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
            Kód produktu: {{ product.id }}
          </p>
        </div>

        <!-- INFO -->
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            {{ product.title }}
          </h1>

          <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            {{ product.description }}
          </p>

          <!-- PRICE -->
          <div class="mb-6">
            <div class="text-3xl font-bold text-indigo-600">
              {{ formatPrice(product.price) }} €
            </div>

            <div
              v-if="product.oldPrice"
              class="text-sm line-through text-gray-500 dark:text-gray-400"
            >
              {{ formatPrice(product.oldPrice) }} €
            </div>

            <div
              v-if="product.monthlyPrice"
              class="text-sm text-gray-600 dark:text-gray-400 mt-1"
            >
              alebo od {{ product.monthlyPrice }} € mesačne
            </div>
          </div>

          <!-- STOCK -->
          <div class="mb-6">
            <span
              class="inline-block px-3 py-1 rounded-full text-sm font-medium"
              :class="
                product.stock > 0
                  ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                  : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
              "
            >
              {{
                product.stock > 0 ? "Na sklade > " + product.stock + " ks" : "Vypredané"
              }}
            </span>
          </div>

          <!-- SPECS -->
          <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
              Parametre
            </h2>
            <ul
              class="list-disc list-inside space-y-1 text-sm text-gray-700 dark:text-gray-300"
            >
              <li v-for="(spec, index) in product.specs" :key="index">{{ spec }}</li>
            </ul>
          </div>

          <!-- REVIEWS -->
          <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
              <span class="font-semibold text-gray-900 dark:text-white">
                {{ product.rating }} ★
              </span>
              <span>({{ product.reviews }} recenzií)</span>
            </div>
          </div>

          <!-- ACTIONS -->
          <div class="flex gap-4">
            <button
              @click="addToCart"
              :disabled="product.stock === 0"
              class="flex-1 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Pridať do košíka
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import api from "@/api";
import SideBarComponent from "../components/SideBarComponent.vue";

export default {
  name: "ProductView",
  components: { SideBarComponent },

  data() {
    return {
      product: {
        id: "RTX5060TI-PYTHON",
        category: "pocitace",
        title: "GAINWARD GeForce RTX 5060 Ti Python III 16G",
        description:
          "Výkonná grafická karta pre hráčov a kreatívcov s najnovšou architektúrou Blackwell.",
        price: 459,
        oldPrice: 529,
        monthlyPrice: "10,29",
        image: "https://picsum.photos/1500/900?random=5",
        rating: 4.8,
        reviews: 38,
        stock: 7,
        specs: [
          "16 GB GDDR7 (28000 MHz)",
          "NVIDIA Blackwell GB206, Boost 2572 MHz",
          "PCI Express x16 5.0",
          "128-bitová zbernica",
          "DisplayPort 2.1b, HDMI 2.1b",
          "Šírka: 291.9 mm",
        ],
      },
    };
  },

  methods: {
    formatPrice(price) {
      return price.toFixed(2);
    },
    addToCart() {
      alert(`${this.product.title} pridaný do košíka!`);
    },
  },
};
</script>
