<template>
  <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-6 py-12">
      <!-- Main footer grid -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
        <!-- Brand column -->
        <div class="text-center">
          <h3 class="font-semibold text-gray-900 dark:text-white mb-4">{{ $t('footer.brand.title') }}</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ $t('footer.brand.desc') }}
          </p>
          <div class="flex gap-3 mt-5 justify-center">
            <img src="@/assets/mastercardLogo.png" alt="Mastercard" class="h-6" />
            <img src="@/assets/VisaLogo.png" alt="Visa" class="h-6" />
          </div>
        </div>

        <!-- Shopping -->
        <div class="text-center">
          <h4 class="font-semibold text-gray-900 dark:text-white mb-4 text-sm">{{ $t('footer.shopping.title') }}</h4>
          <ul class="space-y-2 text-sm">
            <li>
              <router-link
                to="/delivery"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
              >
                {{ $t('footer.shopping.delivery') }}
              </router-link>
            </li>
            <li>
              <router-link
                to="/refunds"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
              >
                {{ $t('footer.shopping.refunds') }}
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Orders -->
        <div class="text-center">
          <h4 class="font-semibold text-gray-900 dark:text-white mb-4 text-sm">
            {{ $t('footer.orders.title') }}
          </h4>
          <ul class="space-y-2 text-sm">
            <li>
              <router-link
                to="/orders"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
              >
                {{ $t('footer.orders.status') }}
              </router-link>
            </li>
            <li>
              <router-link
                to="/contact"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
              >
                {{ $t('footer.orders.contact') }}
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Company -->
        <div class="text-center">
          <h4 class="font-semibold text-gray-900 dark:text-white mb-4 text-sm">
            {{ $t('footer.company.title') }}
          </h4>
          <ul class="space-y-2 text-sm">
            <li>
              <router-link
                to="/aboutus"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
              >
                {{ $t('footer.company.about') }}
              </router-link>
            </li>
            <li>
              <router-link
                to="/privacy-policy"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
              >
                {{ $t('footer.company.privacy') }}
              </router-link>
            </li>
          </ul>
        </div>
      </div>

      <!-- Divider -->
      <div class="border-t border-gray-200 dark:border-gray-800 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">
          {{ $t('footer.copyright') }}
        </p>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: "FooterComponent",
};
</script>

<style scoped>
/* Minimal custom styles; layout driven by Tailwind */
</style>
