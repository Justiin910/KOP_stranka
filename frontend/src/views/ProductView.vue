<template>
  <div class="min-h-screen bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-2 gap-12">
      <!-- Product Image -->
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

      <!-- Product Info -->
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
          {{ product.title }}
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
          {{ product.description }}
        </p>

        <!-- Price -->
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

        <!-- Availability -->
        <div class="mb-6">
          <span
            class="inline-block px-3 py-1 rounded-full text-sm font-medium"
            :class="
              product.stock > 0
                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
            "
          >
            {{ product.stock > 0 ? "Na sklade > " + product.stock + " ks" : "Vypredané" }}
          </span>
        </div>

        <!-- Specs -->
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

        <!-- Reviews -->
        <div class="mb-6">
          <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
            <span class="font-semibold text-gray-900 dark:text-white"
              >{{ product.rating }} ★</span
            >
            <span>({{ product.reviews }} recenzií)</span>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
          <button
            @click="addToCart"
            :disabled="product.stock === 0"
            class="flex-1 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Pridať do košíka
          </button>
          <button
            class="px-6 py-3 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded-lg font-semibold transition"
          >
            Do Alzaboxu
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ProductView",
  data() {
    return {
      product: {
        id: "RTX5060TI-PYTHON",
        title: "GAINWARD GeForce RTX 5060 Ti Python III 16G",
        description:
          "Výkonná grafická karta pre hráčov a kreatívcov s najnovšou architektúrou Blackwell.",
        price: 459,
        oldPrice: 529,
        monthlyPrice: "10,29",
        image:
          "https://images.unsplash.com/photo-1587829191301-59a37e7af0a5?w=800&q=80&auto=format&fit=crop",
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

<style scoped>
/* Tailwind driven layout, no extra styles needed */
</style>
