<template>
  <div class="page-admin-bg">
    <!-- Sidebar -->
    <aside class="w-64 bg-white dark:bg-gray-800 shadow-lg h-auto">
      <div class="p-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Admin Panel</h2>
        <nav class="space-y-2">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            class="w-full text-left px-4 py-3 rounded-lg transition-all duration-200"
            :class="[
              activeTab === tab.id
                ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md'
                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
            ]"
          >
            {{ tab.label }}
          </button>
        </nav>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-16 mr-16 p-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">
          {{ tabs.find((t) => t.id === activeTab)?.label || tabs[0]?.label }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
          {{ tabs.find((t) => t.id === activeTab)?.description || tabs[0]?.description }}
        </p>
      </div>

      <!-- Orders Management -->
      <div v-if="activeTab === 'orders'" class="space-y-6">
        <!-- Search Bar -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
          <input
            v-model="orderSearch"
            type="text"
            placeholder="Hľadať objednávky (ID, email, meno)..."
            class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <!-- Orders Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
          <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  ID
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Zákazník
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Produkty
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Celkom
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Stav
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Akcie
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              <tr
                v-for="order in filteredOrders"
                :key="order.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-700"
              >
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                >
                  #{{ order.id }}
                </td>
                <td class="px-6 py-4 text-sm">
                  <div class="text-gray-900 dark:text-white font-medium">
                    {{ order.customerName }}
                  </div>
                  <div class="text-gray-500 dark:text-gray-400">{{ order.email }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                  {{ order.items }} položiek
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"
                >
                  {{ order.total }} €
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="getStatusClass(order.status)"
                  >
                    {{ order.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                  <button
                    @click="viewOrder(order)"
                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                  >
                    Zobraziť
                  </button>
                  <button
                    @click="deleteOrder(order.id)"
                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                  >
                    Vymazať
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Users Management -->
      <div v-if="activeTab === 'users'" class="space-y-6">
        <!-- Search Bar -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
          <input
            v-model="userSearch"
            type="text"
            placeholder="Hľadať používateľov (email, meno)..."
            class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <!-- Error Message -->
        <div
          v-if="usersError"
          class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-4"
        >
          <p class="text-red-800 dark:text-red-200 mb-3">
            <strong>Chyba:</strong> {{ usersError }}
          </p>
          <button
            @click="fetchUsers"
            :disabled="usersLoading"
            class="px-4 py-2 btn-danger text-sm disabled:opacity-50"
          >
            {{ usersLoading ? "Načítava sa..." : "Skúsiť znovu" }}
          </button>
        </div>

        <!-- Empty State -->
        <div
          v-if="users.length === 0 && !usersError && !usersLoading"
          class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4"
        >
          <p class="text-yellow-800 dark:text-yellow-200 mb-3">
            <strong>Poznámka:</strong> Žiadni používatelia sa nenachádzajú. Skontrolujte,
            že ste prihlásený/á a že máte oprávnenia na prístup k administrácii.
          </p>
          <button @click="fetchUsers" class="px-4 py-2 btn-warning text-sm">
            Skúsiť znovu
          </button>
        </div>

        <!-- Users Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="user in filteredUsers"
            :key="user.id"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow"
          >
            <div class="flex items-center mb-4">
              <div
                class="w-12 h-12 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg"
              >
                {{ user.name.charAt(0) }}
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ user.name }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</p>
              </div>
            </div>
            <div class="space-y-2 mb-4">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Objednávky:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{
                  user.orders
                }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Celková suma:</span>
                <span class="font-medium text-gray-900 dark:text-white"
                  >{{ user.totalSpent }} €</span
                >
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Registrovaný:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{
                  user.registered
                }}</span>
              </div>
            </div>
            <div class="flex gap-2">
              <button
                @click="editUser(user)"
                class="flex-1 px-4 py-2 btn-primary text-sm rounded-lg"
                :disabled="currentUser.role === 'admin' && user.role === 'owner'"
                :title="
                  currentUser.role === 'admin' && user.role === 'owner'
                    ? 'Nemôžete upravovať Owner účet'
                    : ''
                "
                :class="{
                  'opacity-50 cursor-not-allowed':
                    currentUser.role === 'admin' && user.role === 'owner',
                }"
              >
                Upraviť
              </button>
              <button
                @click="resetUserPassword(user.id)"
                class="flex-1 px-4 py-2 btn-warning text-sm rounded-lg"
                :disabled="currentUser.role === 'admin' && user.role === 'owner'"
                :title="
                  currentUser.role === 'admin' && user.role === 'owner'
                    ? 'Nemôžete resetovať heslo Owner účtu'
                    : ''
                "
                :class="{
                  'opacity-50 cursor-not-allowed':
                    currentUser.role === 'admin' && user.role === 'owner',
                }"
              >
                Reset hesla
              </button>
              <button
                @click="deleteUser(user.id)"
                class="flex-1 px-4 py-2 btn-danger text-sm rounded-lg"
                :disabled="currentUser.role === 'admin' && user.role === 'owner'"
                :title="
                  currentUser.role === 'admin' && user.role === 'owner'
                    ? 'Nemôžete vymazať Owner účet'
                    : ''
                "
                :class="{
                  'opacity-50 cursor-not-allowed':
                    currentUser.role === 'admin' && user.role === 'owner',
                }"
              >
                Vymazať
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Products Management -->
      <div v-if="activeTab === 'products'" class="space-y-6">
        <!-- Add Product Button -->
        <div class="flex justify-between items-center">
          <input
            v-model="productSearch"
            type="text"
            placeholder="Hľadať produkty..."
            class="flex-1 mr-4 px-4 py-2 bg-white dark:bg-gray-800 text-gray-800 dark:text-white rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
          <button
            @click="
              showAddProduct = true;
              resetProductForm();
            "
            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-medium hover:shadow-lg transition-all"
          >
            + Pridať produkt
          </button>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div
            v-for="product in filteredProducts"
            :key="product.id"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow flex flex-col"
          >
            <ProductCard
              :product="product"
              :show-actions="false"
              :show-favorite="false"
            />
            <div class="p-4 mt-auto">
              <div class="flex gap-2">
                <button
                  @click="editProduct(product)"
                  class="flex-1 px-4 py-2 btn-primary text-sm rounded-lg"
                >
                  Upraviť
                </button>
                <button
                  @click="deleteProduct(product.id)"
                  class="flex-1 px-4 py-2 btn-danger text-sm rounded-lg"
                >
                  Vymazať
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics -->
      <div v-if="activeTab === 'stats'" class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div
            v-for="stat in stats"
            :key="stat.label"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6"
          >
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                  {{ stat.label }}
                </p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">
                  {{ stat.value }}
                </p>
                <p
                  class="text-sm mt-2"
                  :class="stat.trend > 0 ? 'text-green-500' : 'text-red-500'"
                >
                  {{ stat.trend > 0 ? "+" : "" }}{{ stat.trend }}% vs minulý mesiac
                </p>
              </div>
              <div
                class="w-12 h-12 rounded-full flex items-center justify-center"
                :style="{ background: stat.color }"
              >
                <span class="text-white text-2xl">{{ stat.icon }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
          <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
            Posledná aktivita
          </h3>
          <div class="space-y-4">
            <div
              v-for="activity in recentActivity"
              :key="activity.id"
              class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700 last:border-0"
            >
              <div class="flex items-center">
                <div
                  class="w-10 h-10 rounded-full flex items-center justify-center mr-4"
                  :class="
                    activity.type === 'order'
                      ? 'bg-green-100 dark:bg-green-900'
                      : 'bg-blue-100 dark:bg-blue-900'
                  "
                >
                  <span class="text-lg">{{
                    activity.type === "order" ? "🛒" : "👤"
                  }}</span>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ activity.title }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ activity.time }}
                  </p>
                </div>
              </div>
              <span class="text-sm font-medium text-gray-900 dark:text-white">{{
                activity.value
              }}</span>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal for Order Details -->
    <div
      v-if="selectedOrder"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click="selectedOrder = null"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-2xl w-full mx-4"
        @click.stop
      >
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
          Objednávka #{{ selectedOrder.id }}
        </h2>
        <div class="space-y-4">
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Zákazník</p>
            <p class="text-lg font-medium text-gray-900 dark:text-white">
              {{ selectedOrder.customerName }}
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              {{ selectedOrder.email }}
            </p>
          </div>
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Celková suma</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">
              {{ selectedOrder.total }} €
            </p>
          </div>
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Stav</p>
            <span
              class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
              :class="getStatusClass(selectedOrder.status)"
            >
              {{ selectedOrder.status }}
            </span>
          </div>
        </div>
        <button
          @click="selectedOrder = null"
          class="mt-6 w-full px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-medium hover:shadow-lg transition-all"
        >
          Zavrieť
        </button>
      </div>
    </div>

    <!-- Modal for Edit User -->
    <div
      v-if="editingUser"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-2xl w-full mx-4"
        @click.stop
      >
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            Úprava používateľa
          </h2>
          <button
            type="button"
            @click="editingUser = null"
            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              ></path>
            </svg>
          </button>
        </div>
        <form @submit.prevent="saveUserChanges" class="space-y-6">
          <!-- Name -->
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              Meno
            </label>
            <input
              v-model="editingUser.name"
              type="text"
              required
              placeholder="Meno a priezvisko"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
            />
          </div>

          <!-- Email -->
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              Email
            </label>
            <input
              v-model="editingUser.email"
              type="email"
              required
              placeholder="vas.email@priklad.sk"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
            />
          </div>

          <!-- Phone -->
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              Telefón
            </label>
            <input
              v-model="editingUser.phone"
              type="tel"
              placeholder="+421 123 456 789"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
            />
          </div>

          <!-- Role (only visible to owner) -->
          <div v-if="currentUser.role === 'owner'">
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              Rola
            </label>
            <select
              v-model="editingUser.role"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500"
            >
              <option value="user">User</option>
              <option value="admin">Admin</option>
              <option value="owner">Owner</option>
            </select>
          </div>

          <!-- Password: owners get manual set + generate; admins get generate-only -->
          <div v-if="currentUser.role === 'owner'">
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              Nastaviť heslo
            </label>
            <input
              v-model="editingUser.password"
              type="password"
              placeholder="Zadajte nové heslo"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
            />
            <div class="flex gap-3 mt-3">
              <button
                type="button"
                @click="saveUserPassword"
                :disabled="editingSaving"
                class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50"
              >
                {{ editingSaving ? "Ukladá sa..." : "Nastaviť heslo" }}
              </button>

              <button
                type="button"
                @click="generateRandomPassword"
                class="flex-1 px-4 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium transition-colors"
              >
                Vygenerovať heslo
              </button>
            </div>
          </div>

          <div v-else-if="currentUser.role === 'admin'">
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              Generovať heslo
            </label>
            <div class="flex gap-3 mt-3">
              <button
                type="button"
                @click="generateRandomPassword"
                class="flex-1 px-4 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium transition-colors"
              >
                Vygenerovať heslo
              </button>
            </div>
          </div>

          <!-- Generated Password Display -->
          <div
            v-if="generatedPassword"
            class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-4 mt-3"
          >
            <p class="text-sm text-green-700 dark:text-green-200 font-medium mb-2">
              Heslo vygenerované a uložené:
            </p>
            <p class="text-lg font-mono text-green-900 dark:text-green-100 break-all">
              {{ generatedPassword }}
            </p>
          </div>

          <!-- Error Message -->
          <div
            v-if="editError"
            class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg p-3 mb-4"
          >
            <p class="text-red-800 dark:text-red-200 text-sm">{{ editError }}</p>
          </div>

          <!-- Buttons -->
          <div class="flex gap-3 mt-6">
            <button
              type="submit"
              :disabled="editingSaving"
              class="flex-1 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-medium hover:shadow-lg transition-all disabled:opacity-50"
            >
              {{ editingSaving ? "Ukladá sa..." : "Uložiť zmeny" }}
            </button>
            <button
              type="button"
              @click="editingUser = null"
              class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white rounded-lg font-medium hover:shadow-lg transition-all"
            >
              Zrušiť
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Confirmation Modal for Password Generation -->
    <div
      v-if="showPasswordConfirmation"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click="showPasswordConfirmation = false"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-md w-full mx-4"
        @click.stop
      >
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
          Potvrdiť vygenerovanie hesla
        </h2>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
          Ste si istí, že chcete vygenerovať heslo pre tohto používateľa?
        </p>
        <div class="flex gap-3">
          <button
            @click="confirmAndGeneratePassword"
            class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors"
          >
            Áno, vygenerovať
          </button>
          <button
            @click="showPasswordConfirmation = false"
            class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white rounded-lg font-medium transition-colors"
          >
            Zrušiť
          </button>
        </div>
      </div>
    </div>

    <!-- Modal for Add/Edit Product -->
    <transition name="modal">
      <div
        v-if="showAddProduct || editingProduct"
        class="fixed inset-0 backdrop-blur-lg flex items-center justify-center z-50"
      >
        <div
          class="modal-content bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-8 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto"
          @click.stop
        >
          <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
              {{ editingProduct ? "Upraviť produkt" : "Pridať nový produkt" }}
            </h2>
            <button
              type="button"
              @click="
                showAddProduct = false;
                editingProduct = null;
              "
              class="text-gray-400 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
            >
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                ></path>
              </svg>
            </button>
          </div>
          <form @submit.prevent="saveProduct" class="space-y-6">
            <!-- Name -->
            <div>
              <label
                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
              >
                Názov
              </label>
              <input
                v-model="currentProduct.name"
                type="text"
                placeholder="Názov produktu"
                required
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <!-- Brand -->
            <div>
              <label
                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
              >
                Značka
              </label>
              <input
                v-model="currentProduct.brand"
                type="text"
                placeholder="Značka produktu"
                required
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <!-- Category -->
            <div>
              <label
                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
              >
                Kategória
              </label>
              <select
                v-model="currentProduct.category"
                required
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
                <option value="">-- Vyberte kategóriu --</option>
                <option v-for="cat in uniqueCategories" :key="cat" :value="cat">
                  {{ cat }}
                </option>
              </select>
            </div>

            <!-- Pricing Section -->
            <div class="border-t border-gray-300 dark:border-gray-600 pt-6 mt-6">
              <h3 class="text-base font-bold text-gray-900 dark:text-white mb-5">
                Ceny a zľavy
              </h3>

              <!-- Current Status Summary -->
              <div
                v-if="editingProduct && originalProduct"
                class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-5"
              >
                <div
                  v-if="originalProduct.discount_value > 0"
                  class="grid grid-cols-3 gap-4 text-sm"
                >
                  <div>
                    <p class="text-blue-600 dark:text-blue-300 font-semibold">
                      Aktuálna cena
                    </p>
                    <p class="text-lg font-bold text-blue-900 dark:text-blue-100">
                      {{ originalProduct.oldPrice }}€
                    </p>
                  </div>
                  <div>
                    <p class="text-blue-600 dark:text-blue-300 font-semibold">Zľava</p>
                    <p class="text-lg font-bold text-blue-900 dark:text-blue-100">
                      {{ originalProduct.discount_value
                      }}{{ originalProduct.discount_type === "percent" ? "%" : "€" }}
                    </p>
                  </div>
                  <div>
                    <p class="text-blue-600 dark:text-blue-300 font-semibold">
                      Finálna cena
                    </p>
                    <p class="text-lg font-bold text-blue-900 dark:text-blue-100">
                      {{ originalProduct.price }}€
                    </p>
                  </div>
                </div>
                <div v-else class="flex items-center justify-between">
                  <div>
                    <p class="text-blue-600 dark:text-blue-300 font-semibold mb-2">
                      Aktuálna cena
                    </p>
                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                      {{ originalProduct.oldPrice }}€
                    </p>
                  </div>
                  <div
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 dark:bg-green-900 border border-green-300 dark:border-green-700 rounded-lg"
                  >
                    <span class="text-green-700 dark:text-green-200 font-semibold"
                      >✓ Bez zľavy</span
                    >
                  </div>
                </div>
              </div>

              <!-- Base Price Input -->
              <div class="mb-5">
                <label
                  class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                >
                  Aktuálna cena (€) *
                </label>
                <input
                  v-model.number="currentProduct.oldPrice"
                  type="number"
                  step="1.00"
                  placeholder="0.00"
                  required
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                />
              </div>

              <!-- Discount Controls -->
              <div class="grid grid-cols-3 gap-6 mb-5">
                <div>
                  <label
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                  >
                    Typ zľavy
                  </label>
                  <select
                    v-model="currentProduct.discount_type"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                  >
                    <option value="percent">Percento (%)</option>
                    <option value="fixed">Fixná suma (€)</option>
                  </select>
                </div>
                <div>
                  <label
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                  >
                    Veľkosť zľavy
                    <span
                      v-if="currentProduct.discount_value > 0"
                      class="text-indigo-600 dark:text-indigo-400 font-bold"
                    >
                      {{ currentProduct.discount_value
                      }}{{ currentProduct.discount_type === "percent" ? "%" : "€" }}
                    </span>
                  </label>
                  <input
                    v-model.number="currentProduct.discount_value"
                    type="number"
                    step="1.00"
                    min="0"
                    placeholder="0"
                    :max="currentProduct.discount_type === 'percent' ? 100 : undefined"
                    @input="clampDiscountValue"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-lg"
                  />
                </div>
                <div>
                  <label
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                  >
                    Finálna cena
                  </label>
                  <div
                    class="w-full px-4 py-3 border border-indigo-300 dark:border-indigo-600 rounded-lg bg-indigo-50 dark:bg-indigo-900 text-indigo-900 dark:text-indigo-100 text-lg font-bold"
                  >
                    {{ calculateFinalPrice() }}€
                  </div>
                  <p
                    v-if="currentProduct.discount_value > 0"
                    class="text-xs text-gray-600 dark:text-gray-400 mt-2"
                  >
                    Zľava: {{ getDiscountAmount() }}€
                  </p>
                </div>
              </div>

              <button
                v-if="currentProduct.discount_value > 0"
                type="button"
                @click="removeDiscount"
                class="text-sm font-medium px-4 py-2 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 rounded-lg hover:bg-red-200 dark:hover:bg-red-800 transition-colors"
              >
                Odstrániť zľavu
              </button>
            </div>

            <!-- Stock -->
            <div>
              <label
                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
              >
                Sklad (ks) *
              </label>
              <input
                v-model.number="currentProduct.stock"
                type="number"
                placeholder="0"
                required
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <!-- Image URL -->
            <div>
              <label
                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
              >
                URL obrázka
              </label>
              <input
                v-model="currentProduct.image"
                type="url"
                placeholder="https://example.com/image.jpg"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <!-- Description -->
            <div>
              <label
                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
              >
                Popis
              </label>
              <textarea
                v-model="currentProduct.description"
                placeholder="Podrobný popis produktu..."
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                rows="4"
              ></textarea>
            </div>

            <!-- Buttons -->
            <div
              class="flex gap-4 mt-8 pt-6 border-t border-gray-300 dark:border-gray-600"
            >
              <button
                type="submit"
                :disabled="productSaving"
                class="flex-1 btn-primary text-white rounded-lg font-semibold hover:shadow-lg transition-all disabled:opacity-50"
              >
                {{
                  productSaving
                    ? "Ukladá sa..."
                    : editingProduct
                    ? "Uložiť zmeny"
                    : "Pridať produkt"
                }}
              </button>
              <button
                type="button"
                @click="
                  showAddProduct = false;
                  editingProduct = null;
                "
                class="flex-1 px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg font-semibold hover:shadow-lg transition-all"
              >
                Zrušiť
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import api from "@/api.ts";
import ProductCard from "@/components/ProductCard.vue";

export default {
  name: "AdminView",
  components: {
    ProductCard,
  },
  data() {
    return {
      activeTab: "stats",
      orderSearch: "",
      userSearch: "",
      productSearch: "",
      selectedOrder: null,
      showAddProduct: false,
      editingUser: null,
      editingSaving: false,
      editError: "",
      usersLoading: false,
      usersError: null,
      generatedPassword: "",
      showGeneratedPassword: false,
      showPasswordConfirmation: false,
      editingProduct: null,
      currentProduct: {
        name: "",
        brand: "",
        category: "",
        price: 0,
        oldPrice: null,
        stock: 0,
        image: "",
        description: "",
        discount_type: "percent",
        discount_value: 0,
      },
      originalProduct: null,
      productSaving: false,
      currentUser: JSON.parse(localStorage.getItem("user") || "{}"),

      allTabs: [
        { id: "stats", label: "Štatistiky", description: "Prehľad výkonu vášho obchodu" },
        {
          id: "orders",
          label: "Objednávky",
          description: "Spravujte objednávky zákazníkov",
        },
        {
          id: "users",
          label: "Používatelia",
          description: "Spravujte používateľské účty",
        },
        {
          id: "products",
          label: "Produkty",
          description: "Spravujte produkty v obchode",
        },
      ],

      stats: [
        {
          label: "Celkový predaj",
          value: "45,231 €",
          trend: 12,
          icon: "💰",
          color: "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
        },
        {
          label: "Objednávky",
          value: "342",
          trend: 8,
          icon: "📦",
          color: "linear-gradient(135deg, #f093fb 0%, #f5576c 100%)",
        },
        {
          label: "Zákazníci",
          value: "1,234",
          trend: 15,
          icon: "👥",
          color: "linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)",
        },
        {
          label: "Priem. objednávka",
          value: "132 €",
          trend: -3,
          icon: "📊",
          color: "linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)",
        },
      ],

      recentActivity: [
        {
          id: 1,
          type: "order",
          title: "Nová objednávka od Ján Novák",
          value: "234 €",
          time: "pred 5 min",
        },
        {
          id: 2,
          type: "user",
          title: "Nová registrácia: Peter Malý",
          value: "",
          time: "pred 15 min",
        },
        {
          id: 3,
          type: "order",
          title: "Objednávka #3421 bola doručená",
          value: "156 €",
          time: "pred 1 hod",
        },
        {
          id: 4,
          type: "order",
          title: "Nová objednávka od Mária Veselá",
          value: "892 €",
          time: "pred 2 hod",
        },
        {
          id: 5,
          type: "user",
          title: "Nová registrácia: Tomáš Horný",
          value: "",
          time: "pred 3 hod",
        },
      ],

      orders: [
        {
          id: 1001,
          customerName: "Ján Novák",
          email: "jan.novak@gmail.com",
          items: 3,
          total: 1599,
          status: "Doručené",
        },
        {
          id: 1002,
          customerName: "Peter Malý",
          email: "peter.maly@gmail.com",
          items: 1,
          total: 279,
          status: "V preprave",
        },
        {
          id: 1003,
          customerName: "Mária Veselá",
          email: "maria.vesela@gmail.com",
          items: 2,
          total: 2398,
          status: "Spracováva sa",
        },
        {
          id: 1004,
          customerName: "Tomáš Horný",
          email: "tomas.horny@gmail.com",
          items: 4,
          total: 456,
          status: "Doručené",
        },
        {
          id: 1005,
          customerName: "Eva Nová",
          email: "eva.nova@gmail.com",
          items: 1,
          total: 1199,
          status: "Zrušené",
        },
      ],

      users: [],

      products: [],
    };
  },
  mounted() {
    this.currentUser = JSON.parse(localStorage.getItem("user") || "{}");
    // Set activeTab to first available tab for user
    this.$nextTick(() => {
      if (this.currentUser.role === "admin" && this.activeTab === "stats") {
        this.activeTab = "orders";
      }
    });
    this.fetchUsers();
    this.loadProducts();
    this.updateBodyScroll();
  },
  computed: {
    tabs() {
      // Hide stats tab for admins (only show for owners)
      if (this.currentUser.role === "admin") {
        return this.allTabs.filter((tab) => tab.id !== "stats");
      }
      return this.allTabs;
    },
    filteredOrders() {
      if (!this.orderSearch) return this.orders;
      const search = this.orderSearch.toLowerCase();
      return this.orders.filter(
        (order) =>
          order.id.toString().includes(search) ||
          order.customerName.toLowerCase().includes(search) ||
          order.email.toLowerCase().includes(search)
      );
    },
    filteredUsers() {
      if (!this.userSearch) return this.users;
      const search = this.userSearch.toLowerCase();
      return this.users.filter(
        (user) =>
          user.name.toLowerCase().includes(search) ||
          user.email.toLowerCase().includes(search)
      );
    },
    filteredProducts() {
      if (!this.productSearch) return this.products;
      const search = this.productSearch.toLowerCase();
      return this.products.filter(
        (product) =>
          (product.name || product.title || "").toLowerCase().includes(search) ||
          (product.brand || "").toLowerCase().includes(search) ||
          (product.category || "").toLowerCase().includes(search)
      );
    },
    uniqueCategories() {
      const categories = new Set(this.products.map((p) => p.category).filter(Boolean));
      return Array.from(categories).sort();
    },
  },

  watch: {
    showAddProduct() {
      this.updateBodyScroll();
    },
    editingProduct() {
      this.updateBodyScroll();
    },
    "currentProduct.discount_value"() {
      // When discount value is set to 0, restore price to oldPrice
      if (this.currentProduct.discount_value === 0 && this.currentProduct.oldPrice) {
        this.currentProduct.price = this.currentProduct.oldPrice;
      }
    },
  },

  beforeUnmount() {
    // cleanup any scroll locks/listeners when component unmounts
    this._removePreventScrollListeners();
  },

  methods: {
    updateBodyScroll() {
      const modalOpen = !!this.showAddProduct || !!this.editingProduct;
      if (modalOpen) {
        // Keep scrollbar visible but prevent page scrolling by intercepting scroll/touch/key events
        this._addPreventScrollListeners();
      } else {
        this._removePreventScrollListeners();
      }
    },

    _preventDefault(e) {
      if (e && e.preventDefault) e.preventDefault();
      return false;
    },
    _onWheel(e) {
      // allow inner modal scrolling when target is inside modal-content
      const modal = document.querySelector(".modal-content");
      if (!modal) return;
      if (modal.contains(e.target)) return; // let modal handle scrolling
      this._preventDefault(e);
    },
    _onTouchMove(e) {
      const modal = document.querySelector(".modal-content");
      if (!modal) return;
      if (modal.contains(e.target)) return;
      this._preventDefault(e);
    },
    _onKeyDown(e) {
      // prevent PageUp, PageDown, Space, Arrow keys from scrolling background
      const keys = [32, 33, 34, 35, 36, 37, 38, 39, 40];
      if (keys.includes(e.keyCode)) {
        // if focus is inside modal allow default
        const modal = document.querySelector(".modal-content");
        if (modal && modal.contains(document.activeElement)) return;
        this._preventDefault(e);
      }
    },
    _addPreventScrollListeners() {
      if (this._preventListenersAdded) return;
      // use non-passive to be able to preventDefault
      window.addEventListener("wheel", this._onWheel, { passive: false });
      window.addEventListener("touchmove", this._onTouchMove, { passive: false });
      window.addEventListener("keydown", this._onKeyDown, { passive: false });
      this._preventListenersAdded = true;
    },
    _removePreventScrollListeners() {
      if (!this._preventListenersAdded) return;
      window.removeEventListener("wheel", this._onWheel, { passive: false });
      window.removeEventListener("touchmove", this._onTouchMove, { passive: false });
      window.removeEventListener("keydown", this._onKeyDown, { passive: false });
      this._preventListenersAdded = false;
    },
    getStatusClass(status) {
      const classes = {
        Doručené: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300",
        "V preprave": "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300",
        "Spracováva sa":
          "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300",
        Zrušené: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300",
      };
      return (
        classes[status] || "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300"
      );
    },
    viewOrder(order) {
      this.selectedOrder = order;
    },
    async deleteOrder(id) {
      if (await window.appConfirm("Naozaj chcete vymazať túto objednávku?")) {
        this.orders = this.orders.filter((order) => order.id !== id);
        alert("Objednávka vymazaná!");
      }
    },
    async deleteUser(id) {
      const target = this.users.find((u) => u.id === id);
      if (this.currentUser?.role === "admin" && target?.role === "owner") {
        alert("Nemôžete vymazať Owner účet.");
        return;
      }

      if (await window.appConfirm("Naozaj chcete vymazať tento účet?")) {
        api
          .delete(`api/admin/users/${id}`)
          .then(() => {
            this.users = this.users.filter((user) => user.id !== id);
            alert("Používateľ vymazaný!");
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("Chyba pri vymazávaní používateľa");
          });
      }
    },
    async resetUserPassword(id) {
      const target = this.users.find((u) => u.id === id);
      if (this.currentUser?.role === "admin" && target?.role === "owner") {
        alert("Nemôžete resetovať heslo Owner účtu.");
        return;
      }

      if (await window.appConfirm("Naozaj chcete resetovať heslo tohto používateľa?")) {
        api
          .post(`api/admin/users/${id}/reset-password`, { send_notification: false })
          .then((response) => {
            alert(
              response.data.message || "Reset hesla bol odoslaný na email používateľa!"
            );
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("Chyba pri resetovaní hesla");
          });
      }
    },
    editUser(user) {
      // Prevent admins from editing owner accounts
      if (this.currentUser?.role === "admin" && user?.role === "owner") {
        alert("Nemôžete upravovať Owner účet.");
        return;
      }

      this.editingUser = {
        id: user.id,
        name: user.name,
        email: user.email,
        phone: this.formatPhoneNumber(user.phone) || "",
        role: user.role || "user",
      };
      this.editError = "";
      this.generatedPassword = "";
      this.showGeneratedPassword = false;
    },
    generateRandomPassword() {
      this.showPasswordConfirmation = true;
    },
    confirmAndGeneratePassword() {
      const uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      const lowercase = "abcdefghijklmnopqrstuvwxyz";
      const numbers = "0123456789";
      const symbols = "!@#$%^&*";

      // Weight towards letters more than symbols
      const allChars = uppercase + uppercase + lowercase + lowercase + numbers + symbols;

      let password = "";
      // Ensure at least one of each type
      password += uppercase[Math.floor(Math.random() * uppercase.length)];
      password += lowercase[Math.floor(Math.random() * lowercase.length)];
      password += numbers[Math.floor(Math.random() * numbers.length)];
      password += symbols[Math.floor(Math.random() * symbols.length)];

      // Fill the rest randomly (15 more chars = 19 total)
      for (let i = password.length; i < 16; i++) {
        password += allChars[Math.floor(Math.random() * allChars.length)];
      }

      // Shuffle the password
      password = password
        .split("")
        .sort(() => Math.random() - 0.5)
        .join("");

      this.generatedPassword = password;
      this.showPasswordConfirmation = false;
      // Auto-save the generated password
      this.saveGeneratedPassword();
    },
    async saveGeneratedPassword() {
      if (!this.generatedPassword || !this.editingUser) return;

      try {
        const response = await api.post(
          `api/admin/users/${this.editingUser.id}/set-password`,
          {
            password: this.generatedPassword,
            send_notification: false,
          }
        );
        console.log("Password set response:", response.data);
      } catch (error) {
        console.error("Error setting password:", error);
        const errorMsg =
          error.response?.data?.message || error.response?.data?.errors || error.message;
        console.error("Full error:", errorMsg);
        this.editError = "Chyba pri nastavovaní hesla: " + JSON.stringify(errorMsg);
      }
    },
    async saveUserPassword() {
      if (!this.editingUser || !this.editingUser.id) return;
      if (!this.editingUser.password) {
        this.editError = "Zadajte nové heslo.";
        return;
      }
      this.editingSaving = true;
      this.editError = "";
      try {
        const response = await api.post(
          `api/admin/users/${this.editingUser.id}/set-password`,
          { password: this.editingUser.password, send_notification: false }
        );
        console.log("Password set response:", response.data);
        alert("Heslo bolo nastavené.");
        this.editingUser.password = "";
        this.editingUser = null;
        this.showGeneratedPassword = false;
        this.generatedPassword = "";
      } catch (error) {
        console.error("Error setting password:", error);
        const errorMsg =
          error.response?.data?.message || error.response?.data?.errors || error.message;
        this.editError = "Chyba pri nastavovaní hesla: " + JSON.stringify(errorMsg);
      } finally {
        this.editingSaving = false;
      }
    },
    copyToClipboard() {
      navigator.clipboard.writeText(this.generatedPassword);
    },
    formatPhoneNumber(phone) {
      if (!phone) return "";
      // Remove all non-digits except leading +
      const cleaned = phone.replace(/[^\d+]/g, "");
      // Format as +XXX XXX XXX XXX
      if (cleaned.startsWith("+")) {
        const withoutPlus = cleaned.slice(1);
        return "+" + withoutPlus.replace(/(\d{3})(?=\d)/g, "$1 ");
      }
      return cleaned.replace(/(\d{3})(?=\d)/g, "$1 ");
    },
    saveUserChanges() {
      this.editingSaving = true;
      this.editError = "";

      // Prevent owner from demoting themselves
      if (
        this.currentUser?.id === this.editingUser?.id &&
        this.editingUser.role &&
        this.editingUser.role !== "owner"
      ) {
        this.editError = "Nemôžete odstrániť svoje oprávnenie Owner.";
        this.editingSaving = false;
        return;
      }

      const data = {
        name: this.editingUser.name,
        email: this.editingUser.email,
        phone: this.editingUser.phone,
      };

      // Only owners can change roles
      if (this.currentUser?.role === "owner" && this.editingUser.role) {
        data.role = this.editingUser.role;
      }

      api
        .put(`api/admin/users/${this.editingUser.id}`, data)
        .then((response) => {
          // Update user in the list
          const index = this.users.findIndex((u) => u.id === this.editingUser.id);
          if (index !== -1) {
            this.users[index].name = this.editingUser.name;
            this.users[index].email = this.editingUser.email;
            this.users[index].phone = this.editingUser.phone;
            if (data.role) this.users[index].role = data.role;
          }
          this.editingUser = null;
          this.showGeneratedPassword = false;
          alert("Zmeny boli uložené!");
        })
        .catch((error) => {
          console.error("Error:", error);
          this.editError = error.response?.data?.message || "Chyba pri ukládaní zmien";
        })
        .finally(() => {
          this.editingSaving = false;
        });
    },
    fetchUsers() {
      this.usersLoading = true;
      this.usersError = null;
      api
        .get("api/admin/users")
        .then((response) => {
          console.log("Users fetched:", response.data);
          this.users = response.data;
          this.usersError = null;
        })
        .catch((error) => {
          console.error("Error fetching users:", error);
          if (error.response) {
            console.error("Response status:", error.response.status);
            console.error("Response data:", error.response.data);
            this.usersError = `Chyba ${error.response.status}: ${
              error.response.data?.message || "Nepodarilo sa načítať používateľov"
            }`;
          } else {
            this.usersError = error.message || "Chyba pri načítavaní používateľov";
          }
        })
        .finally(() => {
          this.usersLoading = false;
        });
    },
    async loadProducts() {
      try {
        const response = await api.get("api/admin/products");
        this.products = response.data;
      } catch (error) {
        console.error("Error loading products:", error);
      }
    },
    editProduct(product) {
      const productData = {
        name: product.name || product.title || "",
        title: product.title || product.name || "",
        brand: product.brand || "",
        category: product.category || "",
        price: typeof product.price !== "undefined" ? product.price : 0,
        oldPrice:
          typeof product.oldPrice !== "undefined" && product.oldPrice
            ? product.oldPrice
            : typeof product.price !== "undefined"
            ? product.price
            : 0,
        monthlyPrice:
          typeof product.monthlyPrice !== "undefined" ? product.monthlyPrice : null,
        stock: typeof product.stock !== "undefined" ? product.stock : 0,
        image: product.image || "",
        description: product.description || "",
        rating: typeof product.rating !== "undefined" ? product.rating : 0,
        reviews: typeof product.reviews !== "undefined" ? product.reviews : 0,
        specs: product.specs || null,
        discount_type: product.discount_type || "percent",
        discount_value:
          typeof product.discount_value !== "undefined" ? product.discount_value : 0,
      };

      // Store original state for the blue box (won't change as user edits)
      this.originalProduct = JSON.parse(JSON.stringify(productData));

      // Set editable form data
      this.currentProduct = productData;
      this.editingProduct = product;
      this.showAddProduct = false;
    },
    async saveProduct() {
      this.productSaving = true;
      try {
        // Smart price calculation: oldPrice is always the base price (price without discount)
        let finalPrice = this.currentProduct.price;
        let finalOldPrice = this.currentProduct.oldPrice;
        let finalDiscountType = this.currentProduct.discount_type || "percent";
        let finalDiscountValue = this.currentProduct.discount_value || 0;

        // Always keep oldPrice as the base price
        if (!finalOldPrice || finalOldPrice === 0) {
          finalOldPrice = finalPrice;
        }

        // If discount is applied, calculate the final price from oldPrice
        if (finalDiscountValue > 0) {
          if (finalDiscountType === "percent") {
            finalPrice = (
              finalOldPrice -
              (finalOldPrice * finalDiscountValue) / 100
            ).toFixed(2);
          } else {
            finalPrice = (finalOldPrice - finalDiscountValue).toFixed(2);
          }
        } else {
          // If no discount, price equals oldPrice
          finalPrice = finalOldPrice;
        }

        // Only send form fields, not all product fields
        const payload = {
          name: this.currentProduct.name,
          brand: this.currentProduct.brand,
          category: this.currentProduct.category,
          price: parseFloat(finalPrice),
          oldPrice: parseFloat(finalOldPrice),
          stock: this.currentProduct.stock,
          image: this.currentProduct.image,
          description: this.currentProduct.description,
          discount_type: finalDiscountValue > 0 ? finalDiscountType : null,
          discount_value: finalDiscountValue > 0 ? finalDiscountValue : null,
        };

        if (this.editingProduct) {
          // Update existing product and use API returned product
          const response = await api.put(
            `api/admin/products/${this.editingProduct.id}`,
            payload
          );
          const updated = response.data.product || this.currentProduct;
          const index = this.products.findIndex((p) => p.id === this.editingProduct.id);
          if (index !== -1) {
            this.products.splice(index, 1, updated);
          }
          alert("Produkt bol aktualizovaný!");
        } else {
          // Create new product
          const response = await api.post("api/admin/products", payload);
          this.products.push(response.data.product);
          alert("Produkt bol vytvorený!");
        }
        this.editingProduct = null;
        this.showAddProduct = false;
        this.currentProduct = {
          name: "",
          category: "",
          price: 0,
          oldPrice: null,
          stock: 0,
          image: "",
          description: "",
          discount_type: "percent",
          discount_value: 0,
        };
      } catch (error) {
        console.error("Error saving product:", error);
        alert("Chyba pri ukladaní produktu");
      } finally {
        this.productSaving = false;
      }
    },
    async deleteProduct(id) {
      if (await window.appConfirm("Naozaj chcete vymazať tento produkt?")) {
        try {
          await api.delete(`api/admin/products/${id}`);
          this.products = this.products.filter((product) => product.id !== id);
          alert("Produkt vymazaný!");
        } catch (error) {
          console.error("Error deleting product:", error);
          alert("Chyba pri vymazávaní produktu");
        }
      }
    },
    resetProductForm() {
      this.currentProduct = {
        name: "",
        brand: "",
        category: "",
        price: 0,
        oldPrice: null,
        stock: 0,
        image: "",
        description: "",
        discount_type: "percent",
        discount_value: 0,
      };
      this.originalProduct = null;
    },
    clampDiscountValue(e) {
      // get the typed value (string) or fallback to model
      const raw = e && e.target ? e.target.value : this.currentProduct.discount_value;
      let val = parseFloat(raw);
      if (Number.isNaN(val)) {
        this.currentProduct.discount_value = 0;
        return;
      }

      // Enforce percent limits
      if (this.currentProduct.discount_type === "percent") {
        if (val > 100) val = 100;
      }

      // Never allow negative
      if (val < 0) val = 0;

      // For fixed discounts, don't allow discount > oldPrice (prevents negative final price)
      if (
        this.currentProduct.discount_type === "fixed" &&
        this.currentProduct.oldPrice != null
      ) {
        if (val > this.currentProduct.oldPrice) val = this.currentProduct.oldPrice;
      }

      // Round to 2 decimals and set back
      this.currentProduct.discount_value = Math.round(val * 100) / 100;
    },
    calculateFinalPrice() {
      // If no discount set, return the base price (oldPrice)
      if (
        this.currentProduct.discount_value === 0 ||
        !this.currentProduct.discount_type
      ) {
        return this.currentProduct.oldPrice || this.currentProduct.price;
      }

      // Use oldPrice as the base price for discount calculation
      const basePrice = this.currentProduct.oldPrice || this.currentProduct.price;
      const discount = this.currentProduct.discount_value;

      if (this.currentProduct.discount_type === "percent") {
        return (basePrice - (basePrice * discount) / 100).toFixed(2);
      } else {
        return (basePrice - discount).toFixed(2);
      }
    },
    getDiscountAmount() {
      // If no discount set, return 0
      if (
        this.currentProduct.discount_value === 0 ||
        !this.currentProduct.discount_type
      ) {
        return "0.00";
      }

      // Always use oldPrice if it exists (it's the base for discount calculation)
      // If oldPrice doesn't exist, use price
      const basePrice = this.currentProduct.oldPrice || this.currentProduct.price;
      const discount = this.currentProduct.discount_value;

      if (this.currentProduct.discount_type === "percent") {
        return ((basePrice * discount) / 100).toFixed(2);
      } else {
        return discount.toFixed(2);
      }
    },
    removeDiscount() {
      // When removing discount, price becomes equal to oldPrice (base price)
      this.currentProduct.price = this.currentProduct.oldPrice;
      // Clear discount fields only
      this.currentProduct.discount_type = "percent";
      this.currentProduct.discount_value = 0;
    },
  },
};
</script>

<style scoped>
@media (max-width: 768px) {
  aside {
    width: 100%;
    position: relative;
  }
  main {
    margin-left: 0;
  }
}

/* Modal animation */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 240ms ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
.modal-enter-to,
.modal-leave-from {
  opacity: 1;
}

/* Animate modal content separately (scale + translate) */
.modal-enter-active .modal-content,
.modal-leave-active .modal-content {
  transition: transform 260ms cubic-bezier(0.2, 0.9, 0.2, 1), opacity 260ms ease;
}
.modal-enter-from .modal-content {
  transform: translateY(12px) scale(0.98);
  opacity: 0;
}
.modal-enter-to .modal-content {
  transform: translateY(0) scale(1);
  opacity: 1;
}
.modal-leave-from .modal-content {
  transform: translateY(0) scale(1);
  opacity: 1;
}
.modal-leave-to .modal-content {
  transform: translateY(12px) scale(0.98);
  opacity: 0;
}
</style>
