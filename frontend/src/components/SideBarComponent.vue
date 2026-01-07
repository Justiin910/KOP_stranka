<template>
  <aside
    class="w-64 bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 h-full top-15"
  >
    <nav :aria-label="$t('sidebar.heading')">
      <ul class="space-y-1 pt-5 px-3">
        <li v-for="item in items" :key="item.key">
          <router-link
            :to="'/category/' + item.key"
            class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors rounded-lg"
            :title="item.label"
          >
            <i :class="item.icon" class="w-5 text-center" aria-hidden="true"></i>
            <span class="truncate">{{ item.label }}</span>
          </router-link>
        </li>
      </ul>
    </nav>
  </aside>
</template>

<script>
import api from "@/api";

export default {
  data() {
    return {
      items: [],
      currentLanguage:
        localStorage.getItem("language") ||
        (this.$i18n && this.$i18n.global && this.$i18n.global.locale
          ? this.$i18n.global.locale.value
          : "sk"),
    };
  },
  async mounted() {
    window.addEventListener("language-changed", this.onLanguageChanged);
    await this.loadCategories();
  },
  beforeUnmount() {
    window.removeEventListener("language-changed", this.onLanguageChanged);
  },
  methods: {
    async onLanguageChanged(e) {
      try {
        const lang =
          (e && e.detail && e.detail.language) ||
          localStorage.getItem("language") ||
          "sk";
        this.currentLanguage = lang;
      } catch (err) {}
      await this.loadCategories();
    },
    async loadCategories() {
      try {
        const lang =
          this.currentLanguage ||
          localStorage.getItem("language") ||
          (this.$i18n && this.$i18n.global && this.$i18n.global.locale
            ? this.$i18n.global.locale.value
            : "sk");
        // Request categories with lang param so backend can return localized labels when supported
        const response = await api.get("api/categories", { params: { lang } });
        this.items = response.data.map((cat) => ({
          key: cat.key_name,
          // Prefer localized object keys if backend supplies translations (e.g. cat.labels = { sk: '..', en: '...' })
          label:
            (cat.labels && cat.labels[lang]) ||
            cat.label ||
            (cat.labels && cat.labels["sk"]) ||
            cat.name ||
            cat.key_name,
          icon: cat.icon,
          raw: cat,
        }));
      } catch (error) {
        console.error("Error loading categories:", error);
        this.items = [];
      }
    },
  },
};
</script>

<style scoped>
.truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
