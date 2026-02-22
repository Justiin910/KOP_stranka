<template>
  <div class="relative" ref="notificationDropdown">
    <button
      @click="toggleDropdown"
      class="relative group inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-800 hover:bg-gray-700 transition transform hover:scale-105 focus:outline-none"
    >
      <font-awesome-icon icon="fa-regular fa-bell" />
      <span
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-semibold leading-none text-white bg-red-500 rounded-full shadow"
      >
        {{ unreadCount }}
      </span>
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
        v-if="showDropdown"
        class="absolute right-0 mt-2 w-96 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden z-50"
      >
        <!-- Header -->
        <div
          class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between"
        >
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $t('notifications.dropdown.title') }}</h3>
          <button
            v-if="unreadCount > 0"
            @click="markAllAsRead"
            class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium"
          >
            {{ $t('notifications.dropdown.markAll') }}
          </button>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
          <div
            v-for="notification in notifications"
            :key="notification.id"
            @click="markAsRead(notification.id)"
            class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 cursor-pointer transition-colors select-none"
            :class="!notification.read ? 'bg-indigo-50/50 dark:bg-indigo-900/20' : ''"
          >
            <div class="flex gap-3">
              <!-- Icon -->
              <div class="flex-shrink-0">
                <div
                  class="w-10 h-10 rounded-full flex items-center justify-center"
                  :class="getIconClass(notification.type)"
                >
                  <svg
                    class="w-5 h-5"
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
                  <p
                    class="font-semibold text-sm text-gray-900 dark:text-white line-clamp-1"
                  >
                    {{ notification.title }}
                  </p>
                  <button
                    @click.stop="deleteNotification(notification.id)"
                    class="flex-shrink-0 p-0.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                  >
                    <svg
                      class="w-4 h-4"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                      />
                    </svg>
                  </button>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">
                  {{ notification.message }}
                </p>
                <div
                  v-if="notification.type === 'promotion'"
                  class="mt-2"
                >
                  <button
                    @click.stop="$router.push('/mega-sale')"
                    class="text-xs px-3 py-1 bg-orange-500 hover:bg-orange-600 text-white rounded transition-colors font-medium"
                  >
                    {{ $t('notifications.viewSale') }}
                  </button>
                </div>
                <div class="flex items-center justify-between mt-2">
                  <span class="text-xs text-gray-500 dark:text-gray-500">
                    {{ getTimeAgo(notification.created_at) }}
                  </span>
                  <span
                    v-if="!notification.read"
                    class="w-2 h-2 bg-indigo-600 rounded-full"
                  ></span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="notifications.length === 0" class="px-4 py-8 text-center">
          <svg
            class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-3"
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
          <p class="text-sm text-gray-600 dark:text-gray-400">{{ $t('notifications.dropdown.empty') }}</p>
        </div>

        <!-- Footer -->
        <div
          class="px-4 py-3 bg-gray-50 dark:bg-gray-750 border-t border-gray-200 dark:border-gray-700"
        >
          <button
            @click="viewAll"
            class="w-full text-center text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium"
          >
            {{ $t('notifications.dropdown.viewAll') }}
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import api from "@/api";

export default {
  data() {
    return {
      showDropdown: false,
      notifications: [],
      loading: false,
      notificationRefreshInterval: null,
      timeUpdateInterval: null,
      currentTime: new Date(),
    };
  },
  computed: {
    unreadCount() {
      return this.notifications.filter((n) => !n.read).length;
    },
  },
  mounted() {
    document.addEventListener("click", this.handleClickOutside);
    // Listen for language changes and refetch notifications
    window.addEventListener("language-changed", (event) => {
      this.fetchNotifications();
    });
    // Only fetch notifications if user is authenticated
    const user = JSON.parse(localStorage.getItem("user") || "null");
    if (user && user.id) {
      this.fetchNotifications();
    }
  },
  beforeUnmount() {
    document.removeEventListener("click", this.handleClickOutside);
  },
  methods: {
    async fetchNotifications() {
      try {
        const response = await api.get("api/notifications");
        this.notifications = response.data.data || response.data || [];
      } catch (error) {
        console.error("❌ Error fetching notifications:", error);
        this.notifications = [];
      }
    },
    async toggleDropdown() {
      this.showDropdown = !this.showDropdown;
      // Refetch notifications when opening dropdown to ensure latest language
      if (this.showDropdown) {
        await this.fetchNotifications();
      }
    },
    handleClickOutside(event) {
      const dropdown = this.$refs.notificationDropdown;
      if (dropdown && !dropdown.contains(event.target)) {
        this.showDropdown = false;
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
    async deleteNotification(id) {
      try {
        await api.delete(`api/notifications/${id}`);
        const index = this.notifications.findIndex((n) => n.id === id);
        if (index !== -1) {
          this.notifications.splice(index, 1);
        }
      } catch (error) {
        console.error("Error deleting notification:", error);
      }
    },
    viewAll() {
      this.showDropdown = false;
      // Redirect to notifications page (sa vytvorí neskôr)
      this.$router.push("/notifications");
    },
    getIconClass(type) {
      const classes = {
        order: "bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400",
        payment: "bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400",
        promotion:
          "bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400",
        system: "bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400",
        general: "bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400",
      };
      return classes[type] || classes.general;
    },
    getIconPath(type) {
      const paths = {
        order: "M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z",
        payment:
          "M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z",
        promotion:
          "M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7",
        system: "M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z",
        general:
          "M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9",
      };
      return paths[type] || paths.general;
    },
    getTimeAgo(createdAt) {
      if (!createdAt) return this.$t('time.justNow') || 'Just now';
      
      const created = new Date(createdAt);
      const now = this.currentTime;
      const seconds = Math.floor((now - created) / 1000);
      
      if (seconds < 60) return this.$t('time.justNow') || 'Just now';
      
      const minutes = Math.floor(seconds / 60);
      if (minutes < 60) return `${minutes}m ${this.$t('time.ago') || 'ago'}`;
      
      const hours = Math.floor(minutes / 60);
      if (hours < 24) return `${hours}h ${this.$t('time.ago') || 'ago'}`;
      
      const days = Math.floor(hours / 24);
      return `${days}d ${this.$t('time.ago') || 'ago'}`;
    },
  },
};
</script>
<style scoped>
.select-none {
  user-select: none;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
}
</style>
