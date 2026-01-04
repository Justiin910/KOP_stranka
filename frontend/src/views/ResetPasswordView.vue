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
          Obnovenie hesla
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2 max-w-sm mx-auto">
          Zadajte nové heslo pre váš účet
        </p>
      </div>

      <!-- Form -->
      <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8"
      >
        <!-- Error Message -->
        <div
          v-if="errorMessage"
          class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg"
        >
          <p class="text-red-800 dark:text-red-400 text-sm">{{ errorMessage }}</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-8">
          <div class="animate-spin mb-4">
            <svg
              class="w-8 h-8 text-indigo-600 mx-auto"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
              />
            </svg>
          </div>
          <p class="text-gray-600 dark:text-gray-400">Kontrolujem odkaz...</p>
        </div>

        <!-- Reset Form -->
        <form v-else @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Password Input -->
          <div>
            <label
              for="password"
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              Nové heslo
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
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                  />
                </svg>
              </div>
              <input
                id="password"
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                required
                placeholder="Zadajte nové heslo"
                class="w-full pl-10 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
              >
                <svg
                  v-if="!showPassword"
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                  />
                </svg>
                <svg
                  v-else
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.604-3.778A9.98 9.98 0 0112 3c4.478 0 8.268 2.943 9.543 7a10.079 10.079 0 01-1.563 4.803m-5.604 3.778l2.068 2.068m0 0l1.42 1.42m-1.42-1.42l-1.42-1.42m1.42 1.42l1.42 1.42M9 9h.01M7 9a2 2 0 104 0m6 0h.01"
                  />
                </svg>
              </button>
            </div>
          </div>

          <!-- Confirm Password Input -->
          <div>
            <label
              for="password_confirmation"
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              Potvrďte heslo
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
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                  />
                </svg>
              </div>
              <input
                id="password_confirmation"
                v-model="passwordConfirmation"
                :type="showPasswordConfirm ? 'text' : 'password'"
                required
                placeholder="Potvrďte nové heslo"
                class="w-full pl-10 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
              />
              <button
                type="button"
                @click="showPasswordConfirm = !showPasswordConfirm"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
              >
                <svg
                  v-if="!showPasswordConfirm"
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                  />
                </svg>
                <svg
                  v-else
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.604-3.778A9.98 9.98 0 0112 3c4.478 0 8.268 2.943 9.543 7a10.079 10.079 0 01-1.563 4.803m-5.604 3.778l2.068 2.068m0 0l1.42 1.42m-1.42-1.42l-1.42-1.42m1.42 1.42l1.42 1.42M9 9h.01M7 9a2 2 0 104 0m6 0h.01"
                  />
                </svg>
              </button>
            </div>
          </div>

          <!-- Password Requirements -->
          <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <p class="text-sm font-medium text-blue-900 dark:text-blue-400 mb-3">
              Požiadavky na heslo:
            </p>
            <ul class="space-y-2">
              <li class="flex items-center gap-2 text-sm text-blue-800 dark:text-blue-300">
                <svg
                  class="w-4 h-4"
                  :class="{ 'text-green-600 dark:text-green-400': password.length >= 8 }"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                Minimálne 8 znakov
              </li>
              <li class="flex items-center gap-2 text-sm text-blue-800 dark:text-blue-300">
                <svg
                  class="w-4 h-4"
                  :class="{ 'text-green-600 dark:text-green-400': /[a-z]/.test(password) }"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                Malé písmená (a-z)
              </li>
              <li class="flex items-center gap-2 text-sm text-blue-800 dark:text-blue-300">
                <svg
                  class="w-4 h-4"
                  :class="{ 'text-green-600 dark:text-green-400': /[A-Z]/.test(password) }"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                Veľké písmená (A-Z)
              </li>
              <li class="flex items-center gap-2 text-sm text-blue-800 dark:text-blue-300">
                <svg
                  class="w-4 h-4"
                  :class="{ 'text-green-600 dark:text-green-400': /[0-9]/.test(password) }"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                Čísla (0-9)
              </li>
            </ul>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="!isValid || submitting"
            class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 dark:disabled:bg-gray-700 text-white font-semibold py-3 rounded-lg transition-colors shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] disabled:cursor-not-allowed"
          >
            <span v-if="submitting" class="flex items-center justify-center gap-2">
              <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                />
              </svg>
              Obnovovanie...
            </span>
            <span v-else>Obnoviť heslo</span>
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
            Späť na prihlásenie
          </router-link>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import api from "@/api";

export default {
  name: "ResetPasswordView",
  data() {
    return {
      password: "",
      passwordConfirmation: "",
      showPassword: false,
      showPasswordConfirm: false,
      loading: true,
      submitting: false,
      errorMessage: "",
      token: "",
      email: "",
    };
  },
  computed: {
    isValid() {
      return (
        this.password.length >= 8 &&
        /[a-z]/.test(this.password) &&
        /[A-Z]/.test(this.password) &&
        /[0-9]/.test(this.password) &&
        this.password === this.passwordConfirmation
      );
    },
  },
  mounted() {
    this.loadResetLink();
  },
  methods: {
    loadResetLink() {
      this.loading = true;
      this.token = this.$route.query.token;
      this.email = this.$route.query.email;

      if (!this.token || !this.email) {
        this.errorMessage =
          "Odkaz na obnovenie hesla je neplatný alebo vypršal. Prosím požiadajte o nový odkaz.";
        this.loading = false;
        return;
      }

      this.loading = false;
    },

    async handleSubmit() {
      this.errorMessage = "";

      if (!this.isValid) {
        this.errorMessage =
          "Heslo nespĺňa všetky požiadavky. Prosím skontrolujte nižšie zoznamy.";
        return;
      }

      this.submitting = true;

      try {
        const response = await api.post("/api/reset-password", {
          token: this.token,
          email: this.email,
          password: this.password,
          password_confirmation: this.passwordConfirmation,
        });

        console.log("Password reset successful:", response.data);

        // Show success message
        this.$router.push({
          name: "login",
          query: { reset_success: "true" },
        });
      } catch (error) {
        console.error("Error resetting password:", error);
        this.errorMessage =
          error.response?.data?.message ||
          error.response?.data?.errors?.email?.[0] ||
          "Nepodarilo sa obnoviť heslo. Skúste znova alebo požiadajte o nový odkaz.";

        // If token is invalid, show specific message
        if (error.response?.status === 422) {
          const errors = error.response.data.errors;
          if (errors.token) {
            this.errorMessage =
              "Odkaz na obnovenie hesla je neplatný alebo vypršal. Prosím požiadajte o nový odkaz.";
          }
        }
      } finally {
        this.submitting = false;
      }
    },
  },
};
</script>

<style scoped>
/* Ensure smooth transitions for visibility toggles */
input[type="password"],
input[type="text"] {
  transition: background-color 0.2s ease;
}
</style>
