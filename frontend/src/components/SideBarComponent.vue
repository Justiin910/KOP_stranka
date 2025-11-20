<template>
  <aside
    class="w-64 bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 h-full top-15"
  >
    <nav aria-label="Kategórie">
      <ul class="space-y-1 pt-5 px-3">
        <li v-for="item in items" :key="item.key">
          <button
            @click="select(item.key)"
            class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors rounded-lg"
            :title="item.label"
          >
            <!-- Font Awesome icon from database -->
            <i :class="item.icon" class="w-5 text-center" aria-hidden="true"></i>
            <span class="truncate">{{ item.label }}</span>
          </button>
        </li>
      </ul>
    </nav>
  </aside>
</template>

<script>
import api from "@/api";
import axios from "axios";
export default {
  data() {
    return {
      items: [],
    };
  },
  async mounted() {
    try {
      const response = await api.get("/categories");
      this.items = response.data.map((cat) => ({
        key: cat.key_name,
        label: cat.label,
        icon: cat.icon,
      }));
    } catch (error) {
      console.error("Error loading categories:", error);
    }
  },
};
</script>
<style scoped>
/* keep it compact and drive look from Tailwind; custom overrides minimal */
.truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
