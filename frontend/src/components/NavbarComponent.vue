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
            :placeholder="$t('search.placeholder')"
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
            {{ $t('search.button') }}
          </button>
        </div>
      </div>

      <!-- right: icons -->
      <div class="flex items-center gap-2 flex-shrink-0">
        <!-- Language Dropdown -->
        <div class="relative group" ref="languageDropdown">
          <button
            @click="showLanguageDropdown = !showLanguageDropdown"
            class="p-2 rounded-full bg-gray-100/70 dark:bg-gray-800/70 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center pl-4 font-medium text-sm"
          >
            {{ currentLanguage.toUpperCase() }}
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 ml-1"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              ></path>
            </svg>
          </button>

          <!-- Tooltip -->
          <div
            class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-3 py-1 bg-gray-800 dark:bg-gray-700 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap"
          >
            {{ $t('tooltip.language') }}
            <div
              class="absolute bottom-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-b-4 border-transparent border-b-gray-800 dark:border-b-gray-700"
            ></div>
          </div>

          <!-- Language Dropdown Menu -->
          <div
            v-if="showLanguageDropdown"
            class="absolute right-0 mt-2 w-40 bg-gray-800 border border-gray-700 rounded-lg shadow-lg z-50 overflow-hidden"
          >
            <button
              @click="setLanguage('sk')"
              :class="{
                'bg-indigo-600 text-white': currentLanguage === 'sk',
                'text-gray-100 hover:bg-gray-700': currentLanguage !== 'sk',
              }"
              class="w-full px-4 py-3 text-left text-sm font-medium transition"
            >
              Slovenčina
            </button>
            <button
              @click="setLanguage('en')"
              :class="{
                'bg-indigo-600 text-white': currentLanguage === 'en',
                'text-gray-100 hover:bg-gray-700': currentLanguage !== 'en',
              }"
              class="w-full px-4 py-3 text-left text-sm font-medium transition border-t border-gray-700"
            >
              English
            </button>
          </div>
        </div>

        <!-- Profile with Tooltip -->
        <div class="relative group">
          <Profile />
          <!-- Tooltip -->
          <div
            class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-3 py-1 bg-gray-800 dark:bg-gray-700 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap"
          >
            {{ $t('tooltip.profile') }}
            <div
              class="absolute bottom-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-b-4 border-transparent border-b-gray-800 dark:border-b-gray-700"
            ></div>
          </div>
        </div>
        <!--     <span v-if="isLoggedIn" class="ml-2 text-sm text-gray-200 hidden md:inline">{{ user.name }}</span>

        <!-- Wishlist with counter -->
        <div class="relative group">
          <button
            @click="$router.push('/favorites')"
            class="relative inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-100/70 dark:bg-gray-800/70 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none"
          >
            <font-awesome-icon icon="fa-regular fa-heart" />
            <span
              v-if="favoritesCount > 0"
              class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold leading-none text-indigo-800 bg-white rounded-full border border-gray-200"
            >
              {{ favoritesCount }}
            </span>
          </button>
          <!-- Tooltip -->
          <div
            class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-3 py-1 bg-gray-800 dark:bg-gray-700 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap"
          >
            {{ $t('tooltip.wishlist') }}
            <div
              class="absolute bottom-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-b-4 border-transparent border-b-gray-800 dark:border-b-gray-700"
            ></div>
          </div>
        </div>

        <div class="relative group">
          <Notification />
          <!-- Tooltip -->
          <div
            class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-3 py-1 bg-gray-800 dark:bg-gray-700 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap"
          >
            {{ $t('tooltip.notifications') }}
            <div
              class="absolute bottom-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-b-4 border-transparent border-b-gray-800 dark:border-b-gray-700"
            ></div>
          </div>
        </div>

        <!-- Cart with counter -->
        <div class="relative group">
          <button
            @click="$router.push('/cart')"
            class="relative inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-100/70 dark:bg-gray-800/70 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none"
          >
            <font-awesome-icon icon="fa-solid fa-cart-shopping" />
            <span
              v-if="cartCount > 0"
              class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold leading-none text-indigo-800 bg-white rounded-full border border-gray-200"
            >
              {{ cartCount }}
            </span>
          </button>
          <!-- Tooltip -->
          <div
            class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-3 py-1 bg-gray-800 dark:bg-gray-700 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap"
          >
            {{ $t('tooltip.cart') }}
            <div
              class="absolute bottom-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-b-4 border-transparent border-b-gray-800 dark:border-b-gray-700"
            ></div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import Notification from "./NotificationsComponent.vue";
import Profile from "./ProfileComponent.vue";
import api, { setSessionToken } from "../api";
import { useCartStore } from "../stores/cartStore";

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
      showLanguageDropdown: false,
      currentLanguage: "sk",
      isLoggedIn: false,
      user: {
        name: "Ján Novák",
        email: "jan.novak@email.sk",
      },
    };
  },
  mounted() {
    document.addEventListener("click", this.handleClickOutside);
    // Load saved language preference
    const savedLanguage = localStorage.getItem("language") || "sk";
    this.currentLanguage = savedLanguage;
    // initialize favorites count from localStorage (or server if logged)
    this.updateFavoritesCount();

    // react to favorites changes and auth events
    window.addEventListener("favorites-updated", this.updateFavoritesCount);
    window.addEventListener("favorites-remote-updated", this.updateFavoritesCount);
    window.addEventListener("user-logged-in", this.onUserLoggedIn);
    window.addEventListener("user-logged-out", this.onUserLoggedOut);

    // listen to storage events (other tabs) to stay in sync for anonymous users
    window.addEventListener("storage", this.onStorageEvent);

    // populate auth/display user info
    try {
      const token = localStorage.getItem("token") || sessionStorage.getItem("token");
      const userJson = localStorage.getItem("user");
      if (token && userJson) {
        this.isLoggedIn = true;
        try {
          this.user = JSON.parse(userJson);
        } catch (e) {
          /* ignore parse error */
        }
      }
    } catch (e) {
      console.error("Error reading auth state in navbar", e);
    }

    // Initialize cart store so navbar shows correct count
    try {
      const cartStore = useCartStore();
      cartStore
        .initializeAuth()
        .then(() => cartStore.loadCart())
        .catch(() => cartStore.loadCart());
    } catch (e) {
      console.error("Failed to init cart store in navbar:", e);
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
      const q = (this.query || "").trim();
      if (!q) return;
      this.$router.push({ name: "search", query: { q } });
    },
    toggleProfileDropdown() {
      this.showProfileDropdown = !this.showProfileDropdown;
    },
    handleClickOutside(event) {
      const profileDropdown = this.$refs.profileDropdown;
      const languageDropdown = this.$refs.languageDropdown;
      if (profileDropdown && !profileDropdown.contains(event.target)) {
        this.showProfileDropdown = false;
      }
      if (languageDropdown && !languageDropdown.contains(event.target)) {
        this.showLanguageDropdown = false;
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
    setLanguage(lang) {
      this.currentLanguage = lang;
      localStorage.setItem("language", lang);
      this.showLanguageDropdown = false;
      // Emit language change event for i18n integration
      window.dispatchEvent(
        new CustomEvent("language-changed", { detail: { language: lang } })
      );
    },
  },
  computed: {
    cartCount() {
      try {
        const cartStore = useCartStore();
        // totalItems is a computed ref; Vue will unwrap it in template
        return cartStore.totalItems;
      } catch (e) {
        return 0;
      }
    },
  },
};
</script>

<style scoped>
/* Minimal custom styles; layout driven by Tailwind */
</style>
