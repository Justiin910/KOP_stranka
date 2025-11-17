<template>
  <nav class="bg-gray-900 text-gray-100 shadow-sm sticky top-0 z-40">
    <div class="mx-auto px-4 py-3 flex items-center gap-4">
      <!-- left: logo / store name (fixed width) -->
      <router-link to="/" class="flex items-center flex-shrink-0 gap-2 hover:opacity-80 transition-opacity" role="banner">
        <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-lg shadow-lg">
          <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 5h14M10 21a1 1 0 100-2 1 1 0 000 2zm7 0a1 1 0 100-2 1 1 0 000 2z"/>
          </svg>
        </div>
        <div>
          <div class="text-sm font-bold text-white leading-tight">TechShop</div>
          <div class="text-xs text-indigo-300 font-medium leading-tight">Smart Tech</div>
        </div>
      </router-link>

      <!-- center: sleek search (grows to fill space) -->
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
          <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"></path></svg>
          </div>
          <button @click="onSearch" class="absolute right-1 top-1/2 -translate-y-1/2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium px-3 py-1 rounded text-sm shadow-md transition transform hover:scale-110">
            Hľadať
          </button>
        </div>
      </div>

      

      <!-- right: icons (fixed width, no flex-1) -->
      <div class="flex items-center gap-2 flex-shrink-0">
        


        <Profile />


        <button @click="$router.push('/favorites')" class="group inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-800 hover:bg-gray-700 transition transform hover:scale-105 focus:outline-none" title="Wishlist">
          <font-awesome-icon icon="fa-regular fa-heart" />
        </button>


        <Notification />


        <button @click="$router.push('/cart')" class="relative inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-800 hover:bg-gray-700 transition transform hover:scale-105 focus:outline-none" title="Cart">
          <font-awesome-icon icon="fa-solid fa-cart-shopping" />
          <span v-if="cartCount>0" class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold leading-none text-indigo-800 bg-white rounded-full border border-gray-200">{{ cartCount }}</span>
        </button>
      </div>
    </div>
  </nav>
</template>

<script>
import Notification from './NotificationsComponent.vue'
import Profile from './ProfileComponent.vue'

export default {
  data() {
    return {
      query: '',
      notifCount: 3,
      cartCount: 2,
      showProfileDropdown: false,
      isLoggedIn: false, // Zmeň na true pre testovanie prihláseného stavu
      user: {
        name: 'Ján Novák',
        email: 'jan.novak@email.sk'
      }
    }
  },
  mounted() {
    // Pridať click listener pre zatvorenie dropdownu pri kliku mimo
    document.addEventListener('click', this.handleClickOutside)
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside)
  },
  components: {
    Notification,
    Profile
  },
  methods: {
    onSearch() {
      console.log('Searching for:', this.query)
      // Tu pridáš logiku pre vyhľadávanie
    },
    toggleProfileDropdown() {
      this.showProfileDropdown = !this.showProfileDropdown
    },
    handleClickOutside(event) {
      const dropdown = this.$refs.profileDropdown
      if (dropdown && !dropdown.contains(event.target)) {
        this.showProfileDropdown = false
      }
    },
    goToLogin() {
      this.$router.push('/login')
      this.showProfileDropdown = false
    },
    goToRegister() {
      this.$router.push('/register')
      this.showProfileDropdown = false
    },
    goToProfile() {
      this.$router.push('/profile')
      this.showProfileDropdown = false
    },
    goToOrders() {
      this.$router.push('/orders')
      this.showProfileDropdown = false
    },
    goToSettings() {
      this.$router.push('/settings')
      this.showProfileDropdown = false
    },
    logout() {
      this.isLoggedIn = false
      this.showProfileDropdown = false
      console.log('Logging out...')
      // Tu pridáš logiku pre odhlásenie
      this.$router.push('/')
    }
  }
}
</script>