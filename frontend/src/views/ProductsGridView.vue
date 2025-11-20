<template>
  <div class="flex bg-gray-100 dark:bg-gray-900 min-h-screen">
    <!-- Sidebar -->
    <aside>
      <SideBarComponent />
    </aside>

    <!-- Products Section -->
    <section class="flex-1 px-10 py-12">
      <!-- SORT BAR (Alza style) -->
      <div class="mb-6">
        <p class="text-sm text-white mb-3">{{ products.length }} položiek</p>

        <div
          class="flex items-center gap-2 border-b border-gray-300 dark:border-gray-700 overflow-x-auto pb-2"
        >
          <button
            v-for="option in sortOptions"
            :key="option.value"
            @click="setSort(option.value)"
            class="ml-1 px-5 py-2 text-sm sm:text-base font-medium rounded-full transition-all duration-200 ease-in-out whitespace-nowrap focus:outline-none"
            :class="[
              currentSort === option.value
                ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md hover:shadow-lg scale-105'
                : 'bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-700',
            ]"
          >
            {{ option.label }}
          </button>
        </div>
      </div>
      <!-- END SORT BAR -->

      <!-- PRODUCTS GRID -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
        <ProductCard
          v-for="product in products"
          :key="product.id"
          :product="product"
          @add-to-cart="addToCart"
        />
      </div>
    </section>
  </div>
</template>

<script>
import api from "@/api";
import ProductCard from "../components/ProductCard.vue";
import SideBarComponent from "../components/SideBarComponent.vue";

export default {
  name: "ProductList",
  components: {
    ProductCard,
    SideBarComponent,
  },
  data() {
    return {
      products: [],
      currentSort: "recommended",
      sortOptions: [
        { label: "Odporúčame", value: "recommended" },
        { label: "Najpredávanejšie", value: "bestsellers" },
        { label: "Najlacnejšie", value: "cheapest" },
        { label: "Najdrahšie", value: "expensive" },
        { label: "Podľa recenzií", value: "reviews" },
        { label: "Od najnovších", value: "newest" },
      ],
    };
  },
  async mounted() {
    await this.loadProducts();
  },
  watch: {
    "$route.params.catkey": "loadProducts", // This will reload products when the route changes
  },
  methods: {
    async loadProducts() {
      const category = this.$route.params.catkey;
      try {
        const response = await api.get(`api/category/${category}`); // Adjust API path to match your backend route!
        this.products = response.data;
      } catch (error) {
        console.error("Error loading products:", error);
        this.products = [];
      }
    },
    addToCart(product) {
      console.log("Adding to cart:", product);
      alert(`${product.title} pridané do košíka!`);
    },
    setSort(type) {
      this.currentSort = type;
      console.log("Sorting by:", type);
    },
  },
};
</script>

<style scoped>
@media (max-width: 768px) {
  .ml-64 {
    margin-left: 0;
  }
}
</style>
