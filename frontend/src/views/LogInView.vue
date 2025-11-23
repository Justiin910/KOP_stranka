<template>
  <div
    class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 flex items-center justify-center px-4 py-12"
  >
    <div class="w-full max-w-md">
      <!-- Logo/Header -->
      <div class="text-center mb-8">
        <div
          class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl shadow-lg mb-4"
        >
          <svg class="w-10 h-10 text-white" viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 5h14M10 21a1 1 0 100-2 1 1 0 000 2zm7 0a1 1 0 100-2 1 1 0 000 2z"
            />
          </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Vitajte späť</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Prihláste sa do svojho účtu</p>
      </div>

      <!-- Login Form -->
      <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8"
      >
        <form @submit.prevent="handleLogin">
          <!-- Email -->
          <div class="mb-6">
            <label
              for="email"
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              Email
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
                v-model="form.email"
                type="email"
                required
                placeholder="vas.email@priklad.sk"
                :disabled="isSubmitting"
                class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all disabled:opacity-50 disabled:cursor-not-allowed"
              />
            </div>
          </div>

          <!-- Password -->
          <div class="mb-6">
            <label
              for="password"
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              Heslo
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
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                required
                placeholder="••••••••"
                :disabled="isSubmitting"
                class="w-full pl-10 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all disabled:opacity-50 disabled:cursor-not-allowed"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                :disabled="isSubmitting"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 disabled:opacity-50"
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
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                  />
                </svg>
              </button>
            </div>
          </div>

          <!-- Remember Me & Forgot Password -->
          <div class="flex items-center justify-between mb-6">
            <label class="flex items-center">
              <input
                v-model="form.remember"
                type="checkbox"
                :disabled="isSubmitting"
                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 disabled:opacity-50"
              />
              <span class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                >Zapamätať si ma</span
              >
            </label>
            <router-link
              to="/password-reset"
              class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium"
            >
              Zabudli ste heslo?
            </router-link>
          </div>

          <!-- Error Message -->
          <div
            v-if="loginError"
            class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3"
          >
            <div class="flex items-start gap-2">
              <svg
                class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <p class="text-sm text-red-800 dark:text-red-200">{{ loginError }}</p>
            </div>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="isSubmitting"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg transition-colors shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
          >
            <span v-if="isSubmitting" class="flex items-center justify-center gap-2">
              <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              Prihlasovanie...
            </span>
            <span v-else>Prihlásiť sa</span>
          </button>
        </form>

        <!-- Divider -->
        <div class="relative my-6">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">
              Alebo pokračujte s
            </span>
          </div>
        </div>

        <!-- Sign Up Link -->
        <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-6">
          Nemáte účet?
          <router-link
            to="/register"
            class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold"
          >
            Zaregistrujte sa
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import api, { setSessionToken } from "@/api";

export default {
  name: "LoginView",
  data() {
    return {
      form: {
        email: "",
        password: "",
        remember: false,
      },
      showPassword: false,
      loginError: "",
      isSubmitting: false,
    };
  },
  mounted() {
    this.$nextTick(() => {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  },
  methods: {
    async handleLogin() {
      if (this.isSubmitting) return;

      this.loginError = "";
      this.isSubmitting = true;

      try {
        // 1. Post login credentials
        const response = await api.post(
          "/api/login",
          {
            email: this.form.email,
            password: this.form.password,
            remember: this.form.remember,
          }
        );

        // 2. Store token based on "remember me"
        if (this.form.remember) {
          localStorage.setItem("token", response.data.token);
          setSessionToken(null); // Clear in-memory token
        } else {
          setSessionToken(response.data.token); // Store in memory only
          localStorage.removeItem("token"); // Clear persistent token if exists
        }

        // 3. Store user data
        localStorage.setItem("user", JSON.stringify(response.data.user));

        // 4. Emit event for navbar refresh
        window.dispatchEvent(new Event("user-logged-in"));

        // 5. Redirect to home
        this.$router.push("/");
      } catch (err) {
        console.error("Login error:", err);

        if (err.response?.status === 422) {
          // Validation errors
          const errors = err.response.data.errors;
          if (errors?.email) {
            this.loginError = errors.email[0];
          } else if (errors?.password) {
            this.loginError = errors.password[0];
          } else {
            this.loginError = "Nesprávny email alebo heslo.";
          }
        } else if (err.response?.status === 429) {
          this.loginError = "Príliš mnoho pokusov. Skúste znova neskôr.";
        } else if (err.response?.data?.message) {
          this.loginError = err.response.data.message;
        } else {
          this.loginError = "Chyba pri prihlasovaní. Skúste to znova.";
        }
      } finally {
        this.isSubmitting = false;
      }
    },
  },
};
</script>
