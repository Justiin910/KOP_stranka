<template>
  <div
    class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-blue-950 dark:to-indigo-950 flex items-center justify-center px-4 py-12"
  >
    <div class="w-full max-w-md">
      <!-- Logo/Header -->
      <div class="text-center mb-8">
        <div
          class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl shadow-lg mb-4"
        >
          <svg
            class="w-10 h-10 text-white"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
            />
          </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
          {{ $t("auth.password_reset.title") }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2 max-w-sm mx-auto">
          {{
            !emailSent
              ? $t("auth.password_reset.enter_email")
              : $t("auth.password_reset.check_your_email")
          }}
        </p>
      </div>

      <!-- Form / Success Message -->
      <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8"
      >
        <!-- Email Not Sent Yet -->
        <form v-if="!emailSent" @submit.prevent="handleSubmit">
          <!-- Email Input -->
          <div class="mb-6">
            <label
              for="email"
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              {{ $t("auth.password_reset.email_label") }}
            </label>
            <div class="relative">
              <div
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
              >
                <svg
                  class="w-5 h-5 text-gray-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"
                  />
                </svg>
              </div>
              <input
                id="email"
                v-model="email"
                type="email"
                required
                :placeholder="$t('auth.password_reset.email_placeholder')"
                class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
              />
            </div>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="!email"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg transition-colors shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] disabled:bg-gray-300 disabled:cursor-not-allowed dark:disabled:bg-gray-700 mb-4"
          >
            {{ $t("auth.password_reset.submit") }}
          </button>

          <!-- Back to Login -->
          <router-link
            to="/login"
            class="w-full flex items-center justify-center gap-2 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-gray-700 dark:text-gray-300 font-medium"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10 19l-7-7m0 0l7-7m-7 7h18"
              />
            </svg>
            {{ $t("auth.password_reset.back_to_login") }}
          </router-link>
        </form>

        <!-- Email Sent Success -->
        <div v-else class="text-center">
          <!-- Success Icon -->
          <div
            class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full mb-4"
          >
            <svg
              class="w-8 h-8 text-green-600 dark:text-green-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 13l4 4L19 7"
              />
            </svg>
          </div>

          <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
            {{ $t("auth.password_reset.success_title") }}
          </h3>
          <p class="text-gray-600 dark:text-gray-400 mb-6">
            {{ $t("auth.password_reset.success_sent", { email }) }}
          </p>

          <!-- Info Box -->
          <div
            class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6"
          >
            <div class="flex items-start gap-3">
              <svg
                class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <div class="text-left">
                <p class="text-sm text-blue-800 dark:text-blue-300">
                  {{ $t("auth.password_reset.info_text") }}
                </p>
              </div>
            </div>
          </div>

          <!-- Resend Email -->
          <button
            @click="resendEmail"
            :disabled="resendCooldown > 0"
            class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium mb-4 disabled:text-gray-400 disabled:cursor-not-allowed"
          >
            {{
              resendCooldown > 0
                ? $t("auth.password_reset.resend_wait", { seconds: resendCooldown })
                : $t("auth.password_reset.resend_prompt")
            }}
          </button>

          <!-- Back to Login -->
          <router-link
            to="/login"
            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors font-semibold"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10 19l-7-7m0 0l7-7m-7 7h18"
              />
            </svg>
            {{ $t("auth.password_reset.back_to_login") }}
          </router-link>
        </div>
      </div>

      <!-- Additional Help -->
      <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
          {{ $t("auth.password_reset.need_help") }}
          <a
            href="#"
            class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium"
          >
            <router-link to="/contact">
              {{ $t("auth.password_reset.contact_support") }}
            </router-link>
          </a>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import api from "@/api";

export default {
  data() {
    return {
      email: "",
      emailSent: false,
      resendCooldown: 0,
      errorMessage: "",
    };
  },
  methods: {
    validateEmail(email) {
      const regex = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/;
      return regex.test(email);
    },

    async handleSubmit() {
      this.errorMessage = "";
      const email = this.email.trim();

      if (!this.validateEmail(email)) {
        this.errorMessage = this.$t("auth.password_reset.invalid_email");
        return;
      }

      try {
        const response = await api.post("/api/forgot-password", { email });
        console.log("Reset link response:", response.data);
        this.email = email; // ulož trimnutý email
        this.emailSent = true;
        this.resendCooldown = 60; // nastav cooldown po prvom odoslaní
        this.startCooldown();
      } catch (error) {
        console.error("Error sending reset link:", error);
        this.errorMessage =
          error.response?.data?.errors?.email?.[0] ||
          error.response?.data?.message ||
          this.$t("auth.password_reset.send_failed");
      }
    },

    startCooldown() {
      if (this.resendCooldown <= 0) return;
      const interval = setInterval(() => {
        this.resendCooldown--;
        if (this.resendCooldown === 0) clearInterval(interval);
      }, 1000);
    },

    async resendEmail() {
      this.errorMessage = "";
      const email = this.email.trim();

      if (!this.validateEmail(email)) {
        this.errorMessage = this.$t("auth.password_reset.invalid_email");
        return;
      }
      if (this.resendCooldown > 0) return; // ochrana proti klikanie

      try {
        const response = await api.post("/api/forgot-password", { email });
        console.log("Resend response:", response.data);
        this.resendCooldown = 60; // resetuj cooldown po opätovnom odoslaní
        this.startCooldown();
      } catch (error) {
        console.error("Error resending email:", error);

        // zachyť Breeze hlášky: 'passwords.throttled' alebo 'passwords.user'
        const msg =
          error.response?.data?.errors?.email?.[0] ||
          error.response?.data?.message ||
          this.$t("auth.password_reset.resend_failed");

        this.errorMessage = msg;

        // ak je to throttled, nastav dlhší cooldown
        if (
          msg.toLowerCase().includes("throttle") ||
          msg.toLowerCase().includes("príliš")
        ) {
          this.resendCooldown = 120;
          this.startCooldown();
        }
      }
    },
  },
};
</script>
