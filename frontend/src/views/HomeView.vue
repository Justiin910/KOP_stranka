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
            src="https://picsum.photos/1500/900?random=1"
            class="absolute inset-0 w-full h-full object-cover"
          />
          <div class="absolute inset-0 bg-black/40"></div>

          <div class="relative z-10">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
              {{ $t("home.hero.title") }}
            </h1>
            <p class="text-xl text-white/90 mb-6">{{ $t("home.hero.subtitle") }}</p>
            <button
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
            class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition cursor-pointer"
          >
            <img
              src="https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=1000&q=80&auto=format&fit=crop"
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
              <span class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">{{
                $t("home.news.event1.cta")
              }}</span>
            </div>
          </div>

          <!-- Event Card 2 -->
          <div
            class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition cursor-pointer"
          >
            <img
              src="https://picsum.photos/1500/900?random=2"
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
              <span class="text-sm text-purple-600 dark:text-purple-400 font-medium">{{
                $t("home.news.event2.cta")
              }}</span>
            </div>
          </div>

          <!-- Event Card 3 -->
          <div
            class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition cursor-pointer"
          >
            <img
              src="https://images.unsplash.com/photo-1593642532973-d31b6557fa68?w=1000&q=80&auto=format&fit=crop"
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
              <span class="text-sm text-orange-600 dark:text-orange-400 font-medium">{{
                $t("home.news.event3.cta")
              }}</span>
            </div>
          </div>
        </div>
      </section>

      <!-- Information Banners -->
      <section class="mb-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Info Banner 1 -->
          <div
            class="relative rounded-xl overflow-hidden shadow-lg border border-white/10 hover:shadow-xl transition-shadow"
          >
            <div
              class="absolute inset-0 bg-gradient-to-r from-green-800/70 to-green-700/40"
            ></div>

            <div
              class="absolute inset-0 bg-[url('https://picsum.photos/1500/900?random=3')] bg-cover bg-right opacity-40"
            ></div>

            <div class="relative p-8 text-white">
              <h3 class="text-3xl font-bold mb-3">
                {{ $t("home.banners.free_shipping.title") }}
              </h3>
              <p class="text-white/80 mb-6 text-base leading-relaxed">
                {{ $t("home.banners.free_shipping.desc") }}
              </p>
              <button
                class="bg-white/20 hover:bg-white/30 text-white px-7 py-2.5 rounded-full font-semibold text-sm border border-white/40 transition-all duration-200"
              >
                {{ $t("home.banners.free_shipping.cta") }}
              </button>
            </div>
          </div>

          <!-- Info Banner 2 -->
          <div
            class="relative rounded-xl overflow-hidden shadow-lg border border-white/10 hover:shadow-xl transition-shadow"
          >
            <div
              class="absolute inset-0 bg-gradient-to-r from-yellow-800/70 to-orange-800/40"
            ></div>

            <div
              class="absolute inset-0 bg-[url('https://picsum.photos/id/1025/800/600')] bg-cover bg-right opacity-40"
            ></div>

            <div class="relative p-8 text-white">
              <h3 class="text-3xl font-bold mb-3">
                {{ $t("home.banners.warranty.title") }}
              </h3>
              <p class="text-white/80 mb-6 text-base leading-relaxed">
                {{ $t("home.banners.warranty.desc") }}
              </p>
              <button
                class="bg-white/20 hover:bg-white/30 text-white px-7 py-2.5 rounded-full font-semibold text-sm border border-white/40 transition-all duration-200"
              >
                {{ $t("home.banners.warranty.cta") }}
              </button>
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
        price: product.price,
        image: product.image,
        description: product.description,
        quantity: 1,
      });
      alert(this.$t("products.added_to_cart", { name: product.title }));
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
