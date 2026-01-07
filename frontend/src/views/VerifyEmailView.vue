<template>
  <div
    class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-indigo-950 dark:to-purple-950 flex items-center justify-center px-4 py-12"
  >
    <div class="w-full max-w-md">
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
              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
            />
          </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Overte váš email</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
          Potvrdenie účtu je potrebné pred pokračovaním
        </p>
      </div>

      <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8"
      >
        <!-- Success Message -->
        <div
          v-if="verificationSent"
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
            <p class="text-sm text-green-700 dark:text-green-300">
              Nový overovací link bol odoslaný!
            </p>
          </div>
        </div>

        <div class="text-center mb-6">
          <p class="text-gray-700 dark:text-gray-300 mb-4">
            Ďakujeme za registráciu! Pred začatím, môžete prosím overiť vašu emailovú
            adresu kliknutím na link, ktorý sme vám práve poslali? Ak ste nedostali email,
            radi vám pošleme ďalší.
          </p>

          <p
            class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 rounded-lg p-3 break-all"
          >
            {{ userEmail }}
          </p>
        </div>

        <div class="space-y-3">
          <button
            @click="resendVerification"
            :disabled="isResending || cooldown > 0"
            class="w-full btn-primary-lg"
          >
            <span v-if="isResending">Posielam...</span>
            <span v-else-if="cooldown > 0">Znovu poslať ({{ cooldown }}s)</span>
            <span v-else>Znovu poslať overovací email</span>
          </button>

          <button
            @click="logout"
            class="w-full btn-secondary font-semibold py-3 rounded-lg"
          >
            Odhlásiť sa
          </button>
        </div>

        <div class="mt-6 text-center">
          <p class="text-xs text-gray-500 dark:text-gray-400">
            Skontrolujte aj priečinok so spamom
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api, { setSessionToken } from "@/api";

export default {
  data() {
    return {
      verificationSent: false,
      isResending: false,
      cooldown: 0,
      userEmail: "",
    };
  },
  mounted() {
    // Získaj email z query alebo z API
    this.userEmail = this.$route.query.email || "";

    if (!this.userEmail) {
      this.getUserEmail();
    }
  },
  methods: {
    async getUserEmail() {
      try {
        const response = await api.get("/api/user");
        this.userEmail = response.data.email;
      } catch (err) {
        console.error("Error getting user email:", err);
        this.$router.push("/login");
      }
    },
    async resendVerification() {
      if (this.isResending || this.cooldown > 0) return;

      this.isResending = true;
      this.verificationSent = false;

      try {
        await api.post("/api/email/verification-notification", {});

        this.verificationSent = true;
        this.startCooldown();
      } catch (err) {
        console.error("Error resending verification:", err);
        alert("Chyba pri odosielaní overovacieho emailu.");
      } finally {
        this.isResending = false;
      }
    },
    startCooldown() {
      this.cooldown = 60;
      const interval = setInterval(() => {
        this.cooldown--;
        if (this.cooldown <= 0) {
          clearInterval(interval);
        }
      }, 1000);
    },
    async logout() {
      try {
        await api.post("/api/logout", {});
      } catch (err) {
        console.error("Logout error:", err);
      } finally {
        // Clear tokens from all storage types
        localStorage.removeItem("token");
        setSessionToken(null); // Clear in-memory token
        localStorage.removeItem("user");

        // Emit logout event
        window.dispatchEvent(new Event("user-logged-out"));

        // Redirect to login
        this.$router.push("/login");
      }
    },
  },
};
</script>
