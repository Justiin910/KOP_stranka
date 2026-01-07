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
        v-if="showFavorite"
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
        {{ $t('product.in_stock', { count: product.stock }) }}
      </p>
      <p v-else class="text-xs text-red-400 font-semibold mb-2">{{ $t('product.out_of_stock') }}</p>

      <!-- Price -->
      <div class="flex items-baseline gap-2 mb-3 mt-auto">
        <span class="text-lg font-bold text-white">{{ product.price }}€</span>
        <span v-if="product.oldPrice" class="text-xs text-gray-500 line-through"
          >{{ product.oldPrice }}€</span
        >
      </div>

      <!-- Add to Cart Button -->
      <button
        v-if="showActions"
        @click.stop="$emit('add-to-cart', product)"
        :disabled="product.stock === 0"
        class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-600 text-white font-semibold py-2 rounded text-xs transition transform hover:scale-105 disabled:cursor-not-allowed"
      >
        {{ product.stock > 0 ? $t('product.add_to_cart') : $t('product.out_of_stock') }}
      </button>
    </div>
  </div>
</template>

<script>
import api, { getSessionToken } from '@/api';

export default {
  name: "ProductCard",
  props: {
    product: {
      type: Object,
      required: true,
    },
    showActions: {
      type: Boolean,
      default: true,
    },
    showFavorite: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      isFavorited: false,
    };
  },
  mounted() {
    // Initialize favorite state. Prefer server-side favorites for logged-in users.
    try {
      const loggedIn = !!(localStorage.getItem('token'));

      // If logged in, try to use a cached server favorites list on the window to avoid
      // firing many API requests (many ProductCard instances). If not available, fetch
      // once and store it on the window.
      if (loggedIn) {
        if (window.__favorites_cache && Array.isArray(window.__favorites_cache)) {
          this.isFavorited = window.__favorites_cache.includes(this.product.id) || window.__favorites_cache.some(p => p === this.product.id || (p && p.id === this.product.id));
        } else {
          // mark temporarily from localStorage while we fetch server list
          const raw = localStorage.getItem('favorites');
          const stored = raw ? JSON.parse(raw) : [];
          if (Array.isArray(stored) && stored.length > 0 && typeof stored[0] === 'object') {
            this.isFavorited = stored.some((p) => p.id === this.product.id);
          } else {
            this.isFavorited = Array.isArray(stored) && stored.includes(this.product.id);
          }

          // fetch server favorites once and cache on window
          import('@/api').then(({ default: api }) => {
            api.get('/api/favorites').then(resp => {
              const favs = resp.data || [];
              // normalize to array of ids for quick lookup
              const ids = favs.map(f => (typeof f === 'object' ? f.id : f));
              window.__favorites_cache = ids;
              this.isFavorited = ids.includes(this.product.id);
              // also persist locally so anonymous views still work
              try { localStorage.setItem('favorites', JSON.stringify(favs)); } catch(e) {}
              window.dispatchEvent(new CustomEvent('favorites-remote-updated', { detail: ids }));
            }).catch(() => {
              // ignore fetch errors — keep localStorage-derived state
            });
          }).catch(() => {});
        }
        return;
      }

      // Not logged in: use localStorage favorites (ids or objects)
      try {
        const raw = localStorage.getItem('favorites');
        const stored = raw ? JSON.parse(raw) : [];
        if (Array.isArray(stored) && stored.length > 0 && typeof stored[0] === 'object') {
          this.isFavorited = stored.some((p) => p.id === this.product.id);
        } else {
          this.isFavorited = Array.isArray(stored) && stored.includes(this.product.id);
        }
      } catch (e) {
        // ignore
      }
    } catch (e) {
      // ignore any top-level errors
    }
  },
  methods: {
    goToProduct() {
      this.$router.push(`/product/${this.product.id}`);
    },
    async toggleFavorite() {
      const newState = !this.isFavorited;
      const loggedIn = !!(localStorage.getItem('token') || getSessionToken());

      // If logged in, try to update server first
      if (loggedIn) {
        try {
          if (newState) {
            // attempt to add favorite on server
            await api.post('/api/favorites', { product_id: this.product.id });
          } else {
            // attempt to remove favorite on server
            // try direct id-based delete first
            await api.delete(`/api/favorites/${this.product.id}`).catch(async (err) => {
              // fallback to body-based delete
              await api.delete('/api/favorites', { data: { product_id: this.product.id } });
            });
          }

          // server update succeeded — update local indicator and notify
          this.isFavorited = newState;
          // keep window cache in sync
          try {
            if (!window.__favorites_cache) window.__favorites_cache = [];
            const idx = window.__favorites_cache.indexOf(this.product.id);
            if (newState && idx === -1) window.__favorites_cache.push(this.product.id);
            if (!newState && idx !== -1) window.__favorites_cache.splice(idx, 1);
            window.dispatchEvent(new CustomEvent('favorites-remote-updated', { detail: window.__favorites_cache }));
          } catch (e) {}
          window.dispatchEvent(new Event('favorites-updated'));
          return;
        } catch (err) {
          // server update failed — fallback to localStorage below
          console.warn('Server favorite update failed, falling back to localStorage', err);
        }
      }

      // Anonymous or server failed: persist locally
      this.isFavorited = newState;
      try {
        const raw = localStorage.getItem('favorites');
        let stored = raw ? JSON.parse(raw) : [];
        if (!Array.isArray(stored)) stored = [];

        // Normalize stored format to array of product objects when storing locally
        if (this.isFavorited) {
          // add product object if not present
          const exists = stored.some((p) => (typeof p === 'object' ? p.id === this.product.id : p === this.product.id));
          if (!exists) {
            const item = {
              id: this.product.id,
              title: this.product.title || this.product.name,
              name: this.product.title || this.product.name,
              description: this.product.description || this.product.short_description || '',
              category: this.product.category || this.product.category_name || '',
              price: this.product.price || this.product.current_price || 0,
              originalPrice: this.product.oldPrice || this.product.original_price || null,
              image: this.product.image || (this.product.images && this.product.images[0]) || '/placeholder-product.png',
              inStock: typeof this.product.stock !== 'undefined' ? this.product.stock > 0 : (this.product.inStock ?? true),
            };
            stored.push(item);
          }
        } else {
          // remove by id whether stored entries are ids or objects
          stored = stored.filter((p) => (typeof p === 'object' ? p.id !== this.product.id : p !== this.product.id));
        }

        localStorage.setItem('favorites', JSON.stringify(stored));
        window.dispatchEvent(new CustomEvent('favorites-updated', { detail: stored }));
        // also update window cache for anonymous/local changes
        try {
          if (!window.__favorites_cache) window.__favorites_cache = [];
          window.__favorites_cache = stored.map(p => (typeof p === 'object' ? p.id : p));
          window.dispatchEvent(new CustomEvent('favorites-remote-updated', { detail: window.__favorites_cache }));
        } catch(e) {}
      } catch (e) {
        console.warn('Failed to update favorites in localStorage', e);
      }
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
