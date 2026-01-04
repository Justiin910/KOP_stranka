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
      <!-- LOADING -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="text-center">
          <div
            class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"
          ></div>
          <p class="mt-4 text-gray-600 dark:text-gray-300">Načítavam produkt...</p>
        </div>
      </div>

      <!-- ERROR -->
      <div v-else-if="error" class="flex items-center justify-center py-20">
        <div class="text-center">
          <p class="text-red-600 dark:text-red-400 font-semibold">{{ error }}</p>
          <router-link to="/" class="mt-4 inline-block text-indigo-600 hover:underline"
            >Späť na domov</router-link
          >
        </div>
      </div>

      <!-- CONTENT -->
      <div v-else>
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
            {{ getCategoryLabel(product.category) }}
          </router-link>
          <span>/</span>

          <span class="text-gray-900 dark:text-gray-100 font-semibold">
            {{ product.title || product.name }}
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
              :alt="product.title || product.name"
              class="w-full max-w-md rounded-lg shadow-md"
            />
            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
              Kód produktu: {{ product.id }}
            </p>
          </div>

          <!-- INFO -->
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
              {{ product.title || product.name }}
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
                v-if="product.specs && product.specs.length > 0"
                class="list-disc list-inside space-y-1 text-sm text-gray-700 dark:text-gray-300"
              >
                <li v-for="(spec, index) in product.specs" :key="index">{{ spec }}</li>
              </ul>
              <p v-else class="text-sm text-gray-500 dark:text-gray-400">
                Žiadne parametre.
              </p>
            </div>

            <!-- REVIEWS -->
            <div class="mb-6">
              <div
                class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"
              >
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
      </div>
    </section>
  </div>
</template>

<script>
import api from "@/api";
import SideBarComponent from "../components/SideBarComponent.vue";
import { useCartStore } from "../stores/cartStore";

export default {
  name: "ProductView",
  components: { SideBarComponent },
  setup() {
    const cartStore = useCartStore();
    return { cartStore };
  },

  data() {
    return {
      product: {
        id: null,
        category: "",
        title: "",
        name: "",
        description: "",
        price: 0,
        oldPrice: null,
        monthlyPrice: null,
        image: "",
        rating: 0,
        reviews: 0,
        stock: 0,
        specs: [],
      },
      loading: true,
      error: null,
    };
  },

  async mounted() {
    await this.loadProduct();
  },

  methods: {
    async loadProduct() {
      this.loading = true;
      this.error = null;
      try {
        const id = this.$route.params.slug;
        const response = await api.get(`/api/products/${id}`);
        this.product = response.data;
      } catch (err) {
        console.error("Error loading product:", err);
        this.error = "Produkt sa nepodarilo načítať. Skúste neskôr.";
        this.product = {
          id: null,
          category: "",
          title: "Produkt nenájdený",
          name: "Produkt nenájdený",
          description: "",
          price: 0,
          oldPrice: null,
          monthlyPrice: null,
          image: "",
          rating: 0,
          reviews: 0,
          stock: 0,
          specs: [],
        };
      } finally {
        this.loading = false;
      }
    },
    formatPrice(price) {
      return parseFloat(price).toFixed(2);
    },
    addToCart() {
      this.cartStore.addToCart({
        id: this.product.id,
        product_id: this.product.id,
        name: this.product.title || this.product.name,
        price: this.product.price,
        image: this.product.image,
        description: this.product.description,
        quantity: 1,
      });
      alert(`${this.product.title || this.product.name} pridaný do košíka!`);
    },
    getCategoryLabel(category) {
      const categoryMap = {
        phones: "Telefóny",
        notebooks: "Notebooky",
        tablets: "Tablety",
        pocitace: "Počítače",
        pcs: "Počítače",
        accessories: "Príslušenstvo",
        monitors: "Monitory",
        peripherals: "Periférie",
        mobily: "Mobily",
      };
      return categoryMap[category] || category;
    },
  },
};
</script>
