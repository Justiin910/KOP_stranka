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
          ref="messageContainer"
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
          ref="messageContainer"
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
              <!-- Profile Picture or Initials -->
              <div
                v-if="profile_picture_path"
                class="w-24 h-24 rounded-full bg-gray-200 overflow-hidden"
              >
                <img
                  :src="`${profile_picture_path}?v=${profileImageCacheBuster}`"
                  :alt="user.name"
                  class="w-full h-full object-cover"
                  @error="handleImageError"
                />
              </div>
              <div
                v-else
                class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold"
              >
                {{ getInitials(user.name) }}
              </div>
              <input
                ref="avatarInput"
                type="file"
                accept="image/*"
                @change="handleAvatarChange"
                :disabled="isUploading"
                class="hidden"
              />
              <button
                @click="$refs.avatarInput.click()"
                :disabled="isUploading"
                class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-full p-2 shadow-lg transition-colors"
                title="Zmeniť avatar"
              >
                <svg
                  v-if="!isUploading"
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
                <svg v-else class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
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
              </button>
            </div>

            <div class="flex-1">
              <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ user.name }}
              </h1>
              <div class="flex items-center gap-2 mt-1">
                <p class="text-gray-600 dark:text-gray-400">{{ user.email }}</p>
                <span
                  v-if="user.email_verified_at"
                  class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-200"
                >
                  Overené
                </span>
                <span
                  v-else
                  class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full dark:bg-yellow-900 dark:text-yellow-200"
                >
                  Neoverené
                  <button
                    @click="resendVerification"
                    :disabled="verificationSent"
                    class="ml-2 underline text-xs font-medium"
                    :class="{
                      'text-blue-700 dark:text-blue-300 cursor-pointer hover:text-blue-900 dark:hover:text-blue-200': !verificationSent,
                      'text-gray-400 cursor-not-allowed': verificationSent,
                    }"
                    :title="
                      verificationSent
                        ? 'Email už bol odoslaný'
                        : 'Znovu odoslať overovací email'
                    "
                  >
                    {{ verificationSent ? "Odoslané!" : "Znovu odoslať" }}
                  </button>
                </span>
              </div>

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
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Zmena hesla</h2>
            <button
              @click="sendPasswordResetEmail"
              :disabled="isSendingResetLink"
              class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium disabled:opacity-50"
            >
              {{ isSendingResetLink ? "Odosiela sa..." : "Zabudli ste heslo?" }}
            </button>
          </div>

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

        <!-- Danger Zone - Delete Account -->
        <div
          class="bg-red-50 dark:bg-red-900/10 rounded-lg shadow-sm border-2 border-red-200 dark:border-red-800 p-8 mt-6"
        >
          <h2 class="text-xl font-bold text-red-900 dark:text-red-400 mb-2">
            Nebezpečná zóna
          </h2>
          <p class="text-sm text-red-700 dark:text-red-300 mb-6">
            Po zmazaní účtu stratíte prístup ku všetkým vašim údajom a objednávkam. Táto
            akcia je <strong>nevratná</strong>.
          </p>

          <button
            @click="showDeleteConfirmation = true"
            class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
              />
            </svg>
            Zmazať účet
          </button>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
          v-if="showDeleteConfirmation"
          class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
          @click.self="showDeleteConfirmation = false"
        >
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex items-start gap-4 mb-6">
              <div
                class="flex-shrink-0 w-12 h-12 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center"
              >
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
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                  />
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                  Naozaj chcete zmazať účet?
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                  Táto akcia je nevratná. Všetky vaše údaje, objednávky a história budú
                  permanentne zmazané.
                </p>

                <div class="mb-4">
                  <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                  >
                    Pre potvrdenie zadajte svoje heslo:
                  </label>
                  <input
                    v-model="deleteAccountPassword"
                    type="password"
                    placeholder="Vaše heslo"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent"
                  />
                </div>
              </div>
            </div>

            <div class="flex gap-3">
              <button
                @click="deleteAccount"
                :disabled="isDeletingAccount || !deleteAccountPassword"
                class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
              >
                <svg
                  v-if="isDeletingAccount"
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
                {{ isDeletingAccount ? "Mažem..." : "Áno, zmazať účet" }}
              </button>
              <button
                @click="
                  showDeleteConfirmation = false;
                  deleteAccountPassword = '';
                "
                :disabled="isDeletingAccount"
                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-medium transition-colors disabled:opacity-50"
              >
                Zrušiť
              </button>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import api, { setSessionToken } from "@/api";

export default {
  name: "ProfileView",
  data() {
    return {
      loading: true,
      editMode: false,
      isSaving: false,
      verificationSent: false,
      resendTimeout: null,
      isChangingPassword: false,
      isSendingResetLink: false,
      isDeletingAccount: false,
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
      showDeleteConfirmation: false,
      deleteAccountPassword: "",
      isUploading: false,
      showMessage: false,
      profile_picture_path: null,
      profileImageCacheBuster: 0,
    };
  },
  async mounted() {
    window.scrollTo({ top: 0, behavior: "smooth" });

    // Check if user is logged in by trying to get user data
    try {
      await this.loadUserData();
    } catch (error) {
      console.error("Not authenticated, redirecting to login");
      this.$router.push("/login");
    }
  },
  computed: {
    phoneDigits() {
      return this.phoneNumber.replace(/\D/g, "").length;
    },
    formattedPhoneDisplay() {
      if (!this.formData.phone) return "";

      const match = this.formData.phone.match(/^(\+\d{2,3})(.*)$/);
      if (match) {
        const code = match[1];
        const num = match[2].replace(/\D/g, "");

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
    scrollToMessages() {
      this.$nextTick(() => {
        const messageContainer = this.$refs.messageContainer;
        if (messageContainer) {
          messageContainer.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      });
    },
    async loadUserData() {
      this.loading = true;
      try {
        const userResponse = await api.get("/api/user");
        this.user = userResponse.data;

        // Set profile picture path (use backend base URL so browser requests go to backend)
        if (this.user.profile_picture_path) {
          this.profile_picture_path = api.defaults.baseURL.replace(/\/$/, '') + `/api/user/${this.user.id}/profile-picture`;
          this.profileImageCacheBuster++;
        }

        this.formData = {
          name: this.user.name || "",
          email: this.user.email || "",
          phone: this.user.phone || "",
          address: this.user.address || "",
          city: this.user.city || "",
          zip: this.user.zip || "",
        };

        this.parsePhoneNumber(this.formData.phone);

        try {
          const ordersResponse = await api.get("/api/user/orders");
          this.orders = ordersResponse.data;
        } catch (err) {
          console.log("Orders endpoint not available");
          this.orders = [];
        }
      } catch (error) {
        console.error("Error loading user data:", error);
        this.errorMessage = "Nepodarilo sa načítať údaje profilu.";
        throw error; // Re-throw so mounted() can handle redirect
      } finally {
        this.loading = false;
      }
    },
    async resendVerification() {
      if (this.verificationSent) return; // prevent spam
      this.verificationSent = true;
      try {
        await api.post("/api/email/verification-notification", {});
        this.successMessage = "Overovací email bol znovu odoslaný.";
        // Reset "Odoslané" po 8 sekundách
        this.resendTimeout = setTimeout(() => {
          this.verificationSent = false;
        }, 8000);
      } catch (error) {
        this.errorMessage =
          error.response?.data?.message || "Nepodarilo sa odoslať overenie.";
        this.verificationSent = false;
      }
    },
    beforeUnmount() {
      if (this.resendTimeout) clearTimeout(this.resendTimeout);
    },
    parsePhoneNumber(phone) {
      if (!phone) {
        this.phoneCountryCode = "+421";
        this.phoneNumber = "";
        return;
      }

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

      // Client-side plausibility check for email
      if (!this.isValidEmail(this.formData.email)) {
        this.errorMessage = "Neplatný email.";
        this.scrollToMessages();
        return;
      }

      if (this.phoneNumber && this.phoneDigits !== 9) {
        this.errorMessage = "Telefón musí mať presne 9 číslic.";
        return;
      }

      this.isSaving = true;
      this.successMessage = "";
      this.errorMessage = "";

      const fullPhone = this.phoneNumber
        ? `${this.phoneCountryCode}${this.phoneNumber.replace(/\D/g, "")}`
        : "";

      const dataToSave = {
        ...this.formData,
        phone: fullPhone,
      };

      try {
        const response = await api.put("/api/user/profile", dataToSave);

        this.user = { ...this.user, ...response.data.user };
        this.formData.phone = fullPhone;

        localStorage.setItem("user", JSON.stringify(this.user));
        window.dispatchEvent(new Event("user-logged-in"));

        this.successMessage = "Profil bol úspešne aktualizovaný!";
        this.editMode = false;
        this.originalFormData = null;
        this.scrollToMessages();

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
    // Basic client-side plausibility check for email addresses.
    // This is for user feedback only — server enforces stricter checks (RFC/DNS).
    isValidEmail(email) {
      if (!email || typeof email !== "string") return false;
      // Require an @ and a dot in the domain part with at least 2 char TLD
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
      return re.test(email);
    },
    async changePassword() {
      if (this.isChangingPassword) return;

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
        this.scrollToMessages();
        return;
      }

      if (this.passwordForm.password.length < 8) {
        this.errorMessage = "Heslo musí mať aspoň 8 znakov.";
        this.scrollToMessages();
        return;
      }
      if (this.passwordForm.current_password === this.passwordForm.password) {
        this.errorMessage = "Nové heslo nesmie byť rovnaké ako aktuálne heslo.";
        this.scrollToMessages();
        return;
      }

      this.isChangingPassword = true;
      this.successMessage = "";
      this.errorMessage = "";

      try {
        // Laravel Breeze API endpoint pre zmenu hesla
        await api.put("/api/user/password", this.passwordForm);

        this.successMessage = "Heslo bolo úspešne zmenené!";
        this.scrollToMessages();

        this.passwordForm = {
          current_password: "",
          password: "",
          password_confirmation: "",
        };

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
        this.scrollToMessages();
      } finally {
        this.isChangingPassword = false;
      }
    },
    async sendPasswordResetEmail() {
      if (this.isSendingResetLink) return;

      this.isSendingResetLink = true;
      this.successMessage = "";
      this.errorMessage = "";

      try {
        // Laravel Breeze API endpoint pre reset link
        await api.post("/api/forgot-password", {
          email: this.user.email,
        });

        this.successMessage = "Link na resetovanie hesla bol odoslaný na váš email!";
        this.scrollToMessages();

        setTimeout(() => {
          this.successMessage = "";
        }, 10000);
      } catch (error) {
        console.error("Error sending reset link:", error);

        if (error.response?.data?.errors) {
          const errors = error.response.data.errors;
          const firstError = Object.values(errors)[0];
          this.errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
        } else {
          this.errorMessage =
            error.response?.data?.message || "Nepodarilo sa odoslať reset link.";
        }
        this.scrollToMessages();
      } finally {
        this.isSendingResetLink = false;
      }
    },
    async deleteAccount() {
      if (this.isDeletingAccount || !this.deleteAccountPassword) return;

      this.isDeletingAccount = true;
      this.errorMessage = "";

      try {
        // Laravel Breeze API endpoint pre zmazanie účtu
        // Musíš vytvoriť tento endpoint v Laravel Breeze
        await api.delete("/api/user", {
          data: {
            password: this.deleteAccountPassword,
          },
        });

        // Clear tokens on successful delete
        localStorage.removeItem("token");
        setSessionToken(null); // Clear in-memory token
        localStorage.removeItem("user");
        window.dispatchEvent(new Event("user-logged-out"));

        // Zobraz success správu
        alert("Váš účet bol úspešne zmazaný.");

        // Presmeruj na homepage
        this.$router.push("/");

        // Trigger logout event
        window.dispatchEvent(new Event("user-logged-out"));
      } catch (error) {
        console.error("Error deleting account:", error);

        if (error.response?.data?.errors) {
          const errors = error.response.data.errors;
          const firstError = Object.values(errors)[0];
          this.errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
        } else if (error.response?.status === 403) {
          this.errorMessage = "Nesprávne heslo.";
        } else {
          this.errorMessage =
            error.response?.data?.message || "Nepodarilo sa zmazať účet.";
        }

        this.scrollToMessages();
        this.showDeleteConfirmation = false;
        this.deleteAccountPassword = "";
      } finally {
        this.isDeletingAccount = false;
      }
    },
    handleImageError(event) {
      console.error('Failed to load profile picture from:', event.target.src);
      // Fallback to initials if image fails to load
      this.profile_picture_path = null;
    },
    handleAvatarChange(event) {
      const file = event.target.files[0];
      if (!file) return;

      // Validate file
      const validTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
      const maxSize = 5 * 1024 * 1024; // 5MB

      if (!validTypes.includes(file.type)) {
        this.errorMessage =
          "Nepodporovaný typ súboru. Prosím nahrajte JPG, PNG, GIF alebo WebP.";
        this.showMessage = true;
        return;
      }

      if (file.size > maxSize) {
        this.errorMessage = "Súbor je príliš veľký. Maximálna veľkosť je 5MB.";
        this.showMessage = true;
        return;
      }

      // Upload file
      const formData = new FormData();
      formData.append("profile_picture", file);

      this.isUploading = true;

      api
        .post("/api/user/profile-picture", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((response) => {
          // Update user data in localStorage
          if (localStorage.user) {
            const userData = JSON.parse(localStorage.user);
            userData.profile_picture_path = response.data.user.profile_picture_path;
            userData.id = response.data.user.id;
            localStorage.user = JSON.stringify(userData);
          }

          // Update local profile_picture_path to use absolute backend URL
          this.profile_picture_path = api.defaults.baseURL.replace(/\/$/, '') + `/api/user/${response.data.user.id}/profile-picture`;
          this.user.id = response.data.user.id;
          this.profileImageCacheBuster++;

          // Emit event for navbar/sidebar to update (send absolute URL)
          window.dispatchEvent(
            new CustomEvent("profilePictureUpdated", {
              detail: { profile_picture_url: this.profile_picture_path },
            })
          );

          this.successMessage = response.data.message;
          this.showMessage = true;
          this.errorMessage = "";

          // Clear file input
          event.target.value = "";
        })
        .catch((error) => {
          console.error("Error uploading profile picture:", error);
          const errorMsg =
            error.response?.data?.message || "Chyba pri nahravaní fotografie.";
          this.errorMessage = errorMsg;
          this.showMessage = true;
        })
        .finally(() => {
          this.isUploading = false;
        });
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
    formatZip() {
      let num = this.formData.zip.replace(/\D/g, "").substring(0, 5);
      if (num.length > 3) {
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
