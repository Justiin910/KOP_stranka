<template>
  <nav class="bg-gray-900 text-gray-100 shadow-sm sticky top-0 z-40">
    <div class="mx-auto px-4 py-3 flex items-center gap-4">
      <!-- left: logo / store name -->
      <router-link
        to="/"
        class="flex items-center flex-shrink-0 gap-2 hover:opacity-80 transition-opacity"
        role="banner"
      >
        <div
          class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-lg shadow-lg"
        >
          <svg
            class="w-6 h-6 text-white"
            viewBox="0 0 24 24"
            fill="currentColor"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 5h14M10 21a1 1 0 100-2 1 1 0 000 2zm7 0a1 1 0 100-2 1 1 0 000 2z"
            />
          </svg>
        </div>
        <div>
          <div class="text-sm font-bold text-white leading-tight">TechShop</div>
          <div class="text-xs text-indigo-300 font-medium leading-tight">Smart Tech</div>
        </div>
      </router-link>

      <!-- center: search -->
      <div class="flex-1 flex justify-center">
        <div class="relative max-w-auto w-full md:w-160">
          <input
            v-model="query"
            @keydown.enter.prevent="onSearch"
            type="search"
            placeholder="Čo hľadáte? napr. iPhone 17 Pro..."
            class="w-full bg-gray-800 placeholder-gray-400 text-gray-100 rounded-md py-2 pl-10 pr-24 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-shadow duration-200 text-sm"
            aria-label="Search"
          />
          <div
            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"
              ></path>
            </svg>
          </div>
          <button
            @click="onSearch"
            class="absolute right-1 top-1/2 -translate-y-1/2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium px-3 py-1 rounded text-sm shadow-md transition transform hover:scale-110"
          >
            Hľadať
          </button>
        </div>
      </div>

      <!-- right: icons -->
      <div class="flex items-center gap-2 flex-shrink-0">
        <Profile />

        <!-- Wishlist with counter -->
        <button
          @click="$router.push('/favorites')"
          class="relative inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-800 hover:bg-gray-700 transition transform hover:scale-105 focus:outline-none"
          title="Wishlist"
        >
          <font-awesome-icon icon="fa-regular fa-heart" />
          <span
            v-if="favoritesCount > 0"
            class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold leading-none text-indigo-800 bg-white rounded-full border border-gray-200"
          >
            {{ favoritesCount }}
          </span>
        </button>

        <Notification />

        <!-- Cart with counter -->
        <button
          @click="$router.push('/cart')"
          class="relative inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-800 hover:bg-gray-700 transition transform hover:scale-105 focus:outline-none"
          title="Cart"
        >
          <font-awesome-icon icon="fa-solid fa-cart-shopping" />
          <span
            v-if="cartCount > 0"
            class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold leading-none text-indigo-800 bg-white rounded-full border border-gray-200"
          >
            {{ cartCount }}
          </span>
        </button>
      </div>
    </div>
  </nav>
</template>

<script>
import Notification from "./NotificationsComponent.vue";
import Profile from "./ProfileComponent.vue";
import api, { setSessionToken } from "../api";
import { useCartStore } from '../stores/cartStore'

export default {
  name: "Navbar",
  components: {
    Notification,
    Profile,
  },
  data() {
    return {
      query: "",
      // cartCount provided by cart store (computed)
      favoritesCount: 0, // will be computed from storage/server
      showProfileDropdown: false,
      isLoggedIn: false,
      user: {
        name: "Ján Novák",
        email: "jan.novak@email.sk",
      },
    };
  },
  mounted() {
    document.addEventListener("click", this.handleClickOutside);
    // initialize favorites count from localStorage (or server if logged)
    this.updateFavoritesCount();

    // react to favorites changes and auth events
    window.addEventListener("favorites-updated", this.updateFavoritesCount);
    window.addEventListener("favorites-remote-updated", this.updateFavoritesCount);
    window.addEventListener("user-logged-in", this.onUserLoggedIn);
    window.addEventListener("user-logged-out", this.onUserLoggedOut);

    // listen to storage events (other tabs) to stay in sync for anonymous users
    window.addEventListener("storage", this.onStorageEvent);

    // Initialize cart store so navbar shows correct count
    try {
      const cartStore = useCartStore()
      cartStore.initializeAuth().then(() => cartStore.loadCart()).catch(() => cartStore.loadCart())
    } catch (e) {
      console.error('Failed to init cart store in navbar:', e)
    }
  },
  beforeUnmount() {
    document.removeEventListener("click", this.handleClickOutside);
    window.removeEventListener("favorites-updated", this.updateFavoritesCount);
    window.removeEventListener("favorites-remote-updated", this.updateFavoritesCount);
    window.removeEventListener("user-logged-in", this.onUserLoggedIn);
    window.removeEventListener("user-logged-out", this.onUserLoggedOut);
    window.removeEventListener("storage", this.onStorageEvent);
  },
  methods: {
    onSearch() {
      console.log("Searching for:", this.query);
    },
    toggleProfileDropdown() {
      this.showProfileDropdown = !this.showProfileDropdown;
    },
    handleClickOutside(event) {
      const dropdown = this.$refs.profileDropdown;
      if (dropdown && !dropdown.contains(event.target)) {
        this.showProfileDropdown = false;
      }
    },
    goToLogin() {
      this.$router.push("/login");
      this.showProfileDropdown = false;
    },
    goToRegister() {
      this.$router.push("/register");
      this.showProfileDropdown = false;
    },
    goToProfile() {
      this.$router.push("/profile");
      this.showProfileDropdown = false;
    },
    goToOrders() {
      this.$router.push("/orders");
      this.showProfileDropdown = false;
    },
    goToSettings() {
      this.$router.push("/settings");
      this.showProfileDropdown = false;
    },
    logout() {
      // Clear tokens from all storage types
      localStorage.removeItem("token");
      setSessionToken(null); // Clear in-memory token
      localStorage.removeItem("user");

      this.isLoggedIn = false;
      this.showProfileDropdown = false;
      
      // Emit logout event
      window.dispatchEvent(new Event("user-logged-out"));
      
      this.$router.push("/");
    },
    // Compute favorites count from either local storage (anonymous) or server (logged)
    async updateFavoritesCount() {
      // Always try server first — the API client will include either the
      // persistent token (localStorage) or the in-memory session token set
      // by `setSessionToken`. If that fails, fall back to localStorage.
      try {
        const resp = await api.get("/api/favorites");
        if (Array.isArray(resp.data)) {
          this.favoritesCount = resp.data.length;
          return;
        }
      } catch (e) {
        // Server call failed (unauthorized, network error, etc.) — fallback.
      }

      // fallback: compute from localStorage 'favorites' key
      this.favoritesCount = this.getLocalFavoritesCount();
    },
    getLocalFavoritesCount() {
      try {
        const raw = localStorage.getItem("favorites");
        if (!raw) return 0;
        const parsed = JSON.parse(raw);
        if (Array.isArray(parsed)) {
          // support array of ids or product objects
          return parsed.length;
        }
        return 0;
      } catch (e) {
        return 0;
      }
    },
    async onUserLoggedIn() {
      // when user logs in, try to refresh count from server
      await this.updateFavoritesCount();
    },
    onUserLoggedOut() {
      // revert to local storage count on logout
      this.favoritesCount = this.getLocalFavoritesCount();
    },
    onStorageEvent(e) {
      if (e.key === "favorites") {
        this.favoritesCount = this.getLocalFavoritesCount();
      }
    },
  },
  computed: {
    cartCount() {
      try {
        const cartStore = useCartStore()
        // totalItems is a computed ref; Vue will unwrap it in template
        return cartStore.totalItems
      } catch (e) {
        return 0
      }
    }
  },
};
</script>

<style scoped>
/* Minimal custom styles; layout driven by Tailwind */
</style>
