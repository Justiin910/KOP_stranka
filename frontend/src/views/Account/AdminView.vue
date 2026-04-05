<template>
  <div class="page-admin-bg">
    <!-- Sidebar -->
    <aside class="hidden md:block md:w-64 bg-white dark:bg-gray-800 shadow-lg h-auto">
      <div class="p-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">
          {{ $t("profile.admin_panel") }}
        </h2>
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
            {{ $t(`admin.tabs.${tab.id}`) }}
          </button>
        </nav>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-4 sm:p-6 md:p-8">
      <div class="md:hidden mb-4">
        <details
          ref="mobileTabMenu"
          class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700"
        >
          <summary
            class="list-none cursor-pointer px-4 py-3 flex items-center justify-between text-sm font-semibold text-gray-800 dark:text-gray-100"
          >
            <span>{{ $t("profile.admin_panel") }}</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              ></path>
            </svg>
          </summary>
          <nav class="space-y-2 p-2 border-t border-gray-200 dark:border-gray-700">
            <button
              v-for="tab in tabs"
              :key="`mobile-${tab.id}`"
              @click="selectMobileTab(tab.id)"
              class="w-full text-left px-4 py-3 rounded-lg transition-all duration-200"
              :class="[
                activeTab === tab.id
                  ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md'
                  : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
              ]"
            >
              {{ $t(`admin.tabs.${tab.id}`) }}
            </button>
          </nav>
        </details>
      </div>

      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">
          {{ $t(`admin.tabs.${activeTab}`) }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
          {{ $t(`admin.descriptions.${activeTab}`) }}
        </p>
      </div>

      <!-- Orders Management -->
      <div v-if="activeTab === 'orders'" class="space-y-6">
        <!-- Search Bar -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
          <input
            v-model="orderSearch"
            type="text"
            :placeholder="$t('admin.orders.search_placeholder')"
            class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <!-- Mobile Orders Cards -->
        <div class="space-y-3 md:hidden">
          <article
            v-for="order in filteredOrders"
            :key="`mobile-order-${order.id}`"
            class="bg-white dark:bg-gray-800 rounded-lg shadow p-4"
          >
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">#{{ order.id }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ order.reference }}</p>
              </div>
              <span
                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                :class="getStatusClass(order.status)"
              >
                {{ formatStatus(order.status) }}
              </span>
            </div>

            <div class="mt-3 space-y-1 text-sm">
              <p class="text-gray-900 dark:text-white font-medium">{{ order.customerName }}</p>
              <p class="text-gray-500 dark:text-gray-400 break-all">{{ order.email }}</p>
            </div>

            <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
              <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                  {{ $t("admin.table.products") }}
                </p>
                <p class="text-gray-900 dark:text-white font-medium">
                  {{ formatItems(order.items) }}
                </p>
              </div>
              <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                  {{ $t("admin.table.total") }}
                </p>
                <p class="text-gray-900 dark:text-white font-medium">{{ order.total }} €</p>
              </div>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700 flex gap-2">
              <button
                @click="viewOrder(order)"
                class="flex-1 px-3 py-2 rounded-lg bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 text-sm font-medium"
              >
                {{ $t("admin.actions.view") }}
              </button>
              <button
                @click="deleteOrder(order.id)"
                class="flex-1 px-3 py-2 rounded-lg bg-red-50 dark:bg-red-900 text-red-700 dark:text-red-300 text-sm font-medium"
              >
                {{ $t("admin.actions.delete") }}
              </button>
            </div>
          </article>
        </div>

        <!-- Orders Table -->
        <div class="hidden md:block bg-white dark:bg-gray-800 rounded-lg shadow overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t("admin.table.id") }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t("admin.table.reference") }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t("admin.table.customer") }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t("admin.table.products") }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t("admin.table.total") }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t("admin.table.status") }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t("admin.table.actions") }}
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
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                >
                  {{ order.reference }}
                </td>
                <td class="px-6 py-4 text-sm">
                  <div class="text-gray-900 dark:text-white font-medium">
                    {{ order.customerName }}
                  </div>
                  <div class="text-gray-500 dark:text-gray-400">{{ order.email }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                  {{ formatItems(order.items) }}
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
                    {{ formatStatus(order.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                  <button
                    @click="viewOrder(order)"
                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                  >
                    {{ $t("admin.actions.view") }}
                  </button>
                  <button
                    @click="deleteOrder(order.id)"
                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                  >
                    {{ $t("admin.actions.delete") }}
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
            :placeholder="
              $t('admin.users.search_placeholder') +
              ' (napr. meno, email alebo #3 pre ID)'
            "
            class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <!-- Error Message -->
        <div
          v-if="usersError"
          class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-4"
        >
          <p class="text-red-800 dark:text-red-200 mb-3">
            <strong>{{ $t("admin.error_label") }}:</strong> {{ usersError }}
          </p>
          <button
            @click="fetchUsers"
            :disabled="usersLoading"
            class="px-4 py-2 btn-danger text-sm disabled:opacity-50"
          >
            {{ usersLoading ? $t("admin.loading") : $t("admin.retry") }}
          </button>
        </div>

        <!-- Empty State -->
        <div
          v-if="users.length === 0 && !usersError && !usersLoading"
          class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4"
        >
          <p class="text-yellow-800 dark:text-yellow-200 mb-3">
            <strong>{{ $t("admin.note_label") }}</strong>
            {{ $t("admin.users.empty_desc") }}
          </p>
          <button @click="fetchUsers" class="px-4 py-2 btn-warning text-sm">
            {{ $t("admin.retry") }}
          </button>
        </div>

        <!-- Users Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="user in filteredUsers"
            :key="user.id"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow relative"
          >
            <!-- User ID Badge in top-right corner -->
            <div
              class="absolute top-4 right-4 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs font-mono text-gray-600 dark:text-gray-400"
            >
              #{{ user.id }}
            </div>
            <div class="flex items-center mb-4">
              <div
                class="w-12 h-12 rounded-full overflow-hidden flex items-center justify-center"
              >
                <template v-if="user.avatar">
                  <img
                    :src="getAvatarUrl(user.avatar)"
                    :alt="user.name"
                    class="w-full h-full object-cover"
                  />
                </template>
                <template v-else>
                  <div
                    class="w-12 h-12 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg"
                  >
                    {{ getInitials(user.name) }}
                  </div>
                </template>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ user.name }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</p>
                <div class="mt-1 h-6">
                  <span
                    v-if="isElevatedRole(user.role)"
                    class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full"
                    :class="
                      user.role === 'owner'
                        ? 'bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200'
                        : 'bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200'
                    "
                  >
                    {{ getRoleLabel(user.role) }}
                  </span>
                </div>
              </div>
            </div>
            <div class="space-y-2 mb-4">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400"
                  >{{ $t("admin.users.orders_label") }}:</span
                >
                <span class="font-medium text-gray-900 dark:text-white">{{
                  user.orders
                }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400"
                  >{{ $t("admin.orders.total_amount_label") }}:</span
                >
                <span class="font-medium text-gray-900 dark:text-white"
                  >{{ user.totalSpent }} €</span
                >
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400"
                  >{{ $t("admin.users.registered_label") }}:</span
                >
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
                    ? $t('admin.messages.cannot_edit_owner')
                    : ''
                "
                :class="{
                  'opacity-50 cursor-not-allowed':
                    currentUser.role === 'admin' && user.role === 'owner',
                }"
              >
                {{ $t("admin.actions.edit") }}
              </button>
              <button
                @click="resetUserPassword(user.id)"
                class="flex-1 px-4 py-2 btn-warning text-sm rounded-lg"
                :disabled="currentUser.role === 'admin' && user.role === 'owner'"
                :title="
                  currentUser.role === 'admin' && user.role === 'owner'
                    ? $t('admin.users.cannot_reset_owner_password')
                    : ''
                "
                :class="{
                  'opacity-50 cursor-not-allowed':
                    currentUser.role === 'admin' && user.role === 'owner',
                }"
              >
                {{ $t("admin.actions.reset_password") }}
              </button>
              <button
                @click="deleteUser(user.id)"
                class="flex-1 px-4 py-2 btn-danger text-sm rounded-lg"
                :disabled="currentUser.role === 'admin' && user.role === 'owner'"
                :title="
                  currentUser.role === 'admin' && user.role === 'owner'
                    ? $t('admin.messages.cannot_delete_owner')
                    : ''
                "
                :class="{
                  'opacity-50 cursor-not-allowed':
                    currentUser.role === 'admin' && user.role === 'owner',
                }"
              >
                {{ $t("admin.actions.delete") }}
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
            :placeholder="$t('admin.products.search_placeholder')"
            class="flex-1 mr-4 px-4 py-2 bg-white dark:bg-gray-800 text-gray-800 dark:text-white rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
          <button
            @click="
              showAddProduct = true;
              resetProductForm();
            "
            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-medium hover:shadow-lg transition-all"
          >
            {{ "+ " + $t("admin.products.add_product") }}
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
                  {{ $t("admin.actions.edit") }}
                </button>
                <button
                  @click="deleteProduct(product.id)"
                  class="flex-1 px-4 py-2 btn-danger text-sm rounded-lg"
                >
                  {{ $t("admin.actions.delete") }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics -->
      <div v-if="activeTab === 'stats'" class="space-y-6">
        <!-- Loading Skeleton -->
        <div v-if="statsLoading" class="space-y-6">
          <!-- Stats Cards Skeleton -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
              v-for="i in 4"
              :key="`stat-skeleton-${i}`"
              class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 animate-pulse"
            >
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-24 mb-3"></div>
                  <div class="h-8 bg-gray-300 dark:bg-gray-700 rounded w-32 mb-3"></div>
                  <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-40"></div>
                </div>
                <div class="w-12 h-12 rounded-full bg-gray-300 dark:bg-gray-700"></div>
              </div>
            </div>
          </div>

          <!-- Recent Activity Skeleton -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 animate-pulse">
            <div class="h-6 bg-gray-300 dark:bg-gray-700 rounded w-32 mb-4"></div>
            <div class="space-y-4">
              <div
                v-for="i in 5"
                :key="`activity-skeleton-${i}`"
                class="flex items-center justify-between py-3"
              >
                <div class="flex items-center flex-1">
                  <div
                    class="w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-700 mr-4"
                  ></div>
                  <div class="flex-1">
                    <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-48 mb-2"></div>
                    <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-24"></div>
                  </div>
                </div>
                <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-20"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Stats Content -->
        <div v-else class="space-y-6">
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
                    {{ stat.trend > 0 ? "+" : "" }}{{ stat.trend }}%
                    {{ $t("admin.stats.vs_last_month") }}
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
              {{ $t("admin.recent_activity") }}
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
                      {{ formatActivityTitle(activity) }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                      {{ formatActivityTime(activity.created_at) }}
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
      </div>

      <!-- Notifications Management -->
      <div v-if="activeTab === 'notifications'" class="space-y-6">
        <!-- Grid Layout: Stats on left, Form on right -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Notification Statistics (Left Side) -->
          <div class="md:col-span-1 space-y-6">
            <!-- Total Notifications Card -->
            <div
              class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg shadow p-6 text-white"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-indigo-100 text-sm font-medium">
                    {{ $t("admin.notifications.statistics.total_count") }}
                  </p>
                  <p class="text-3xl font-bold mt-1">{{ notifications.length }}</p>
                </div>
                <div
                  class="bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full p-3"
                >
                  <svg class="w-8 h-8" fill="white" viewBox="0 0 24 24">
                    <path
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                    ></path>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Notifications by Type -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
              <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
                {{ $t("admin.notifications.by_type") }}
              </h4>
              <div class="space-y-3">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-indigo-500"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{
                      $t("admin.notifications.type_general")
                    }}</span>
                  </div>
                  <span class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ notifications.filter((n) => n.type === "general").length }}
                  </span>
                </div>
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{
                      $t("admin.notifications.type_promotion")
                    }}</span>
                  </div>
                  <span class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ notifications.filter((n) => n.type === "promotion").length }}
                  </span>
                </div>
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{
                      $t("admin.notifications.type_order")
                    }}</span>
                  </div>
                  <span class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ notifications.filter((n) => n.type === "order").length }}
                  </span>
                </div>
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{
                      $t("admin.notifications.type_payment")
                    }}</span>
                  </div>
                  <span class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ notifications.filter((n) => n.type === "payment").length }}
                  </span>
                </div>
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-gray-500"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{
                      $t("admin.notifications.type_system")
                    }}</span>
                  </div>
                  <span class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ notifications.filter((n) => n.type === "system").length }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
              <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
                {{ $t("admin.notifications.quick_actions") }}
              </h4>
              <div class="space-y-2">
                <button
                  @click="
                    notificationForm.type = 'promotion';
                    notificationForm.sendToAll = true;
                  "
                  class="w-full px-4 py-2 bg-purple-50 dark:bg-purple-900 text-purple-700 dark:text-purple-300 rounded-lg text-sm font-medium hover:bg-purple-100 dark:hover:bg-purple-800 transition"
                >
                  {{ $t("admin.notifications.quick_action_promotion") }}
                </button>
                <button
                  @click="
                    notificationForm.type = 'general';
                    notificationForm.sendToAll = true;
                  "
                  class="w-full px-4 py-2 bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-lg text-sm font-medium hover:bg-blue-100 dark:hover:bg-blue-800 transition"
                >
                  {{ $t("admin.notifications.quick_action_announcement") }}
                </button>
                <button
                  @click="sendSaleNotification"
                  class="w-full px-4 py-2 bg-red-50 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-lg text-sm font-medium hover:bg-red-100 dark:hover:bg-red-800 transition"
                >
                  {{ $t("admin.notifications.quick_action_sale") }}
                </button>
              </div>
            </div>
          </div>

          <!-- Create Notification Form (Right Side) -->
          <div class="md:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              {{ $t("admin.notifications.title") }}
            </h3>
            <form @submit.prevent="sendNotification" class="space-y-4">
              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                  {{ $t("admin.notifications.type_label") }}
                </label>
                <select
                  v-model="notificationForm.type"
                  class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                  <option value="general">
                    {{ $t("admin.notifications.type_general") }}
                  </option>
                  <option value="promotion">
                    {{ $t("admin.notifications.type_promotion") }}
                  </option>
                  <option value="order">
                    {{ $t("admin.notifications.type_order") }}
                  </option>
                  <option value="payment">
                    {{ $t("admin.notifications.type_payment") }}
                  </option>
                  <option value="system">
                    {{ $t("admin.notifications.type_system") }}
                  </option>
                </select>
              </div>

              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                  {{ $t("admin.notifications.title_label") }}
                </label>
                <input
                  v-model="notificationForm.title"
                  type="text"
                  :placeholder="$t('admin.notifications.title_placeholder')"
                  class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
              </div>

              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                  {{ $t("admin.notifications.message_label") }}
                </label>
                <textarea
                  v-model="notificationForm.message"
                  :placeholder="$t('admin.notifications.message_placeholder')"
                  rows="4"
                  class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                ></textarea>
              </div>

              <div class="flex items-center gap-4">
                <label class="flex items-center gap-2">
                  <input
                    v-model="notificationForm.sendToAll"
                    type="checkbox"
                    class="rounded"
                  />
                  <span class="text-sm text-gray-700 dark:text-gray-300">{{
                    $t("admin.notifications.send_all")
                  }}</span>
                </label>
              </div>

              <div
                v-if="!notificationForm.sendToAll"
                class="space-y-2 relative"
                data-notification-user-dropdown
              >
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                  {{ $t("admin.notifications.specific_user") }}
                </label>
                <input
                  v-model="userSearchQueryForNotification"
                  @focus="showUserDropdownForNotification = true"
                  type="text"
                  :placeholder="$t('admin.notifications.user_search_placeholder')"
                  class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />

                <!-- Selected User Display -->
                <div
                  v-if="selectedUserForNotification"
                  class="mt-2 p-3 bg-indigo-50 dark:bg-indigo-900 rounded-lg border border-indigo-200 dark:border-indigo-700 flex items-center justify-between"
                >
                  <div class="flex-1">
                    <div class="font-medium text-sm text-gray-900 dark:text-white">
                      {{ selectedUserForNotification.name }}
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                      #{{ selectedUserForNotification.id }} •
                      {{ selectedUserForNotification.email }}
                    </div>
                  </div>
                  <button
                    @click="
                      selectedUserForNotification = null;
                      userSearchQueryForNotification = '';
                      notificationForm.userId = null;
                    "
                    type="button"
                    class="ml-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300"
                  >
                    ✕
                  </button>
                </div>

                <!-- User Dropdown -->
                <div
                  v-if="
                    showUserDropdownForNotification &&
                    (userSearchQueryForNotification ||
                      filteredUsersForNotification.length > 0)
                  "
                  class="absolute top-17 left-0 right-0 z-10 max-h-64 overflow-y-auto custom-scrollbar bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg"
                >
                  <div
                    v-if="filteredUsersForNotification.length === 0"
                    class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400"
                  >
                    {{ $t("admin.notifications.no_users_found") }}
                  </div>
                  <div
                    v-for="user in filteredUsersForNotification"
                    :key="user.id"
                    @click="selectUserForNotification(user)"
                    :class="[
                      'px-4 py-3 cursor-pointer transition-colors border-b border-gray-100 dark:border-gray-600 last:border-0',
                      selectedUserForNotification?.id === user.id
                        ? 'bg-indigo-100 dark:bg-indigo-900'
                        : 'hover:bg-gray-50 dark:hover:bg-gray-600',
                    ]"
                  >
                    <div class="font-medium text-sm text-gray-900 dark:text-white">
                      #{{ user.id }} • {{ user.name }}
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                      {{ user.email }}
                    </div>
                  </div>
                </div>
              </div>

              <button
                type="submit"
                :disabled="
                  !notificationForm.title ||
                  !notificationForm.message ||
                  notificationsLoading
                "
                class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold disabled:opacity-50 transition"
              >
                {{
                  notificationsLoading
                    ? $t("admin.notifications.sending")
                    : $t("admin.notifications.send_button")
                }}
              </button>
            </form>
          </div>
        </div>

        <!-- Notifications List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            {{ $t("admin.notifications.sent_title") }}
          </h3>

          <!-- Search Bar -->
          <div class="mb-4">
            <input
              v-model="notificationSearch"
              type="text"
              :placeholder="$t('admin.notifications.search_placeholder')"
              class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <!-- Mobile Notifications Cards -->
          <div class="space-y-3 md:hidden">
            <article
              v-for="notification in filteredNotifications"
              :key="`mobile-notification-${notification.id}`"
              class="rounded-lg border border-gray-200 dark:border-gray-700 p-4"
            >
              <div class="flex items-start justify-between gap-3">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                  #{{ notification.id }}
                </p>
                <span
                  class="px-2 py-1 rounded text-xs font-medium"
                  :class="getNotificationTypeClass(notification.type)"
                >
                  {{ getNotificationTypeName(notification.type) }}
                </span>
              </div>

              <h4 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                {{ notification.title }}
              </h4>
              <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 line-clamp-3">
                {{ notification.message }}
              </p>

              <div class="mt-3 flex items-center justify-between gap-2 text-xs">
                <span class="text-gray-500 dark:text-gray-400">
                  {{ formatDate(notification.created_at) }}
                </span>
                <span
                  v-if="notification.isBroadcast"
                  class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200"
                >
                  {{ notification.broadcastCount }}
                  {{ $t("admin.notifications.table_headers.users") }}
                </span>
                <span v-else class="text-gray-600 dark:text-gray-400">
                  1 {{ $t("admin.notifications.table_headers.users") }}
                </span>
              </div>

              <button
                @click="deleteNotificationAdm(notification, $event)"
                class="mt-3 w-full px-3 py-2 rounded-lg bg-red-50 dark:bg-red-900 text-red-700 dark:text-red-300 text-sm font-medium"
              >
                {{ $t("admin.notifications.delete_button") }}
              </button>
            </article>

            <div
              v-if="filteredNotifications.length === 0"
              class="text-center py-8 text-gray-500 dark:text-gray-400"
            >
              {{ $t("admin.notifications.empty") }}
            </div>
          </div>

          <!-- Notifications Table -->
          <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th
                    class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300"
                  >
                    {{ $t("admin.notifications.table_headers.id") }}
                  </th>
                  <th
                    class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300"
                  >
                    {{ $t("admin.notifications.table_headers.type") }}
                  </th>
                  <th
                    class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300"
                  >
                    {{ $t("admin.notifications.table_headers.title") }}
                  </th>
                  <th
                    class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300"
                  >
                    {{ $t("admin.notifications.table_headers.message") }}
                  </th>
                  <th
                    class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300"
                  >
                    {{ $t("admin.notifications.table_headers.date") }}
                  </th>
                  <th
                    class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300"
                  >
                    {{ $t("admin.notifications.table_headers.users") }}
                  </th>
                  <th
                    class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300"
                  >
                    {{ $t("admin.notifications.table_headers.actions") }}
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr
                  v-for="notification in filteredNotifications"
                  :key="notification.id"
                  class="hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                >
                  <td class="px-4 py-3 text-gray-900 dark:text-white">
                    #{{ notification.id }}
                  </td>
                  <td class="px-4 py-3">
                    <span
                      class="px-2 py-1 rounded text-xs font-medium"
                      :class="getNotificationTypeClass(notification.type)"
                    >
                      {{ getNotificationTypeName(notification.type) }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">
                    {{ notification.title }}
                  </td>
                  <td class="px-4 py-3 text-gray-600 dark:text-gray-400 line-clamp-2">
                    {{ notification.message }}
                  </td>
                  <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                    {{ formatDate(notification.created_at) }}
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span
                      v-if="notification.isBroadcast"
                      class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full text-xs font-medium"
                    >
                      {{ notification.broadcastCount }}
                    </span>
                    <span v-else class="text-gray-600 dark:text-gray-400 text-xs">1</span>
                  </td>
                  <td class="px-4 py-3 space-x-2">
                    <button
                      @click="deleteNotificationAdm(notification, $event)"
                      class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-xs font-medium"
                    >
                      {{ $t("admin.notifications.delete_button") }}
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
            <div
              v-if="notifications.length === 0"
              class="text-center py-8 text-gray-500 dark:text-gray-400"
            >
              {{ $t("admin.notifications.empty") }}
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal for Order Details / Edit -->
    <div
      v-if="selectedOrder"
      class="fixed inset-0 bg-white/30 backdrop-blur-md flex items-center justify-center z-50 overflow-y-auto"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-4xl w-full mx-4 my-8 max-h-[90vh] overflow-y-auto custom-scrollbar"
        @click.stop
      >
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $t("admin.order.title", { id: selectedOrder.id }) }}
          </h2>
          <button
            @click="toggleOrderEditing"
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium"
          >
            {{ editingOrder ? $t("admin.actions.cancel") : $t("admin.actions.edit") }}
          </button>
        </div>

        <div
          v-if="orderError"
          class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded"
        >
          {{ orderError }}
        </div>

        <div v-if="!editingOrder" class="space-y-6">
          <!-- View Mode -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t("admin.table.customer") }}
              </p>
              <p class="text-lg font-medium text-gray-900 dark:text-white">
                {{ selectedOrder.customerName }}
              </p>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ selectedOrder.email }}
              </p>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                {{ $t("admin.orders.linked_account") }}
              </p>
              <p class="text-sm text-gray-700 dark:text-gray-300">
                {{ linkedUserDisplay(selectedOrder.linkedUser) }}
              </p>
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t("admin.orders.reference_label") }}
              </p>
              <p class="text-lg font-medium text-gray-900 dark:text-white">
                {{ selectedOrder.reference }}
              </p>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ selectedOrder.created_at }}
              </p>
            </div>
          </div>

          <div class="grid grid-cols-3 gap-4">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t("admin.orders.total_amount_label") }}
              </p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ selectedOrder.total }} €
              </p>
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t("admin.orders.status_label") }}
              </p>
              <span
                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                :class="getStatusClass(selectedOrder.status)"
              >
                {{ formatStatus(selectedOrder.status) }}
              </span>
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t("admin.orders.items_count_label") }}
              </p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ formatItems(selectedOrder.items) }}
              </p>
            </div>
          </div>

          <div v-if="selectedOrder.address" class="border-t pt-4">
            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">
              {{ $t("admin.orders.delivery_address") }}
            </p>
            <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
              <p>{{ selectedOrder.address.fullName }}</p>
              <p>{{ selectedOrder.address.street }}</p>
              <p>{{ selectedOrder.address.zip }} {{ selectedOrder.address.city }}</p>
              <p>{{ selectedOrder.address.country }}</p>
              <p class="pt-2">
                {{ $t("admin.orders.phone_label") }} {{ formatPhone(selectedOrder.address.phone) }}
              </p>
              <p>
                {{ $t("admin.orders.email_label") }} {{ selectedOrder.address.email }}
              </p>
            </div>
          </div>

          <div class="border-t pt-4">
            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">
              {{ $t("admin.orders.methods_section") }}
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <span class="text-xs text-gray-600 dark:text-gray-400">{{
                  $t("admin.orders.delivery_label")
                }}</span>
                <p class="text-sm text-gray-900 dark:text-white">
                  {{ selectedOrder.delivery_method }}
                </p>
              </div>
              <div>
                <span class="text-xs text-gray-600 dark:text-gray-400">{{
                  $t("admin.orders.payment_label")
                }}</span>
                <p class="text-sm text-gray-900 dark:text-white">
                  {{ selectedOrder.payment_method }}
                </p>
              </div>
            </div>
          </div>

          <div class="border-t pt-4">
            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3">
              {{ $t("admin.orders.items_title") }}
            </p>
            <div class="overflow-x-auto order-items-scroll">
              <table class="w-full min-w-[680px] text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-400">
                      {{ $t("admin.order.product") }}
                    </th>
                    <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-400">
                      {{ $t("admin.orders.options_label") || "Options" }}
                    </th>
                    <th class="px-4 py-2 text-center text-gray-600 dark:text-gray-400">
                      {{ $t("admin.order.quantity") }}
                    </th>
                    <th class="px-4 py-2 text-right text-gray-600 dark:text-gray-400">
                      {{ $t("admin.order.price") }}
                    </th>
                    <th class="px-4 py-2 text-right text-gray-600 dark:text-gray-400">
                      {{ $t("admin.order.total") }}
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y">
                  <tr v-for="item in selectedOrder.order_items" :key="item.id">
                    <td class="px-4 py-2 text-gray-900 dark:text-white">
                      {{ item.product_name }}
                    </td>
                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300 text-sm">
                      <div
                        v-if="
                          item.variant_options &&
                          Object.keys(item.variant_options).length > 0
                        "
                        class="space-y-1"
                      >
                        <div v-for="(value, key) in item.variant_options" :key="key">
                          <span class="font-medium capitalize">{{ key }}:</span>
                          {{ value }}
                        </div>
                      </div>
                      <span v-else class="text-gray-500 italic">{{
                        $t("admin.orders.no_variants") || "No options"
                      }}</span>
                    </td>
                    <td class="px-4 py-2 text-center text-gray-900 dark:text-white">
                      {{ item.quantity }}
                    </td>
                    <td class="px-4 py-2 text-right text-gray-900 dark:text-white">
                      {{ item.price.toFixed(2) }} €
                    </td>
                    <td
                      class="px-4 py-2 text-right text-gray-900 dark:text-white font-semibold"
                    >
                      {{ (item.quantity * item.price).toFixed(2) }} €
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Edit Mode -->
        <div v-else class="space-y-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                >{{ $t("admin.table.customer") }}</label
              >
              <input
                v-model="editingOrder.customerName"
                type="text"
                readonly
                class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-gray-600"
              />
            </div>
            <div>
              <label
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                >{{ $t("admin.orders.email_input_label") }}</label
              >
              <input
                v-model="editingOrder.address.email"
                type="email"
                class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-gray-600"
              />
            </div>
          </div>

          <div class="border-t pt-4">
            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3">
              {{ $t("admin.orders.linked_account_section") }}
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label
                  class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1"
                  >{{ $t("admin.orders.linked_account") }}</label
                >
                <select
                  v-model="editingOrder.user_id"
                  @change="syncCustomerFromLinkedAccount"
                  class="w-full px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-gray-600 text-sm"
                >
                  <option :value="null">
                    {{ $t("admin.orders.guest_order") }}
                  </option>
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    #{{ user.id }} - {{ user.name }} ({{ user.email }})
                  </option>
                </select>
              </div>
              <div class="text-xs text-gray-600 dark:text-gray-400 flex items-end">
                {{ $t("admin.orders.linked_account_hint") }}
              </div>
            </div>
          </div>

          <div class="grid grid-cols-3 gap-4">
            <div>
              <label
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                >{{ $t("admin.orders.delivery") }}</label
              >
              <select
                v-model="editingOrder.delivery_method"
                class="w-full px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-gray-600"
              >
                <option v-for="opt in deliveryOptions" :key="opt" :value="opt">
                  {{ opt }}
                </option>
              </select>
            </div>
            <div>
              <label
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                >{{ $t("admin.orders.status_label") }}</label
              >
              <select
                v-model="editingOrder.status"
                class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-gray-600"
              >
                <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
                  {{ opt.display }}
                </option>
              </select>
            </div>
            <div>
              <label
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                >{{ $t("admin.orders.payment") }}</label
              >
              <select
                v-model="editingOrder.payment_method"
                class="w-full px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-gray-600"
              >
                <option v-for="opt in paymentOptions" :key="opt" :value="opt">
                  {{ opt }}
                </option>
              </select>
            </div>
          </div>

          <div v-if="editingOrder.address" class="border-t pt-4">
            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3">
              {{ $t("admin.orders.delivery_address") }}
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label
                  class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1"
                  >{{ $t("admin.orders.address_name") }}</label
                >
                <input
                  v-model="editingOrder.address.fullName"
                  type="text"
                  class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-gray-600 text-sm"
                />
              </div>
              <div>
                <label
                  class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1"
                  >{{ $t("admin.orders.address_street") }}</label
                >
                <input
                  v-model="editingOrder.address.street"
                  type="text"
                  class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-gray-600 text-sm"
                />
              </div>
              <div>
                <label
                  class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1"
                  >{{ $t("admin.orders.address_zip") }}</label
                >
                <input
                  v-model="editingOrder.address.zip"
                  type="text"
                  class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-gray-600 text-sm"
                />
              </div>
              <div>
                <label
                  class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1"
                  >{{ $t("admin.orders.address_city") }}</label
                >
                <input
                  v-model="editingOrder.address.city"
                  type="text"
                  class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-gray-600 text-sm"
                />
              </div>
              <div>
                <label
                  class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1"
                  >{{ $t("admin.orders.address_country") }}</label
                >
                <select
                  v-model="editingOrder.address.country"
                  class="w-full px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-gray-600 text-sm"
                >
                  <option v-for="c in countryOptions" :key="c.code" :value="c.name">
                    {{ c.name }}
                  </option>
                </select>
              </div>
              <div>
                <label
                  class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1"
                  >{{ $t("admin.orders.address_phone") }}</label
                >
                <input
                  v-model="editingOrder.address.phone"
                  @input="onPhoneInput"
                  type="text"
                  placeholder="0912345678 alebo +421912345678"
                  class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-gray-600 text-sm"
                />
              </div>
            </div>
          </div>

          <div class="border-t pt-4">
            <div class="flex justify-between items-center mb-4">
              <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                {{ $t("admin.orders.items_title") }}
              </p>
              <button
                @click="showAddItemForm = !showAddItemForm"
                class="px-3 py-1 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
              >
                {{
                  showAddItemForm
                    ? $t("admin.orders.hide")
                    : "+ " + $t("admin.orders.add_item")
                }}
              </button>
            </div>

            <!-- Add Item Popup Modal -->
            <transition name="popup" v-if="showAddItemForm">
              <div
                class="mb-4 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700"
              >
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ $t("admin.orders.add_new_item") }}
                  </h3>
                  <button
                    @click="
                      showAddItemForm = false;
                      productSearchQuery = '';
                    "
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 text-xl"
                  >
                    ✕
                  </button>
                </div>

                <div class="space-y-4">
                  <div>
                    <label
                      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                      {{ $t("admin.order.product") }}
                    </label>
                    <input
                      v-model="productSearchQuery"
                      type="text"
                      :placeholder="$t('admin.products.search_placeholder')"
                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                    />
                    <div
                      class="mt-3 max-h-64 overflow-y-auto custom-scrollbar border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 divide-y"
                      v-if="productSearchQuery || newOrderItem.product_id"
                    >
                      <div
                        v-for="product in filteredProductsForModal"
                        :key="product.id"
                        @click="selectProduct(product)"
                        :class="[
                          'px-4 py-3 cursor-pointer transition-colors bg-white dark:bg-gray-700 flex gap-3 items-center',
                          newOrderItem.product_id == product.id
                            ? 'bg-blue-100 dark:bg-blue-900 border-l-4 border-blue-600'
                            : 'hover:bg-gray-100 dark:hover:bg-gray-600',
                        ]"
                      >
                        <img
                          v-if="product.image"
                          :src="product.image"
                          :alt="product.name || product.title"
                          class="w-12 h-12 object-cover rounded border border-gray-200 dark:border-gray-600"
                        />
                        <div
                          v-else
                          class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center"
                        >
                          <span class="text-xs text-gray-500 dark:text-gray-400"
                            >No img</span
                          >
                        </div>
                        <div class="flex-1 min-w-0">
                          <div class="font-medium text-gray-900 dark:text-white truncate">
                            {{ product.name || product.title }}
                          </div>
                          <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ parseFloat(product.price || 0).toFixed(2) }} €
                          </div>
                        </div>
                      </div>
                      <div
                        v-if="filteredProductsForModal.length === 0"
                        class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400"
                      >
                        {{ $t("admin.orders.no_products") }}
                      </div>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                      >
                        {{ $t("admin.orders.quantity_label") }}
                      </label>
                      <input
                        v-model.number="newOrderItem.quantity"
                        type="number"
                        min="1"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                      />
                    </div>

                    <!--
                    <div>
                      <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                      >
                        {{ $t("admin.orders.price_currency") }}
                      </label>
                      <input
                        v-model.number="newOrderItem.price"
                        type="number"
                        step="0.01"
                        min="0"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                      />
                    </div>
                    -->
                  </div>
                  <div class="flex gap-2 pt-2">
                    <button
                      @click="addItemToOrder"
                      class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors"
                    >
                      {{ $t("admin.orders.add_item") }}
                    </button>
                    <button
                      @click="
                        showAddItemForm = false;
                        productSearchQuery = '';
                      "
                      class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg font-medium transition-colors"
                    >
                      {{ $t("admin.orders.cancel_action") }}
                    </button>
                  </div>
                </div>
              </div>
            </transition>

            <div class="overflow-x-auto order-items-scroll">
              <table class="w-full min-w-[680px] text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-400">
                      {{ $t("admin.orders.product") }}
                    </th>
                    <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-400">
                      {{ $t("admin.orders.options_label") || "Options" }}
                    </th>
                    <th class="px-4 py-2 text-center text-gray-600 dark:text-gray-400">
                      {{ $t("admin.orders.quantity_label") }}
                    </th>
                    <th class="px-4 py-2 text-right text-gray-600 dark:text-gray-400">
                      {{ $t("admin.table.price") }}
                    </th>
                    <th class="px-4 py-2 text-right text-gray-600 dark:text-gray-400">
                      {{ $t("admin.table.actions") }}
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y">
                  <tr v-for="item in editingOrder.order_items" :key="item.id">
                    <td class="px-4 py-2 text-gray-900 dark:text-white">
                      {{ item.product_name }}
                    </td>
                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300 text-sm">
                      <div
                        v-if="
                          item.variant_options &&
                          Object.keys(item.variant_options).length > 0
                        "
                        class="space-y-1"
                      >
                        <div v-for="(value, key) in item.variant_options" :key="key">
                          <span class="font-medium capitalize">{{ key }}:</span>
                          {{ value }}
                        </div>
                        <button
                          @click="openEditVariant(item)"
                          class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium mt-1"
                        >
                          {{ $t("admin.actions.edit") }}
                        </button>
                      </div>
                      <span v-else class="text-gray-500 italic">{{
                        $t("admin.orders.no_variants") || "No options"
                      }}</span>
                    </td>
                    <td class="px-4 py-2 text-center">
                      <input
                        v-model.number="item.quantity"
                        type="number"
                        min="1"
                        class="w-16 px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded border border-gray-300 dark:border-gray-600 text-center"
                      />
                    </td>
                    <td class="px-4 py-2 text-right">
                      <input
                        v-model.number="item.price"
                        type="number"
                        step="1"
                        min="0"
                        class="w-24 px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded border border-gray-300 dark:border-gray-600 text-right"
                      />
                      €
                    </td>
                    <td class="px-4 py-2 text-right">
                      <button
                        @click="
                          editingOrder.order_items = editingOrder.order_items.filter(
                            (i) => i.id !== item.id
                          )
                        "
                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm hover:underline"
                      >
                        {{ $t("admin.actions.remove") }}
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="border-t pt-4">
            <p class="text-lg font-bold text-gray-900 dark:text-white">
              Celkom:
              {{
                editingOrder.order_items
                  .reduce((sum, item) => sum + item.quantity * item.price, 0)
                  .toFixed(2)
              }}
              €
            </p>
            <p
              v-if="variantChangesPending"
              class="mt-2 text-sm text-amber-700 dark:text-amber-300"
            >
              {{ $t("admin.orders.variant_changes_pending") }}
            </p>
          </div>

          <div class="flex flex-wrap gap-3">
            <button
              @click="saveOrderChanges"
              :disabled="orderSaving"
              class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium disabled:opacity-50"
            >
              {{
                orderSaving ? $t("admin.orders.saving") : $t("admin.orders.save_changes")
              }}
            </button>
            <button
              @click="cancelOrderEditing"
              class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-white rounded-lg hover:bg-gray-400 dark:hover:bg-gray-700 font-medium"
            >
              {{ $t("admin.orders.cancel_action") }}
            </button>
          </div>
        </div>

        <button
          @click="closeOrderModal"
          class="mt-6 w-full px-4 py-2 btn-primary text-white rounded-lg font-medium hover:shadow-lg transition-all"
        >
          {{ $t("admin.actions.close") }}
        </button>
      </div>
    </div>

    <!-- Modal for Edit Variant -->
    <div
      v-if="editingVariant"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
          {{ $t("admin.orders.edit_options_modal") || "Edit Options" }}
        </h3>

        <div
          v-if="
            editingVariant.variant_options &&
            Object.keys(editingVariant.variant_options).length > 0
          "
          class="space-y-4 mb-6"
        >
          <div v-for="(value, key) in editingVariantData" :key="key">
            <label
              class="block text-sm font-medium text-gray-900 dark:text-white mb-2 capitalize"
            >
              {{ $t(`admin.variants.${key}`) || key }}
            </label>
            <select
              v-model="editingVariantData[key]"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
            >
              <option value="" disabled>
                {{
                  $t("admin.variants.select", {
                    name: $t(`admin.variants.${key}`) || key,
                  }) || `Select ${key}`
                }}
              </option>
              <option
                v-for="option in getVariantOptions(key)"
                :key="option"
                :value="option"
              >
                {{ option }}
              </option>
            </select>
          </div>
        </div>

        <div class="flex flex-wrap gap-3">
          <button
            @click="closeEditVariant"
            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition"
          >
            {{ $t("admin.actions.cancel") || $t("common.cancel") || "Cancel" }}
          </button>
          <button
            @click="saveVariantChanges"
            class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition"
          >
            {{ $t("admin.actions.save") || $t("common.save") || "Save" }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modal for Edit User -->
    <div
      v-if="editingUser"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-2xl w-full mx-4 my-8 max-h-[90vh] overflow-y-auto custom-scrollbar"
        @click.stop
      >
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $t("admin.users.edit") }}
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
              {{ $t("profile.name_label") }}
            </label>
            <input
              v-model="editingUser.name"
              type="text"
              required
              :placeholder="$t('auth.register.name_placeholder')"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
            />
          </div>

          <!-- Email -->
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              {{ $t("profile.email_label") }}
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
              {{ $t("profile.phone_label") }}
            </label>
            <input
              v-model="editingUser.phone"
              @input="onEditUserPhoneInput"
              type="tel"
              placeholder="+421 123 456 789"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
            />
          </div>

          <!-- Avatar controls -->
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              {{ $t("admin.users.avatar_label") }}
            </label>
            <div class="flex items-center gap-4">
              <div
                class="w-14 h-14 rounded-full overflow-hidden flex items-center justify-center bg-gray-100 dark:bg-gray-700"
              >
                <template v-if="editingUser.avatar && !editingUser.remove_avatar">
                  <img
                    :src="getAvatarUrl(editingUser.avatar)"
                    :alt="editingUser.name"
                    class="w-full h-full object-cover"
                  />
                </template>
                <template v-else>
                  <div
                    class="w-14 h-14 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full flex items-center justify-center text-white font-bold"
                  >
                    {{ getInitials(editingUser.name) }}
                  </div>
                </template>
              </div>

              <button
                type="button"
                @click="toggleAvatarRemoval"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                :class="
                  editingUser.remove_avatar
                    ? 'bg-gray-200 dark:bg-gray-600 text-gray-900 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-500'
                    : 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 hover:bg-red-200 dark:hover:bg-red-800'
                "
              >
                {{
                  editingUser.remove_avatar
                    ? $t("admin.users.avatar_restore")
                    : $t("admin.users.avatar_remove")
                }}
              </button>
            </div>
            <p
              v-if="editingUser.remove_avatar"
              class="text-xs text-orange-700 dark:text-orange-300 mt-2"
            >
              {{ $t("admin.users.avatar_remove_pending") }}
            </p>
          </div>

          <!-- Two-factor toggle -->
          <div
            class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/30"
          >
            <div class="flex items-center justify-between gap-4">
              <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ $t("admin.users.two_factor_toggle") }}
                </p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                  {{
                    editingUser.two_factor_enabled
                      ? $t("admin.users.two_factor_enabled")
                      : $t("admin.users.two_factor_disabled")
                  }}
                </p>
              </div>

              <button
                type="button"
                role="switch"
                :aria-checked="editingUser.two_factor_enabled"
                @click="editingUser.two_factor_enabled = !editingUser.two_factor_enabled"
                class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors"
                :class="
                  editingUser.two_factor_enabled
                    ? 'bg-green-500'
                    : 'bg-gray-300 dark:bg-gray-600'
                "
              >
                <span
                  class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform"
                  :class="editingUser.two_factor_enabled ? 'translate-x-7' : 'translate-x-1'"
                ></span>
              </button>
            </div>
          </div>

          <!-- Role (only visible to owner) -->
          <div v-if="currentUser.role === 'owner'">
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              {{ $t("admin.users.role") }}
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
              {{ $t("admin.users.set_password") }}
            </label>
            <input
              v-model="editingUser.password"
              type="password"
              :placeholder="$t('admin.users.set_password')"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
            />
            <div class="flex flex-wrap gap-3 mt-3">
              <button
                type="button"
                @click="saveUserPassword"
                :disabled="editingSaving"
                class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50"
              >
                {{
                  editingSaving
                    ? $t("admin.orders.saving")
                    : $t("admin.users.set_password")
                }}
              </button>

              <button
                type="button"
                @click="generateRandomPassword"
                class="flex-1 px-4 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium transition-colors"
              >
                {{ $t("admin.users.generate_password") }}
              </button>
            </div>
          </div>

          <div v-else-if="currentUser.role === 'admin'">
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              {{ $t("admin.users.generate_password") }}
            </label>
            <div class="flex flex-wrap gap-3 mt-3">
              <button
                type="button"
                @click="generateRandomPassword"
                class="flex-1 px-4 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium transition-colors"
              >
                {{ $t("admin.users.generate_password") }}
              </button>
            </div>
          </div>

          <!-- Generated Password Display -->
          <div
            v-if="generatedPassword"
            class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-4 mt-3"
          >
            <p class="text-sm text-green-700 dark:text-green-200 font-medium mb-2">
              {{ $t("admin.users.password_generated") }}
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
          <div class="flex flex-wrap gap-3 mt-6">
            <button
              type="submit"
              :disabled="editingSaving"
              class="flex-1 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-medium hover:shadow-lg transition-all disabled:opacity-50"
            >
              {{
                editingSaving
                  ? $t("admin.orders.saving")
                  : $t("admin.orders.save_changes")
              }}
            </button>
            <button
              type="button"
              @click="editingUser = null"
              class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white rounded-lg font-medium hover:shadow-lg transition-all"
            >
              {{ $t("admin.orders.cancel_action") }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Confirmation Modal for Notification Deletion -->
    <div
      v-if="showDeleteNotificationConfirm && notificationToDelete"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click="showDeleteNotificationConfirm = false"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-md w-full mx-4"
        @click.stop
      >
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
          {{ $t("admin.notifications.delete_button") }}?
        </h2>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
          <span v-if="notificationToDelete.isBroadcast">
            {{
              $t("admin.notifications.delete_broadcast_confirm", {
                count: notificationToDelete.broadcastCount,
              })
            }}
          </span>
          <span v-else>
            {{ $t("admin.notifications.delete_single_confirm") }}
          </span>
        </p>
        <div class="flex flex-wrap gap-3">
          <button
            @click="confirmDeleteNotification"
            class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors"
          >
            {{ $t("admin.notifications.delete_button") }}
          </button>
          <button
            @click="showDeleteNotificationConfirm = false"
            class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white rounded-lg font-medium transition-colors"
          >
            {{ $t("admin.actions.cancel") }}
          </button>
        </div>
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
          {{ $t("admin.users.confirm_generate_password") }}
        </h2>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
          {{ $t("admin.confirm.generate_password") }}
        </p>
        <div class="flex flex-wrap gap-3">
          <button
            @click="confirmAndGeneratePassword"
            class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors"
          >
            {{ $t("admin.users.confirm_yes") }}
          </button>
          <button
            @click="showPasswordConfirmation = false"
            class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white rounded-lg font-medium transition-colors"
          >
            {{ $t("admin.orders.cancel_action") }}
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
          class="modal-content bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-8 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto custom-scrollbar"
          @click.stop
        >
          <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
              {{
                editingProduct
                  ? $t("admin.products.edit_product_title")
                  : $t("admin.products.add_product_title")
              }}
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
                {{ $t("admin.products.name") }}
              </label>
              <input
                v-model="currentProduct.name"
                type="text"
                :placeholder="$t('admin.products.product_name_placeholder')"
                required
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <!-- Brand -->
            <div>
              <label
                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
              >
                {{ $t("admin.products.brand") }}
              </label>
              <input
                v-model="currentProduct.brand"
                type="text"
                :placeholder="$t('admin.products.product_brand_placeholder')"
                required
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <!-- Category -->
            <div>
              <label
                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
              >
                {{ $t("admin.products.category") }}
              </label>
              <select
                v-model="currentProduct.category"
                required
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
                <option value="">{{ $t("admin.products.select_category") }}</option>
                <option v-for="cat in uniqueCategories" :key="cat" :value="cat">
                  {{ cat }}
                </option>
              </select>
            </div>

            <!-- Pricing Section -->
            <div class="border-t border-gray-300 dark:border-gray-600 pt-6 mt-6">
              <h3 class="text-base font-bold text-gray-900 dark:text-white mb-5">
                {{ $t("admin.products.prices_discounts") }}
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
                      {{ $t("admin.products.current_price") }}
                    </p>
                    <p class="text-lg font-bold text-blue-900 dark:text-blue-100">
                      {{ originalProduct.oldPrice }}€
                    </p>
                  </div>
                  <div>
                    <p class="text-blue-600 dark:text-blue-300 font-semibold">
                      {{ $t("common.discount") }}
                    </p>
                    <p class="text-lg font-bold text-blue-900 dark:text-blue-100">
                      {{ originalProduct.discount_value
                      }}{{ originalProduct.discount_type === "percent" ? "%" : "€" }}
                    </p>
                  </div>
                  <div>
                    <p class="text-blue-600 dark:text-blue-300 font-semibold">
                      {{ $t("admin.products.final_price") }}
                    </p>
                    <p class="text-lg font-bold text-blue-900 dark:text-blue-100">
                      {{ originalProduct.price }}€
                    </p>
                  </div>
                </div>
                <div v-else class="flex items-center justify-between">
                  <div>
                    <p class="text-blue-600 dark:text-blue-300 font-semibold mb-2">
                      {{ $t("admin.products.current_price") }}
                    </p>
                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                      {{ originalProduct.oldPrice }}€
                    </p>
                  </div>
                  <div
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 dark:bg-green-900 border border-green-300 dark:border-green-700 rounded-lg"
                  >
                    <span class="text-green-700 dark:text-green-200 font-semibold"
                      >✓ {{ $t("admin.products.no_discount") }}</span
                    >
                  </div>
                </div>
              </div>

              <!-- Base Price Input -->
              <div class="mb-5">
                <label
                  class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                >
                  {{ $t("admin.products.current_price") }} (€) *
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
                    {{ $t("admin.products.discount_type") }}
                  </label>
                  <select
                    v-model="currentProduct.discount_type"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                  >
                    <option value="percent">{{ $t("admin.products.percent") }}</option>
                    <option value="fixed">{{ $t("admin.products.fixed_amount") }}</option>
                  </select>
                </div>
                <div>
                  <label
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                  >
                    {{ $t("admin.products.discount_amount") }}
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
                    {{ $t("admin.products.final_price") }}
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
                {{ $t("admin.products.remove_discount") }}
              </button>
            </div>

            <!-- Stock -->
            <div>
              <label
                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
              >
                {{ $t("admin.products.stock") }} *
              </label>
              <input
                v-model.number="currentProduct.stock"
                type="number"
                min="0"
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
                {{ $t("admin.products.image_url") }}
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
                {{ $t("admin.products.description") }}
              </label>
              <textarea
                v-model="currentProduct.description"
                :placeholder="$t('admin.products.description_placeholder')"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                rows="4"
              ></textarea>
            </div>
            <!-- Specifications -->
            <div>
              <label
                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
              >
                {{ $t("admin.products.specs") }}
              </label>
              <textarea
                v-model="currentProduct.specs"
                :placeholder="$t('admin.products.specs_placeholder')"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                rows="5"
              ></textarea>
            </div>

            <!-- Variant Types Section -->
            <div class="border-t border-gray-300 dark:border-gray-600 pt-6">
              <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-semibold text-gray-800 dark:text-white">
                  {{ $t("admin.products.variants") || "Product Variants" }}
                </h4>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                  <input
                    v-model="productVariantsEnabled"
                    type="checkbox"
                    class="h-4 w-4 text-indigo-600 rounded border-gray-300 dark:border-gray-600"
                    @change="toggleProductVariants"
                  />
                  <span class="text-sm text-gray-700 dark:text-gray-300">
                    {{ $t("admin.products.use_variants") || "Use variants" }}
                  </span>
                </label>
              </div>

              <div
                v-if="!productVariantsEnabled"
                class="text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 rounded-lg p-4"
              >
                {{
                  $t("admin.products.enable_variants_help") ||
                  "Enable variants to add options like color, size, or storage."
                }}
              </div>

              <div v-else>

              <!-- Add Variant Type -->
              <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <label
                  class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                >
                  {{
                    $t("admin.products.add_variant_type") ||
                    "Add Variant Type (e.g., Color, Size)"
                  }}
                </label>
                <div class="flex gap-2">
                  <input
                    v-model="variantInputs.newType"
                    type="text"
                    :placeholder="
                      $t('admin.products.variant_type_placeholder') || 'e.g., Color'
                    "
                    class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-600 text-gray-900 dark:text-white"
                  />
                  <button
                    type="button"
                    @click="addVariantType"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                  >
                    {{ $t("admin.actions.add") || "Add" }}
                  </button>
                </div>
              </div>

              <!-- Variant Types List -->
              <div class="space-y-4">
                <div
                  v-for="(options, typeName) in currentProduct.variants"
                  :key="typeName"
                  class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg"
                >
                  <div class="flex justify-between items-center mb-3">
                    <h5 class="font-semibold text-gray-800 dark:text-white">
                      {{ typeName }}
                    </h5>
                    <button
                      type="button"
                      @click="removeVariantType(typeName)"
                      class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                    >
                      ✕ {{ $t("admin.actions.remove") || "Remove" }}
                    </button>
                  </div>

                  <!-- Options for this type -->
                  <div class="space-y-2 mb-3">
                    <div
                      v-for="option in options"
                      :key="option"
                      class="flex items-center gap-2"
                    >
                      <span
                        class="flex-1 px-3 py-2 text-white bg-white dark:bg-gray-600 rounded border border-gray-300 dark:border-gray-500"
                      >
                        {{ option }}
                      </span>
                      <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                          {{ $t("admin.products.price_modifier") || "Price +" }}
                        </span>
                        <input
                          v-model.number="
                            currentProduct.variant_pricing[typeName][option]
                          "
                          type="number"
                          step="0.1"
                          min="0"
                          class="w-20 px-2 py-1 border border-gray-300 dark:border-gray-500 rounded bg-white dark:bg-gray-600 text-gray-900 dark:text-white"
                        />
                      </div>
                      <button
                        type="button"
                        @click="removeVariantOption(typeName, option)"
                        class="text-red-600 hover:text-red-800 dark:text-red-400"
                      >
                        ✕
                      </button>
                    </div>
                  </div>

                  <!-- Add option for this type -->
                  <div class="flex gap-2">
                    <input
                      v-model="variantInputs[typeName]"
                      type="text"
                      :placeholder="`${
                        $t('admin.products.add_option') || 'Add option'
                      } (e.g., Red)`"
                      class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-600 text-gray-900 dark:text-white"
                    />
                    <button
                      type="button"
                      @click="addVariantOption(typeName)"
                      class="px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm"
                    >
                      {{ $t("admin.actions.add") || "Add" }}
                    </button>
                  </div>
                </div>
              </div>
              </div>
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
                    ? $t("admin.products.saving")
                    : editingProduct
                    ? $t("admin.orders.save_changes")
                    : $t("admin.products.add_product")
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
                {{ $t("admin.orders.cancel_action") }}
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
      editingOrder: null,
      editingVariant: null,
      editingVariantData: {},
      productVariants: {},
      showAddItemForm: false,
      productSearchQuery: "",
      newOrderItem: {
        product_id: "",
        quantity: 1,
        price: 0,
      },
      orderSaving: false,
      orderError: "",
      variantChangesPending: false,
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
      productVariantsEnabled: false,
      currentProduct: {
        name: "",
        brand: "",
        category: "",
        price: 0,
        oldPrice: null,
        stock: 0,
        image: "",
        description: "",
        specs: "",
        discount_type: "percent",
        discount_value: 0,
        variants: {},
        variant_pricing: {},
      },
      variantInputs: {},
      originalProduct: null,
      productSaving: false,
      currentUser: JSON.parse(localStorage.getItem("user") || "{}"),
      deliveryOptionsKeys: ["delivery_option_1", "delivery_option_3"],
      paymentOptionsKeys: [
        "payment_option_1",
        "payment_option_2",
        "payment_option_3",
        "payment_option_4",
      ],
      countryOptionsCodes: [
        { code: "SK", key: "slovakia" },
        { code: "CZ", key: "czechia" },
        { code: "PL", key: "poland" },
        { code: "HU", key: "hungary" },
        { code: "AT", key: "austria" },
        { code: "DE", key: "germany" },
      ],
      statusValueMap: {
        čakajúce: "status_pending",
        "Spracováva sa": "status_processing",
        "V preprave": "status_shipped",
        Doručené: "status_delivered",
        Zrušené: "status_cancelled",
      },

      allTabs: [
        { id: "stats" },
        { id: "orders" },
        { id: "users" },
        { id: "products" },
        { id: "notifications" },
      ],

      stats: [],
      recentActivity: [],
      activityRefreshTimer: null,
      statsLoading: true,

      orders: [],

      users: [],

      products: [],

      // Notifications
      notifications: [],
      notificationForm: {
        type: "general",
        title: "",
        message: "",
        sendToAll: false,
        userId: null,
      },
      notificationSearch: "",
      notificationsLoading: false,
      showDeleteNotificationConfirm: false,
      notificationToDelete: null,
      userSearchQueryForNotification: "",
      showUserDropdownForNotification: false,
      selectedUserForNotification: null,
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
    this.fetchStats();
    this.fetchUsers();
    this.fetchOrders();
    this.loadProducts();
    // Load notifications if method exists
    if (typeof this.fetchNotifications === "function") {
      this.fetchNotifications().catch((error) => {
        console.error("Error in fetchNotifications:", error);
      });
    }
    this.updateBodyScroll();

    // Add click-outside handler for notification user dropdown
    document.addEventListener("click", this.handleClickOutsideNotificationDropdown);

    // Listen for language changes and refetch notifications
    this.languageChangeHandler = () => {
      if (typeof this.fetchNotifications === "function") {
        this.fetchNotifications().catch((error) => {
          console.error("Error refetching notifications:", error);
        });
      }
    };
    window.addEventListener("language-changed", this.languageChangeHandler);
  },
  beforeUnmount() {
    document.removeEventListener("click", this.handleClickOutsideNotificationDropdown);
    if (this.languageChangeHandler) {
      window.removeEventListener("language-changed", this.languageChangeHandler);
    }
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
      return this.orders.filter((order) => {
        if (order.id && order.id.toString().includes(search)) return true;
        if (order.reference && order.reference.toLowerCase().includes(search))
          return true;
        if (order.customerName && order.customerName.toLowerCase().includes(search))
          return true;
        if (order.email && order.email.toLowerCase().includes(search)) return true;
        return false;
      });
    },
    filteredUsers() {
      if (!this.userSearch) return this.users;
      const search = this.userSearch.toLowerCase();

      // Check if search is by ID (starts with #)
      if (search.startsWith("#")) {
        const userId = search.substring(1).trim();
        if (userId) {
          return this.users.filter((user) => user.id.toString() === userId);
        }
      }

      // Regular search by name or email
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
    filteredUsersForNotification() {
      if (!this.userSearchQueryForNotification) return this.users;
      const search = this.userSearchQueryForNotification.toLowerCase();

      // Check if search is by ID (starts with #)
      if (search.startsWith("#")) {
        const userId = search.substring(1).trim();
        if (userId) {
          return this.users.filter((user) => user.id.toString() === userId);
        }
      }

      // Regular search by name, email, or ID
      return this.users.filter(
        (user) =>
          user.name.toLowerCase().includes(search) ||
          user.email.toLowerCase().includes(search) ||
          user.id.toString().includes(search)
      );
    },
    filteredProductsForModal() {
      if (!this.productSearchQuery) return this.products;
      const search = this.productSearchQuery.toLowerCase();
      return this.products.filter(
        (product) =>
          (product.name || product.title || "").toLowerCase().includes(search) ||
          (product.brand || "").toLowerCase().includes(search) ||
          (product.category || "").toLowerCase().includes(search)
      );
    },
    deliveryOptions() {
      return this.deliveryOptionsKeys.map((key) => this.$t(`admin.orders.${key}`));
    },
    paymentOptions() {
      return this.paymentOptionsKeys.map((key) => this.$t(`admin.orders.${key}`));
    },
    countryOptions() {
      return this.countryOptionsCodes.map((item) => ({
        code: item.code,
        name: this.$t(`admin.countries.${item.key}`),
      }));
    },
    statusOptions() {
      return Object.keys(this.statusValueMap).map((value) => ({
        value,
        display: this.$t(`admin.orders.${this.statusValueMap[value]}`),
      }));
    },
    filteredNotifications() {
      let filtered = this.notifications;

      // Apply search filter
      if (this.notificationSearch) {
        const search = this.notificationSearch.toLowerCase();
        filtered = filtered.filter((notification) => {
          if (notification.title && notification.title.toLowerCase().includes(search))
            return true;
          if (notification.message && notification.message.toLowerCase().includes(search))
            return true;
          if (notification.type && notification.type.toLowerCase().includes(search))
            return true;
          return false;
        });
      }

      // Group broadcast notifications (same title, type, and created at same minute)
      const grouped = [];
      const seenGroups = new Set();

      filtered.forEach((notif) => {
        const createdMinute = new Date(notif.created_at).toISOString().slice(0, 16);
        const groupKey = `${notif.title}|${notif.type}|${createdMinute}`;

        if (!seenGroups.has(groupKey)) {
          const groupNotifications = filtered.filter((n) => {
            const nMinute = new Date(n.created_at).toISOString().slice(0, 16);
            return (
              n.title === notif.title &&
              n.type === notif.type &&
              nMinute === createdMinute
            );
          });

          // If multiple notifications with same title/type/time, it's likely a broadcast
          if (groupNotifications.length > 1) {
            grouped.push({
              ...notif,
              isBroadcast: true,
              broadcastCount: groupNotifications.length,
              broadcastIds: groupNotifications.map((n) => n.id),
            });
            seenGroups.add(groupKey);
          } else {
            grouped.push(notif);
          }
        }
      });

      return grouped;
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
    // cleanup activity refresh timer
    if (this.activityRefreshTimer) {
      clearInterval(this.activityRefreshTimer);
    }
  },

  methods: {
    selectMobileTab(tabId) {
      this.activeTab = tabId;
      const mobileTabMenu = this.$refs.mobileTabMenu;
      if (mobileTabMenu && mobileTabMenu.open) {
        mobileTabMenu.open = false;
      }
    },

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
    formatActivityTime(dateString) {
      if (!dateString) return this.$t("admin.activity.just_now");

      const activityDate = new Date(dateString);
      const now = new Date();
      const diffSeconds = Math.floor((now - activityDate) / 1000);

      if (diffSeconds < 60) {
        return this.$t("admin.activity.just_now");
      } else if (diffSeconds < 3600) {
        const minutes = Math.floor(diffSeconds / 60);
        return this.$t("admin.activity.minutes_ago", { count: minutes });
      } else if (diffSeconds < 86400) {
        const hours = Math.floor(diffSeconds / 3600);
        return this.$t("admin.activity.hours_ago", { count: hours });
      } else if (diffSeconds < 604800) {
        const days = Math.floor(diffSeconds / 86400);
        return this.$t("admin.activity.days_ago", { count: days });
      } else {
        return activityDate.toLocaleDateString(
          this.$i18n.locale === "sk" ? "sk-SK" : "en-US"
        );
      }
    },
    formatActivityTitle(activity) {
      if (activity.type === "order") {
        // Extract name after "od " (Slovak: "од ") or any language variant
        // Activity title format: "Nová objednávka od [name]"
        const match = activity.title.match(/(?:od|от)\s+(.+)$/i);
        const name = match ? match[1] : activity.title;
        return this.$t("admin.activity.new_order") + " " + name;
      } else if (activity.type === "registration") {
        // Extract name after ": "
        // Activity title format: "Nová registrácia: [name]"
        const match = activity.title.match(/:\s*(.+)$/);
        const name = match ? match[1] : activity.title;
        return this.$t("admin.activity.new_registration") + ": " + name;
      }
      return activity.title;
    },
    getStatusClass(status) {
      const classes = {
        Doručené: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300",
        "V preprave": "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300",
        "Spracováva sa": "bg-orange-600 text-white dark:bg-orange-500 dark:text-white",
        Čakajúce: "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300",
        Pending: "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300",
        Zrušené: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300",
      };
      return (
        classes[status] || "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300"
      );
    },
    formatStatus(status) {
      if (!status) return "";
      const key = this.statusValueMap[status];
      if (key) {
        return this.$t(`admin.orders.${key}`);
      }
      try {
        return status.charAt(0).toUpperCase() + status.slice(1);
      } catch (e) {
        return status;
      }
    },
    onPhoneInput(e) {
      if (!this.editingOrder || !this.editingOrder.address) return;
      const raw = e.target.value || "";
      this.editingOrder.address.phone = this.formatPhone(raw);
    },
    formatPhone(value) {
      if (!value) return "";
      let digits = String(value).replace(/\D/g, "");

      // Normalize common variants: 00421..., 421..., 09...
      if (digits.startsWith("00")) {
        digits = digits.slice(2);
      }
      if (digits.startsWith("421")) {
        digits = digits.slice(3);
      } else if (digits.startsWith("0")) {
        digits = digits.slice(1);
      }

      if (!digits) return "";

      // Keep only local 9 digits to prevent accidental long numbers.
      const local = digits.slice(0, 9);
      const grouped = local.replace(/(\d{3})(?=\d)/g, "$1 ");

      return `+421 ${grouped}`.trim();
    },
    formatItems(count) {
      const n = Number(count) || 0;
      if (n === 1) return "1 " + this.$t("admin.orders.items_one");
      if (n >= 2 && n <= 4) return n + " " + this.$t("admin.orders.items_few");
      return n + " " + this.$t("admin.orders.items_many");
    },
    linkedUserDisplay(linkedUser) {
      if (!linkedUser) {
        return this.$t("admin.orders.guest_order");
      }

      const id = linkedUser.id ?? "?";
      const name = linkedUser.name ?? "";
      const email = linkedUser.email ?? "";

      return `#${id} - ${name} (${email})`;
    },
    syncCustomerFromLinkedAccount() {
      if (!this.editingOrder) {
        return;
      }

      const parsedUserId = Number(this.editingOrder.user_id);
      const resolvedUserId =
        this.editingOrder.user_id === null || this.editingOrder.user_id === ""
          ? null
          : Number.isFinite(parsedUserId)
          ? parsedUserId
          : null;

      this.editingOrder.user_id = resolvedUserId;

      if (resolvedUserId === null) {
        this.editingOrder.customerName =
          this.editingOrder.address?.fullName || this.$t("admin.orders.guest_order");
        return;
      }

      const linkedUser = this.users.find((user) => Number(user.id) === resolvedUserId);
      if (linkedUser) {
        this.editingOrder.customerName = linkedUser.name || "";
      }
    },
    viewOrder(order) {
      this.selectedOrder = order;
      this.editingOrder = null;
      this.orderError = "";
      this.variantChangesPending = false;
    },
    toggleOrderEditing() {
      if (this.editingOrder) {
        this.cancelOrderEditing();
        return;
      }

      const draftOrder = JSON.parse(JSON.stringify(this.selectedOrder));

      if (!draftOrder.address || typeof draftOrder.address !== "object") {
        draftOrder.address = {};
      }

      draftOrder.address.fullName =
        draftOrder.address.fullName || draftOrder.customerName || "";
      draftOrder.address.email = draftOrder.address.email || draftOrder.email || "";
      draftOrder.address.phone = this.formatPhone(draftOrder.address.phone || "");
      draftOrder.address.street = draftOrder.address.street || "";
      draftOrder.address.city = draftOrder.address.city || "";
      draftOrder.address.zip = draftOrder.address.zip || "";
      draftOrder.address.country = draftOrder.address.country || "";

      if (typeof draftOrder.user_id === "undefined") {
        draftOrder.user_id = draftOrder.linkedUser?.id ?? null;
      }

      if (draftOrder.user_id === null || draftOrder.user_id === "") {
        draftOrder.user_id = null;
      } else {
        const parsedUserId = Number(draftOrder.user_id);
        draftOrder.user_id = Number.isFinite(parsedUserId) ? parsedUserId : null;
      }

      this.editingOrder = draftOrder;
      this.syncCustomerFromLinkedAccount();
      this.variantChangesPending = false;
      this.orderError = "";
    },
    cancelOrderEditing() {
      this.editingOrder = null;
      this.variantChangesPending = false;
      this.orderError = "";
    },
    closeOrderModal() {
      this.selectedOrder = null;
      this.editingOrder = null;
      this.variantChangesPending = false;
      this.orderError = "";
    },
    saveOrderChanges() {
      this.orderSaving = true;
      this.orderError = "";

      const normalizedAddress = {
        fullName: this.editingOrder.address?.fullName || "",
        email: this.editingOrder.address?.email || "",
        phone: this.formatPhone(this.editingOrder.address?.phone || ""),
        street: this.editingOrder.address?.street || "",
        city: this.editingOrder.address?.city || "",
        zip: this.editingOrder.address?.zip || "",
        country: this.editingOrder.address?.country || "",
      };

      const parsedUserId = Number(this.editingOrder.user_id);
      const resolvedUserId =
        this.editingOrder.user_id === null || this.editingOrder.user_id === ""
          ? null
          : Number.isFinite(parsedUserId)
          ? parsedUserId
          : null;

      const updateData = {
        status: this.editingOrder.status,
        payment_method: this.editingOrder.payment_method,
        delivery_method: this.editingOrder.delivery_method,
        address: normalizedAddress,
        user_id: resolvedUserId,
        items: this.editingOrder.order_items.map((item) => {
          const itemData = {
            product_id: item.product_id,
            quantity: item.quantity,
            price: item.price,
          };
          // Include variant_options if present
          if (item.variant_options && Object.keys(item.variant_options).length > 0) {
            itemData.variant_options = item.variant_options;
          }
          // Only include id if it's an existing item (not a temporary one)
          if (item.id && typeof item.id === "number" && item.id < 1000000000) {
            itemData.id = item.id;
          }
          return itemData;
        }),
      };

      api
        .put(`api/admin/orders/${this.editingOrder.id}`, updateData)
        .then((response) => {
          // Update the selectedOrder and orders list
          const updatedOrder = response.data.order;
          const index = this.orders.findIndex((o) => o.id === updatedOrder.id);
          if (index !== -1) {
            this.orders[index] = updatedOrder;
          }
          this.selectedOrder = updatedOrder;
          this.editingOrder = null;
          this.variantChangesPending = false;
          alert(this.$t("admin.messages.order_updated"));
        })
        .catch((error) => {
          console.error("Error:", error);
          this.orderError =
            error.response?.data?.message || this.$t("admin.messages.order_save_error");
        })
        .finally(() => {
          this.orderSaving = false;
        });
    },
    openEditVariant(item) {
      this.editingVariant = item;
      this.editingVariantData = JSON.parse(JSON.stringify(item.variant_options || {}));

      // Load product variants for dropdown options
      if (item.product_id && this.editingOrder) {
        const product = this.products.find((p) => p.id === item.product_id);
        if (product && product.variants) {
          let variants = product.variants;
          if (typeof variants === "string") {
            try {
              variants = JSON.parse(variants);
            } catch (e) {
              variants = {};
            }
          }
          this.productVariants = variants || {};
        }
      }
    },
    closeEditVariant() {
      this.editingVariant = null;
      this.editingVariantData = {};
      this.productVariants = {};
    },
    getVariantOptions(variantKey) {
      const options = this.productVariants[variantKey];
      if (Array.isArray(options)) {
        return options;
      }
      return [];
    },
    saveVariantChanges() {
      if (!this.editingVariant || !this.editingOrder) {
        return;
      }

      this.editingVariant.variant_options = JSON.parse(
        JSON.stringify(this.editingVariantData)
      );
      this.variantChangesPending = true;
      this.closeEditVariant();
    },
    selectProduct(product) {
      this.newOrderItem.product_id = product.id;
      this.newOrderItem.price = parseFloat(product.price || 0);
    },
    addItemToOrder() {
      // Validate
      if (!this.newOrderItem.product_id) {
        alert(this.$t("admin.messages.select_product"));
        return;
      }
      if (this.newOrderItem.quantity < 1) {
        alert(this.$t("admin.messages.min_quantity"));
        return;
      }

      // Find product name
      const product = this.products.find((p) => p.id === this.newOrderItem.product_id);
      const productName = product?.name || product?.title || "Unknown";

      // Check if product already exists in order
      const existingItem = this.editingOrder.order_items.find(
        (item) => item.product_id === this.newOrderItem.product_id
      );

      if (existingItem) {
        // Increase quantity of existing item
        existingItem.quantity += this.newOrderItem.quantity;
      } else {
        // Create new item object
        const newItem = {
          id: Date.now(), // Temporary ID for new items
          product_id: this.newOrderItem.product_id,
          product_name: productName,
          quantity: this.newOrderItem.quantity,
          price: this.newOrderItem.price || parseFloat(product?.price || 0),
        };

        // Add to order items
        this.editingOrder.order_items.push(newItem);
      }

      // Reset form and close modal
      this.newOrderItem = { product_id: "", quantity: 1, price: 0 };
      this.productSearchQuery = "";
      this.showAddItemForm = false;
    },
    async deleteOrder(id) {
      if (await window.appConfirm(this.$t("admin.confirm.delete_order"))) {
        api
          .delete(`api/admin/orders/${id}`)
          .then(() => {
            this.orders = this.orders.filter((order) => order.id !== id);
            alert(this.$t("admin.messages.order_deleted"));
          })
          .catch((error) => {
            console.error("Error:", error);
            alert(this.$t("admin.messages.delete_order_failed"));
          });
      }
    },
    async deleteUser(id) {
      const target = this.users.find((u) => u.id === id);
      if (this.currentUser?.role === "admin" && target?.role === "owner") {
        alert(this.$t("admin.messages.cannot_delete_owner"));
        return;
      }

      if (await window.appConfirm(this.$t("admin.confirm.delete_user"))) {
        api
          .delete(`api/admin/users/${id}`)
          .then(() => {
            this.users = this.users.filter((user) => user.id !== id);
            alert(this.$t("admin.messages.user_deleted"));
          })
          .catch((error) => {
            console.error("Error:", error);
            alert(this.$t("admin.messages.delete_user_failed"));
          });
      }
    },
    async resetUserPassword(id) {
      const target = this.users.find((u) => u.id === id);
      if (this.currentUser?.role === "admin" && target?.role === "owner") {
        alert(this.$t("admin.messages.cannot_reset_owner"));
        return;
      }

      if (await window.appConfirm(this.$t("admin.confirm.reset_user_password"))) {
        api
          .post(`api/admin/users/${id}/reset-password`, { send_notification: false })
          .then((response) => {
            alert(response.data.message || this.$t("admin.messages.reset_sent"));
          })
          .catch((error) => {
            console.error("Error:", error);
            alert(this.$t("admin.messages.reset_failed"));
          });
      }
    },
    editUser(user) {
      // Prevent admins from editing owner accounts
      if (this.currentUser?.role === "admin" && user?.role === "owner") {
        alert(this.$t("admin.messages.cannot_edit_owner"));
        return;
      }

      this.editingUser = {
        id: user.id,
        name: user.name,
        email: user.email,
        phone: this.formatPhoneNumber(user.phone) || "",
        role: user.role || "user",
        avatar: user.avatar || null,
        original_avatar: user.avatar || null,
        remove_avatar: false,
        two_factor_enabled: !!user.two_factor_enabled,
      };
      this.editError = "";
      this.generatedPassword = "";
      this.showGeneratedPassword = false;
    },
    toggleAvatarRemoval() {
      if (!this.editingUser) return;

      this.editingUser.remove_avatar = !this.editingUser.remove_avatar;
      this.editingUser.avatar = this.editingUser.remove_avatar
        ? null
        : this.editingUser.original_avatar || null;
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
        this.editError =
          this.$t("admin.messages.password_set_error") + ": " + JSON.stringify(errorMsg);
      }
    },
    async saveUserPassword() {
      if (!this.editingUser || !this.editingUser.id) return;
      if (!this.editingUser.password) {
        this.editError = this.$t("admin.messages.enter_new_password");
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
        alert(this.$t("admin.messages.password_set"));
        this.editingUser.password = "";
        this.editingUser = null;
        this.showGeneratedPassword = false;
        this.generatedPassword = "";
      } catch (error) {
        console.error("Error setting password:", error);
        const errorMsg =
          error.response?.data?.message || error.response?.data?.errors || error.message;
        this.editError =
          this.$t("admin.messages.password_set_error") + ": " + JSON.stringify(errorMsg);
      } finally {
        this.editingSaving = false;
      }
    },
    copyToClipboard() {
      navigator.clipboard.writeText(this.generatedPassword);
    },
    onEditUserPhoneInput() {
      if (!this.editingUser) return;
      this.editingUser.phone = this.formatPhoneNumber(this.editingUser.phone);
    },
    formatPhoneNumber(phone) {
      if (!phone) return "";

      const raw = String(phone).trim();
      let digits = raw.replace(/\D/g, "");

      // Normalize common Slovak variants: 00421..., 421..., 09...
      if (digits.startsWith("00421")) {
        digits = digits.slice(5);
      } else if (digits.startsWith("421")) {
        digits = digits.slice(3);
      } else if (digits.startsWith("0")) {
        digits = digits.slice(1);
      }

      if (!digits) return "";

      // Keep only local 9 digits for display.
      if (digits.length > 9) {
        digits = digits.slice(-9);
      }

      const local = digits.slice(0, 9);
      const grouped = local.replace(/(\d{3})(?=\d)/g, "$1 ");

      return `+421 ${grouped}`.trim();
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
        this.editError = this.$t("admin.messages.cannot_remove_owner_privilege");
        this.editingSaving = false;
        return;
      }

      const data = {
        name: this.editingUser.name,
        email: this.editingUser.email,
        phone: this.editingUser.phone,
        two_factor_enabled: !!this.editingUser.two_factor_enabled,
      };

      if (this.editingUser.remove_avatar) {
        data.remove_avatar = true;
      }

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
            this.users[index].two_factor_enabled = !!this.editingUser.two_factor_enabled;
            if (this.editingUser.remove_avatar) {
              this.users[index].avatar = null;
            }
            if (data.role) this.users[index].role = data.role;
          }
          this.editingUser = null;
          this.showGeneratedPassword = false;
          alert(this.$t("admin.messages.changes_saved"));
        })
        .catch((error) => {
          console.error("Error:", error);
          this.editError =
            error.response?.data?.message || this.$t("admin.messages.user_save_error");
        })
        .finally(() => {
          this.editingSaving = false;
        });
    },
    fetchStats() {
      this.statsLoading = true;
      api
        .get("api/admin/stats")
        .then((response) => {
          console.log("Stats fetched:", response.data);
          const data = response.data;

          // Build stats array with real data
          this.stats = [
            {
              label: this.$t("admin.stats.total_revenue"),
              value: data.totalRevenue.toLocaleString("sk-SK") + " €",
              trend: data.revenueTrend,
              icon: "💰",
              color: "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
            },
            {
              label: this.$t("admin.stats.total_orders"),
              value: data.totalOrders.toString(),
              trend: data.ordersTrend,
              icon: "📦",
              color: "linear-gradient(135deg, #f093fb 0%, #f5576c 100%)",
            },
            {
              label: this.$t("admin.stats.total_users"),
              value: data.totalUsers.toString(),
              trend: data.usersTrend,
              icon: "👥",
              color: "linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)",
            },
            {
              label: this.$t("admin.stats.avg_order_value"),
              value: data.avgOrderValue.toLocaleString("sk-SK") + " €",
              trend: data.avgOrderTrend,
              icon: "📊",
              color: "linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)",
            },
          ];

          // Update recent activity
          if (data.recentActivity && Array.isArray(data.recentActivity)) {
            this.recentActivity = data.recentActivity;

            // Set up a timer to refresh activity times every minute
            if (!this.activityRefreshTimer) {
              this.activityRefreshTimer = setInterval(() => {
                // Force Vue to re-render by triggering reactivity
                this.$forceUpdate();
              }, 60000); // Refresh every 60 seconds
            }
          }

          this.statsLoading = false;
        })
        .catch((error) => {
          console.error("Error fetching stats:", error);
          this.statsLoading = false;
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
            this.usersError = `${this.$t("admin.error_label")} ${
              error.response.status
            }: ${
              error.response.data?.message ||
              this.$t("admin.messages.users_loading_error")
            }`;
          } else {
            this.usersError =
              error.message || this.$t("admin.messages.users_loading_error");
          }
        })
        .finally(() => {
          this.usersLoading = false;
        });
    },
    fetchOrders() {
      api
        .get("api/admin/orders")
        .then((response) => {
          console.log("Orders fetched:", response.data);
          this.orders = response.data;
        })
        .catch((error) => {
          console.error("Error fetching orders:", error);
          alert(this.$t("admin.messages.orders_loading_error"));
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
      // Parse variants if it's a JSON string
      let variants = product.variants;
      if (typeof variants === "string") {
        try {
          variants = JSON.parse(variants);
        } catch (e) {
          variants = null;
        }
      }

      // Parse variant_pricing if it's a JSON string
      let variant_pricing = product.variant_pricing;
      if (typeof variant_pricing === "string") {
        try {
          variant_pricing = JSON.parse(variant_pricing);
        } catch (e) {
          variant_pricing = null;
        }
      }

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
        discount_value: product.discount_value ? parseFloat(product.discount_value) : 0,
        variants: variants || null,
        variant_pricing: variant_pricing || null,
      };

      // If discount_value is 0 or null but oldPrice differs from price, calculate discount from price difference
      if (
        (!productData.discount_value || productData.discount_value === 0) &&
        productData.oldPrice &&
        productData.price &&
        productData.oldPrice !== productData.price
      ) {
        const priceDiff = productData.oldPrice - productData.price;
        productData.discount_value = parseFloat(
          ((priceDiff / productData.oldPrice) * 100).toFixed(2)
        );
        productData.discount_type = "percent";
      }

      // Store original state for the blue box (won't change as user edits)
      this.originalProduct = JSON.parse(JSON.stringify(productData));

      // Set editable form data
      this.currentProduct = productData;
      this.productVariantsEnabled =
        !!this.currentProduct.variants &&
        Object.keys(this.currentProduct.variants).length > 0;

      // Initialize variantInputs for each variant type
      this.variantInputs = {};
      if (this.currentProduct.variants) {
        Object.keys(this.currentProduct.variants).forEach((type) => {
          this.variantInputs[type] = "";
        });
      }

      // Ensure variant_pricing has all necessary structure
      if (this.currentProduct.variants && this.currentProduct.variant_pricing) {
        Object.keys(this.currentProduct.variants).forEach((type) => {
          if (!this.currentProduct.variant_pricing[type]) {
            this.currentProduct.variant_pricing[type] = {};
          }
          this.currentProduct.variants[type].forEach((option) => {
            if (!this.currentProduct.variant_pricing[type][option]) {
              this.currentProduct.variant_pricing[type][option] = 0;
            }
          });
        });
      }

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

        const hasVariants =
          this.productVariantsEnabled &&
          this.currentProduct.variants &&
          Object.keys(this.currentProduct.variants).length > 0;

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
          specs: this.currentProduct.specs || "",
          discount_type: finalDiscountValue > 0 ? finalDiscountType : null,
          discount_value: finalDiscountValue > 0 ? finalDiscountValue : null,
          variants: hasVariants
            ? JSON.stringify(this.currentProduct.variants)
            : null,
          variant_pricing: hasVariants
            ? JSON.stringify(this.currentProduct.variant_pricing || {})
            : null,
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
          alert(this.$t("admin.messages.product_updated"));
        } else {
          // Create new product
          const response = await api.post("api/admin/products", payload);
          this.products.push(response.data.product);
          alert(this.$t("admin.messages.product_created"));
        }
        this.editingProduct = null;
        this.showAddProduct = false;
        this.currentProduct = {
          name: "",
          brand: "",
          category: "",
          price: 0,
          oldPrice: null,
          stock: 0,
          image: "",
          description: "",
          specs: "",
          discount_type: "percent",
          discount_value: 0,
          variants: null,
          variant_pricing: null,
        };
        this.productVariantsEnabled = false;
      } catch (error) {
        console.error("Error saving product:", error);
        alert(this.$t("admin.messages.product_save_error"));
      } finally {
        this.productSaving = false;
      }
    },
    addVariantType() {
      if (!this.productVariantsEnabled) {
        this.productVariantsEnabled = true;
      }
      if (!this.currentProduct.variants) {
        this.currentProduct.variants = {};
      }
      if (!this.currentProduct.variant_pricing) {
        this.currentProduct.variant_pricing = {};
      }
      const typeName = this.variantInputs.newType?.trim();
      if (!typeName) {
        alert(this.$t("admin.messages.variant_type_required"));
        return;
      }
      if (this.currentProduct.variants[typeName]) {
        alert(this.$t("admin.messages.variant_type_exists"));
        return;
      }
      this.currentProduct.variants[typeName] = [];
      this.currentProduct.variant_pricing[typeName] = {};
      this.variantInputs.newType = "";
    },
    toggleProductVariants() {
      if (this.productVariantsEnabled) {
        if (!this.currentProduct.variants) {
          this.currentProduct.variants = {};
        }
        if (!this.currentProduct.variant_pricing) {
          this.currentProduct.variant_pricing = {};
        }
        return;
      }

      this.currentProduct.variants = null;
      this.currentProduct.variant_pricing = null;
      this.variantInputs = {};
    },
    removeVariantType(typeName) {
      delete this.currentProduct.variants[typeName];
      delete this.currentProduct.variant_pricing[typeName];
    },
    addVariantOption(typeName) {
      const optionName = this.variantInputs[typeName]?.trim();
      if (!optionName) {
        alert(this.$t("admin.messages.variant_option_required"));
        return;
      }
      if (this.currentProduct.variants[typeName].includes(optionName)) {
        alert(this.$t("admin.messages.variant_option_exists"));
        return;
      }
      this.currentProduct.variants[typeName].push(optionName);
      this.currentProduct.variant_pricing[typeName][optionName] = 0;
      this.variantInputs[typeName] = "";
    },
    removeVariantOption(typeName, optionName) {
      const index = this.currentProduct.variants[typeName].indexOf(optionName);
      if (index > -1) {
        this.currentProduct.variants[typeName].splice(index, 1);
      }
      delete this.currentProduct.variant_pricing[typeName][optionName];
    },
    async deleteProduct(id) {
      if (await window.appConfirm(this.$t("admin.confirm.delete_product"))) {
        try {
          await api.delete(`api/admin/products/${id}`);
          this.products = this.products.filter((product) => product.id !== id);
          alert(this.$t("admin.messages.product_deleted"));
        } catch (error) {
          console.error("Error deleting product:", error);
          alert(this.$t("admin.messages.product_delete_error"));
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
        specs: "",
        discount_type: "percent",
        discount_value: 0,
        variants: null,
        variant_pricing: null,
      };
      this.originalProduct = null;
      this.productVariantsEnabled = false;
      this.variantInputs = {};
    },
    isElevatedRole(role) {
      return role === "admin" || role === "owner";
    },
    getRoleLabel(role) {
      const key = `roles.${role}`;
      const translated = this.$t(key);
      return translated === key ? String(role || "").toUpperCase() : translated;
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
      //When removing discount, price becomes equal to oldPrice (base price)
      this.currentProduct.price = this.currentProduct.oldPrice;
      // Clear discount fields only
      this.currentProduct.discount_type = "percent";
      this.currentProduct.discount_value = 0;
    },
    // Notification Methods
    async sendNotification() {
      console.log("🔔 sendNotification called!");
      console.log("notificationForm:", this.notificationForm);
      this.notificationsLoading = true;
      try {
        // Validate form
        if (!this.notificationForm.title || !this.notificationForm.title.trim()) {
          alert(this.$t("admin.notifications.validation.title_required"));
          this.notificationsLoading = false;
          return;
        }
        if (!this.notificationForm.message || !this.notificationForm.message.trim()) {
          alert(this.$t("admin.notifications.validation.message_required"));
          this.notificationsLoading = false;
          return;
        }

        const payload = {
          type: this.notificationForm.type,
          title: this.notificationForm.title.trim(),
          message: this.notificationForm.message.trim(),
        };

        let response;
        if (this.notificationForm.sendToAll) {
          console.log("Sending broadcast notification:", payload);
          response = await api.post("api/admin/notifications/broadcast", payload);
        } else if (this.notificationForm.userId) {
          payload.user_id = this.notificationForm.userId;
          console.log("Sending notification to user:", payload);
          response = await api.post("api/admin/notifications", payload);
        } else {
          alert(this.$t("admin.notifications.validation.recipient_required"));
          this.notificationsLoading = false;
          return;
        }

        console.log("Notification sent successfully:", response);

        // Reset form
        this.notificationForm = {
          type: "general",
          title: "",
          message: "",
          sendToAll: false,
          userId: null,
        };

        alert(this.$t("admin.notifications.alerts.sent"));
        await this.fetchNotifications();
      } catch (error) {
        console.error("Error sending notification:", error);
        console.error("Error response:", error.response);
        const errorMessage =
          error.response?.data?.message || error.message || "Unknown error";
        alert(this.$t("admin.notifications.alerts.send_error", { error: errorMessage }));
      } finally {
        this.notificationsLoading = false;
      }
    },
    selectUserForNotification(user) {
      this.selectedUserForNotification = user;
      this.notificationForm.userId = user.id;
      this.userSearchQueryForNotification = "";
      this.showUserDropdownForNotification = false;
    },
    handleClickOutsideNotificationDropdown(event) {
      const notificationUserContainer = document.querySelector(
        "[data-notification-user-dropdown]"
      );
      if (
        notificationUserContainer &&
        !notificationUserContainer.contains(event.target)
      ) {
        this.showUserDropdownForNotification = false;
      }
    },
    async sendSaleNotification() {
      // Get products with discount >= 10%
      const discountedProducts = this.products.filter(
        (product) => product.discount_value && product.discount_value >= 10
      );

      const maxDiscount =
        discountedProducts.length > 0
          ? Math.max(...discountedProducts.map((p) => p.discount_value))
          : 10;

      this.notificationForm.type = "promotion";
      this.notificationForm.title = "Výpredaj! 🎉";
      this.notificationForm.message = `Zľavy na vybrané položky až -${maxDiscount}%! Pospešujte, ponuka platí len do vypredania!`;
      this.notificationForm.sendToAll = true;

      // Auto-send after a short delay
      setTimeout(() => {
        this.sendNotification();
      }, 100);
    },
    async fetchNotifications() {
      try {
        const response = await api.get("api/admin/notifications");
        this.notifications = response.data.data || response.data || [];
      } catch (error) {
        console.error("Error fetching notifications:", error);
        this.notifications = [];
      }
    },
    async deleteNotificationAdm(notification, event) {
      this.notificationToDelete = notification;
      this.showDeleteNotificationConfirm = true;
    },
    async confirmDeleteNotification() {
      try {
        const notification = this.notificationToDelete;
        this.showDeleteNotificationConfirm = false;

        if (notification.isBroadcast) {
          for (const id of notification.broadcastIds) {
            await api.delete(`api/admin/notifications/${id}`);
          }
          alert(
            `Notifikácia bola vymazaná pre ${notification.broadcastCount} používateľov!`
          );
        } else {
          await api.delete(`api/admin/notifications/${notification.id}`);
        }
        await this.fetchNotifications();
      } catch (error) {
        console.error("Error deleting notification:", error);
        alert("Chyba pri vymazávaní notifikácie!");
      } finally {
        this.notificationToDelete = null;
      }
    },
    getNotificationTypeName(type) {
      const typeMap = {
        general: this.$t("admin.notifications.type_general"),
        promotion: this.$t("admin.notifications.type_promotion"),
        order: this.$t("admin.notifications.type_order"),
        payment: this.$t("admin.notifications.type_payment"),
        system: this.$t("admin.notifications.type_system"),
      };
      return typeMap[type] || type;
    },
    getNotificationTypeClass(type) {
      const classes = {
        order: "bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200",
        payment: "bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200",
        promotion:
          "bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200",
        system: "bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200",
        general: "bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200",
      };
      return classes[type] || classes.general;
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString("sk-SK", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
      });
    },
    getAvatarUrl(avatar) {
      if (!avatar) return "";
      if (typeof avatar === "string" && avatar.startsWith("http")) return avatar;
      const base = import.meta.env.VITE_API_URL || "https://backend.techstore.lznet.work";
      return `${base}/storage/${avatar}`;
    },
    getInitials(name) {
      if (!name) return "?";
      const parts = name.split(" ").filter(Boolean);
      if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
      }
      return name.substring(0, 2).toUpperCase();
    },
  },
  watch: {
    editingOrder(newVal) {
      if (!newVal) {
        // Reset country codes when exiting edit mode
        this.countryOptionsCodes = [
          { code: "SK", key: "slovakia" },
          { code: "CZ", key: "czechia" },
          { code: "PL", key: "poland" },
          { code: "HU", key: "hungary" },
          { code: "AT", key: "austria" },
          { code: "DE", key: "germany" },
        ];
        return;
      }

      // Ensure address exists
      if (!newVal.address) newVal.address = {};

      // Format phone for display
      if (newVal.address.phone) {
        newVal.address.phone = this.formatPhone(newVal.address.phone);
      }

      // Handle country mapping
      const countryCode = newVal.address.country_code;
      if (countryCode) {
        const countryObj = this.countryOptionsCodes.find((c) => c.code === countryCode);
        if (countryObj) {
          newVal.address.country = this.$t(`admin.orders.${countryObj.key}`);
        }
      } else {
        // If no country code, try to find it from country name
        const countryName = newVal.address.country;
        const matchedCountry = this.countryOptionsCodes.find(
          (c) => this.$t(`admin.orders.${c.key}`) === countryName
        );
        if (matchedCountry) {
          newVal.address.country_code = matchedCountry.code;
        } else if (this.countryOptions.length > 0) {
          // Fallback to first option
          newVal.address.country = this.countryOptions[0]?.name || "";
        }
      }

      // If delivery method is empty or not in current language options, use first option
      if (
        !newVal.delivery_method ||
        !this.deliveryOptions.includes(newVal.delivery_method)
      ) {
        if (this.deliveryOptions.length > 0) {
          newVal.delivery_method = this.deliveryOptions[0];
        }
      }
      // If payment method is empty or not in current language options, use first option
      if (
        !newVal.payment_method ||
        !this.paymentOptions.includes(newVal.payment_method)
      ) {
        if (this.paymentOptions.length > 0) {
          newVal.payment_method = this.paymentOptions[0];
        }
      }
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

/* Popup modal animation */
.popup-enter-active,
.popup-leave-active {
  transition: opacity 200ms ease;
}
.popup-enter-from,
.popup-leave-to {
  opacity: 0;
}
.popup-enter-to,
.popup-leave-from {
  opacity: 1;
}

.popup-enter-active > div,
.popup-leave-active > div {
  transition: transform 220ms cubic-bezier(0.2, 0.9, 0.2, 1), opacity 220ms ease;
}
.popup-enter-from > div {
  transform: translateY(-16px) scale(0.96);
  opacity: 0;
}
.popup-enter-to > div {
  transform: translateY(0) scale(1);
  opacity: 1;
}
.popup-leave-from > div {
  transform: translateY(0) scale(1);
  opacity: 1;
}
.popup-leave-to > div {
  transform: translateY(-16px) scale(0.96);
  opacity: 0;
}

/* Custom scrollbar styling */
.order-items-touch,
.order-items-scroll {
  -webkit-overflow-scrolling: touch;
  touch-action: pan-x pan-y;
  overscroll-behavior-x: contain;
}

.order-items-scroll,
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: var(--scrollbar-thumb) var(--scrollbar-track);
}

.order-items-scroll::-webkit-scrollbar,
.custom-scrollbar::-webkit-scrollbar {
  width: var(--scrollbar-size);
  height: var(--scrollbar-size);
}

.order-items-scroll::-webkit-scrollbar-track,
.custom-scrollbar::-webkit-scrollbar-track {
  background: var(--scrollbar-track);
  border-radius: 9999px;
}

.order-items-scroll::-webkit-scrollbar-thumb,
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: var(--scrollbar-thumb);
  background: var(--scrollbar-thumb);
  border-radius: 9999px;
  transition: background-color 160ms ease;
}

.order-items-scroll::-webkit-scrollbar-thumb:hover,
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: var(--scrollbar-thumb-hover);
  background: var(--scrollbar-thumb-hover);
}
</style>
