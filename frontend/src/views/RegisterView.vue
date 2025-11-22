<template>
  <div
    class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-indigo-950 dark:to-purple-950 flex items-center justify-center px-4 py-12"
  >
    <div class="w-full max-w-md relative">
      <!-- Success Message after Registration -->
      <div
        v-if="showSuccessMessage"
        class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4"
      >
        <div class="flex items-center gap-3">
          <svg
            class="w-6 h-6 text-green-600 dark:text-green-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <div>
            <h3 class="font-semibold text-green-900 dark:text-green-100">
              Účet vytvorený!
            </h3>
            <p class="text-sm text-green-700 dark:text-green-300">
              Overovací email bol odoslaný na <strong>{{ form.email }}</strong>
            </p>
          </div>
        </div>
      </div>

      <!-- Registration Form -->
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
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Vytvorte si účet</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Začnite nakupovať už dnes</p>
      </div>

      <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8"
      >
        <form @submit.prevent="handleRegister">
          <!-- Name -->
          <div class="mb-5">
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              Meno a priezvisko
            </label>
            <input
              v-model="form.name"
              required
              placeholder="Ján Novák"
              class="w-full py-3 px-4 border rounded-lg dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <!-- Email -->
          <div class="mb-5">
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              Email
            </label>
            <input
              v-model="form.email"
              @input="validateEmail"
              type="email"
              required
              placeholder="vas.email@priklad.sk"
              class="w-full py-3 px-4 border rounded-lg dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-2 focus:ring-indigo-500"
            />
            <p v-if="form.email && !validEmail" class="text-red-500 text-sm mt-1">
              Neplatný email
            </p>
          </div>

          <!-- Phone -->
          <div class="mb-5">
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              Telefón
            </label>
            <div class="flex gap-3">
              <select
                v-model="form.country"
                class="py-3 px-3 border rounded-lg dark:bg-gray-700 dark:text-white dark:border-gray-600 w-28 focus:ring-2 focus:ring-indigo-500"
              >
                <option v-for="c in countries" :key="c.code" :value="c.code">
                  {{ c.flag }} {{ c.code }}
                </option>
              </select>
              <input
                v-model="form.phone"
                @input="formatPhone"
                type="tel"
                required
                placeholder="912 345 678"
                class="flex-1 py-3 px-4 border rounded-lg dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-2 focus:ring-indigo-500"
              />
            </div>
            <p v-if="form.phone && phoneDigits !== 9" class="text-red-500 text-sm mt-1">
              Telefón musí mať 9 číslic
            </p>
          </div>

          <!-- Password -->
          <div class="mb-5">
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              Heslo
            </label>
            <div class="relative">
              <input
                :type="showPassword ? 'text' : 'password'"
                v-model="form.password"
                required
                placeholder="••••••••"
                class="w-full py-3 px-4 border rounded-lg dark:bg-gray-700 dark:text-white dark:border-gray-600 pr-12 focus:ring-2 focus:ring-indigo-500"
              />
              <button
                type="button"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-indigo-600"
                @click="showPassword = !showPassword"
                tabindex="-1"
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
            <p
              v-if="form.password && form.password.length < 8"
              class="text-red-500 text-sm mt-1"
            >
              Heslo musí mať aspoň 8 znakov
            </p>
          </div>

          <!-- Confirm Password -->
          <div class="mb-5">
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            >
              Potvrďte heslo
            </label>
            <div class="relative">
              <input
                :type="showConfirmPassword ? 'text' : 'password'"
                v-model="form.confirmPassword"
                required
                placeholder="••••••••"
                class="w-full py-3 px-4 border rounded-lg dark:bg-gray-700 dark:text-white dark:border-gray-600 pr-12 focus:ring-2 focus:ring-indigo-500"
              />
              <button
                type="button"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-indigo-600"
                @click="showConfirmPassword = !showConfirmPassword"
                tabindex="-1"
              >
                <svg
                  v-if="!showConfirmPassword"
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
            <p
              v-if="form.confirmPassword && form.password !== form.confirmPassword"
              class="text-red-500 text-sm mt-1"
            >
              Heslá sa nezhodujú
            </p>
          </div>

          <!-- Terms -->
          <div class="mb-6">
            <label
              class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"
            >
              <input
                type="checkbox"
                v-model="form.acceptTerms"
                required
                class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500"
              />
              Súhlasím s podmienkami
            </label>
          </div>

          <button
            type="submit"
            :disabled="!isFormValid || isSubmitting"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg shadow-lg disabled:bg-gray-300 disabled:cursor-not-allowed transition"
          >
            <span v-if="isSubmitting">Vytváram účet...</span>
            <span v-else>Vytvoriť účet</span>
          </button>

          <p v-if="registerError" class="text-red-500 text-sm text-center mt-3">
            {{ registerError }}
          </p>

          <div class="mt-6 text-center">
            <span class="text-sm text-gray-600 dark:text-gray-400">Už máte účet?</span>
            <button
              @click="$router.push('/login')"
              type="button"
              class="ml-2 text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold"
            >
              Prihlásiť sa
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import api from "@/api";

export default {
  data() {
    return {
      countries: [
        { code: "+421", flag: "🇸🇰" },
        { code: "+420", flag: "🇨🇿" },
        { code: "+48", flag: "🇵🇱" },
        { code: "+43", flag: "🇦🇹" },
      ],
      form: {
        name: "",
        email: "",
        country: "+421",
        phone: "",
        password: "",
        confirmPassword: "",
        acceptTerms: false,
      },
      validEmail: true,
      showPassword: false,
      showConfirmPassword: false,
      registerError: "",
      isSubmitting: false,
      showSuccessMessage: false,
    };
  },
  computed: {
    phoneDigits() {
      return this.form.phone.replace(/\D/g, "").length;
    },
    isFormValid() {
      return (
        this.form.name &&
        this.validEmail &&
        this.phoneDigits === 9 &&
        this.form.password.length >= 8 &&
        this.form.password === this.form.confirmPassword &&
        this.form.acceptTerms
      );
    },
  },
  methods: {
    validateEmail() {
      const regex = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/;
      this.validEmail = regex.test(this.form.email);
    },
    formatPhone() {
      let num = this.form.phone.replace(/\D/g, "").substring(0, 9);
      let parts = [];
      if (num.length > 0) parts.push(num.substring(0, 3));
      if (num.length > 3) parts.push(num.substring(3, 6));
      if (num.length > 6) parts.push(num.substring(6));
      this.form.phone = parts.join(" ");
    },
    async handleRegister() {
      this.registerError = "";
      if (!this.isFormValid || this.isSubmitting) return;

      this.isSubmitting = true;

      try {
        // Získaj CSRF token
        await api.get("/sanctum/csrf-cookie", { withCredentials: true });

        // Registrácia s telefónom
        const fullPhone = `${this.form.country}${this.form.phone.replace(/\D/g, "")}`;

        const response = await api.post(
          "api/register",
          {
            name: this.form.name,
            email: this.form.email,
            phone: fullPhone,
            password: this.form.password,
            password_confirmation: this.form.confirmPassword,
          },
          { withCredentials: true }
        );

        // Ukáž success message
        this.showSuccessMessage = true;

        // Počkaj 3 sekundy a presmeruj na verification notice
        setTimeout(() => {
          this.$router.push({
            name: "verify-email",
            query: { email: this.form.email },
          });
        }, 3000);
      } catch (err) {
        console.error("Registration error:", err);
        this.registerError =
          err.response?.data?.message ||
          err.response?.data?.errors?.email?.[0] ||
          err.response?.data?.errors?.phone?.[0] ||
          "Chyba pri registrácii.";
      } finally {
        this.isSubmitting = false;
      }
    },
  },
};
</script>
