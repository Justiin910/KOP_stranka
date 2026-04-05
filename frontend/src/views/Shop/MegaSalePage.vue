<template>
  <div class="page-home-bg">
    <!-- Sidebar -->
    <aside class="hidden md:block md:w-64 md:flex-shrink-0">
      <SideBarComponent />
    </aside>

    <!-- Main Content -->
    <main class="flex-1 px-4 sm:px-6 md:px-8 lg:px-10 py-6 sm:py-8 overflow-x-hidden">
      <!-- Header Section -->
      <section class="mb-12">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
          <div>
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-2">
              {{ $t("home.mega_sale.title") }}
            </h1>
            <p class="text-gray-600 dark:text-gray-300">
              {{ $t("home.news.heading") }}
            </p>
          </div>
          <router-link
            to="/"
            class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium"
          >
            ← {{ $t("nav.home") }}
          </router-link>
        </div>
      </section>

      <!-- Products Grid -->
      <section>
        <div v-if="saleProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
          <ProductCard
            v-for="product in saleProducts"
            :key="product.id"
            :product="product"
            @add-to-cart="addToCart"
          />
        </div>
        <div v-else class="text-center py-12">
          <p class="text-gray-600 dark:text-gray-300 text-lg">
            {{ $t("search.no_results") }}
          </p>
        </div>
      </section>
    </main>
  </div>
</template>

<script>
import api from "@/api";
import ProductCard from "../../components/ProductCard.vue";
import SideBarComponent from "../../components/SideBarComponent.vue";
import { useCartStore } from "../../stores/cartStore";

export default {
  name: "MegaSalePage",
  components: {
    ProductCard,
    SideBarComponent,
  },
  setup() {
    const cartStore = useCartStore();
    return { cartStore };
  },
  data() {
    return {
      saleProducts: [],
      loading: false,
    };
  },
  async mounted() {
    await this.loadSaleProducts();
  },
  methods: {
    async loadSaleProducts() {
      this.loading = true;
      try {
        const response = await api.get("api/products/sale", {
          params: { min_discount: 10, limit: 200 },
        });
        this.saleProducts = response.data || [];
      } catch (error) {
        console.error("Error loading sale products:", error);
        this.saleProducts = [];
      } finally {
        this.loading = false;
      }
    },
    async addToCart(product) {
      this.cartStore.addProduct(product);
      this.$forceUpdate();
      alert(this.$t("products.added_to_cart", { name: product.title }));
    },
  },
};
</script>

<style scoped>
</style>
