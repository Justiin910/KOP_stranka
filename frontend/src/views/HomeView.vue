<template>
  <div class="flex bg-gray-100 dark:bg-gray-900 min-h-screen">
    <!-- Sidebar -->
    <aside class="left-0 top-0 h-full z-50">
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
              Vianočné darčeky 2025
            </h1>
            <p class="text-xl text-white/90 mb-6">Najlepšie technológie za skvelé ceny</p>
            <button
              class="bg-white text-indigo-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition"
            >
              Pozrieť ponuku
            </button>
          </div>
        </div>
      </section>

      <!-- News/Events Grid -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
          Novinky a udalosti
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
                >UDALOSŤ</span
              >
              <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-2 mb-2">
                Nové telefóny Google Pixel 9
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                Objavte najnovšiu generáciu smartfónov s AI funkciami a vylepšeným
                fotoaparátom.
              </p>
              <span class="text-sm text-indigo-600 dark:text-indigo-400 font-medium"
                >Zistiť viac →</span
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
                >NOVINKA</span
              >
              <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-2 mb-2">
                Apple AirPods Pro (3. gen)
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                Luxusné bezdrôtové slúchadlá s adaptívnym zvukom a vylepšeným ANC.
              </p>
              <span class="text-sm text-purple-600 dark:text-purple-400 font-medium"
                >Predĺžený predpredaj →</span
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
                >Gaming</span
              >
              <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-2 mb-2">
                Gaming week - Až -30%
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                Herné konzoly, príslušenstvo a PC komponenty za bezkonkurenčné ceny.
              </p>
              <span class="text-sm text-orange-600 dark:text-orange-400 font-medium"
                >Do akcie →</span
              >
            </div>
          </div>
        </div>
      </section>

      <!-- Information Banners -->
      <section class="mb-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Info Banner 1 -->
          <div class="relative rounded-xl overflow-hidden">
            <div
              class="absolute inset-0 bg-gradient-to-r from-green-600 to-green-500"
            ></div>

            <div
              class="absolute inset-0 bg-[url('https://picsum.photos/1500/900?random=3')] bg-cover bg-right opacity-25"
            ></div>

            <div class="relative p-6 text-white">
              <h3 class="text-2xl font-bold mb-2">Doprava zdarma</h3>
              <p class="text-white/90 mb-4">Pri nákupe nad 50€ doprava úplne zadarmo!</p>
              <button
                class="bg-white text-green-600 px-6 py-2 rounded-full font-semibold text-sm hover:bg-gray-100 transition"
              >
                Zistiť viac
              </button>
            </div>
          </div>

          <!-- Info Banner 2 -->
          <div class="relative rounded-xl overflow-hidden">
            <div
              class="absolute inset-0 bg-gradient-to-r from-yellow-600 to-orange-600"
            ></div>

            <div
              class="absolute inset-0 bg-[url('https://picsum.photos/id/1025/800/600')] bg-cover bg-right opacity-25"
            ></div>

            <div class="relative p-6 text-white">
              <h3 class="text-2xl font-bold mb-2">Rozšírená záruka</h3>
              <p class="text-white/90 mb-4">
                Rozšírte si záruku až na 5 rokov s TechStore+
              </p>
              <button
                class="bg-white text-orange-600 px-6 py-2 rounded-full font-semibold text-sm hover:bg-gray-100 transition"
              >
                Aktivovať
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Recommended Products Section -->
      <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            Odporúčame pre vás
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
          <h3 class="font-bold text-gray-900 dark:text-white mb-2">Rýchle dodanie</h3>
          <p class="text-sm text-gray-600 dark:text-gray-300">
            Z vašej objednávky sa môžete tešiť už zajtra!
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
          <h3 class="font-bold text-gray-900 dark:text-white mb-2">Záručný servis</h3>
          <p class="text-sm text-gray-600 dark:text-gray-300">
            Vlastná servisná sieť po celom Slovensku
          </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center">
          <div
            class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4"
          >
            <i class="fa-solid fa-star text-purple-600 dark:text-purple-400 text-2xl"></i>
          </div>
          <h3 class="font-bold text-gray-900 dark:text-white mb-2">Overené recenzie</h3>
          <p class="text-sm text-gray-600 dark:text-gray-300">
            Hodnotenia od skutočných zákazníkov
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
