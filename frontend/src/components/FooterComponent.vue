<template>
  <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-6 py-12">
      <!-- Main footer grid -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">

        <!-- Brand column -->
        <div class="text-center">
          <h3 class="font-semibold text-gray-900 dark:text-white mb-4">TechShop</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">Kvalitné technológie na dosah.</p>
          <div class="flex gap-3 mt-5 justify-center">
            <img src="@/assets/mastercardLogo.png" alt="Mastercard" class="h-6">
            <img src="@/assets/VisaLogo.png" alt="Visa" class="h-6">
          </div>
        </div>

        <!-- Shopping -->
        <div class="text-center">
          <h4 class="font-semibold text-gray-900 dark:text-white mb-4 text-sm">Nákup</h4>
          <ul class="space-y-2 text-sm">
            <li>
              <router-link
                to="/delivery"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
              >
                Doručenie
              </router-link>
            </li>
            <li>
              <router-link
                to="/refunds"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
              >
                Vrátenia
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Orders -->
        <div class="text-center">
          <h4 class="font-semibold text-gray-900 dark:text-white mb-4 text-sm">Objednávky</h4>
          <ul class="space-y-2 text-sm">
            <li>
              <router-link
                to="/orders"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
              >
                Stav objednávky
              </router-link>
            </li>
            <li>
              <a
                href="#"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
              >
                Kontakt
              </a>
            </li>
          </ul>
        </div>

        <!-- Company -->
        <div class="text-center">
          <h4 class="font-semibold text-gray-900 dark:text-white mb-4 text-sm">Spoločnosť</h4>
          <ul class="space-y-2 text-sm">
            <li>
              <router-link
                to="/aboutus"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
              >
                O nás
              </router-link>
            </li>
            <li>
              <router-link
                to="/privacy-policy"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
              >
                Ochrana údajov
              </router-link>
            </li>
          </ul>
        </div>

      </div>

      <!-- Divider -->
      <div class="border-t border-gray-200 dark:border-gray-800 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">
          © 2025 TechShop s.r.o. Všetky práva vyhradené.
        </p>
      </div>

    </div>
  </footer>
</template>

<script>
export default {
  name: 'FooterComponent'
}
</script>

<style scoped>
/* Minimal custom styles; layout driven by Tailwind */
</style>
