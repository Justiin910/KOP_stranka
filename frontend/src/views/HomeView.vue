<template>
  <div class="page-home-bg">
    <!-- Sidebar -->
    <aside class="left-0 top-0 h-auto z-50">
      <SideBarComponent />
    </aside>

    <!-- Main Content -->
    <main class="ml-12 mr-12 flex-1 px-6 py-8">
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
              {{ $t('home.hero.title') }}
            </h1>
            <p class="text-xl text-white/90 mb-6">{{ $t('home.hero.subtitle') }}</p>
            <button
              class="bg-white text-indigo-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition"
            >
              {{ $t('home.hero.cta') }}
            </button>
          </div>
        </div>
      </section>

      <!-- News/Events Grid -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
          {{ $t('home.news.heading') }}
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
              <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400"
                >{{ $t('home.news.event1.tag') }}</span
              >
              <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-2 mb-2">
                {{ $t('home.news.event1.title') }}
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                {{ $t('home.news.event1.desc') }}
              </p>
              <span class="text-sm text-indigo-600 dark:text-indigo-400 font-medium"
                >{{ $t('home.news.event1.cta') }}</span
              >
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
              <span class="text-xs font-semibold text-purple-600 dark:text-purple-400"
                >{{ $t('home.news.event2.tag') }}</span
              >
              <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-2 mb-2">
                {{ $t('home.news.event2.title') }}
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                {{ $t('home.news.event2.desc') }}
              </p>
              <span class="text-sm text-purple-600 dark:text-purple-400 font-medium"
                >{{ $t('home.news.event2.cta') }}</span
              >
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
              <span class="text-xs font-semibold text-orange-600 dark:text-orange-400"
                >{{ $t('home.news.event3.tag') }}</span
              >
              <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-2 mb-2">
                {{ $t('home.news.event3.title') }}
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                {{ $t('home.news.event3.desc') }}
              </p>
              <span class="text-sm text-orange-600 dark:text-orange-400 font-medium"
                >{{ $t('home.news.event3.cta') }}</span
              >
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
              <h3 class="text-3xl font-bold mb-3">{{ $t('home.banners.free_shipping.title') }}</h3>
                  <p class="text-white/80 mb-6 text-base leading-relaxed">
                    {{ $t('home.banners.free_shipping.desc') }}
                  </p>
                  <button
                    class="bg-white/20 hover:bg-white/30 text-white px-7 py-2.5 rounded-full font-semibold text-sm border border-white/40 transition-all duration-200"
                  >
                    {{ $t('home.banners.free_shipping.cta') }}
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
              <h3 class="text-3xl font-bold mb-3">{{ $t('home.banners.warranty.title') }}</h3>
              <p class="text-white/80 mb-6 text-base leading-relaxed">
                {{ $t('home.banners.warranty.desc') }}
              </p>
              <button
                class="bg-white/20 hover:bg-white/30 text-white px-7 py-2.5 rounded-full font-semibold text-sm border border-white/40 transition-all duration-200"
              >
                {{ $t('home.banners.warranty.cta') }}
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Recommended Products Section -->
      <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $t('home.recommended.title') }}
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

      <!-- Bottom Info Section -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center">
          <div
            class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mx-auto mb-4"
          >
            <i
              class="fa-solid fa-truck text-indigo-600 dark:text-indigo-400 text-2xl"
            ></i>
          </div>
          <h3 class="font-bold text-gray-900 dark:text-white mb-2">{{ $t('home.bottom.fast_delivery.title') }}</h3>
          <p class="text-sm text-gray-600 dark:text-gray-300">
            {{ $t('home.bottom.fast_delivery.desc') }}
          </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center">
          <div
            class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4"
          >
            <i
              class="fa-solid fa-shield-halved text-green-600 dark:text-green-400 text-2xl"
            ></i>
          </div>
          <h3 class="font-bold text-gray-900 dark:text-white mb-2">{{ $t('home.bottom.warranty_service.title') }}</h3>
          <p class="text-sm text-gray-600 dark:text-gray-300">
            {{ $t('home.bottom.warranty_service.desc') }}
          </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center">
          <div
            class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4"
          >
            <i class="fa-solid fa-star text-purple-600 dark:text-purple-400 text-2xl"></i>
          </div>
          <h3 class="font-bold text-gray-900 dark:text-white mb-2">{{ $t('home.bottom.verified_reviews.title') }}</h3>
          <p class="text-sm text-gray-600 dark:text-gray-300">
            {{ $t('home.bottom.verified_reviews.desc') }}
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
    };
  },
  async mounted() {
    await this.loadRecommended();
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
      alert(`${product.title} pridané do košíka!`);
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
