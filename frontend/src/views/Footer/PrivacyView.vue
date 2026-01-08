<template>
  <div class="min-h-screen bg-white dark:bg-gray-900">
    <div class="max-w-4xl mx-auto px-6 py-12">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
          {{ $t("pages.privacy.title") }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
          {{ $t("pages.privacy.last_updated") }} 17. november 2025
        </p>
      </div>

      <!-- Content -->
      <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8 space-y-8"
      >
        <!-- Section 1 -->
        <section>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            {{ $t("pages.privacy.section1_title") }}
          </h2>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
            {{ $t("pages.privacy.section1_text") }}
          </p>
        </section>

        <!-- Section 2 -->
        <section>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            {{ $t("pages.privacy.section2_title") }}
          </h2>
          <div
            class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 text-gray-600 dark:text-gray-400"
          >
            <p class="mb-2">
              <strong class="text-gray-900 dark:text-white">{{
                $t("pages.privacy.section2_company")
              }}</strong>
            </p>
            <p>{{ $t("pages.privacy.section2_address") }}</p>
            <p>{{ $t("pages.privacy.section2_ico") }}</p>
            <p>{{ $t("pages.privacy.section2_email") }}</p>
            <p>{{ $t("pages.privacy.section2_phone") }}</p>
          </div>
        </section>

        <!-- Section 3 -->
        <section>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            {{ $t("pages.privacy.section3_title") }}
          </h2>
          <div class="space-y-4 text-gray-600 dark:text-gray-400">
            <div>
              <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                {{ $t("pages.privacy.section3_item1_title") }}
              </h3>
              <p>{{ $t("pages.privacy.section3_item1_text") }}</p>
            </div>
            <div>
              <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                {{ $t("pages.privacy.section3_item2_title") }}
              </h3>
              <p>{{ $t("pages.privacy.section3_item2_text") }}</p>
            </div>
            <div>
              <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                {{ $t("pages.privacy.section3_item3_title") }}
              </h3>
              <p>{{ $t("pages.privacy.section3_item3_text") }}</p>
            </div>
            <div>
              <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                {{ $t("pages.privacy.section3_item4_title") }}
              </h3>
              <p>{{ $t("pages.privacy.section3_item4_text") }}</p>
            </div>
          </div>
        </section>

        <!-- Section 4 -->
        <section>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            {{ $t("pages.privacy.section4_title") }}
          </h2>
          <ul class="list-disc list-inside space-y-2 text-gray-600 dark:text-gray-400">
            <li>{{ $t("pages.privacy.section4_item1") }}</li>
            <li>{{ $t("pages.privacy.section4_item2") }}</li>
            <li>{{ $t("pages.privacy.section4_item3") }}</li>
            <li>{{ $t("pages.privacy.section4_item4") }}</li>
            <li>{{ $t("pages.privacy.section4_item5") }}</li>
            <li>{{ $t("pages.privacy.section4_item6") }}</li>
          </ul>
        </section>

        <!-- Section 5 -->
        <section>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            {{ $t("pages.privacy.section5_title") }}
          </h2>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
            {{ $t("pages.privacy.section5_text") }}
          </p>
          <ul class="list-disc list-inside space-y-2 text-gray-600 dark:text-gray-400">
            <li>{{ $t("pages.privacy.section5_item1") }}</li>
            <li>{{ $t("pages.privacy.section5_item2") }}</li>
            <li>{{ $t("pages.privacy.section5_item3") }}</li>
            <li>{{ $t("pages.privacy.section5_item4") }}</li>
          </ul>
        </section>

        <!-- Section 6 -->
        <section>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            {{ $t("pages.privacy.section6_title") }}
          </h2>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
            {{ $t("pages.privacy.section6_text") }}
          </p>
          <ul class="list-disc list-inside space-y-2 text-gray-600 dark:text-gray-400">
            <li>{{ $t("pages.privacy.section6_item1") }}</li>
            <li>{{ $t("pages.privacy.section6_item2") }}</li>
            <li>{{ $t("pages.privacy.section6_item3") }}</li>
            <li>{{ $t("pages.privacy.section6_item4") }}</li>
            <li>{{ $t("pages.privacy.section6_item5") }}</li>
          </ul>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed mt-4">
            {{ $t("pages.privacy.section6_footer") }}
          </p>
        </section>

        <!-- Section 7 -->
        <section>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            {{ $t("pages.privacy.section7_title") }}
          </h2>
          <div class="space-y-3 text-gray-600 dark:text-gray-400">
            <p>
              <strong class="text-gray-900 dark:text-white">{{
                $t("pages.privacy.section7_item1_label")
              }}</strong>
              {{ $t("pages.privacy.section7_item1_text") }}
            </p>
            <p>
              <strong class="text-gray-900 dark:text-white">{{
                $t("pages.privacy.section7_item2_label")
              }}</strong>
              {{ $t("pages.privacy.section7_item2_text") }}
            </p>
            <p>
              <strong class="text-gray-900 dark:text-white">{{
                $t("pages.privacy.section7_item3_label")
              }}</strong>
              {{ $t("pages.privacy.section7_item3_text") }}
            </p>
            <p>
              <strong class="text-gray-900 dark:text-white">{{
                $t("pages.privacy.section7_item4_label")
              }}</strong>
              {{ $t("pages.privacy.section7_item4_text") }}
            </p>
          </div>
        </section>

        <!-- Section 8 -->
        <section>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            {{ $t("pages.privacy.section8_title") }}
          </h2>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
            {{ $t("pages.privacy.section8_text") }}
          </p>
          <ul class="list-disc list-inside space-y-2 text-gray-600 dark:text-gray-400">
            <li>
              <strong class="text-gray-900 dark:text-white">{{
                $t("pages.privacy.section8_item1_label")
              }}</strong>
              {{ $t("pages.privacy.section8_item1_text") }}
            </li>
            <li>
              <strong class="text-gray-900 dark:text-white">{{
                $t("pages.privacy.section8_item2_label")
              }}</strong>
              {{ $t("pages.privacy.section8_item2_text") }}
            </li>
            <li>
              <strong class="text-gray-900 dark:text-white">{{
                $t("pages.privacy.section8_item3_label")
              }}</strong>
              {{ $t("pages.privacy.section8_item3_text") }}
            </li>
            <li>
              <strong class="text-gray-900 dark:text-white">{{
                $t("pages.privacy.section8_item4_label")
              }}</strong>
              {{ $t("pages.privacy.section8_item4_text") }}
            </li>
            <li>
              <strong class="text-gray-900 dark:text-white">{{
                $t("pages.privacy.section8_item5_label")
              }}</strong>
              {{ $t("pages.privacy.section8_item5_text") }}
            </li>
            <li>
              <strong class="text-gray-900 dark:text-white">{{
                $t("pages.privacy.section8_item6_label")
              }}</strong>
              {{ $t("pages.privacy.section8_item6_text") }}
            </li>
            <li>
              <strong class="text-gray-900 dark:text-white">{{
                $t("pages.privacy.section8_item7_label")
              }}</strong>
              {{ $t("pages.privacy.section8_item7_text") }}
            </li>
          </ul>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed mt-4">
            {{ $t("pages.privacy.section8_footer") }}
            <a
              href="mailto:techstore99x@gmail.com"
              class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
              >techstore99x@gmail.com</a
            >
          </p>
        </section>

        <!-- Section 9 -->
        <section>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            {{ $t("pages.privacy.section9_title") }}
          </h2>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
            {{ $t("pages.privacy.section9_text") }}
          </p>
        </section>

        <!-- Section 10 -->
        <section>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            {{ $t("pages.privacy.section10_title") }}
          </h2>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
            {{ $t("pages.privacy.section10_text") }}
          </p>
        </section>

        <!-- Section 11 -->
        <section>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            {{ $t("pages.privacy.section11_title") }}
          </h2>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
            {{ $t("pages.privacy.section11_text") }}
          </p>
        </section>

        <!-- Section 12 -->
        <section>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            {{ $t("pages.privacy.section12_title") }}
          </h2>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
            {{ $t("pages.privacy.section12_text") }}
          </p>
          <div
            class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 text-gray-600 dark:text-gray-400"
          >
            <p>
              {{ $t("pages.privacy.section12_email_label") }}
              <a
                href="mailto:techstore99x@gmail.com"
                class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
                >techstore99x@gmail.com</a
              >
            </p>
            <p>{{ $t("pages.privacy.section12_phone_label") }} +421 900 123 456</p>
            <p class="mt-4">{{ $t("pages.privacy.section12_complaint_text") }}</p>
            <p class="mt-2">
              <a
                href="https://dataprotection.gov.sk"
                target="_blank"
                class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
              >
                www.dataprotection.gov.sk
              </a>
            </p>
          </div>
        </section>
      </div>

      <!-- Back to home -->
      <div class="mt-8 text-center">
        <router-link
          to="/"
          class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M10 19l-7-7m0 0l7-7m-7 7h18"
            />
          </svg>
          {{ $t("pages.privacy.back_link") }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "PrivacyView",
  mounted() {
    window.scrollTo({ top: 0, behavior: "smooth" });
  },
};
</script>
