<template>
  <div
    class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-blue-950 dark:to-indigo-950 px-4 py-12"
  >
    <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Obnova hesla</h1>

      <form @submit.prevent="handleSubmit" novalidate>
        <!-- New Password -->
        <div class="mb-4">
          <label
            for="password"
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
          >
            Nové heslo
          </label>

          <div class="relative">
            <input
              id="password"
              :type="showPassword ? 'text' : 'password'"
              v-model.trim="password"
              required
              placeholder="********"
              @input="validatePassword()"
              class="w-full px-4 py-3 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent pr-12"
              :class="{
                'border-gray-300 dark:border-gray-600': !passwordError,
                'border-red-500 focus:ring-red-500': !!passwordError,
              }"
            />

            <button
              type="button"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-indigo-600"
              @click="showPassword = !showPassword"
              tabindex="-1"
            >
              <!-- eye closed -->
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

              <!-- eye open -->
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
                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                />
              </svg>
            </button>
          </div>

          <p v-if="passwordError" class="mt-1 text-sm text-red-600">
            {{ passwordError }}
          </p>
          <p v-else class="mt-1 text-xs text-gray-500">
            Minimálne 8 znakov. Odporúčané: písmená, čísla, symboly.
          </p>
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
          <label
            for="password_confirmation"
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
          >
            Potvrdiť heslo
          </label>

          <div class="relative">
            <input
              id="password_confirmation"
              :type="showPasswordConfirm ? 'text' : 'password'"
              v-model.trim="password_confirmation"
              required
              placeholder="********"
              @input="validateConfirmation()"
              class="w-full px-4 py-3 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent pr-12"
              :class="{
                'border-gray-300 dark:border-gray-600': !confirmationError,
                'border-red-500 focus:ring-red-500': !!confirmationError,
              }"
            />

            <button
              type="button"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-indigo-600"
              @click="showPasswordConfirm = !showPasswordConfirm"
              tabindex="-1"
            >
              <!-- closed -->
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

              <!-- open -->
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
                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                />
              </svg>
            </button>
          </div>

          <p v-if="confirmationError" class="mt-1 text-sm text-red-600">
            {{ confirmationError }}
          </p>
        </div>

        <!-- Global error (server) -->
        <div
          v-if="errorMessage"
          class="mb-4 rounded-md bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 p-3"
        >
          <p class="text-sm text-red-700 dark:text-red-300">{{ errorMessage }}</p>
        </div>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="!isFormValid || isSubmitting"
          class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 dark:disabled:bg-gray-700 disabled:cursor-not-allowed text-white font-semibold py-3 rounded-lg transition-colors shadow-lg hover:shadow-xl"
        >
          {{ isSubmitting ? "Prebieha..." : "Obnoviť heslo" }}
        </button>
      </form>

      <!-- Success Message -->
      <div v-if="success" class="mt-6 text-center">
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
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
          Heslo bolo úspešne zmenené!
        </h3>
        <router-link
          to="/login"
          class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 font-medium"
        >
          Späť na prihlásenie
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import api from "@/api";

export default {
  props: ["hash"],
  data() {
    return {
      password: "",
      password_confirmation: "",
      passwordError: "",
      confirmationError: "",
      errorMessage: "",
      success: false,
      isSubmitting: false,
      showPassword: false,
      showPasswordConfirm: false,
    };
  },
  computed: {
    isFormValid() {
      return (
        !this.passwordError &&
        !this.confirmationError &&
        this.password &&
        this.password_confirmation
      );
    },
  },
  methods: {
    validatePassword() {
      if (!this.password) {
        this.passwordError = "Heslo je povinné.";
      } else if (this.password.length < 8) {
        this.passwordError = "Heslo musí mať aspoň 8 znakov.";
      } else {
        this.passwordError = "";
      }
      // pri zmene hlavného hesla vždy znova skontroluj potvrdenie
      this.validateConfirmation();
    },
    validateConfirmation() {
      if (!this.password_confirmation) {
        this.confirmationError = "Potvrdenie hesla je povinné.";
      } else if (this.password !== this.password_confirmation) {
        this.confirmationError = "Heslá sa musia zhodovať.";
      } else {
        this.confirmationError = "";
      }
    },
    async handleSubmit() {
      // spusti validácie pred odoslaním
      this.validatePassword();
      this.validateConfirmation();
      if (!this.isFormValid || this.isSubmitting) return;

      const email = this.$route.query.email;
      const token = this.$route.params.hash;

      this.isSubmitting = true;
      this.errorMessage = "";

      try {
        await api.post("/reset-password", {
          email,
          token,
          password: this.password,
          password_confirmation: this.password_confirmation,
        });

        this.success = true;
        this.password = "";
        this.password_confirmation = "";

        setTimeout(() => {
          this.$router.push({ name: "login" });
        }, 2500);
      } catch (error) {
        // zobraz prvú chybu z Breeze API, ak existuje
        if (error.response?.data?.errors) {
          const errors = error.response.data.errors;
          const first = Object.values(errors)[0];
          this.errorMessage = Array.isArray(first) ? first[0] : String(first);
        } else {
          this.errorMessage =
            error.response?.data?.message || "Nepodarilo sa obnoviť heslo.";
        }
      } finally {
        this.isSubmitting = false;
      }
    },
  },
};
</script>
