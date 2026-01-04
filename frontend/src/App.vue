<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import { onMounted } from 'vue'
import { useCartStore } from './stores/cartStore'
import NavbarComponent from './components/NavbarComponent.vue'
import FooterComponent from './components/FooterComponent.vue'  
import ProductCard from './components/ProductCardComponent.vue';
import NotificationBar from './components/NotificationBar.vue'

const cartStore = useCartStore()

// Initialize auth on app mount so it's available globally
onMounted(async () => {
  await cartStore.initializeAuth()
})
</script>



<template>
  <header>
    <NavbarComponent />
  </header>

  
  <NotificationBar />

  <RouterView />

  <footer>
    <FooterComponent />
  </footer>
  
</template>

<style scoped>

</style>
