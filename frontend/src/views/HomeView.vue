<template>
  <div class="page-home-bg">
    <!-- Sidebar -->
    <aside class="left-0 top-0 h-auto z-50">
      <SideBarComponent />
    </aside>

    <!-- Main Content -->
    <main class="ml-12 mr-12 flex-1 px-6 py-8 overflow-x-hidden">
      <!-- Hero Banner Section -->
      <section class="mb-8">
        <div
          class="relative rounded-2xl overflow-hidden p-8 md:p-12 h-72 md:h-96 flex items-center"
        >
          <img
            src="@/assets/saleBanner2.png"
            class="absolute inset-0 w-full h-full object-cover"
          />
          <div class="absolute inset-0 bg-black/40"></div>

          <div class="relative z-10">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
              {{ $t("home.hero.title") }}
            </h1>
            <p class="text-xl text-white/90 mb-6">{{ $t("home.hero.subtitle") }}</p>
            <button
              @click="goToMegaSale"
              class="bg-white text-indigo-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition"
            >
              {{ $t("home.hero.cta") }}
            </button>
          </div>
        </div>
      </section>

      <!-- Recommended Products Section -->
      <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $t("home.recommended.title") }}
          </h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">
          <ProductCard
            v-for="product in recommendedProducts"
            :key="product.id"
            :product="product"
            @add-to-cart="addToCart"
          />
        </div>
      </section>

      <!-- Mega Sale Section -->
      <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-red-600 dark:text-white">
            {{ $t("home.mega_sale.title") }}
          </h2>
          <router-link
            to="/mega-sale"
            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium"
          >
            {{ $t("home.mega_sale.show_all") }}
          </router-link>
        </div>

        <div class="mx-auto relative">
          <div ref="saleScroll" class="overflow-x-auto scrollbar-hide py-4">
            <div class="flex gap-3">
              <ProductCard
                v-for="product in saleProducts"
                :key="`sale-${product.id}`"
                :product="product"
                @add-to-cart="addToCart"
                class="w-1/6 flex-shrink-0"
              />
            </div>
          </div>

          <!-- Left arrow -->
          <button
            v-show="saleCanScrollLeft"
            @click.prevent="scrollSale('left')"
            class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white/20 text-white p-2 rounded-full z-20 focus:outline-none"
            aria-label="Scroll left"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 transform rotate-180"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M12.293 16.293a1 1 0 010-1.414L15.586 11H4a1 1 0 110-2h11.586l-3.293-3.293a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z"
                clip-rule="evenodd"
              />
            </svg>
          </button>

          <!-- Right arrow -->
          <button
            v-show="saleCanScrollRight"
            @click.prevent="scrollSale('right')"
            class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white/20 text-white p-2 rounded-full z-20 focus:outline-none"
            aria-label="Scroll right"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M12.293 16.293a1 1 0 010-1.414L15.586 11H4a1 1 0 110-2h11.586l-3.293-3.293a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
        </div>
      </section>

      <!-- News/Events Grid -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
          {{ $t("home.news.heading") }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Event Card 1 -->
          <div
            @click="openExternalUrl($t('home.news.event1.url'))"
            class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition cursor-pointer"
          >
            <img
              src="data:image/jpeg;base64,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"
              class="h-48 w-full object-cover"
            />
            <div class="p-6">
              <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">{{
                $t("home.news.event1.tag")
              }}</span>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-2 mb-2">
                {{ $t("home.news.event1.title") }}
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                {{ $t("home.news.event1.desc") }}
              </p>
              <span
                class="text-sm text-indigo-600 dark:text-indigo-400 font-medium hover:underline"
                >{{ $t("home.news.event1.cta") }}</span
              >
            </div>
          </div>

          <!-- Event Card 2 -->
          <div
            @click="openExternalUrl($t('home.news.event2.url'))"
            class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition cursor-pointer"
          >
            <img
              src="data:image/jpeg;base64,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"
              class="h-48 w-full object-cover"
            />
            <div class="p-6">
              <span class="text-xs font-semibold text-purple-600 dark:text-purple-400">{{
                $t("home.news.event2.tag")
              }}</span>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-2 mb-2">
                {{ $t("home.news.event2.title") }}
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                {{ $t("home.news.event2.desc") }}
              </p>
              <span
                class="text-sm text-purple-600 dark:text-purple-400 font-medium hover:underline"
                >{{ $t("home.news.event2.cta") }}</span
              >
            </div>
          </div>

          <!-- Event Card 3 -->
          <div
            @click="openExternalUrl($t('home.news.event3.url'))"
            class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition cursor-pointer"
          >
            <img
              src="data:image/jpeg;base64,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"
              class="h-48 w-full object-cover"
            />
            <div class="p-6">
              <span class="text-xs font-semibold text-orange-600 dark:text-orange-400">{{
                $t("home.news.event3.tag")
              }}</span>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-2 mb-2">
                {{ $t("home.news.event3.title") }}
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                {{ $t("home.news.event3.desc") }}
              </p>
              <span
                class="text-sm text-orange-600 dark:text-orange-400 font-medium hover:underline"
                >{{ $t("home.news.event3.cta") }}</span
              >
            </div>
          </div>
        </div>
      </section>

      <!-- Bottom Info Section -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center">
          <div
            class="w-16 h-16 bg-slate-200 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4"
          >
            <i class="fa-solid fa-truck text-slate-700 dark:text-slate-300 text-2xl"></i>
          </div>
          <h3 class="font-bold text-gray-900 dark:text-white mb-2">
            {{ $t("home.bottom.fast_delivery.title") }}
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-300">
            {{ $t("home.bottom.fast_delivery.desc") }}
          </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center">
          <div
            class="w-16 h-16 bg-slate-200 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4"
          >
            <i class="fa-solid fa-wrench text-slate-700 dark:text-slate-300 text-2xl"></i>
          </div>
          <h3 class="font-bold text-gray-900 dark:text-white mb-2">
            {{ $t("home.bottom.warranty_service.title") }}
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-300">
            {{ $t("home.bottom.warranty_service.desc") }}
          </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center">
          <div
            class="w-16 h-16 bg-slate-200 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4"
          >
            <i class="fa-solid fa-star text-slate-700 dark:text-slate-300 text-2xl"></i>
          </div>
          <h3 class="font-bold text-gray-900 dark:text-white mb-2">
            {{ $t("home.bottom.verified_reviews.title") }}
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-300">
            {{ $t("home.bottom.verified_reviews.desc") }}
          </p>
        </div>
      </section>
    </main>
  </div>
</template>

<script>
import api from "@/api";
import ProductCard from "../components/ProductCard.vue";
import SideBarComponent from "../components/SideBarComponent.vue";
import { useCartStore } from "../stores/cartStore";

export default {
  name: "ProductList",
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
      recommendedProducts: [],
      saleProducts: [],
      saleCanScrollLeft: false,
      saleCanScrollRight: false,
      saleScrollListenerAttached: false,
    };
  },
  async mounted() {
    await Promise.all([this.loadRecommended(), this.loadSaleProducts()]);
  },
  methods: {
    async loadRecommended() {
      try {
        const response = await api.get("api/products/recommended");
        this.recommendedProducts = response.data;
      } catch (error) {
        console.error("Error loading recommended products:", error);
        this.recommendedProducts = [];
      }
    },
    async loadSaleProducts() {
      try {
        // Request sale products from new public endpoint
        const serverResp = await api.get("api/products/sale", {
          params: { min_discount: 10, limit: 12 },
        });
        const items = serverResp.data || [];
        // Debug: log received items to help diagnose missing products
        console.debug(
          "loadSaleProducts: received",
          items.length,
          "items from /api/products/sale",
          items
        );
        this.saleProducts = items;
        this.$nextTick(() => {
          this.attachSaleScrollListeners();
          this.updateSaleScrollButtons();
        });
      } catch (error) {
        console.error("Error loading sale products:", error);
        this.saleProducts = [];
      }
    },
    attachSaleScrollListeners() {
      if (this.saleScrollListenerAttached) return;
      const el = this.$refs.saleScroll;
      if (!el) return;
      el.addEventListener("scroll", this.updateSaleScrollButtons.bind(this));
      // also update on resize
      window.addEventListener("resize", this.updateSaleScrollButtons.bind(this));
      this.saleScrollListenerAttached = true;
    },
    updateSaleScrollButtons() {
      const el = this.$refs.saleScroll;
      if (!el) return;
      this.saleCanScrollLeft = el.scrollLeft > 5;
      this.saleCanScrollRight = el.scrollWidth - el.clientWidth - el.scrollLeft > 5;
    },
    scrollSale(direction) {
      const el = this.$refs.saleScroll;
      if (!el) return;
      const amount = Math.floor(el.clientWidth * 0.8) || 300;
      el.scrollBy({ left: direction === "right" ? amount : -amount, behavior: "smooth" });
    },
    async addToCart(product) {
      await this.cartStore.addToCart({
        id: product.id,
        product_id: product.id,
        title: product.title,
        price: product.calculated_price ?? product.price,
        image: product.image,
        description: product.description,
        quantity: 1,
      });
      alert(this.$t("products.added_to_cart", { name: product.title }));
    },
    openExternalUrl(url) {
      if (url) {
        window.open(url, "_blank");
      }
    },
    goToMegaSale() {
      this.$router.push("/mega-sale");
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

/* hide horizontal scrollbar for sale carousel */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none; /* IE and Edge */
  scrollbar-width: none; /* Firefox */
}
</style>
