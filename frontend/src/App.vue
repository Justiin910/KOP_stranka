<script setup lang="ts">
import { RouterView, useRoute } from "vue-router";
import { computed, onMounted, onUnmounted, ref, watch } from "vue";
import { useCartStore } from "./stores/cartStore";
import NavbarComponent from "./components/NavbarComponent.vue";
import FooterComponent from "./components/FooterComponent.vue";
import NotificationBar from "./components/NotificationBar.vue";
import SideBarComponent from "./components/SideBarComponent.vue";

const cartStore = useCartStore();
const route = useRoute();
const showScrollToTop = ref(false);
const isClickingScroll = ref(false);
const showMobileSidebar = ref(false);

const sidebarRoutes = new Set(["home", "megaSale", "category", "search", "product"]);
const showCategorySidebar = computed(() =>
  sidebarRoutes.has(String(route.name || ""))
);

// Initialize auth on app mount so it's available globally
onMounted(async () => {
  await cartStore.initializeAuth();
  window.addEventListener("scroll", handleScroll);
  window.addEventListener("mobile-sidebar-toggle", handleMobileSidebarToggle);
});

onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll);
  window.removeEventListener("mobile-sidebar-toggle", handleMobileSidebarToggle);
  document.body.style.overflow = "";
});

watch(
  () => route.fullPath,
  () => {
    showMobileSidebar.value = false;
  }
);

watch(showMobileSidebar, (isOpen) => {
  document.body.style.overflow = isOpen ? "hidden" : "";
});

const handleScroll = () => {
  showScrollToTop.value = window.scrollY > 300;
};

const handleMobileSidebarToggle = () => {
  if (!showCategorySidebar.value) {
    return;
  }
  showMobileSidebar.value = !showMobileSidebar.value;
};

const closeMobileSidebar = () => {
  showMobileSidebar.value = false;
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

  <transition name="sidebar-fade">
    <div
      v-if="showMobileSidebar && showCategorySidebar"
      class="fixed inset-0 z-50 md:hidden"
      aria-label="Mobile sidebar drawer"
    >
      <button
        class="absolute inset-0 bg-black/50"
        @click="closeMobileSidebar"
        aria-label="Close sidebar"
      ></button>

      <aside
        class="relative h-full w-72 max-w-[85vw] bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 shadow-xl overflow-y-auto"
      >
        <div
          class="sticky top-0 z-10 flex items-center justify-between px-4 py-3 bg-white/95 dark:bg-gray-900/95 border-b border-gray-200 dark:border-gray-800"
        >
          <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
            {{ $t("sidebar.heading") }}
          </h2>
          <button
            class="h-9 w-9 inline-flex items-center justify-center rounded-full text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
            @click="closeMobileSidebar"
            aria-label="Close sidebar"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              ></path>
            </svg>
          </button>
        </div>
        <SideBarComponent @navigate="closeMobileSidebar" />
      </aside>
    </div>
  </transition>

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
        'btn-primary fixed bottom-4 right-4 md:bottom-8 md:right-8 w-10 h-10 md:w-12 md:h-12 rounded-full shadow-lg hover:shadow-xl flex items-center justify-center z-40 duration-300',
        { 'animate-click': isClickingScroll },
      ]"
      :title="'Scroll to top'"
    >
      <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

.sidebar-fade-enter-active,
.sidebar-fade-leave-active {
  transition: opacity 0.2s ease;
}

.sidebar-fade-enter-from,
.sidebar-fade-leave-to {
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
