<template>
  <div class="flex bg-gray-50 dark:bg-gray-900 min-h-screen">
    <!-- Sidebar -->
    <aside>
      <SideBarComponent />
    </aside>

    <!-- Results -->
    <section class="flex-1 px-6 py-10">
      <div class="max-w-6xl mx-auto">
        <h1 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ $t("search.results_title") }}</h1>
        <p v-if="!query" class="text-gray-700 dark:text-gray-300 mb-4">{{ $t("search.enter_query") }}</p>
        <p v-else class="text-gray-700 dark:text-gray-300 mb-4">{{ $t("search.searching") }}: <strong class="text-gray-900 dark:text-gray-100">{{ query }}</strong></p>

        <div v-if="loading" class="text-center py-10 text-gray-600 dark:text-gray-300">{{ $t("search.loading") }}</div>

        <div v-if="!loading && products.length === 0" class="text-center py-10 text-gray-600 dark:text-gray-300">
          {{ $t("search.no_results") }}
        </div>

        <div v-if="products.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <ProductCard v-for="p in products" :key="p.id" :product="p" @add-to-cart="addToCart" />
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import api from '@/api';
import ProductCard from '@/components/ProductCard.vue';
import SideBarComponent from "@/components/SideBarComponent.vue";
import { useCartStore } from '@/stores/cartStore';

export default {
  name: 'SearchResultsView',
  components: { ProductCard, SideBarComponent },
  data() {
    return {
      query: '',
      products: [],
      loading: false,
    };
  },
  async mounted() {
    this.query = this.$route.query.q || '';
    await this.fetchResults();
  },
  watch: {
    '$route.query.q'(val) {
      this.query = val || '';
      this.fetchResults();
    }
  },
  methods: {
    async fetchResults() {
      this.products = [];
      if (!this.query) return;
      this.loading = true;
      try {
        const resp = await api.get(`/api/products/search`, { params: { q: this.query } });
        this.products = resp.data || [];
      } catch (e) {
        console.error('Search failed', e);
        this.products = [];
      } finally {
        this.loading = false;
      }
    },
    addToCart(product) {
      try {
        const cartStore = useCartStore();
        cartStore.addToCart({
          id: product.id,
          product_id: product.id,
          name: product.title || product.name || product.slug,
          price: product.price || 0,
          image: product.image || null,
          description: product.description || '',
          quantity: 1,
        });
        alert(this.$t('products.added_to_cart', { name: product.title || product.name }));
      } catch (e) {
        console.error('Failed add to cart', e);
      }
    }
  }
}
</script>
