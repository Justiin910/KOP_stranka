<template>
  <div class="min-h-screen bg-white dark:bg-gray-900">
    <div class="max-w-4xl mx-auto px-6 py-12">
      <!-- Back Button -->
      <button
        @click="$router.push('/')"
        class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-6 transition-colors"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M10 19l-7-7m0 0l7-7m-7 7h18"
          />
        </svg>
        {{ $t("pages.aboutus.back") }}
      </button>

      <!-- Header -->
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">
        {{ $t("pages.aboutus.title") }}
      </h1>

      <!-- Content Sections -->
      <div class="space-y-6">
        <!-- Hero Section -->
        <section
          class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg p-8 text-white"
        >
          <h2 class="text-2xl font-bold mb-3">{{ $t("pages.aboutus.hero_title") }}</h2>
          <p class="text-indigo-100">
            {{ $t("pages.aboutus.hero_text") }}
          </p>
        </section>

        <!-- Our Story -->
        <section
          class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
            {{ $t("pages.aboutus.story_title") }}
          </h2>
          <div class="space-y-3 text-gray-600 dark:text-gray-400">
            <p>
              {{ $t("pages.aboutus.story_text1") }}
            </p>
            <p>
              {{ $t("pages.aboutus.story_text2_prefix") }}
              <strong class="text-gray-900 dark:text-white">{{
                $t("pages.aboutus.story_text2_customers")
              }}</strong>
              {{ $t("pages.aboutus.story_text2_suffix") }}
            </p>
            <p>
              {{ $t("pages.aboutus.story_text3") }}
            </p>
          </div>
        </section>

        <!-- Values -->
        <section
          class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
            {{ $t("pages.aboutus.values_title") }}
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div
              v-for="value in values"
              :key="value.id"
              class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4"
            >
              <div class="flex items-start gap-3">
                <div
                  class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0"
                >
                  <div
                    v-html="value.icon"
                    class="text-indigo-600 dark:text-indigo-400"
                  ></div>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900 dark:text-white mb-1">
                    {{ value.title }}
                  </h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ value.description }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Stats -->
        <section
          class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 text-center">
            {{ $t("pages.aboutus.stats_title") }}
          </h2>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div v-for="stat in stats" :key="stat.id" class="text-center">
              <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mb-1">
                {{ stat.value }}
              </div>
              <div class="text-sm text-gray-600 dark:text-gray-400">{{ stat.label }}</div>
            </div>
          </div>
        </section>

        <!-- Team -->
        <section
          class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
            {{ $t("pages.aboutus.team_title") }}
          </h2>
          <div class="space-y-3 text-gray-600 dark:text-gray-400">
            <p>
              {{ $t("pages.aboutus.team_text") }}
            </p>
            <div
              class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mt-4"
            >
              <p class="text-sm text-blue-800 dark:text-blue-200">
                <strong>{{ $t("pages.aboutus.team_cta_title") }}</strong>
                {{ $t("pages.aboutus.team_cta_text") }}
              </p>
            </div>
          </div>
        </section>

        <!-- Certifications -->
        <section
          class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
            {{ $t("pages.aboutus.partners_title") }}
          </h2>
          <div class="space-y-3 text-gray-600 dark:text-gray-400">
            <p>{{ $t("pages.aboutus.partners_text") }}</p>
            <div class="flex flex-wrap gap-4 mt-4">
              <div
                v-for="partner in partners"
                :key="partner"
                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm font-semibold text-gray-900 dark:text-white"
              >
                {{ partner }}
              </div>
            </div>
          </div>
        </section>

        <!-- Mission -->
        <section
          class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg p-6 text-white"
        >
          <h2 class="text-xl font-bold mb-3">{{ $t("pages.aboutus.mission_title") }}</h2>
          <p class="text-purple-100">
            {{ $t("pages.aboutus.mission_text") }}
          </p>
        </section>

        <!-- Contact CTA -->
        <section
          class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-6 text-center"
        >
          <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
            {{ $t("pages.aboutus.cta_title") }}
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            {{ $t("pages.aboutus.cta_text") }}
          </p>
          <button
            @click="$router.push('/contact')"
            class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors"
          >
            {{ $t("pages.aboutus.cta_button") }}
          </button>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AboutusView",
  data() {
    return {
      values: [
        {
          id: 1,
          title: this.$t("pages.aboutus.values_item1_title"),
          description: this.$t("pages.aboutus.values_item1_desc"),
          icon:
            '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>',
        },
        {
          id: 2,
          title: this.$t("pages.aboutus.values_item2_title"),
          description: this.$t("pages.aboutus.values_item2_desc"),
          icon:
            '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>',
        },
        {
          id: 3,
          title: this.$t("pages.aboutus.values_item3_title"),
          description: this.$t("pages.aboutus.values_item3_desc"),
          icon:
            '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>',
        },
      ],
      stats: [
        { id: 1, value: "100 000+", label: this.$t("pages.aboutus.stats_item1") },
        { id: 2, value: "1", label: this.$t("pages.aboutus.stats_item2") },
        { id: 3, value: "200+", label: this.$t("pages.aboutus.stats_item3") },
        { id: 4, value: "3", label: this.$t("pages.aboutus.stats_item4") },
      ],
      partners: ["Huawei", "Dell", "HP"],
    };
  },
  mounted() {
    window.scrollTo({ top: 0, behavior: "smooth" });
  },
};
</script>
