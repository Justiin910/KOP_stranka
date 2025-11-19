<template>
  <div
    class="bg-gray-800 dark:bg-gray-900 rounded-lg overflow-hidden border border-gray-700 hover:shadow-lg transition group cursor-pointer h-full flex flex-col"
    @click="goToProduct"
  >
    <!-- Image Container -->
    <div
      class="relative w-full h-32 bg-gray-700 overflow-hidden flex items-center justify-center"
    >
      <img
        :src="product.image"
        :alt="product.title"
        class="w-full h-full object-cover group-hover:scale-110 transition duration-300"
      />

      <!-- Badge -->
      <div v-if="product.oldPrice" class="absolute top-2 right-2">
        <span class="bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full">
          -{{ calculateDiscount() }}%
        </span>
      </div>

      <!-- Favorite Button -->
      <button
        @click.stop="toggleFavorite"
        class="absolute top-2 left-2 w-7 h-7 bg-gray-900 bg-opacity-70 rounded-full flex items-center justify-center hover:bg-opacity-100 transition"
      >
        <font-awesome-icon
          :icon="isFavorited ? 'fa-solid fa-heart' : 'fa-regular fa-heart'"
          :class="isFavorited ? 'text-red-500' : 'text-gray-300'"
          class="w-3 h-3"
        />
      </button>
    </div>

    <!-- Content -->
    <div class="p-3 flex flex-col flex-1">
      <!-- Brand -->
      <p class="text-xs text-gray-400 font-semibold uppercase mb-1">
        {{ product.brand }}
      </p>

      <!-- Title -->
      <h3
        class="text-xs font-bold text-gray-100 mb-2 line-clamp-2 group-hover:text-indigo-400 transition"
      >
        {{ product.title }}
      </h3>

      <!-- Rating -->
      <div class="flex items-center gap-1 mb-2">
        <div class="flex text-yellow-400 text-xs">
          <font-awesome-icon
            v-for="i in 5"
            :key="i"
            :icon="
              i <= Math.round(product.rating) ? 'fa-solid fa-star' : 'fa-regular fa-star'
            "
          />
        </div>
        <span class="text-xs text-gray-500">{{ product.rating }}</span>
      </div>

      <!-- Stock Info -->
      <p v-if="product.stock > 0" class="text-xs text-green-400 font-semibold mb-2">
        ✓ Skladom ({{ product.stock }})
      </p>
      <p v-else class="text-xs text-red-400 font-semibold mb-2">✗ Vypredané</p>

      <!-- Price -->
      <div class="flex items-baseline gap-2 mb-3 mt-auto">
        <span class="text-lg font-bold text-white">{{ product.price }}€</span>
        <span v-if="product.oldPrice" class="text-xs text-gray-500 line-through"
          >{{ product.oldPrice }}€</span
        >
      </div>

      <!-- Add to Cart Button -->
      <button
        @click.stop="$emit('add-to-cart', product)"
        :disabled="product.stock === 0"
        class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-600 text-white font-semibold py-2 rounded text-xs transition transform hover:scale-105 disabled:cursor-not-allowed"
      >
        {{ product.stock > 0 ? "Do košíka" : "Vypredané" }}
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: "ProductCard",
  props: {
    product: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      isFavorited: false,
    };
  },
  methods: {
    goToProduct() {
      this.$router.push(`/product/${this.product.id}`);
    },
    toggleFavorite() {
      this.isFavorited = !this.isFavorited;
    },
    calculateDiscount() {
      if (!this.product.oldPrice) return 0;
      const discount =
        ((this.product.oldPrice - this.product.price) / this.product.oldPrice) * 100;
      return Math.round(discount);
    },
  },
};
</script>
