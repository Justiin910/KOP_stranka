<template>
  <div class="min-h-screen bg-white dark:bg-gray-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
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
            ></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
          <p class="mt-4 text-gray-600 dark:text-gray-400">{{ $t("profile.loading") }}</p>
        </div>
      </div>

      <!-- Success Message -->
      <div
        v-if="successMessage"
        ref="messageContainer"
        class="mb-6 bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-800 rounded-lg p-4"
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
        ref="messageContainer"
        class="mb-6 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 rounded-lg p-4"
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
        <div class="flex flex-col sm:flex-row sm:items-start gap-6">
          <div class="relative">
            <div
              v-if="showAvatarImage"
              class="w-24 h-24 rounded-full overflow-hidden border-2 border-gray-200 dark:border-gray-700"
            >
              <img
                :src="getAvatarUrl(user.avatar)"
                :alt="user.name"
                @error="onAvatarImageError"
                class="w-full h-full object-cover"
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
              class="hidden"
            />
            <button
              @click="$refs.avatarInput.click()"
              :disabled="isUploadingAvatar"
              class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-full p-2 shadow-lg transition-colors"
              :title="$t('profile.change_avatar')"
            >
              <svg
                v-if="!isUploadingAvatar"
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
              <svg
                v-else
                class="w-4 h-4 animate-spin"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 12a8 8 0 018-8V0c4.418 0 8 3.582 8 8h-2c0-3.316-2.686-6-6-6v2c1.657 0 3 1.343 3 3v3h3a3 3 0 01-3 3v2a5 5 0 005-5h2z"
                />
              </svg>
            </button>
          </div>

          <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
              {{ user.name }}
            </h1>
            <span
              v-if="isElevatedRole(user.role)"
              class="inline-flex items-center px-2 py-0.5 mt-2 text-xs font-semibold rounded-full"
              :class="
                user.role === 'owner'
                  ? 'bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200'
                  : 'bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200'
              "
            >
              {{ getRoleLabel(user.role) }}
            </span>
            <div class="flex items-center gap-2 mt-1">
              <p class="text-gray-600 dark:text-gray-400">{{ user.email }}</p>
              <span
                v-if="user.email_verified_at"
                class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-200"
                >{{ $t("profile.verified") }}</span
              >
              <span
                v-else
                class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full dark:bg-yellow-900 dark:text-yellow-200"
              >
                {{ $t("profile.unverified") }}
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
                      ? $t('profile.verification_sent_title')
                      : $t('profile.resend_verification_title')
                  "
                >
                  {{ verificationSent ? $t("profile.sent") : $t("profile.resend") }}
                </button>
              </span>
            </div>

            <div class="flex flex-wrap gap-2 mt-3">
              <span
                class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm font-medium rounded-full"
                >{{ formatMemberSince(user.created_at) }}</span
              >
              <span
                class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-sm font-medium rounded-full"
                >{{ $t("profile.orders_count", { count: orders.length }) }}</span
              >
              <button
                v-if="user.avatar"
                type="button"
                :disabled="isResettingAvatar"
                class="px-3 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-sm font-medium rounded-full disabled:opacity-50"
                @click="openAvatarResetConfirmation"
              >
                {{
                  isResettingAvatar
                    ? $t("profile.avatar_resetting")
                    : $t("profile.avatar_reset")
                }}
              </button>
            </div>
          </div>

          <button
            @click="toggleEditMode"
            class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-medium transition-colors"
          >
            {{ editMode ? $t("profile.cancel") : $t("profile.edit_profile") }}
          </button>
        </div>
      </div>

      <!-- Mini Tabs -->
      <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-2 mb-6"
      >
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
          <button
            v-for="tab in profileMiniTabs"
            :key="tab.id"
            type="button"
            @click="activeProfileMiniTab = tab.id"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
            :class="
              activeProfileMiniTab === tab.id
                ? 'bg-indigo-600 text-white'
                : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
            "
          >
            {{ tab.label }}
          </button>
        </div>
      </div>

      <!-- Profile Details -->
      <div
        v-if="activeProfileMiniTab === 'overview'"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8"
      >
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
          {{ $t("profile.personal_info") }}
        </h2>

        <form @submit.prevent="saveProfile" novalidate>
          <div class="space-y-6">
            <!-- Name -->
            <div>
              <label
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >{{ $t("profile.name_label") }}
                <span v-if="editMode" class="text-red-600 dark:text-red-400"
                  >*</span
                ></label
              >
              <input
                v-model="formData.name"
                :disabled="!editMode"
                type="text"
                :aria-invalid="!!fieldErrors.name"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed"
              />
              <Transition name="slideDown">
                <p
                  v-if="fieldErrors.name"
                  class="text-sm text-red-600 mt-2 flex items-center gap-2 bg-red-50 dark:bg-red-900/20 px-3 py-2 rounded-md border border-red-200 dark:border-red-800"
                >
                  <svg
                    class="w-4 h-4 flex-shrink-0"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                      clip-rule="evenodd"
                    ></path>
                  </svg>
                  {{ fieldErrors.name }}
                </p>
              </Transition>
            </div>

            <!-- Email -->
            <div>
              <label
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >{{ $t("profile.email_label") }}
                <span v-if="editMode" class="text-red-600 dark:text-red-400"
                  >*</span
                ></label
              >
              <input
                v-model="formData.email"
                :disabled="!editMode"
                type="email"
                :aria-invalid="!!fieldErrors.email"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed"
              />
              <Transition name="slideDown">
                <p
                  v-if="fieldErrors.email"
                  class="text-sm text-red-600 mt-2 flex items-center gap-2 bg-red-50 dark:bg-red-900/20 px-3 py-2 rounded-md border border-red-200 dark:border-red-800"
                >
                  <svg
                    class="w-4 h-4 flex-shrink-0"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                      clip-rule="evenodd"
                    ></path>
                  </svg>
                  {{ fieldErrors.email }}
                </p>
              </Transition>
              <p v-if="editMode" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                {{ $t("profile.email_change_note") }}
              </p>
            </div>

            <!-- Phone -->
            <div>
              <label
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >{{ $t("profile.phone_label") }}
                <span v-if="editMode" class="text-red-600 dark:text-red-400"
                  >*</span
                ></label
              >

              <!-- Edit Mode - Country code + input -->
              <div v-if="editMode" class="flex gap-3">
                <CountryCodeSelect
                  v-model="phoneCountryCode"
                  :options="phoneCountryOptions"
                  button-class="py-3"
                  class="w-32"
                />
                <input
                  v-model="phoneNumber"
                  @input="formatPhoneInput"
                  type="tel"
                  :placeholder="$t('profile.phone_placeholder')"
                  class="flex px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
                {{ $t("profile.validation.phone_digits") }}
              </p>
            </div>

            <!-- Address -->
            <div>
              <label
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >{{ $t("profile.address_label") }}</label
              >
              <input
                v-model="formData.address"
                :disabled="!editMode"
                type="text"
                :placeholder="$t('profile.address_placeholder')"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed"
              />
            </div>

            <!-- City and ZIP -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                  >{{ $t("profile.city_label") }}</label
                >
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
                  >{{ $t("profile.zip_label") }}</label
                >
                <input
                  v-model="formData.zip"
                  :disabled="!editMode"
                  type="text"
                  :placeholder="$t('profile.zip_placeholder')"
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
                {{ isSaving ? $t("profile.saving") : $t("profile.save_changes") }}
              </button>
              <button
                type="button"
                @click="cancelEdit"
                :disabled="isSaving"
                class="px-6 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ $t("profile.cancel") }}
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Security: Two-Factor Authentication -->
      <div
        v-if="activeProfileMiniTab === 'security'"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 mt-6"
      >
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
          {{ $t("profile.two_factor_title") }}
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-5">
          {{ $t("profile.two_factor_desc") }}
        </p>

        <div class="flex items-center justify-between gap-4">
          <div>
            <p class="text-sm font-medium text-gray-900 dark:text-white">
              {{ twoFactorEnabled ? $t("profile.two_factor_on") : $t("profile.two_factor_off") }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {{ $t("profile.two_factor_email_channel") }}
            </p>
          </div>

          <button
            type="button"
            role="switch"
            :aria-checked="twoFactorEnabled"
            :disabled="isUpdatingTwoFactor"
            @click="toggleTwoFactor(!twoFactorEnabled)"
            class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            :class="twoFactorEnabled ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600'"
          >
            <span
              class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform"
              :class="twoFactorEnabled ? 'translate-x-7' : 'translate-x-1'"
            ></span>
          </button>
        </div>

        <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
          {{
            isUpdatingTwoFactor
              ? $t("profile.saving")
              : twoFactorEnabled
              ? $t("profile.two_factor_disable")
              : $t("profile.two_factor_enable")
          }}
        </p>

        <p
          v-if="!user.email_verified_at"
          class="text-xs text-amber-700 dark:text-amber-300 mt-4"
        >
          {{ $t("profile.two_factor_requires_verified") }}
        </p>
      </div>

      <!-- Saved Payment Card -->
      <div
        v-if="activeProfileMiniTab === 'payment'"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 mt-6"
      >
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            {{ $t("profile.saved_card_title") }}
          </h2>
          <span
            v-if="savedCard"
            class="text-xs px-2 py-1 rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200"
          >
            {{ $t("profile.saved_card_exists") }}
          </span>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ $t("pages.checkout.payment.card_name") }}
            </label>
            <input
              v-model="paymentCardForm.cardholder_name"
              type="text"
              :disabled="!editMode"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ $t("pages.checkout.payment.card_number") }}
            </label>
            <input
              v-model="paymentCardForm.card_number"
              type="text"
              inputmode="numeric"
              maxlength="19"
              @input="formatPaymentCardNumber"
              :disabled="!editMode"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ $t("pages.checkout.payment.card_expiry") }}
            </label>
            <input
              v-model="paymentCardForm.expiry"
              type="text"
              maxlength="5"
              @input="formatPaymentCardExpiry"
              :disabled="!editMode"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed"
              placeholder="MM/YY"
            />
          </div>

          <div v-if="savedCard" class="text-sm text-gray-600 dark:text-gray-400">
            {{ $t("profile.saved_card_masked", { number: savedCard.masked_number }) }}
          </div>

          <div v-if="editMode" class="flex gap-3 pt-1">
            <button
              type="button"
              :disabled="!editMode || isSavingPaymentCard"
              @click="savePaymentCard"
              class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium disabled:opacity-50"
            >
              {{ isSavingPaymentCard ? $t("profile.saving") : $t("profile.saved_card_save") }}
            </button>
            <button
              v-if="savedCard"
              type="button"
              :disabled="!editMode || isDeletingPaymentCard"
              @click="deletePaymentCard"
              class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium disabled:opacity-50"
            >
              {{
                isDeletingPaymentCard
                  ? $t("profile.deleting")
                  : $t("profile.saved_card_delete")
              }}
            </button>
          </div>
        </div>
      </div>

      <!-- Pending email change 2FA modal -->
      <div
        v-if="pendingEmailChange"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="closeEmailChangeModal"
      >
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-xl w-full p-8">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
            {{ $t("profile.email_change_verify_title") }}
          </h2>
          <p class="text-base leading-7 text-gray-700 dark:text-gray-300 mb-5">
            {{ $t("profile.email_change_verify_desc", { email: pendingEmailTarget }) }}
          </p>

          <div class="grid grid-cols-6 gap-3" @paste.prevent="onPendingEmailPaste">
            <input
              v-for="(digit, idx) in pendingEmailDigits"
              :key="idx"
              :ref="`pendingEmailDigit${idx}`"
              v-model="pendingEmailDigits[idx]"
              type="text"
              inputmode="numeric"
              maxlength="1"
              :disabled="isConfirmingEmailChange || isResendingEmailChangeCode"
              :autocomplete="idx === 0 ? 'one-time-code' : 'off'"
              class="w-full h-14 text-center text-2xl font-semibold border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed"
              @input="onPendingEmailDigitInput(idx, $event)"
              @keydown="onPendingEmailDigitKeydown(idx, $event)"
            />
          </div>

          <div class="mt-3 mb-1">
            <button
              type="button"
              @click="resendEmailChangeCode"
              :disabled="isConfirmingEmailChange || isResendingEmailChangeCode"
              class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 disabled:opacity-50"
            >
              {{ isResendingEmailChangeCode ? $t("profile.sending") : $t("profile.email_change_verify_resend") }}
            </button>
          </div>

          <div class="flex gap-3 mt-5">
            <button
              type="button"
              @click="closeEmailChangeModal"
              :disabled="isConfirmingEmailChange || isResendingEmailChangeCode"
              class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-medium disabled:opacity-50"
            >
              {{ $t("common.cancel") }}
            </button>
            <button
              type="button"
              :disabled="isConfirmingEmailChange || isResendingEmailChangeCode || pendingEmailCode.length !== 6"
              @click="confirmEmailChangeCode"
              class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium disabled:opacity-50"
            >
              {{
                isConfirmingEmailChange
                  ? $t("profile.saving")
                  : $t("profile.email_change_verify_confirm")
              }}
            </button>
          </div>
        </div>
      </div>

      <!-- Change Password Section -->
      <div
        v-if="activeProfileMiniTab === 'security'"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 mt-6"
      >
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            {{ $t("profile.change_password_title") }}
          </h2>
          <button
            @click="sendPasswordResetEmail"
            :disabled="isSendingResetLink"
            class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium disabled:opacity-50"
          >
            {{
              isSendingResetLink ? $t("profile.sending") : $t("profile.forgot_password")
            }}
          </button>
        </div>

        <form @submit.prevent="changePassword">
          <div class="space-y-6">
            <div>
              <label
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >{{ $t("profile.current_password_label") }}</label
              >
              <input
                v-model="passwordForm.current_password"
                type="password"
                @input="onCurrentPasswordInput"
                :class="[
                  'w-full px-4 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent',
                  passwordFieldErrors.current_password
                    ? 'border-red-400 dark:border-red-500'
                    : 'border-gray-300 dark:border-gray-600',
                ]"
              />
              <p
                v-if="passwordFieldErrors.current_password"
                class="text-sm text-red-600 dark:text-red-400 mt-2"
              >
                {{ passwordFieldErrors.current_password }}
              </p>
            </div>
            <div>
              <label
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >{{ $t("profile.new_password_label") }}</label
              >
              <input
                v-model="passwordForm.password"
                type="password"
                @input="onNewPasswordInput"
                :class="[
                  'w-full px-4 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent',
                  passwordFieldErrors.password
                    ? 'border-red-400 dark:border-red-500'
                    : 'border-gray-300 dark:border-gray-600',
                ]"
              />
              <p
                v-if="passwordFieldErrors.password"
                class="text-sm text-red-600 dark:text-red-400 mt-2"
              >
                {{ passwordFieldErrors.password }}
              </p>

              <div
                class="mt-3 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4"
              >
                <p class="text-sm font-medium text-blue-900 dark:text-blue-400 mb-3">
                  {{ $t("reset.requirements_title") }}
                </p>
                <ul class="space-y-2">
                  <li class="flex items-center gap-2 text-sm text-blue-800 dark:text-blue-300">
                    <svg
                      class="w-4 h-4"
                      :class="{
                        'text-green-600 dark:text-green-400':
                          passwordForm.password.length >= 8,
                      }"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    {{ $t("reset.requirements.min_length") }}
                  </li>
                  <li class="flex items-center gap-2 text-sm text-blue-800 dark:text-blue-300">
                    <svg
                      class="w-4 h-4"
                      :class="{
                        'text-green-600 dark:text-green-400': /[a-z]/.test(
                          passwordForm.password
                        ),
                      }"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    {{ $t("reset.requirements.lowercase") }}
                  </li>
                  <li class="flex items-center gap-2 text-sm text-blue-800 dark:text-blue-300">
                    <svg
                      class="w-4 h-4"
                      :class="{
                        'text-green-600 dark:text-green-400': /[A-Z]/.test(
                          passwordForm.password
                        ),
                      }"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    {{ $t("reset.requirements.uppercase") }}
                  </li>
                  <li class="flex items-center gap-2 text-sm text-blue-800 dark:text-blue-300">
                    <svg
                      class="w-4 h-4"
                      :class="{
                        'text-green-600 dark:text-green-400': /[0-9]/.test(
                          passwordForm.password
                        ),
                      }"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    {{ $t("reset.requirements.digits") }}
                  </li>
                </ul>
              </div>
            </div>
            <div>
              <label
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >{{ $t("profile.confirm_password_label") }}</label
              >
              <input
                v-model="passwordForm.password_confirmation"
                type="password"
                @input="onConfirmPasswordInput"
                :class="[
                  'w-full px-4 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent',
                  passwordFieldErrors.password_confirmation
                    ? 'border-red-400 dark:border-red-500'
                    : 'border-gray-300 dark:border-gray-600',
                ]"
              />
              <p
                v-if="passwordFieldErrors.password_confirmation"
                class="text-sm text-red-600 dark:text-red-400 mt-2"
              >
                {{ passwordFieldErrors.password_confirmation }}
              </p>
            </div>
            <button
              type="submit"
              :disabled="isChangingPassword"
              class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{
                isChangingPassword
                  ? $t("profile.saving")
                  : $t("profile.change_password_button")
              }}
            </button>
          </div>
        </form>
      </div>

      <!-- Order History -->
      <div
        v-if="activeProfileMiniTab === 'overview'"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 mt-6"
      >
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            {{ $t("profile.order_history") }}
          </h2>
          <router-link
            to="/orders"
            class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium"
            >{{ $t("profile.view_all") }}</router-link
          >
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
          <p class="text-gray-600 dark:text-gray-400">{{ $t("profile.no_orders") }}</p>
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
                <span class="font-semibold text-gray-900 dark:text-white">{{
                  $t("profile.order_number", { id: order.id })
                }}</span>
                <span
                  class="px-2 py-1 text-xs font-medium rounded"
                  :class="getStatusClass(order.status)"
                  >{{ order.status }}</span
                >
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
                {{ $t("profile.view_detail") }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Danger Zone - Delete Account -->
      <div
        v-if="activeProfileMiniTab === 'overview'"
        class="bg-red-50 dark:bg-red-900/10 rounded-lg shadow-sm border-2 border-red-200 dark:border-red-800 p-8 mt-6"
      >
        <h2 class="text-xl font-bold text-red-900 dark:text-red-400 mb-2">
          {{ $t("profile.danger_zone_title") }}
        </h2>
        <p class="text-sm text-red-700 dark:text-red-300 mb-6">
          {{ $t("profile.danger_zone_desc") }}
          <strong class="font-semibold">{{
            $t("profile.danger_zone_desc_emphasis")
          }}</strong>
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
          {{ $t("profile.delete_confirm_button") }}
        </button>
      </div>

      <!-- Avatar Crop Modal -->
      <div
        v-if="showAvatarCropModal"
        class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4"
      >
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full p-6">
          <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
            {{ $t("profile.avatar_crop_title") }}
          </h3>

          <div class="flex justify-center mb-4">
            <div
              v-if="avatarCropIsGif"
              class="w-72 h-72 rounded-full overflow-hidden border-2 border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700"
            >
              <img
                ref="avatarCropImage"
                :src="avatarCropPreviewUrl"
                alt="avatar-crop-preview"
                class="w-full h-full object-cover"
                draggable="false"
                @load="onAvatarCropImageLoad"
              />
            </div>

            <div
              v-else
              ref="avatarCropStage"
              class="w-72 h-72 rounded-xl overflow-hidden border-2 border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 relative cursor-grab active:cursor-grabbing"
              @wheel.prevent="onAvatarCropWheel"
              @mousedown.prevent="startAvatarDrag"
            >
              <img
                ref="avatarCropImage"
                :src="avatarCropPreviewUrl"
                alt="avatar-crop-preview"
                class="absolute select-none max-w-none"
                draggable="false"
                @load="onAvatarCropImageLoad"
                :style="avatarCropImageStyle"
              />
              <div class="absolute inset-0 pointer-events-none">
                <div class="absolute inset-0 border border-black/20 dark:border-white/10"></div>
                <div class="absolute inset-3 rounded-full border-[3px] border-white/95 shadow-[0_0_0_999px_rgba(0,0,0,0.28)]"></div>
              </div>
            </div>
          </div>

          <div v-if="!avatarCropIsGif" class="space-y-3">
            <div>
              <div class="flex items-center justify-between text-sm text-gray-700 dark:text-gray-300">
                <span>{{ $t("profile.avatar_zoom") }}</span>
                <span>{{ avatarCropZoom.toFixed(1) }}x</span>
              </div>
              <input
                v-model.number="avatarCropZoom"
                type="range"
                min="1"
                max="3"
                step="0.1"
                class="w-full"
                @input="setAvatarZoom(avatarCropZoom)"
              />
              <div class="flex gap-2 justify-center">
                <button
                  type="button"
                  class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md"
                  @click="decreaseAvatarZoom"
                >
                  {{ $t("profile.avatar_zoom_out") }}
                </button>
                <button
                  type="button"
                  class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md"
                  @click="increaseAvatarZoom"
                >
                  {{ $t("profile.avatar_zoom_in") }}
                </button>
              </div>
            </div>
          </div>

          <div class="flex gap-3 mt-6">
            <button
              type="button"
              class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-medium"
              :disabled="isCroppingAvatar"
              @click="closeAvatarCropModal"
            >
              {{ $t("profile.cancel") }}
            </button>
            <button
              type="button"
              class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium disabled:opacity-50"
              :disabled="isCroppingAvatar"
              @click="confirmAvatarCrop"
            >
              {{
                isCroppingAvatar
                  ? $t("profile.avatar_processing")
                  : $t("profile.avatar_confirm")
              }}
            </button>
          </div>
        </div>
      </div>

      <!-- Avatar Reset Confirmation Modal -->
      <div
        v-if="showAvatarResetConfirmation"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="closeAvatarResetConfirmation"
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
                  d="M13 16h-1v-4h-1m1-4h.01M12 22a10 10 0 100-20 10 10 0 000 20z"
                />
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                {{ $t("profile.avatar_reset_confirm_title") }}
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t("profile.avatar_reset_confirm_desc") }}
              </p>
            </div>
          </div>

          <div class="flex gap-3">
            <button
              @click="closeAvatarResetConfirmation"
              :disabled="isResettingAvatar"
              class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-medium transition-colors disabled:opacity-50"
            >
              {{ $t("profile.cancel") }}
            </button>
            <button
              @click="resetAvatarToDefault"
              :disabled="isResettingAvatar"
              class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <svg
                v-if="isResettingAvatar"
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
                />
              </svg>
              {{
                isResettingAvatar
                  ? $t("profile.avatar_resetting")
                  : $t("profile.avatar_reset_confirm_button")
              }}
            </button>
          </div>
        </div>
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
                {{ $t("profile.delete_confirm_title") }}
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                {{ $t("profile.delete_confirm_desc") }}
              </p>

              <div class="mb-4">
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                  >{{ $t("profile.confirm_delete_password_label") }}</label
                >
                <input
                  v-model="deleteAccountPassword"
                  type="password"
                  :placeholder="$t('profile.password_placeholder')"
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
                />
              </svg>
              {{
                isDeletingAccount
                  ? $t("profile.deleting")
                  : $t("profile.delete_confirm_button")
              }}
            </button>
            <button
              @click="(showDeleteConfirmation = false), (deleteAccountPassword = '')"
              :disabled="isDeletingAccount"
              class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-medium transition-colors disabled:opacity-50"
            >
              {{ $t("profile.cancel") }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api, { setLocale, setSessionToken } from "@/api";
import CountryCodeSelect from "@/components/CountryCodeSelect.vue";
import { PHONE_COUNTRY_OPTIONS } from "@/utils/localeCountryData";

export default {
  name: "ProfileView",
  components: {
    CountryCodeSelect,
  },
  data() {
    return {
      loading: true,
      editMode: false,
      isSaving: false,
      isUploadingAvatar: false,
      isCroppingAvatar: false,
      isResettingAvatar: false,
      verificationSent: false,
      resendTimeout: null,
      isChangingPassword: false,
      isSendingResetLink: false,
      isDeletingAccount: false,
      isUpdatingTwoFactor: false,
      isConfirmingEmailChange: false,
      isResendingEmailChangeCode: false,
      isSavingPaymentCard: false,
      isDeletingPaymentCard: false,
      avatarLoadFailed: false,
      successMessage: "",
      errorMessage: "",
      showAvatarResetConfirmation: false,
      activeProfileMiniTab: "overview",
      twoFactorEnabled: false,
      pendingEmailChange: false,
      pendingEmailTarget: "",
      pendingEmailCode: "",
      pendingEmailDigits: ["", "", "", "", "", ""],
      savedCard: null,
      paymentCardForm: {
        cardholder_name: "",
        card_number: "",
        expiry: "",
      },
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
      phoneCountryOptions: PHONE_COUNTRY_OPTIONS,
      passwordForm: {
        current_password: "",
        password: "",
        password_confirmation: "",
      },
      fieldErrors: {
        name: null,
        email: null,
        phone: null,
      },
      passwordFieldErrors: {
        current_password: null,
        password: null,
        password_confirmation: null,
      },
      orders: [],
      showDeleteConfirmation: false,
      deleteAccountPassword: "",
      showAvatarCropModal: false,
      avatarCropPreviewUrl: "",
      avatarCropZoom: 1,
      avatarCropOffsetX: 0,
      avatarCropOffsetY: 0,
      avatarCropStageSize: 288,
      avatarCropBaseWidth: 288,
      avatarCropBaseHeight: 288,
      avatarCropNaturalWidth: 0,
      avatarCropNaturalHeight: 0,
      avatarCropDragging: false,
      avatarDragStartX: 0,
      avatarDragStartY: 0,
      avatarDragInitialOffsetX: 0,
      avatarDragInitialOffsetY: 0,
      avatarCropFileName: "",
      avatarCropOriginalFile: null,
      avatarCropIsGif: false,
      avatarCropMimeType: "image/jpeg",
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
    avatarCropImageStyle() {
      const width = this.avatarCropBaseWidth * this.avatarCropZoom;
      const height = this.avatarCropBaseHeight * this.avatarCropZoom;
      const left = (this.avatarCropStageSize - width) / 2 + this.avatarCropOffsetX;
      const top = (this.avatarCropStageSize - height) / 2 + this.avatarCropOffsetY;
      return {
        width: `${width}px`,
        height: `${height}px`,
        left: `${left}px`,
        top: `${top}px`,
      };
    },
    profileMiniTabs() {
      return [
        { id: "overview", label: this.$t("profile.tabs.overview") },
        { id: "security", label: this.$t("profile.tabs.security") },
        { id: "payment", label: this.$t("profile.tabs.payment") },
      ];
    },
    showAvatarImage() {
      return Boolean(this.user?.avatar) && !this.avatarLoadFailed;
    },
  },
  beforeUnmount() {
    this.removeAvatarDragListeners();
    if (this.avatarCropPreviewUrl) {
      URL.revokeObjectURL(this.avatarCropPreviewUrl);
    }
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
        this.avatarLoadFailed = false;

        const preferredLanguage = String(this.user?.language || "").toLowerCase();
        if (["sk", "en"].includes(preferredLanguage)) {
          setLocale(preferredLanguage);
          window.dispatchEvent(
            new CustomEvent("language-changed", {
              detail: { language: preferredLanguage },
            })
          );
        }

        this.twoFactorEnabled = !!this.user.two_factor_enabled;

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
          const ordersResponse = await api.get("/api/orders");
          this.orders = Array.isArray(ordersResponse.data) ? ordersResponse.data : [];
        } catch (err) {
          console.log("Orders endpoint not available");
          this.orders = [];
        }

        await this.loadSavedPaymentCard();
      } catch (error) {
        console.error("Error loading user data:", error);
        this.errorMessage = this.$t("profile.load_failed");
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
        this.successMessage = this.$t("profile.verification_resent");
        // Reset "Odoslané" po 8 sekundách
        this.resendTimeout = setTimeout(() => {
          this.verificationSent = false;
        }, 8000);
      } catch (error) {
        this.errorMessage =
          error.response?.data?.message || this.$t("profile.verification_failed");
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

      // Clear prior field errors
      this.fieldErrors = {
        name: null,
        email: null,
        subject: null,
        message: null,
        phone: null,
      };

      // Required fields: name and email always, phone when editing
      if (!this.formData.name || !this.formData.name.trim()) {
        this.fieldErrors.name = this.$t("profile.validation.required_field", {
          field: this.$t("profile.name_label"),
        });
        this.errorMessage = this.fieldErrors.name;
        this.scrollToMessages();
        return;
      }

      if (!this.formData.email || !this.formData.email.trim()) {
        this.fieldErrors.email = this.$t("profile.validation.required_field", {
          field: this.$t("profile.email_label"),
        });
        this.errorMessage = this.fieldErrors.email;
        this.scrollToMessages();
        return;
      }

      // Client-side plausibility check for email
      if (!this.isValidEmail(this.formData.email)) {
        this.fieldErrors.email = this.$t("profile.validation.email_invalid");
        this.errorMessage = this.fieldErrors.email;
        this.scrollToMessages();
        return;
      }

      if (this.editMode) {
        if (!this.phoneNumber || !this.phoneNumber.trim()) {
          this.fieldErrors.phone = this.$t("profile.validation.phone_required");
          this.errorMessage = this.fieldErrors.phone;
          this.scrollToMessages();
          return;
        }
        if (this.phoneDigits !== 9) {
          this.fieldErrors.phone = this.$t("profile.validation.phone_digits");
          this.errorMessage = this.fieldErrors.phone;
          this.scrollToMessages();
          return;
        }
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

        if (response.data?.requires_email_change_2fa) {
          this.pendingEmailChange = true;
          this.pendingEmailTarget = response.data.pending_email || this.formData.email;
          this.resetPendingEmailDigits();
          this.successMessage =
            response.data.message || this.$t("profile.email_change_verify_title");
          this.scrollToMessages();
          this.$nextTick(() => this.focusPendingEmailDigit(0));
          return;
        }

        this.user = { ...this.user, ...response.data.user };
        this.formData.phone = fullPhone;

        localStorage.setItem("user", JSON.stringify(this.user));
        window.dispatchEvent(new Event("user-logged-in"));

        this.successMessage = this.$t("profile.save_success");
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
            error.response?.data?.message || this.$t("profile.save_failed");
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
    resetPasswordFieldErrors() {
      this.passwordFieldErrors = {
        current_password: null,
        password: null,
        password_confirmation: null,
      };
    },
    onCurrentPasswordInput() {
      this.passwordFieldErrors.current_password = null;
      this.errorMessage = "";
    },
    onNewPasswordInput() {
      this.passwordFieldErrors.password = null;
      if (this.passwordForm.password_confirmation) {
        this.passwordFieldErrors.password_confirmation = null;
      }
      this.errorMessage = "";
    },
    onConfirmPasswordInput() {
      this.passwordFieldErrors.password_confirmation = null;
      this.errorMessage = "";
    },
    passwordMeetsRequirements(value) {
      if (!value) return false;

      return (
        value.length >= 8 &&
        /[a-z]/.test(value) &&
        /[A-Z]/.test(value) &&
        /[0-9]/.test(value)
      );
    },
    validatePasswordForm() {
      this.resetPasswordFieldErrors();

      let isValid = true;
      const currentPassword = this.passwordForm.current_password || "";
      const newPassword = this.passwordForm.password || "";
      const confirmation = this.passwordForm.password_confirmation || "";

      if (!currentPassword.trim()) {
        this.passwordFieldErrors.current_password = this.$t("profile.validation.required_field", {
          field: this.$t("profile.current_password_label"),
        });
        isValid = false;
      }

      if (!newPassword.trim()) {
        this.passwordFieldErrors.password = this.$t("profile.validation.required_field", {
          field: this.$t("profile.new_password_label"),
        });
        isValid = false;
      } else if (!this.passwordMeetsRequirements(newPassword)) {
        this.passwordFieldErrors.password = this.$t("profile.validation.password_requirements");
        isValid = false;
      }

      if (!confirmation.trim()) {
        this.passwordFieldErrors.password_confirmation = this.$t(
          "profile.validation.required_field",
          {
            field: this.$t("profile.confirm_password_label"),
          }
        );
        isValid = false;
      } else if (newPassword !== confirmation) {
        this.passwordFieldErrors.password_confirmation = this.$t(
          "profile.validation.passwords_mismatch"
        );
        isValid = false;
      }

      if (currentPassword && newPassword && currentPassword === newPassword) {
        this.passwordFieldErrors.password = this.$t("profile.validation.password_same_as_current");
        isValid = false;
      }

      return isValid;
    },
    async changePassword() {
      if (this.isChangingPassword) return;

      this.errorMessage = "";

      if (!this.validatePasswordForm()) {
        this.scrollToMessages();
        return;
      }

      this.isChangingPassword = true;
      this.successMessage = "";
      this.errorMessage = "";

      try {
        // Laravel Breeze API endpoint pre zmenu hesla
        await api.put("/api/user/password", this.passwordForm);

        this.successMessage = this.$t("profile.password_changed");
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
          this.resetPasswordFieldErrors();

          if (errors.current_password?.[0]) {
            this.passwordFieldErrors.current_password = errors.current_password[0];
          }

          if (errors.password?.[0]) {
            this.passwordFieldErrors.password = errors.password[0];
          }

          if (errors.password_confirmation?.[0]) {
            this.passwordFieldErrors.password_confirmation = errors.password_confirmation[0];
          }

          if (
            !this.passwordFieldErrors.current_password &&
            !this.passwordFieldErrors.password &&
            !this.passwordFieldErrors.password_confirmation
          ) {
            const firstError = Object.values(errors)[0];
            this.errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
          }
        } else {
          this.errorMessage =
            error.response?.data?.message || this.$t("profile.password_change_failed");
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

        this.successMessage = this.$t("profile.reset_link_sent");
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
            error.response?.data?.message || this.$t("profile.reset_link_failed");
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
        alert(this.$t("profile.account_deleted"));

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
          this.errorMessage = this.$t("profile.invalid_password");
        } else {
          this.errorMessage =
            error.response?.data?.message || this.$t("profile.delete_failed");
        }

        this.scrollToMessages();
        this.showDeleteConfirmation = false;
        this.deleteAccountPassword = "";
      } finally {
        this.isDeletingAccount = false;
      }
    },
    handleAvatarChange(event) {
      const file = event.target.files[0];
      if (!file) return;

      // Validate file
      const validTypes = ["image/jpeg", "image/png", "image/jpg", "image/gif"];
      const maxSize = 12 * 1024 * 1024; // 12MB

      if (!validTypes.includes(file.type)) {
        this.errorMessage = this.$t("profile.validation.avatar_invalid_format");
        this.scrollToMessages();
        return;
      }

      if (file.size > maxSize) {
        this.errorMessage = this.$t("profile.validation.avatar_too_large");
        this.scrollToMessages();
        return;
      }

      this.openAvatarCropModal(file);
    },
    onAvatarImageError() {
      this.avatarLoadFailed = true;
    },
    openAvatarCropModal(file) {
      if (this.avatarCropPreviewUrl) {
        URL.revokeObjectURL(this.avatarCropPreviewUrl);
      }
      this.avatarCropFileName = file.name || "avatar";
      this.avatarCropOriginalFile = file;
      this.avatarCropIsGif = file.type === "image/gif";
      this.avatarCropMimeType = file.type || "image/jpeg";
      this.avatarCropZoom = 1;
      this.avatarCropOffsetX = 0;
      this.avatarCropOffsetY = 0;
      this.avatarCropNaturalWidth = 0;
      this.avatarCropNaturalHeight = 0;
      this.avatarCropBaseWidth = this.avatarCropStageSize;
      this.avatarCropBaseHeight = this.avatarCropStageSize;
      this.avatarCropPreviewUrl = URL.createObjectURL(file);
      this.showAvatarCropModal = true;
    },
    closeAvatarCropModal() {
      this.removeAvatarDragListeners();
      this.showAvatarCropModal = false;
      this.avatarCropZoom = 1;
      this.avatarCropOffsetX = 0;
      this.avatarCropOffsetY = 0;
      this.avatarCropNaturalWidth = 0;
      this.avatarCropNaturalHeight = 0;
      this.avatarCropBaseWidth = this.avatarCropStageSize;
      this.avatarCropBaseHeight = this.avatarCropStageSize;
      this.avatarCropFileName = "";
      this.avatarCropOriginalFile = null;
      this.avatarCropIsGif = false;
      this.avatarCropMimeType = "image/jpeg";
      if (this.avatarCropPreviewUrl) {
        URL.revokeObjectURL(this.avatarCropPreviewUrl);
      }
      this.avatarCropPreviewUrl = "";
      if (this.$refs.avatarInput) {
        this.$refs.avatarInput.value = "";
      }
    },
    onAvatarCropImageLoad(event) {
      const img = event?.target;
      if (!img || !img.naturalWidth || !img.naturalHeight) return;

      const naturalWidth = img.naturalWidth;
      const naturalHeight = img.naturalHeight;
      this.avatarCropNaturalWidth = naturalWidth;
      this.avatarCropNaturalHeight = naturalHeight;

      const ratio = naturalWidth / naturalHeight;
      if (ratio >= 1) {
        this.avatarCropBaseWidth = this.avatarCropStageSize;
        this.avatarCropBaseHeight = this.avatarCropStageSize / ratio;
      } else {
        this.avatarCropBaseHeight = this.avatarCropStageSize;
        this.avatarCropBaseWidth = this.avatarCropStageSize * ratio;
      }

      this.clampAvatarCropOffset();
    },
    setAvatarZoom(value) {
      if (this.avatarCropIsGif) return;
      const normalized = Math.min(3, Math.max(1, Math.round(value * 10) / 10));
      this.avatarCropZoom = normalized;
      this.clampAvatarCropOffset();
    },
    onAvatarCropWheel(event) {
      if (this.avatarCropIsGif) return;
      const step = event.deltaY < 0 ? 0.1 : -0.1;
      this.setAvatarZoom(this.avatarCropZoom + step);
    },
    startAvatarDrag(event) {
      if (this.avatarCropIsGif) return;
      if (!this.showAvatarCropModal) return;
      this.avatarCropDragging = true;
      this.avatarDragStartX = event.clientX;
      this.avatarDragStartY = event.clientY;
      this.avatarDragInitialOffsetX = this.avatarCropOffsetX;
      this.avatarDragInitialOffsetY = this.avatarCropOffsetY;
      this.addAvatarDragListeners();
    },
    onAvatarDragMove(event) {
      if (!this.avatarCropDragging) return;
      const deltaX = event.clientX - this.avatarDragStartX;
      const deltaY = event.clientY - this.avatarDragStartY;
      this.avatarCropOffsetX = this.avatarDragInitialOffsetX + deltaX;
      this.avatarCropOffsetY = this.avatarDragInitialOffsetY + deltaY;
      this.clampAvatarCropOffset();
    },
    stopAvatarDrag() {
      this.avatarCropDragging = false;
      this.removeAvatarDragListeners();
    },
    addAvatarDragListeners() {
      window.addEventListener("mousemove", this.onAvatarDragMove);
      window.addEventListener("mouseup", this.stopAvatarDrag);
      window.addEventListener("mouseleave", this.stopAvatarDrag);
    },
    removeAvatarDragListeners() {
      window.removeEventListener("mousemove", this.onAvatarDragMove);
      window.removeEventListener("mouseup", this.stopAvatarDrag);
      window.removeEventListener("mouseleave", this.stopAvatarDrag);
    },
    clampAvatarCropOffset() {
      const renderedWidth = this.avatarCropBaseWidth * this.avatarCropZoom;
      const renderedHeight = this.avatarCropBaseHeight * this.avatarCropZoom;
      const maxOffsetX = Math.max(0, (renderedWidth - this.avatarCropStageSize) / 2);
      const maxOffsetY = Math.max(0, (renderedHeight - this.avatarCropStageSize) / 2);
      this.avatarCropOffsetX = Math.min(
        maxOffsetX,
        Math.max(-maxOffsetX, this.avatarCropOffsetX)
      );
      this.avatarCropOffsetY = Math.min(
        maxOffsetY,
        Math.max(-maxOffsetY, this.avatarCropOffsetY)
      );
    },
    increaseAvatarZoom() {
      if (this.avatarCropIsGif) return;
      this.setAvatarZoom(this.avatarCropZoom + 0.1);
    },
    decreaseAvatarZoom() {
      if (this.avatarCropIsGif) return;
      this.setAvatarZoom(this.avatarCropZoom - 0.1);
    },
    calculateCropRectangle(image) {
      const renderedWidth = this.avatarCropBaseWidth * this.avatarCropZoom;
      const renderedHeight = this.avatarCropBaseHeight * this.avatarCropZoom;

      let sourceX =
        ((this.avatarCropStageSize / 2 - this.avatarCropOffsetX) - renderedWidth / 2) /
        renderedWidth *
        image.naturalWidth;
      let sourceY =
        ((this.avatarCropStageSize / 2 - this.avatarCropOffsetY) - renderedHeight / 2) /
        renderedHeight *
        image.naturalHeight;
      let sourceWidth =
        (this.avatarCropStageSize / renderedWidth) * image.naturalWidth;
      let sourceHeight =
        (this.avatarCropStageSize / renderedHeight) * image.naturalHeight;

      sourceX = Math.max(0, Math.min(image.naturalWidth - sourceWidth, sourceX));
      sourceY = Math.max(0, Math.min(image.naturalHeight - sourceHeight, sourceY));
      sourceWidth = Math.max(1, Math.min(image.naturalWidth, sourceWidth));
      sourceHeight = Math.max(1, Math.min(image.naturalHeight, sourceHeight));

      return {
        x: Math.round(sourceX),
        y: Math.round(sourceY),
        width: Math.round(sourceWidth),
        height: Math.round(sourceHeight),
      };
    },
    async confirmAvatarCrop() {
      if (this.isCroppingAvatar) return;

      if (this.avatarCropIsGif && this.avatarCropOriginalFile) {
        this.isCroppingAvatar = true;
        try {
          await this.uploadAvatar(this.avatarCropOriginalFile);
          this.closeAvatarCropModal();
        } catch (error) {
          console.error("Error uploading GIF avatar:", error);
        } finally {
          this.isCroppingAvatar = false;
        }
        return;
      }

      const image = this.$refs.avatarCropImage;
      if (!image || !image.naturalWidth || !image.naturalHeight) {
        this.errorMessage = this.$t("profile.avatar_crop_error");
        this.scrollToMessages();
        return;
      }

      this.isCroppingAvatar = true;
      try {
        const cropRect = this.calculateCropRectangle(image);

        const canvas = document.createElement("canvas");
        canvas.width = 512;
        canvas.height = 512;
        const ctx = canvas.getContext("2d");

        if (!ctx) {
          throw new Error("Unable to create crop canvas context");
        }

        ctx.drawImage(
          image,
          cropRect.x,
          cropRect.y,
          cropRect.width,
          cropRect.height,
          0,
          0,
          canvas.width,
          canvas.height
        );

        const outputMime = this.avatarCropMimeType === "image/png" ? "image/png" : "image/jpeg";
        const blob = await new Promise((resolve) => {
          canvas.toBlob((result) => resolve(result), outputMime, 0.92);
        });

        if (!blob) {
          throw new Error("Crop image generation failed");
        }

        const ext = outputMime === "image/png" ? "png" : "jpg";
        const baseName = this.avatarCropFileName.replace(/\.[^/.]+$/, "") || "avatar";
        const croppedFile = new File([blob], `${baseName}-cropped.${ext}`, {
          type: outputMime,
        });

        await this.uploadAvatar(croppedFile);
        this.closeAvatarCropModal();
      } catch (error) {
        console.error("Error cropping avatar:", error);
        this.errorMessage = this.$t("profile.avatar_crop_error");
        this.scrollToMessages();
      } finally {
        this.isCroppingAvatar = false;
      }
    },
    openAvatarResetConfirmation() {
      if (this.isResettingAvatar) return;
      this.showAvatarResetConfirmation = true;
    },
    closeAvatarResetConfirmation() {
      if (this.isResettingAvatar) return;
      this.showAvatarResetConfirmation = false;
    },
    async resetAvatarToDefault() {
      if (this.isResettingAvatar) return;
      this.isResettingAvatar = true;
      this.errorMessage = "";
      this.successMessage = "";

      try {
        const response = await api.delete("/api/user/avatar");
        this.user = response.data.user;
        this.avatarLoadFailed = false;
        this.showAvatarResetConfirmation = false;
        this.successMessage = response.data.message || this.$t("profile.avatar_reset_success");
        this.scrollToMessages();
      } catch (error) {
        console.error("Error resetting avatar:", error);
        this.errorMessage =
          error.response?.data?.message || this.$t("profile.avatar_reset_error");
        this.scrollToMessages();
      } finally {
        this.isResettingAvatar = false;
      }
    },
    async uploadAvatar(file, cropData = null) {
      this.isUploadingAvatar = true;
      this.errorMessage = "";
      this.successMessage = "";

      const formData = new FormData();
      formData.append("avatar", file);
      if (cropData) {
        Object.entries(cropData).forEach(([key, value]) => {
          formData.append(key, String(value));
        });
      }

      try {
        const response = await api.post("api/user/avatar", formData, {
          transformRequest: [(data) => data],
        });

        // Update user data
        this.user = response.data.user;
        this.avatarLoadFailed = false;
        this.successMessage = response.data.message;
        this.scrollToMessages();

        setTimeout(() => {
          this.successMessage = "";
        }, 3000);
      } catch (error) {
        console.error("Error uploading avatar:", error);

        if (error.response?.data?.message) {
          this.errorMessage = error.response.data.message;
        } else if (error.response?.data?.errors) {
          const errors = error.response.data.errors;
          const firstError = Object.values(errors)[0];
          this.errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
        } else {
          this.errorMessage = this.$t("profile.avatar_upload_error");
        }
        this.scrollToMessages();
      } finally {
        this.isUploadingAvatar = false;
        // Reset file input
        if (this.$refs.avatarInput) {
          this.$refs.avatarInput.value = "";
        }
      }
    },
    async toggleTwoFactor(enabled) {
      if (this.isUpdatingTwoFactor) return;

      this.isUpdatingTwoFactor = true;
      this.errorMessage = "";

      try {
        const response = await api.put("/api/user/2fa", { enabled });
        this.twoFactorEnabled = !!response.data.two_factor_enabled;
        this.user.two_factor_enabled = this.twoFactorEnabled;
        this.successMessage = response.data.message;
        this.scrollToMessages();
      } catch (error) {
        this.errorMessage =
          error.response?.data?.message || this.$t("profile.two_factor_toggle_failed");
        this.scrollToMessages();
      } finally {
        this.isUpdatingTwoFactor = false;
      }
    },
    async confirmEmailChangeCode() {
      if (this.isConfirmingEmailChange || this.pendingEmailCode.length !== 6) return;

      this.isConfirmingEmailChange = true;
      this.errorMessage = "";

      try {
        const response = await api.post("/api/user/profile/confirm-email-change", {
          code: this.pendingEmailCode,
        });

        this.user = { ...this.user, ...response.data.user };
        this.formData.email = this.user.email;
        localStorage.setItem("user", JSON.stringify(this.user));
        this.pendingEmailChange = false;
        this.pendingEmailTarget = "";
        this.pendingEmailCode = "";
        this.successMessage = response.data.message;
        this.scrollToMessages();
      } catch (error) {
        this.errorMessage =
          error.response?.data?.message || this.$t("profile.email_change_verify_failed");
        this.scrollToMessages();
      } finally {
        this.isConfirmingEmailChange = false;
      }
    },
    closeEmailChangeModal() {
      if (this.isConfirmingEmailChange) return;
      this.pendingEmailChange = false;
      this.resetPendingEmailDigits();
    },
    async resendEmailChangeCode() {
      if (this.isResendingEmailChangeCode || this.isConfirmingEmailChange) return;

      this.isResendingEmailChangeCode = true;
      this.errorMessage = "";

      const fullPhone = this.phoneNumber
        ? `${this.phoneCountryCode}${this.phoneNumber.replace(/\D/g, "")}`
        : "";

      const dataToSave = {
        ...this.formData,
        phone: fullPhone,
        email: this.pendingEmailTarget || this.formData.email,
        resend_email_change_2fa: true,
      };

      try {
        const response = await api.put("/api/user/profile", dataToSave);

        if (response.data?.requires_email_change_2fa) {
          this.pendingEmailTarget = response.data.pending_email || dataToSave.email;
          this.resetPendingEmailDigits();
          this.successMessage =
            response.data.message || this.$t("profile.email_change_verify_title");
          this.scrollToMessages();
          this.$nextTick(() => this.focusPendingEmailDigit(0));
          return;
        }

        this.errorMessage = this.$t("profile.email_change_verify_failed");
      } catch (error) {
        this.errorMessage =
          error.response?.data?.message || this.$t("profile.email_change_verify_failed");
        this.scrollToMessages();
      } finally {
        this.isResendingEmailChangeCode = false;
      }
    },
    onPendingEmailDigitInput(index, event) {
      const value = (event.target.value || "").replace(/\D/g, "").slice(-1);
      this.pendingEmailDigits[index] = value;
      this.syncPendingEmailCode();

      if (value && index < this.pendingEmailDigits.length - 1) {
        this.$nextTick(() => this.focusPendingEmailDigit(index + 1));
      }
    },
    onPendingEmailDigitKeydown(index, event) {
      if (event.key !== "Backspace") return;

      if (this.pendingEmailDigits[index]) {
        event.preventDefault();
        this.pendingEmailDigits[index] = "";
        this.syncPendingEmailCode();
        this.$nextTick(() => this.focusPendingEmailDigit(index));
        return;
      }

      if (index > 0) {
        event.preventDefault();
        this.pendingEmailDigits[index - 1] = "";
        this.syncPendingEmailCode();
        this.$nextTick(() => this.focusPendingEmailDigit(index - 1));
      }
    },
    onPendingEmailPaste(event) {
      const pasted = (event.clipboardData?.getData("text") || "")
        .replace(/\D/g, "")
        .slice(0, 6);
      if (!pasted) return;

      for (let i = 0; i < 6; i += 1) {
        this.pendingEmailDigits[i] = pasted[i] || "";
      }
      this.syncPendingEmailCode();
      const nextIndex = Math.min(pasted.length, 5);
      this.$nextTick(() => this.focusPendingEmailDigit(nextIndex));
    },
    focusPendingEmailDigit(index) {
      const ref = this.$refs[`pendingEmailDigit${index}`];
      const input = Array.isArray(ref) ? ref[0] : ref;
      if (input && typeof input.focus === "function") {
        input.focus();
        if (typeof input.select === "function") input.select();
      }
    },
    syncPendingEmailCode() {
      this.pendingEmailCode = this.pendingEmailDigits.join("");
    },
    resetPendingEmailDigits() {
      this.pendingEmailDigits = ["", "", "", "", "", ""];
      this.pendingEmailCode = "";
    },
    async loadSavedPaymentCard() {
      try {
        const response = await api.get("/api/user/payment-card");
        this.savedCard = response.data?.card || null;
        if (this.savedCard) {
          this.paymentCardForm.cardholder_name = this.savedCard.cardholder_name || "";
          this.paymentCardForm.card_number = this.savedCard.card_number || "";
          const year = String(this.savedCard.expiry_year || "").slice(-2);
          const month = String(this.savedCard.expiry_month || "").padStart(2, "0");
          this.paymentCardForm.expiry = `${month}/${year}`;
          this.formatPaymentCardNumber();
          this.formatPaymentCardExpiry();
        } else {
          this.paymentCardForm = {
            cardholder_name: "",
            card_number: "",
            expiry: "",
          };
        }
      } catch (error) {
        this.savedCard = null;
      }
    },
    formatPaymentCardNumber() {
      this.paymentCardForm.card_number = this.paymentCardForm.card_number
        .replace(/\D/g, "")
        .substring(0, 19)
        .replace(/(.{4})/g, "$1 ")
        .trim();
    },
    formatPaymentCardExpiry() {
      let digits = this.paymentCardForm.expiry.replace(/\D/g, "").substring(0, 4);
      if (digits.length <= 2) {
        this.paymentCardForm.expiry = digits;
        return;
      }
      const month = Math.min(12, Math.max(1, parseInt(digits.substring(0, 2), 10) || 1));
      const year = digits.substring(2, 4);
      this.paymentCardForm.expiry = `${String(month).padStart(2, "0")}/${year}`;
    },
    async savePaymentCard() {
      if (!this.editMode) return;
      if (this.isSavingPaymentCard) return;
      this.isSavingPaymentCard = true;
      this.errorMessage = "";

      try {
        const response = await api.post("/api/user/payment-card", this.paymentCardForm);
        this.savedCard = response.data.card;
        this.paymentCardForm.cardholder_name = this.savedCard?.cardholder_name || "";
        this.paymentCardForm.card_number = this.savedCard.card_number || "";
        const year = String(this.savedCard?.expiry_year || "").slice(-2);
        const month = String(this.savedCard?.expiry_month || "").padStart(2, "0");
        this.paymentCardForm.expiry = `${month}/${year}`;
        this.formatPaymentCardNumber();
        this.formatPaymentCardExpiry();
        this.successMessage = response.data.message;
        this.scrollToMessages();
      } catch (error) {
        this.errorMessage =
          error.response?.data?.message || this.$t("profile.saved_card_save_failed");
        this.scrollToMessages();
      } finally {
        this.isSavingPaymentCard = false;
      }
    },
    async deletePaymentCard() {
      if (!this.editMode) return;
      if (this.isDeletingPaymentCard) return;
      this.isDeletingPaymentCard = true;
      this.errorMessage = "";

      try {
        const response = await api.delete("/api/user/payment-card");
        this.savedCard = null;
        this.paymentCardForm = {
          cardholder_name: "",
          card_number: "",
          expiry: "",
        };
        this.successMessage = response.data.message;
        this.scrollToMessages();
      } catch (error) {
        this.errorMessage =
          error.response?.data?.message || this.$t("profile.saved_card_delete_failed");
        this.scrollToMessages();
      } finally {
        this.isDeletingPaymentCard = false;
      }
    },
    isElevatedRole(role) {
      return role === "admin" || role === "owner";
    },
    getRoleLabel(role) {
      const key = `roles.${role}`;
      const translated = this.$t(key);
      return translated === key ? String(role || "").toUpperCase() : translated;
    },
    getAvatarUrl(avatar) {
      if (!avatar) return "";
      // If it's already a full URL, return it
      if (avatar.startsWith("http")) return avatar;
      // Otherwise, construct the storage URL
      return `${
        import.meta.env.VITE_API_URL || "https://backend.techstore.lznet.work"
      }/storage/${avatar}`;
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
      if (!date) return this.$t("profile.member_since_unknown");
      const d = new Date(date);
      const year = d.getFullYear();
      const month = d.getMonth() + 1;
      return this.$t("profile.member_since", { month, year });
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
