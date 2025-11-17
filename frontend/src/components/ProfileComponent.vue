<template>
<!-- Profile Dropdown -->
        <div class="relative" ref="profileDropdown">
          <button 
            @click="toggleProfileDropdown" 
            class="group inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-800 hover:bg-gray-700 transition transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-indigo-500" 
            title="Profile"
          >
            <font-awesome-icon icon="fa-regular fa-circle-user" />
          </button>

          <!-- Dropdown Menu -->
          <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
          >
            <div 
              v-if="showProfileDropdown" 
              class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2"
            >
              <!-- Not Logged In -->
              <template v-if="!isLoggedIn">
                <button 
                  @click="goToLogin"
                  class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-3"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                  </svg>
                  Prihlásiť sa
                </button>
                <button 
                  @click="goToRegister"
                  class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-3"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                  </svg>
                  Registrovať sa
                </button>
              </template>

              <!-- Logged In -->
              <template v-else>
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ user.name }}</p>
                  <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ user.email }}</p>
                </div>

                <button 
                  @click="goToProfile"
                  class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-3"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  Môj profil
                </button>

                <button 
                  @click="goToOrders"
                  class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-3"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                  </svg>
                  Moje objednávky
                </button>

                <button 
                  @click="goToSettings"
                  class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-3"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  Nastavenia
                </button>

                <div class="border-t border-gray-200 dark:border-gray-700 mt-2 pt-2">
                  <button 
                    @click="logout"
                    class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-3"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Odhlásiť sa
                  </button>
                </div>
              </template>
            </div>
          </transition>
        </div>

</template>

<script>
export default {
  data() {
    return {
      query: '',
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