<template>
  <div class="min-h-screen bg-white dark:bg-gray-900">
    <div class="max-w-4xl mx-auto px-6 py-12">
      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="text-center">
          <svg
            class="animate-spin h-12 w-12 mx-auto text-indigo-600"
            fill="none"
            viewBox="0 0 24 24"
          >
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
          <p class="mt-4 text-gray-600 dark:text-gray-400">Načítavam profil...</p>
        </div>
      </div>

      <!-- Profile Content -->
      <template v-else>
        <!-- Success Message -->
        <div
          v-if="successMessage"
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
            <p class="text-sm text-green-800 dark:text-green-200">{{ successMessage }}</p>
          </div>
        </div>

        <!-- Error Message -->
        <div
          v-if="errorMessage"
          class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4"
        >
          <div class="flex items-center gap-3">
            <svg
              class="w-6 h-6 text-red-600 dark:text-red-400"
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
            <p class="text-sm text-red-800 dark:text-red-200">{{ errorMessage }}</p>
          </div>
        </div>

        <!-- Profile Header -->
        <div
          class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 mb-6"
        >
          <div class="flex items-start gap-6">
            <div class="relative">
              <div
                class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold"
              >
                {{ getInitials(user.name) }}
              </div>
              <input
                ref="avatarInput"
                type="file"
                accept="image/*"
                @change="handleAvatarChange"
                class="hidden"
              />
              <button
                @click="$refs.avatarInput.click()"
                class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-2 shadow-lg transition-colors"
                title="Zmeniť avatar"
              >
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                </svg>
              </button>
            </div>

            <div class="flex-1">
              <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ user.name }}
              </h1>
              <p class="text-gray-600 dark:text-gray-400 mt-1">{{ user.email }}</p>
              <div class="flex gap-2 mt-3">
                <span
                  class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm font-medium rounded-full"
                >
                  {{ formatMemberSince(user.created_at) }}
                </span>
                <span
                  class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-sm font-medium rounded-full"
                >
                  {{ orders.length }} objednávok
                </span>
              </div>
            </div>

            <button
              @click="toggleEditMode"
              class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-medium transition-colors"
            >
              {{ editMode ? "Zrušiť" : "Upraviť profil" }}
            </button>
          </div>
        </div>

        <!-- Profile Details -->
        <div
          class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8"
        >
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
            Osobné údaje
          </h2>

          <form @submit.prevent="saveProfile">
            <div class="space-y-6">
              <!-- Name -->
              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                  Meno a priezvisko *
                </label>
                <input
                  v-model="formData.name"
                  :disabled="!editMode"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed"
                />
              </div>

              <!-- Email -->
              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                  Email *
                </label>
                <input
                  v-model="formData.email"
                  :disabled="!editMode"
                  type="email"
                  required
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed"
                />
                <p v-if="editMode" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  Po zmene emailu budete musieť potvrdiť novú adresu.
                </p>
              </div>

              <!-- Phone -->
              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                  Telefón
                </label>

                <!-- Edit Mode - Country code + input -->
                <div v-if="editMode" class="flex gap-3">
                  <select
                    v-model="phoneCountryCode"
                    class="py-3 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white w-32 focus:ring-2 focus:ring-blue-500"
                  >
                    <option v-for="c in countries" :key="c.code" :value="c.code">
                      {{ c.flag }} {{ c.code }}
                    </option>
                  </select>
                  <input
                    v-model="phoneNumber"
                    @input="formatPhoneInput"
                    type="tel"
                    placeholder="912 345 678"
                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>

                <!-- Display Mode - Formatted phone -->
                <input
                  v-else
                  :value="formattedPhoneDisplay"
                  disabled
                  type="text"
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white disabled:cursor-not-allowed"
                />

                <p
                  v-if="editMode && phoneDigits > 0 && phoneDigits !== 9"
                  class="text-red-500 text-sm mt-1"
                >
                  Telefón musí mať 9 číslic
                </p>
              </div>

              <!-- Address -->
              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                  Adresa
                </label>
                <input
                  v-model="formData.address"
                  :disabled="!editMode"
                  type="text"
                  placeholder="Hlavná 123"
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed"
                />
              </div>

              <!-- City and ZIP -->
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                  >
                    Mesto
                  </label>
                  <input
                    v-model="formData.city"
                    :disabled="!editMode"
                    type="text"
                    placeholder="Bratislava"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed"
                  />
                </div>
                <div>
                  <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                  >
                    PSČ
                  </label>
                  <input
                    v-model="formData.zip"
                    :disabled="!editMode"
                    type="text"
                    placeholder="811 01"
                    @input="formatZip"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed"
                  />
                </div>
              </div>

              <!-- Save Button -->
              <div v-if="editMode" class="flex gap-3 pt-4">
                <button
                  type="submit"
                  :disabled="isSaving"
                  class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                >
                  <svg
                    v-if="isSaving"
                    class="animate-spin h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
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
                  {{ isSaving ? "Ukladám..." : "Uložiť zmeny" }}
                </button>
                <button
                  type="button"
                  @click="cancelEdit"
                  :disabled="isSaving"
                  class="px-6 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Zrušiť
                </button>
              </div>
            </div>
          </form>
        </div>

        <!-- Change Password Section -->
        <div
          class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 mt-6"
        >
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
            Zmena hesla
          </h2>

          <form @submit.prevent="changePassword">
            <div class="space-y-6">
              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                  Súčasné heslo
                </label>
                <input
                  v-model="passwordForm.current_password"
                  type="password"
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                  Nové heslo
                </label>
                <input
                  v-model="passwordForm.password"
                  type="password"
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                  Potvrdiť nové heslo
                </label>
                <input
                  v-model="passwordForm.password_confirmation"
                  type="password"
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <button
                type="submit"
                :disabled="isChangingPassword"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ isChangingPassword ? "Ukladám..." : "Zmeniť heslo" }}
              </button>
            </div>
          </form>
        </div>

        <!-- Order History -->
        <div
          class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 mt-6"
        >
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
              História objednávok
            </h2>
            <router-link
              to="/orders"
              class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium"
            >
              Zobraziť všetky →
            </router-link>
          </div>

          <div v-if="orders.length === 0" class="text-center py-8">
            <svg
              class="w-16 h-16 mx-auto text-gray-400 mb-4"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
              />
            </svg>
            <p class="text-gray-600 dark:text-gray-400">
              Zatiaľ nemáte žiadne objednávky
            </p>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="order in orders.slice(0, 5)"
              :key="order.id"
              class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer"
              @click="goToOrder(order.id)"
            >
              <div class="flex-1">
                <div class="flex items-center gap-3">
                  <span class="font-semibold text-gray-900 dark:text-white"
                    >Objednávka #{{ order.id }}</span
                  >
                  <span
                    class="px-2 py-1 text-xs font-medium rounded"
                    :class="getStatusClass(order.status)"
                  >
                    {{ order.status }}
                  </span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                  {{ formatDate(order.created_at) }}
                </p>
              </div>
              <div class="text-right">
                <p class="font-bold text-gray-900 dark:text-white">
                  {{ formatPrice(order.total) }} €
                </p>
                <p
                  class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 mt-1"
                >
                  Zobraziť detail →
                </p>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import api from "@/api";

export default {
  name: "ProfileView",
  data() {
    return {
      loading: true,
      editMode: false,
      isSaving: false,
      isChangingPassword: false,
      successMessage: "",
      errorMessage: "",
      user: {},
      formData: {
        name: "",
        email: "",
        phone: "",
        address: "",
        city: "",
        zip: "",
      },
      originalFormData: null,
      phoneCountryCode: "+421",
      phoneNumber: "",
      countries: [
        { code: "+421", flag: "🇸🇰", name: "Slovensko" },
        { code: "+420", flag: "🇨🇿", name: "Česko" },
        { code: "+48", flag: "🇵🇱", name: "Poľsko" },
        { code: "+43", flag: "🇦🇹", name: "Rakúsko" },
        { code: "+36", flag: "🇭🇺", name: "Maďarsko" },
      ],
      passwordForm: {
        current_password: "",
        password: "",
        password_confirmation: "",
      },
      orders: [],
    };
  },
  async mounted() {
    window.scrollTo({ top: 0, behavior: "smooth" });
    await this.loadUserData();
  },
  computed: {
    phoneDigits() {
      return this.phoneNumber.replace(/\D/g, "").length;
    },
    formattedPhoneDisplay() {
      if (!this.formData.phone) return "";

      // Rozdeľ country code a číslo
      const match = this.formData.phone.match(/^(\+\d{2,3})(.*)$/);
      if (match) {
        const code = match[1];
        const num = match[2].replace(/\D/g, "");

        // Formátuj číslo XXX XXX XXX
        let formatted = "";
        if (num.length > 0) formatted += num.substring(0, 3);
        if (num.length > 3) formatted += " " + num.substring(3, 6);
        if (num.length > 6) formatted += " " + num.substring(6, 9);

        return `${code} ${formatted}`;
      }

      return this.formData.phone;
    },
  },
  methods: {
    async loadUserData() {
      this.loading = true;
      try {
        const userResponse = await api.get("/api/user", { withCredentials: true });
        this.user = userResponse.data;

        this.formData = {
          name: this.user.name || "",
          email: this.user.email || "",
          phone: this.user.phone || "",
          address: this.user.address || "",
          city: this.user.city || "",
          zip: this.user.zip || "",
        };

        // Parse phone number
        this.parsePhoneNumber(this.formData.phone);

        try {
          const ordersResponse = await api.get("/api/user/orders", {
            withCredentials: true,
          });
          this.orders = ordersResponse.data;
        } catch (err) {
          console.log("Orders endpoint not available");
          this.orders = [];
        }
      } catch (error) {
        console.error("Error loading user data:", error);
        this.errorMessage = "Nepodarilo sa načítať údaje profilu.";

        if (error.response?.status === 401) {
          this.$router.push("/login");
        }
      } finally {
        this.loading = false;
      }
    },
    parsePhoneNumber(phone) {
      if (!phone) {
        this.phoneCountryCode = "+421";
        this.phoneNumber = "";
        return;
      }

      // Hľadaj country code
      const match = phone.match(/^(\+\d{2,3})(.*)$/);
      if (match) {
        this.phoneCountryCode = match[1];
        this.phoneNumber = match[2].replace(/\D/g, "");
        this.formatPhoneInput();
      } else {
        this.phoneCountryCode = "+421";
        this.phoneNumber = phone.replace(/\D/g, "");
        this.formatPhoneInput();
      }
    },
    formatPhoneInput() {
      let num = this.phoneNumber.replace(/\D/g, "").substring(0, 9);
      let parts = [];
      if (num.length > 0) parts.push(num.substring(0, 3));
      if (num.length > 3) parts.push(num.substring(3, 6));
      if (num.length > 6) parts.push(num.substring(6, 9));
      this.phoneNumber = parts.join(" ");
    },
    toggleEditMode() {
      if (this.editMode) {
        this.cancelEdit();
      } else {
        this.editMode = true;
        this.originalFormData = { ...this.formData };
        this.parsePhoneNumber(this.formData.phone);
      }
    },
    cancelEdit() {
      if (this.originalFormData) {
        this.formData = { ...this.originalFormData };
        this.parsePhoneNumber(this.formData.phone);
      }
      this.editMode = false;
      this.originalFormData = null;
      this.errorMessage = "";
    },
    async saveProfile() {
      if (this.isSaving) return;

      // Skontroluj phone validation ak je vyplnený
      if (this.phoneNumber && this.phoneDigits !== 9) {
        this.errorMessage = "Telefón musí mať presne 9 číslic.";
        return;
      }

      this.isSaving = true;
      this.successMessage = "";
      this.errorMessage = "";

      // Spoj country code a číslo
      const fullPhone = this.phoneNumber
        ? `${this.phoneCountryCode}${this.phoneNumber.replace(/\D/g, "")}`
        : "";

      const dataToSave = {
        ...this.formData,
        phone: fullPhone,
      };

      try {
        const response = await api.put("/api/user/profile", dataToSave, {
          withCredentials: true,
        });

        this.user = { ...this.user, ...response.data.user };
        this.formData.phone = fullPhone;

        localStorage.setItem("user", JSON.stringify(this.user));
        window.dispatchEvent(new Event("user-logged-in"));

        this.successMessage = "Profil bol úspešne aktualizovaný!";
        this.editMode = false;
        this.originalFormData = null;

        setTimeout(() => {
          this.successMessage = "";
        }, 5000);
      } catch (error) {
        console.error("Error saving profile:", error);

        if (error.response?.data?.errors) {
          const errors = error.response.data.errors;
          const firstError = Object.values(errors)[0];
          this.errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
        } else {
          this.errorMessage =
            error.response?.data?.message || "Nepodarilo sa uložiť zmeny.";
        }
      } finally {
        this.isSaving = false;
      }
    },
    async changePassword() {
      if (this.isChangingPassword) return;

      // Validácia
      if (
        !this.passwordForm.current_password ||
        !this.passwordForm.password ||
        !this.passwordForm.password_confirmation
      ) {
        this.errorMessage = "Všetky polia hesla sú povinné.";
        return;
      }

      if (this.passwordForm.password !== this.passwordForm.password_confirmation) {
        this.errorMessage = "Nové heslá sa nezhodujú.";
        return;
      }

      if (this.passwordForm.password.length < 8) {
        this.errorMessage = "Heslo musí mať aspoň 8 znakov.";
        return;
      }

      this.isChangingPassword = true;
      this.successMessage = "";
      this.errorMessage = "";

      try {
        await api.put("/api/user/password", this.passwordForm, {
          withCredentials: true,
        });

        this.successMessage = "Heslo bolo úspešne zmenené!";

        // Vyčisti formulár
        this.passwordForm = {
          current_password: "",
          password: "",
          password_confirmation: "",
        };

        // Skry success message po 5 sekundách
        setTimeout(() => {
          this.successMessage = "";
        }, 5000);
      } catch (error) {
        console.error("Error changing password:", error);

        if (error.response?.data?.errors) {
          const errors = error.response.data.errors;
          const firstError = Object.values(errors)[0];
          this.errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
        } else {
          this.errorMessage =
            error.response?.data?.message || "Nepodarilo sa zmeniť heslo.";
        }
      } finally {
        this.isChangingPassword = false;
      }
    },
    handleAvatarChange(event) {
      const file = event.target.files[0];
      if (file) {
        // TODO: Implementovať upload avatara
        console.log("Avatar file:", file);
        alert("Funkcia uploadu avatara bude implementovaná neskôr.");
      }
    },
    getInitials(name) {
      if (!name) return "?";
      const parts = name.split(" ");
      if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
      }
      return name.substring(0, 2).toUpperCase();
    },
    formatMemberSince(date) {
      if (!date) return "Člen od ...";
      const d = new Date(date);
      const year = d.getFullYear();
      const month = d.getMonth() + 1;
      return `Člen od ${month}/${year}`;
    },
    formatDate(date) {
      if (!date) return "";
      const d = new Date(date);
      return d.toLocaleDateString("sk-SK", {
        year: "numeric",
        month: "long",
        day: "numeric",
      });
    },
    formatPrice(price) {
      return parseFloat(price).toFixed(2);
    },
    formatPhone() {
      let num = this.form.phone.replace(/\D/g, "").substring(0, 9);
      let parts = [];
      if (num.length > 0) parts.push(num.substring(0, 3));
      if (num.length > 3) parts.push(num.substring(3, 6));
      if (num.length > 6) parts.push(num.substring(6));
      this.form.phone = parts.join(" ");
    },
    formatZip() {
      // Only allow digits (and optionally auto add a space after third digit, for visual style "XXX XX")
      let num = this.formData.zip.replace(/\D/g, "").substring(0, 5);
      if (num.length > 3) {
        // Insert a space after the third digit, for standard Slovak format
        num = num.slice(0, 3) + " " + num.slice(3);
      }
      this.formData.zip = num;
    },
    getStatusClass(status) {
      const classes = {
        Doručené: "bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200",
        "V preprave": "bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200",
        "Spracováva sa":
          "bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200",
        Zrušené: "bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200",
      };
      return (
        classes[status] || "bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200"
      );
    },
    goToOrder(orderId) {
      this.$router.push(`/orders/${orderId}`);
    },
  },
};
</script>
