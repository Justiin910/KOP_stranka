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
import ProductCard from "../components/ProductCard.vue";
import SideBarComponent from "../components/SideBarComponent.vue";

export default {
  name: "ProductList",
  components: {
    ProductCard,
    SideBarComponent,
  },
  data() {
    const now = Date.now();

    return {
      currentSort: "recommended",

      sortOptions: [
        { label: "Odporúčame", value: "recommended" },
        { label: "Najpredávanejšie", value: "bestsellers" },
        { label: "Najlacnejšie", value: "cheapest" },
        { label: "Najdrahšie", value: "expensive" },
        { label: "Podľa recenzií", value: "reviews" },
        { label: "Od najnovších", value: "newest" },
      ],

      email: "",
      products: [
        {
          id: 1,
          title: "iPhone 15 Pro",
          brand: "Apple",
          price: 1299,
          oldPrice: 1499,
          image:
            "https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=600&q=80&auto=format&fit=crop",
          rating: 4.7,
          reviews: 128,
          category: "phones",
          stock: 10,
          monthlyPrice: "28,50",
          description: 'Displej 6.7", A17 Pro, 256GB, Titanium Black, Action button',
          service: "Do polnoci objednáš, ráno v AlzaBoxe máš!",
          topBadge: null,
          addedAt: now - 1000,
        },
        {
          id: 2,
          title: "AirPods Pro (2. gen)",
          brand: "Apple",
          price: 279,
          oldPrice: null,
          image:
            "https://images.unsplash.com/photo-1606841837239-c5a1a4a07af7?w=600&q=80&auto=format&fit=crop",
          rating: 4.6,
          reviews: 342,
          category: "phones",
          stock: 15,
          monthlyPrice: null,
          description: "Adaptívny zvuk, aktívna tlmenie hluku, bezdrôtové nabíjanie",
          service: "Do polnoci objednáš, ráno v AlzaBoxe máš!",
          topBadge: null,
          addedAt: now - 2000,
        },
        {
          id: 3,
          title: "MacBook Air M2",
          brand: "Apple",
          price: 1199,
          oldPrice: 1399,
          image:
            "https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&q=80&auto=format&fit=crop",
          rating: 4.8,
          reviews: 203,
          category: "notebooks",
          stock: 5,
          monthlyPrice: "26,35",
          description: 'Chip M2, 8GB RAM, 512GB SSD, Space Gray, 13,6" Retina',
          service: "Do polnoci objednáš, ráno v AlzaBoxe máš!",
          topBadge: "TOP 2",
          addedAt: now - 3000,
        },
        {
          id: 4,
          title: "Samsung Galaxy S24",
          brand: "Samsung",
          price: 999,
          oldPrice: null,
          image:
            "https://images.unsplash.com/photo-1681592744820-6a0f4f1d3b5b?w=600&q=80&auto=format&fit=crop",
          rating: 4.5,
          reviews: 156,
          category: "phones",
          stock: 8,
          monthlyPrice: "21,95",
          description: 'Displej 6.1" AMOLED, Snapdragon 8 Gen 3, 128GB, Phantom Black',
          service: "Do polnoci objednáš, ráno v AlzaBoxe máš!",
          topBadge: null,
          addedAt: now - 4000,
        },
        {
          id: 5,
          title: "Dell XPS 13",
          brand: "Dell",
          price: 1099,
          oldPrice: 1299,
          image:
            "https://images.unsplash.com/photo-1541807084-5c52b6b1f88b?w=600&q=80&auto=format&fit=crop",
          rating: 4.4,
          reviews: 87,
          category: "notebooks",
          stock: 12,
          monthlyPrice: "24,15",
          description: 'Intel Core i5, 8GB RAM, 512GB SSD, 13.4" FHD+ InfinityEdge',
          service: "Do polnoci objednáš, ráno v AlzaBoxe máš!",
          topBadge: null,
          addedAt: now - 5000,
        },
        {
          id: 6,
          title: 'iPad Pro 12.9"',
          brand: "Apple",
          price: 1199,
          oldPrice: null,
          image:
            "https://images.unsplash.com/photo-1566241842506-59d440a1b006?w=600&q=80&auto=format&fit=crop",
          rating: 4.7,
          reviews: 71,
          category: "tablets",
          stock: 7,
          monthlyPrice: "26,35",
          description: 'M2 Chip, 8GB RAM, 128GB, 12,9" Liquid Retina XDR, USB-C',
          service: "Do polnoci objednáš, ráno v AlzaBoxe máš!",
          topBadge: "TOP 4",
          addedAt: now - 6000,
        },
        {
          id: 7,
          title: "Samsung Galaxy Tab S9",
          brand: "Samsung",
          price: 899,
          oldPrice: null,
          image:
            "https://images.unsplash.com/photo-1583394838336-acd977736f90?w=600&q=80&auto=format&fit=crop",
          rating: 4.5,
          reviews: 95,
          category: "tablets",
          stock: 9,
          monthlyPrice: "19,75",
          description: 'Snapdragon 8 Gen 2, 8GB RAM, 128GB, 11" AMOLED, S Pen',
          service: "Do polnoci objednáš, ráno v AlzaBoxe máš!",
          topBadge: null,
          addedAt: now - 7000,
        },
        {
          id: 8,
          title: "ASUS ROG Gaming PC",
          brand: "ASUS",
          price: 2499,
          oldPrice: 2799,
          image:
            "https://images.unsplash.com/photo-1587829191301-59a37e7af0a5?w=600&q=80&auto=format&fit=crop",
          rating: 4.9,
          reviews: 512,
          category: "pcs",
          stock: 3,
          monthlyPrice: "54,95",
          description: "RTX 4090, Intel i9-13900K, 32GB RAM, 2TB SSD, RGB",
          service: "Do polnoci objednáš, ráno v AlzaBoxe máš!",
          topBadge: null,
          addedAt: now - 8000,
        },
        {
          id: 9,
          title: "Anker PowerCore 20000",
          brand: "Anker",
          price: 45,
          oldPrice: 65,
          image:
            "https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=600&q=80&auto=format&fit=crop",
          rating: 4.6,
          reviews: 512,
          category: "powerbanks",
          stock: 50,
          monthlyPrice: null,
          description: "20000mAh, USB-C, rýchle nabíjanie, kompaktný dizajn",
          service: "Do polnoci objednáš, ráno v AlzaBoxe máš!",
          topBadge: null,
          addedAt: now - 9000,
        },
        {
          id: 10,
          title: "Xiaomi Mi 11T Pro",
          brand: "Xiaomi",
          price: 599,
          oldPrice: null,
          image:
            "https://images.unsplash.com/photo-1592286927505-1def25115558?w=600&q=80&auto=format&fit=crop",
          rating: 4.3,
          reviews: 234,
          category: "phones",
          stock: 20,
          monthlyPrice: "13,15",
          description: 'Displej 6.67" AMOLED, Snapdragon 888, 8GB RAM, 128GB',
          service: "Do polnoci objednáš, ráno v AlzaBoxe máš!",
          topBadge: null,
          addedAt: now - 10000,
        },
        {
          id: 11,
          title: "Lenovo ThinkPad X1",
          brand: "Lenovo",
          price: 1399,
          oldPrice: null,
          image:
            "https://images.unsplash.com/photo-1593642632823-8f36a24f0c88?w=600&q=80&auto=format&fit=crop",
          rating: 4.4,
          reviews: 145,
          category: "notebooks",
          stock: 6,
          monthlyPrice: "30,75",
          description: 'Intel Core i7, 16GB RAM, 512GB SSD, 14" FHD, Thunderbolt',
          service: "Do polnoci objednáš, ráno v AlzaBoxe máš!",
          topBadge: null,
          addedAt: now - 11000,
        },
        {
          id: 12,
          title: "Sony WH-1000XM5",
          brand: "Sony",
          price: 379,
          oldPrice: 399,
          image:
            "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&q=80&auto=format&fit=crop",
          rating: 4.8,
          reviews: 678,
          category: "phones",
          stock: 25,
          monthlyPrice: "8,35",
          description: "Aktívna tlmenie hluku, bezdrôtové, čas hrania 30 hodín",
          service: "Do polnoci objednáš, ráno v AlzaBoxe máš!",
          topBadge: null,
          addedAt: now - 12000,
        },
      ],
    };
  },
  methods: {
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
