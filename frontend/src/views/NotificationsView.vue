<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
              {{ $t('notifications.dropdown.title') }}
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
              {{ notifications.length }} {{ $t('notifications.all') || 'notifications' }}
            </p>
          </div>
          <button
            @click="goBack"
            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
          >
            ← {{ $t('common.back') || 'Back' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Empty State -->
      <div v-if="notifications.length === 0" class="text-center py-12">
        <svg
          class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
          />
        </svg>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
          {{ $t('notifications.dropdown.empty') }}
        </h2>
        <p class="text-gray-600 dark:text-gray-400">
          {{ $t('notifications.no_notifications') || 'You have no notifications.' }}
        </p>
      </div>

      <!-- Notifications List -->
      <div v-else class="space-y-4">
        <div
          v-for="notification in notifications"
          :key="notification.id"
          class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow"
          :class="!notification.read ? 'border-indigo-500 dark:border-indigo-400' : ''"
        >
          <div class="p-6">
            <div class="flex gap-4">
              <!-- Icon -->
              <div class="flex-shrink-0">
                <div
                  class="w-12 h-12 rounded-full flex items-center justify-center"
                  :class="getIconClass(notification.type)"
                >
                  <svg
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      :d="getIconPath(notification.type)"
                    />
                  </svg>
                </div>
              </div>

              <!-- Content -->
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                  <div class="flex-1">
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                      {{ notification.title }}
                    </p>
                    <div class="flex items-center gap-2 mt-1">
                      <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ getTimeAgo(notification.created_at) }}
                      </span>
                      <span
                        v-if="!notification.read"
                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200"
                      >
                        {{ $t('notifications.unread') || 'Unread' }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Message -->
                <p class="text-gray-700 dark:text-gray-300 mt-3 leading-relaxed">
                  {{ notification.message }}
                </p>

                <!-- Action Button for Promotions -->
                <div v-if="notification.type === 'promotion'" class="mt-4">
                  <button
                    @click="$router.push('/mega-sale')"
                    class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors font-medium"
                  >
                    {{ $t('notifications.viewSale') }}
                  </button>
                </div>

                <!-- Mark as Read Button -->
                <div v-if="!notification.read" class="mt-4">
                  <button
                    @click="markAsRead(notification.id)"
                    class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium"
                  >
                    {{ $t('notifications.markAsRead') || 'Mark as read' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div v-if="notifications.length > 0" class="mt-8 flex gap-4 justify-center">
        <button
          v-if="unreadCount > 0"
          @click="markAllAsRead"
          class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors font-medium"
        >
          {{ $t('notifications.dropdown.markAll') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import api from "@/api";

export default {
  data() {
    return {
      notifications: [],
    };
  },
  computed: {
    unreadCount() {
      return this.notifications.filter((n) => !n.read).length;
    },
  },
  mounted() {
    this.fetchNotifications();
    // Listen for language changes
    window.addEventListener("language-changed", () => {
      this.fetchNotifications();
    });
  },
  beforeUnmount() {
    window.removeEventListener("language-changed", this.fetchNotifications);
  },
  methods: {
    async fetchNotifications() {
      try {
        const response = await api.get("api/notifications");
        this.notifications = response.data.data || response.data || [];
      } catch (error) {
        console.error("Error fetching notifications:", error);
        this.notifications = [];
      }
    },
    async markAsRead(id) {
      try {
        await api.put(`api/notifications/${id}/mark-read`);
        const notification = this.notifications.find((n) => n.id === id);
        if (notification) {
          notification.read = true;
        }
      } catch (error) {
        console.error("Error marking notification as read:", error);
      }
    },
    async markAllAsRead() {
      try {
        await api.post("api/notifications/mark-all-read");
        this.notifications.forEach((n) => (n.read = true));
      } catch (error) {
        console.error("Error marking all notifications as read:", error);
      }
    },
    getIconClass(type) {
      const classes = {
        order: "bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400",
        payment: "bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400",
        promotion: "bg-orange-100 dark:bg-orange-900 text-orange-600 dark:text-orange-400",
        system: "bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400",
        general: "bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400",
      };
      return classes[type] || classes.general;
    },
    getIconPath(type) {
      const paths = {
        order:
          "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4",
        payment:
          "M3 10a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM3 10V7a1 1 0 011-1h12a1 1 0 011 1v3",
        promotion:
          "M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z",
        system:
          "M13 10V3L4 14h7v7l9-11h-7z",
        general:
          "M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9",
      };
      return paths[type] || paths.general;
    },
    getTimeAgo(date) {
      const now = new Date();
      const notifDate = new Date(date);
      const seconds = Math.floor((now - notifDate) / 1000);

      if (seconds < 60) return this.$t('time.justNow') || 'Just now';
      const minutes = Math.floor(seconds / 60);
      if (minutes < 60) return `${minutes}m ${this.$t('time.ago') || 'ago'}`;
      const hours = Math.floor(minutes / 60);
      if (hours < 24) return `${hours}h ${this.$t('time.ago') || 'ago'}`;
      const days = Math.floor(hours / 24);
      if (days < 7) return `${days}d ${this.$t('time.ago') || 'ago'}`;
      const weeks = Math.floor(days / 7);
      if (weeks < 4) return `${weeks}w ${this.$t('time.ago') || 'ago'}`;

      return notifDate.toLocaleDateString();
    },
    goBack() {
      this.$router.back();
    },
  },
};
</script>
