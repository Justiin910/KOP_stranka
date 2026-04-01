<template>
  <div ref="container" class="relative" @keydown.esc="closeMenu">
    <button
      type="button"
      :disabled="disabled"
      class="w-full flex items-center justify-between gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
      :class="buttonClass"
      @click="toggleMenu"
    >
      <span v-if="selectedOption" class="flex min-w-0 items-center gap-2">
        <span class="fi rounded-sm" :class="`fi-${selectedOption.flagCode}`"></span>
        <span class="truncate">{{ selectedOption.code }}</span>
      </span>
      <span v-else class="text-gray-500 dark:text-gray-400">Select</span>
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-4 w-4 text-gray-500"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M19 9l-7 7-7-7"
        ></path>
      </svg>
    </button>

    <div
      v-if="isOpen"
      class="country-code-menu absolute left-0 z-50 mt-2 max-h-64 min-w-[14rem] max-w-xs overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800"
      :class="menuClass"
    >
      <div class="sticky top-0 z-10 border-b border-gray-200 bg-white p-2 dark:border-gray-700 dark:bg-gray-800">
        <input
          ref="searchInput"
          v-model="searchQuery"
          type="text"
          class="w-full rounded-md border border-gray-300 bg-white px-2 py-1.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
          :placeholder="searchPlaceholder"
        />
      </div>
      <button
        v-for="option in filteredOptions"
        :key="option.code"
        type="button"
        class="flex w-full items-start gap-2 px-3 py-2 text-left text-sm transition hover:bg-gray-100 dark:hover:bg-gray-700"
        :class="
          modelValue === option.code
            ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'
            : 'text-gray-700 dark:text-gray-200'
        "
        @click="selectOption(option.code)"
      >
        <span class="fi rounded-sm" :class="`fi-${option.flagCode}`"></span>
        <span class="shrink-0 font-medium">{{ option.code }}</span>
        <span class="min-w-0 flex-1 whitespace-normal break-words text-xs leading-tight text-gray-500 dark:text-gray-400">{{ getOptionLabel(option) }}</span>
      </button>
      <p
        v-if="!filteredOptions.length"
        class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400"
      >
        {{ noResultsText }}
      </p>
    </div>
  </div>
</template>

<script>
export default {
  name: "CountryCodeSelect",
  props: {
    modelValue: {
      type: String,
      default: "",
    },
    options: {
      type: Array,
      default: () => [],
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    buttonClass: {
      type: String,
      default: "",
    },
    menuClass: {
      type: String,
      default: "",
    },
  },
  emits: ["update:modelValue"],
  data() {
    return {
      isOpen: false,
      searchQuery: "",
    };
  },
  computed: {
    selectedOption() {
      return this.options.find((option) => option.code === this.modelValue) || null;
    },
    filteredOptions() {
      const query = this.normalizeSearchValue(this.searchQuery);

      if (!query) {
        return this.options;
      }

      const queryDigits = query.replace(/\D/g, "");

      return this.options.filter((option) => {
        const code = String(option?.code || "").toLowerCase();
        const codeDigits = code.replace(/\D/g, "");
        const localizedLabel = this.normalizeSearchValue(this.getOptionLabel(option));
        const fallbackLabel = this.normalizeSearchValue(option?.name);

        if (code.includes(query)) {
          return true;
        }

        if (queryDigits && codeDigits.includes(queryDigits)) {
          return true;
        }

        return localizedLabel.includes(query) || fallbackLabel.includes(query);
      });
    },
    searchPlaceholder() {
      if (typeof this.$t === "function") {
        const translated = this.$t("phone_country.search_placeholder");
        if (typeof translated === "string" && translated !== "phone_country.search_placeholder") {
          return translated;
        }
      }

      return "Search country or code";
    },
    noResultsText() {
      if (typeof this.$t === "function") {
        const translated = this.$t("phone_country.no_results");
        if (typeof translated === "string" && translated !== "phone_country.no_results") {
          return translated;
        }
      }

      return "No matching country";
    },
  },
  mounted() {
    document.addEventListener("click", this.handleClickOutside);
    window.addEventListener("language-changed", this.handleLanguageChanged);
  },
  beforeUnmount() {
    document.removeEventListener("click", this.handleClickOutside);
    window.removeEventListener("language-changed", this.handleLanguageChanged);
  },
  methods: {
    toggleMenu() {
      if (this.disabled) {
        return;
      }

      this.isOpen = !this.isOpen;

      if (this.isOpen) {
        this.$nextTick(() => {
          this.$refs.searchInput?.focus();
        });
      } else {
        this.searchQuery = "";
      }
    },
    closeMenu() {
      this.isOpen = false;
      this.searchQuery = "";
    },
    selectOption(code) {
      this.$emit("update:modelValue", code);
      this.closeMenu();
    },
    handleClickOutside(event) {
      if (!this.$refs.container?.contains(event.target)) {
        this.closeMenu();
      }
    },
    handleLanguageChanged() {
      this.$forceUpdate();
    },
    normalizeSearchValue(value) {
      return String(value || "")
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .trim();
    },
    getOptionLabel(option) {
      if (!option) {
        return "";
      }

      if (option.nameKey && typeof this.$t === "function") {
        const translated = this.$t(option.nameKey);
        if (typeof translated === "string" && translated !== option.nameKey) {
          return translated;
        }
      }

      return option.name || "";
    },
  },
};
</script>

<style scoped>
.country-code-menu {
  scrollbar-width: thin;
  scrollbar-color: rgba(148, 163, 184, 0.55) transparent;
}

.country-code-menu::-webkit-scrollbar {
  width: 6px;
}

.country-code-menu::-webkit-scrollbar-track {
  background: transparent;
}

.country-code-menu::-webkit-scrollbar-thumb {
  border-radius: 9999px;
  background-color: rgba(148, 163, 184, 0.55);
}

.country-code-menu::-webkit-scrollbar-thumb:hover {
  background-color: rgba(100, 116, 139, 0.75);
}

.dark .country-code-menu {
  scrollbar-color: rgba(148, 163, 184, 0.45) transparent;
}

.dark .country-code-menu::-webkit-scrollbar-thumb {
  background-color: rgba(148, 163, 184, 0.45);
}

.dark .country-code-menu::-webkit-scrollbar-thumb:hover {
  background-color: rgba(148, 163, 184, 0.68);
}
</style>
