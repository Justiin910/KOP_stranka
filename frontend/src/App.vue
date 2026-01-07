<script setup lang="ts">
import { RouterLink, RouterView } from "vue-router";
import { onMounted, onUnmounted, ref } from "vue";
import { useCartStore } from "./stores/cartStore";
import NavbarComponent from "./components/NavbarComponent.vue";
import FooterComponent from "./components/FooterComponent.vue";
import ProductCard from "./components/ProductCardComponent.vue";
import NotificationBar from "./components/NotificationBar.vue";

const cartStore = useCartStore();
const showScrollToTop = ref(false);
const isClickingScroll = ref(false);

// Initialize auth on app mount so it's available globally
onMounted(async () => {
  await cartStore.initializeAuth();
  window.addEventListener("scroll", handleScroll);
});

onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll);
});

const handleScroll = () => {
  showScrollToTop.value = window.scrollY > 300;
};

const scrollToTop = () => {
  isClickingScroll.value = true;
  setTimeout(() => {
    isClickingScroll.value = false;
  }, 600);

  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
};
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

  <!-- Scroll to Top Button -->
  <transition name="fade">
    <button
      v-if="showScrollToTop"
      @click="scrollToTop"
      :class="[
        'btn-primary fixed bottom-8 right-8 w-12 h-12 rounded-full shadow-lg hover:shadow-xl flex items-center justify-center z-40 duration-300',
        { 'animate-click': isClickingScroll },
      ]"
      :title="'Scroll to top'"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M5 10l7-7m0 0l7 7m-7-7v18"
        ></path>
      </svg>
    </button>
  </transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.animate-click {
  animation: clickPulse 0.45s ease-out;
}

@keyframes clickPulse {
  0% {
    transform: scale(1);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.5);
  }
  50% {
    transform: scale(0.95);
  }
  100% {
    transform: scale(1);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
  }
}
</style>
