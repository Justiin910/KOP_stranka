<template>
  <div class="flex bg-white dark:bg-gray-900 min-h-screen">
    <!-- SIDEBAR -->
    <aside
      class="w-64 h-screen bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800"
    >
      <SideBarComponent />
    </aside>

    <main class="flex-1 p-8">
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="text-center">
          <div
            class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"
          ></div>
          <p class="mt-4 text-gray-600 dark:text-gray-300">{{ t("product.loading") }}</p>
        </div>
      </div>

      <div v-else-if="error" class="flex items-center justify-center py-20">
        <div class="text-center">
          <p class="text-red-600 dark:text-red-400 font-semibold">{{ error }}</p>
          <router-link to="/" class="mt-4 inline-block text-indigo-600 hover:underline">{{
            t("product.back_home")
          }}</router-link>
        </div>
      </div>

      <div v-else>
        <!-- PRODUCT HEADER -->
        <nav
          class="text-sm text-gray-600 dark:text-gray-300 mb-4 flex items-center gap-2 flex-wrap"
        >
          <router-link to="/" class="hover:underline">{{ t("nav.home") }}</router-link>
          <span>/</span>
          <router-link
            :to="`/category/${product.category}`"
            class="hover:underline capitalize"
            >{{ getCategoryLabel(product.category) }}</router-link
          >
          <span>/</span>
          <span class="text-gray-900 dark:text-gray-100 font-semibold">{{
            product.title || product.name
          }}</span>
        </nav>

        <div class="border-t border-gray-200 dark:border-gray-800 mb-8"></div>

        <div class="max-w-6xl grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
          <div class="flex flex-col items-center">
            <img
              :src="product.image"
              :alt="product.title || product.name"
              class="w-full max-w-md rounded-lg shadow-md"
            />
            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
              {{ t("product.product_code") }} {{ product.id }}
            </p>
          </div>

          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">
              {{ product.title || product.name }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
              {{ translatedDescription || product.description }}
            </p>
            <p
              v-if="!translatedDescription && product.description"
              class="text-xs text-gray-500 dark:text-gray-400 mb-4"
            >
              Prekladá sa...
            </p>

            <div class="mb-6">
              <div class="text-4xl font-bold text-indigo-600">
                {{ formatPrice(calculateCurrentPrice()) }} €
              </div>
              <div
                v-if="product.oldPrice"
                class="text-sm line-through text-gray-500 dark:text-gray-400"
              >
                {{ formatPrice(product.oldPrice) }} €
              </div>
              <!--<div v-if="product.monthlyPrice" class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ t('product.monthly_price', { price: product.monthlyPrice }) }}</div> !-->
            </div>

            <div class="mb-6">
              <span
                :class="
                  product.stock > 0
                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                    : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                "
                class="inline-block px-3 py-1 rounded-full text-sm font-medium"
                >{{
                  product.stock > 0
                    ? t("product.in_stock_units", { count: product.stock })
                    : t("product.out_of_stock")
                }}</span
              >
            </div>

            <div class="mb-8 flex items-center gap-2">
              <div class="flex items-center gap-1">
                <span class="text-2xl font-semibold text-gray-900 dark:text-white">{{
                  parseFloat(product.rating).toFixed(1)
                }}</span>
                <span class="text-2xl text-yellow-400">★</span>
              </div>
              <span class="text-sm text-gray-600 dark:text-gray-400">{{
                t("product.reviews_count", product.reviews)
              }}</span>
            </div>

            <!-- VARIANTS SELECTION -->
            <div
              v-if="product.variants && Object.keys(product.variants).length > 0"
              class="mb-8 space-y-4 border-t border-gray-200 dark:border-gray-800 pt-6"
            >
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                {{ t("product.select_options") || "Select Options" }}
              </h3>
              <div
                v-for="(options, variantKey) in product.variants"
                :key="variantKey"
                class="space-y-2"
              >
                <label
                  class="block text-sm font-medium text-gray-900 dark:text-white capitalize"
                >
                  {{ variantKey }}
                </label>
                <div class="flex gap-2 flex-wrap">
                  <button
                    v-for="option in options"
                    :key="option"
                    @click="selectVariant(variantKey, option)"
                    :class="[
                      selectedVariants[variantKey] === option
                        ? 'bg-indigo-600 text-white border-indigo-600'
                        : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600 hover:border-indigo-600 dark:hover:border-indigo-600',
                    ]"
                    class="px-4 py-2 border-2 rounded-lg font-medium transition-colors cursor-pointer"
                  >
                    {{ option }}
                  </button>
                </div>
              </div>
            </div>

            <button
              @click="addToCart"
              :disabled="
                product.stock === 0 || (product.variants && !areAllVariantsSelected())
              "
              class="w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition disabled:opacity-50 cursor-pointer"
            >
              {{ t("product.add_to_cart") }}
            </button>
            <p
              v-if="product.variants && !areAllVariantsSelected()"
              class="text-sm text-red-600  dark:text-red-400 mt-2"
            >
              {{
                t("product.select_all_variants") || "Please select all available options"
              }}
            </p>
          </div>
        </div>

        <!-- SPECS -->
        <div class="mb-12">
          <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">
            {{ t("product.specs") }}
          </h2>
          <div
            v-if="product.specs"
            class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg text-gray-700 dark:text-gray-300 space-y-2 whitespace-pre-line"
          >
            {{ product.specs }}
          </div>
          <p v-else class="text-gray-500 dark:text-gray-400">
            {{ t("product.no_specs") }}
          </p>
        </div>

        <!-- WRITE REVIEW -->
        <div class="mb-12 pb-12 border-b border-gray-200 dark:border-gray-800">
          <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">
            {{ t("product.write_review") }}
          </h2>

          <div
            v-if="!user"
            class="text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 p-4 rounded-lg"
          >
            {{ t("product.login_to_review") }}
            <router-link
              to="/login"
              class="text-indigo-600 font-semibold hover:underline"
              >{{ t("product.login_link") }}</router-link
            >.
          </div>

          <div v-else class="max-w-2xl">
            <div class="mb-4">
              <label
                class="block text-sm font-medium text-gray-900 dark:text-white mb-2"
                >{{ t("product.rating_label") }}</label
              >
              <div class="flex items-center gap-2">
                <div
                  v-for="n in 5"
                  :key="n"
                  class="relative w-8 h-8 cursor-pointer"
                  @mousemove="onStarHover($event, n)"
                  @mouseleave="onStarLeave"
                  @click="onStarClick($event, n)"
                >
                  <svg
                    class="w-8 h-8 text-gray-300 absolute inset-0"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.174c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.97c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.84-.197-1.54-1.118l1.287-3.97a1 1 0 00-.364-1.118L2.05 9.397c-.783-.57-.38-1.81.588-1.81h4.174a1 1 0 00.95-.69l1.287-3.97z"
                    />
                  </svg>
                  <div
                    class="absolute inset-0 overflow-hidden pointer-events-none"
                    :style="{ width: getFillWidth(n) + '%' }"
                  >
                    <svg
                      class="w-8 h-8 text-yellow-400"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.174c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.97c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.84-.197-1.54-1.118l1.287-3.97a1 1 0 00-.364-1.118L2.05 9.397c-.783-.57-.38-1.81.588-1.81h4.174a1 1 0 00.95-.69l1.287-3.97z"
                      />
                    </svg>
                  </div>
                </div>
                <span
                  v-if="newReview.rating"
                  class="ml-2 text-lg font-semibold text-gray-900 dark:text-white"
                  >{{ newReview.rating.toFixed(1) }}
                  {{ t("product.rating_out_of") }}</span
                >
              </div>
            </div>

            <div class="mb-4">
              <label
                class="block text-sm font-medium text-gray-900 dark:text-white mb-2"
                >{{ t("product.comment_label") }}</label
              >
              <textarea
                v-model="newReview.comment"
                rows="4"
                class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                :placeholder="t('product.comment_placeholder')"
              ></textarea>
            </div>

            <div class="flex gap-3">
              <button
                @click="submitReview"
                :disabled="submittingReview || newReview.rating === 0"
                class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{
                  submittingReview ? t("product.submitting") : t("product.submit_review")
                }}
              </button>
            </div>
            <p v-if="reviewError" class="text-red-600 dark:text-red-400 mt-3">
              {{ reviewError }}
            </p>
          </div>
        </div>

        <!-- LATEST REVIEWS -->
        <div v-if="product.latest_reviews && product.latest_reviews.length" class="mb-6">
          <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">
            {{ t("product.latest_reviews") }}
          </h2>
          <div class="space-y-4">
            <div
              v-for="rev in product.latest_reviews"
              :key="rev.id"
              class="p-5 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800"
            >
              <div v-if="editingReviewId !== rev.id">
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700">
                      <img
                        v-if="rev.user_avatar"
                        :src="getAvatarUrl(rev.user_avatar)"
                        :alt="rev.user_name || t('product.anonymous_user')"
                        class="w-full h-full object-cover"
                      />
                      <div
                        v-else
                        class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center text-xs font-semibold"
                      >
                        {{ getInitials(rev.user_name || t("product.anonymous_user")) }}
                      </div>
                    </div>
                    <div>
                      <div class="font-semibold text-gray-900 dark:text-white">
                        {{ rev.user_name || t("product.anonymous_user") }}
                      </div>
                      <div class="text-xs text-gray-600 dark:text-gray-400">
                        {{
                          new Date(rev.created_at).toLocaleDateString(
                            this.$i18n && this.$i18n.locale
                              ? String(this.$i18n.locale)
                              : "sk-SK" === "sk"
                              ? "sk-SK"
                              : "en-US"
                          )
                        }}
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center gap-3">
                    <div class="text-lg font-semibold text-yellow-500">
                      {{ parseFloat(rev.rating).toFixed(1) }} ★
                    </div>
                    <div v-if="canDeleteReview(rev)" class="flex gap-2">
                      <button
                        v-if="isReviewOwner(rev)"
                        @click="startEditReview(rev)"
                        class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium"
                      >
                        {{ t("product.edit") }}
                      </button>
                      <button
                        @click="promptDeleteReview(rev.id)"
                        class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium"
                      >
                        {{ t("product.delete") }}
                      </button>
                    </div>
                  </div>
                </div>
                <p class="text-gray-700 dark:text-gray-300 italic">
                  {{ translatedComments[rev.id] || rev.comment }}
                </p>
                <p
                  v-if="!translatedComments[rev.id]"
                  class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                >
                  Prekladá sa...
                </p>

                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                  <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ t("product.review_discussion") }}
                  </h4>

                  <div
                    v-if="!isReviewCommentOpen(rev.id) && !isReplyingTo(rev.id)"
                    class="mt-3 flex justify-end"
                  >
                    <button
                      type="button"
                      @click="openReviewCommentComposer(rev.id)"
                      class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium"
                    >
                      {{ t("product.open_review_comment_form") }}
                    </button>
                  </div>

                  <div
                    v-if="flattenReviewComments(rev.comments || []).length"
                    class="space-y-3 mt-3"
                  >
                    <div
                      v-for="item in flattenReviewComments(rev.comments || [])"
                      :key="item.id"
                      class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-3"
                      :style="{ marginLeft: `${Math.min(item.level, 4) * 16}px` }"
                    >
                      <div class="flex items-start gap-3">
                        <div
                          class="w-8 h-8 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 shrink-0"
                        >
                          <img
                            v-if="item.user_avatar"
                            :src="getAvatarUrl(item.user_avatar)"
                            :alt="item.user_name || t('product.anonymous_user')"
                            class="w-full h-full object-cover"
                          />
                          <div
                            v-else
                            class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center text-[10px] font-semibold"
                          >
                            {{ getInitials(item.user_name || t("product.anonymous_user")) }}
                          </div>
                        </div>

                        <div class="min-w-0 flex-1">
                          <div class="flex items-center gap-2">
                            <p class="text-xs font-semibold text-gray-900 dark:text-white">
                              {{ item.user_name || t("product.anonymous_user") }}
                            </p>
                            <span class="text-[11px] text-gray-500 dark:text-gray-400">
                              {{
                                new Date(item.created_at).toLocaleDateString(
                                  this.$i18n && this.$i18n.locale
                                    ? String(this.$i18n.locale)
                                    : "sk-SK" === "sk"
                                    ? "sk-SK"
                                    : "en-US"
                                )
                              }}
                            </span>
                          </div>
                          <template v-if="!isEditingReviewComment(rev.id, item.id)">
                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-1 whitespace-pre-line">
                              {{ translatedReviewDiscussionComments[item.id] || item.comment }}
                            </p>

                            <div
                              v-if="user"
                              class="mt-2 flex items-center gap-3 flex-wrap"
                            >
                              <button
                                @click="setReplyTarget(rev.id, item.id, item.user_name)"
                                class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium"
                              >
                                {{ t("product.reply") }}
                              </button>
                              <button
                                v-if="canEditReviewComment(item)"
                                @click="startEditReviewComment(rev.id, item)"
                                class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium"
                              >
                                {{ t("product.edit") }}
                              </button>
                              <button
                                v-if="canDeleteReviewComment(item)"
                                @click="deleteReviewComment(rev.id, item.id)"
                                :disabled="isReviewCommentDeleting(item.id)"
                                class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                              >
                                {{
                                  isReviewCommentDeleting(item.id)
                                    ? t("common.deleting")
                                    : t("product.delete")
                                }}
                              </button>
                            </div>
                          </template>

                          <div v-else class="mt-2 space-y-2">
                            <textarea
                              v-model="editingReviewCommentText"
                              :ref="(el) => setReviewCommentEditTextareaRef(item.id, el)"
                              rows="2"
                              class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                              :placeholder="t('product.review_comment_placeholder')"
                              :disabled="isReviewCommentUpdating(item.id)"
                            ></textarea>

                            <div class="flex items-center justify-end gap-2">
                              <button
                                type="button"
                                @click="cancelEditReviewComment"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg text-sm font-medium transition"
                              >
                                {{ t("common.cancel") }}
                              </button>
                              <button
                                type="button"
                                @click="submitEditReviewComment(rev.id, item.id)"
                                :disabled="
                                  isReviewCommentUpdating(item.id) ||
                                  !editingReviewCommentText.trim().length
                                "
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed"
                              >
                                {{
                                  isReviewCommentUpdating(item.id)
                                    ? t("product.saving")
                                    : t("product.save_changes")
                                }}
                              </button>
                            </div>
                          </div>

                          <div
                            v-if="isReplyingToComment(rev.id, item.id) && !isEditingReviewComment(rev.id, item.id)"
                            class="mt-3 space-y-2"
                          >
                            <div
                              class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400"
                            >
                              <span>
                                {{ t("product.replying_to", { name: getReplyTargetName(rev.id) }) }}
                              </span>
                            </div>

                            <textarea
                              v-model="reviewCommentForms[rev.id]"
                              :ref="(el) => setReviewCommentTextareaRef(rev.id, el, item.id)"
                              rows="2"
                              class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                              :placeholder="
                                user
                                  ? t('product.review_comment_placeholder')
                                  : t('product.login_to_comment_review')
                              "
                              :disabled="!user || isReviewCommentSubmitting(rev.id)"
                            ></textarea>

                            <div class="flex items-center justify-end gap-2">
                              <button
                                type="button"
                                @click="cancelReply(rev.id)"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg text-sm font-medium transition"
                              >
                                {{ t("product.cancel_reply") }}
                              </button>
                              <button
                                v-if="user"
                                @click="submitReviewComment(rev.id)"
                                :disabled="
                                  isReviewCommentSubmitting(rev.id) ||
                                  !(reviewCommentForms[rev.id] || '').trim().length
                                "
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed"
                              >
                                {{
                                  isReviewCommentSubmitting(rev.id)
                                    ? t("product.submitting_comment")
                                    : t("product.submit_review_comment")
                                }}
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <p
                    v-else
                    class="text-xs text-gray-500 dark:text-gray-400 mt-3"
                  >
                    {{ t("product.no_review_comments") }}
                  </p>

                  <div
                    v-if="isReviewCommentOpen(rev.id) && !isReplyingTo(rev.id)"
                    class="mt-4"
                  >
                    <textarea
                      v-model="reviewCommentForms[rev.id]"
                      :ref="(el) => setReviewCommentTextareaRef(rev.id, el)"
                      rows="2"
                      class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                      :placeholder="
                        user
                          ? t('product.review_comment_placeholder')
                          : t('product.login_to_comment_review')
                      "
                      :disabled="!user || isReviewCommentSubmitting(rev.id)"
                    ></textarea>

                    <div class="mt-2 flex items-center justify-between gap-2">
                      <p
                        v-if="!user"
                        class="text-xs text-gray-500 dark:text-gray-400"
                      >
                        {{ t("product.login_to_review") }}
                        <router-link
                          to="/login"
                          class="text-indigo-600 font-semibold hover:underline"
                        >
                          {{ t("product.login_link") }}
                        </router-link>
                        .
                      </p>

                      <div class="ml-auto flex items-center gap-2">
                        <button
                          v-if="isReviewCommentOpen(rev.id) && !isReplyingTo(rev.id)"
                          type="button"
                          @click="closeReviewCommentComposer(rev.id)"
                          class="px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg text-sm font-medium transition"
                        >
                          {{ t("common.cancel") }}
                        </button>

                      <button
                        v-if="user"
                        @click="submitReviewComment(rev.id)"
                        :disabled="
                          isReviewCommentSubmitting(rev.id) ||
                          !(reviewCommentForms[rev.id] || '').trim().length
                        "
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed"
                      >
                        {{
                          isReviewCommentSubmitting(rev.id)
                            ? t("product.submitting_comment")
                            : t("product.submit_review_comment")
                        }}
                      </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div v-else>
                <div class="mb-4">
                  <label
                    class="block text-sm font-medium text-gray-900 dark:text-white mb-2"
                    >{{ t("product.rating_label") }}</label
                  >
                  <div class="flex items-center gap-2">
                    <div
                      v-for="n in 5"
                      :key="n"
                      class="relative w-6 h-6 cursor-pointer"
                      @mousemove="
                        (e) => {
                          const rect = e.currentTarget.getBoundingClientRect();
                          const x = e.clientX - rect.left;
                          const half = rect.width / 2;
                          editHoverRating = x < half ? n - 0.5 : n;
                        }
                      "
                      @mouseleave="editHoverRating = 0"
                      @click="
                        (e) => {
                          const rect = e.currentTarget.getBoundingClientRect();
                          const x = e.clientX - rect.left;
                          const half = rect.width / 2;
                          editingReview.rating = x < half ? n - 0.5 : n;
                        }
                      "
                    >
                      <svg
                        class="w-6 h-6 text-gray-300 absolute inset-0"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path
                          d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.174c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.97c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.84-.197-1.54-1.118l1.287-3.97a1 1 0 00-.364-1.118L2.05 9.397c-.783-.57-.38-1.81.588-1.81h4.174a1 1 0 00.95-.69l1.287-3.97z"
                        />
                      </svg>
                      <div
                        class="absolute inset-0 overflow-hidden pointer-events-none"
                        :style="{ width: getEditFillWidth(n) + '%' }"
                      >
                        <svg
                          class="w-6 h-6 text-yellow-400"
                          fill="currentColor"
                          viewBox="0 0 20 20"
                        >
                          <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.174c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.97c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.84-.197-1.54-1.118l1.287-3.97a1 1 0 00-.364-1.118L2.05 9.397c-.783-.57-.38-1.81.588-1.81h4.174a1 1 0 00.95-.69l1.287-3.97z"
                          />
                        </svg>
                      </div>
                    </div>
                    <span
                      v-if="editingReview.rating"
                      class="ml-2 text-sm font-semibold text-gray-900 dark:text-white"
                      >{{ editingReview.rating.toFixed(1) }}
                      {{ t("product.rating_out_of") }}</span
                    >
                  </div>
                </div>

                <div class="mb-4">
                  <label
                    class="block text-sm font-medium text-gray-900 dark:text-white mb-2"
                    >{{ t("product.comment_label") }}</label
                  >
                  <textarea
                    v-model="editingReview.comment"
                    rows="3"
                    class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                    :placeholder="t('product.comment_placeholder')"
                  ></textarea>
                </div>

                <div class="flex gap-2">
                  <button
                    @click="submitEditReview(rev.id)"
                    :disabled="updatingReview || editingReview.rating === 0"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    {{ updatingReview ? t("product.saving") : t("product.save_changes") }}
                  </button>
                  <button
                    @click="cancelEditReview"
                    type="button"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800"
                  >
                    {{ t("product.cancel") }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Delete confirmation modal -->
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
      >
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg w-full max-w-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
            {{ $t ? $t("product.confirm_delete_title") : "Potvrdenie" }}
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
            {{
              $t
                ? $t("product.review_delete_confirm")
                : "Naozaj chcete vymazať svoju recenziu?"
            }}
          </p>
          <div class="flex justify-end gap-3">
            <button
              @click="cancelDelete"
              class="px-4 py-2 border rounded-lg bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white"
            >
              {{ $t ? $t("common.cancel") : "Zrušiť" }}
            </button>
            <button
              @click="confirmDelete"
              :disabled="deleteProcessing"
              class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{
                deleteProcessing
                  ? $t
                    ? $t("common.deleting")
                    : "Maže sa..."
                  : $t
                  ? $t("common.delete")
                  : "Vymazať"
              }}
            </button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import api from "@/api";
import SideBarComponent from "../../components/SideBarComponent.vue";
import { useCartStore } from "../../stores/cartStore";
import { useI18n } from "vue-i18n";

export default {
  name: "ProductView",
  components: { SideBarComponent },
  setup() {
    const cartStore = useCartStore();
    const { t } = useI18n();
    return { cartStore, t };
  },

  data() {
    return {
      product: {
        id: null,
        category: "",
        title: "",
        name: "",
        description: "",
        price: 0,
        oldPrice: null,
        monthlyPrice: null,
        image: "",
        rating: 0,
        reviews: 0,
        stock: 0,
        specs: [],
        variants: null,
      },
      selectedVariants: {},
      loading: true,
      error: null,
      user: null,
      newReview: {
        rating: 0,
        comment: "",
      },
      submittingReview: false,
      reviewError: null,
      hoverRating: 0,
      editingReviewId: null,
      editingReview: {
        rating: 0,
        comment: "",
      },
      editHoverRating: 0,
      updatingReview: false,
      // Delete modal state
      showDeleteModal: false,
      deleteTargetReviewId: null,
      deleteProcessing: false,
      // Translation cache for comments
      translatedComments: {},
      translatingComments: {},
      // Translation cache for product description
      translatedDescription: "",
      translatingDescription: false,
      // Review comments and reply state
      reviewCommentForms: {},
      reviewCommentSubmitting: {},
      reviewReplyTargets: {},
      reviewCommentComposerOpen: {},
      reviewCommentTextareaRefs: {},
      reviewCommentEditTextareaRefs: {},
      editingReviewCommentId: null,
      editingReviewCommentReviewId: null,
      editingReviewCommentText: "",
      reviewCommentUpdating: {},
      reviewCommentDeleting: {},
      translatedReviewDiscussionComments: {},
      translatingReviewDiscussionComments: {},
    };
  },

  async mounted() {
    await this.loadProduct();
  },

  watch: {
    "$i18n.locale"() {
      // When user changes language, refresh all translations (description + reviews)
      console.log("[ProductView] Language changed to:", this.$i18n?.locale);

      // Clear and re-translate product description
      if (this.product && this.product.description) {
        this.translatedDescription = "";
        this.translatingDescription = false;
        console.log(
          "[ProductView] Re-translating product description to new locale:",
          this.$i18n?.locale
        );
        this.getTranslatedDescription(this.product.description);
      }

      // Clear and re-translate review comments
      if (
        this.product &&
        this.product.latest_reviews &&
        Array.isArray(this.product.latest_reviews)
      ) {
        this.translatedComments = {};
        this.translatingComments = {};
        this.translatedReviewDiscussionComments = {};
        this.translatingReviewDiscussionComments = {};

        this.$nextTick(() => {
          this.product.latest_reviews.forEach((review) => {
            if (review && review.id && review.comment) {
              console.log(
                "[ProductView] Re-translating review",
                review.id,
                "to new locale:",
                this.$i18n?.locale
              );
              this.getTranslatedComment(review.id, review.comment);
            }
          });

          this.queueReviewDiscussionTranslations();
        });
      }
    },
  },

  methods: {
    async loadProduct() {
      this.loading = true;
      this.error = null;
      this.selectedVariants = {};
      try {
        const id = this.$route.params.slug;
        const response = await api.get(`/api/products/${id}`);
        this.product = response.data;

        if (Array.isArray(this.product.latest_reviews)) {
          this.product.latest_reviews = this.product.latest_reviews.map((review) => ({
            ...review,
            comments: this.normalizeReviewCommentsTree(review?.comments || []),
          }));
        }

        // Parse variants when returned as a JSON string
        if (this.product.variants && typeof this.product.variants === "string") {
          try {
            this.product.variants = JSON.parse(this.product.variants);
          } catch (e) {
            console.warn("Could not parse variants JSON:", e);
            this.product.variants = null;
          }
        }

        // Parse variant_pricing when returned as a JSON string
        if (
          this.product.variant_pricing &&
          typeof this.product.variant_pricing === "string"
        ) {
          try {
            this.product.variant_pricing = JSON.parse(this.product.variant_pricing);
          } catch (e) {
            console.warn("Could not parse variant_pricing JSON:", e);
            this.product.variant_pricing = null;
          }
        }

        // Auto-select the first (lowest) option for each variant
        if (this.product.variants) {
          for (const [variantKey, options] of Object.entries(this.product.variants)) {
            if (Array.isArray(options) && options.length > 0) {
              this.selectedVariants[variantKey] = options[0];
            }
          }
        }

        // try to load current user (if authenticated)
        try {
          const userResp = await api.get("/api/user");
          this.user = userResp.data || null;
        } catch (e) {
          this.user = null;
        }
      } catch (err) {
        console.error("Error loading product:", err);
        this.error = "Produkt sa nepodarilo načítať. Skúste neskôr.";
        this.product = {
          id: null,
          category: "",
          title: "Produkt nenájdený",
          name: "Produkt nenájdený",
          description: "",
          price: 0,
          oldPrice: null,
          monthlyPrice: null,
          image: "",
          rating: 0,
          reviews: 0,
          stock: 0,
          specs: [],
          variants: null,
        };
      } finally {
        this.loading = false;

        // Translate product description
        if (this.product && this.product.description) {
          console.log(
            "[ProductView] Starting product description translation, locale:",
            this.$i18n?.locale
          );
          this.getTranslatedDescription(this.product.description);
        }

        // Translate all comments after product is loaded
        if (
          this.product &&
          this.product.latest_reviews &&
          Array.isArray(this.product.latest_reviews) &&
          this.product.latest_reviews.length > 0
        ) {
          console.log(
            "[ProductView] Starting review translation, locale:",
            this.$i18n?.locale,
            "reviews count:",
            this.product.latest_reviews.length
          );

          this.$nextTick(() => {
            this.product.latest_reviews.forEach((review) => {
              if (review && review.id && review.comment) {
                console.log("[ProductView] Queueing translation for review", review.id);
                // Call without await to fire in background
                this.getTranslatedComment(review.id, review.comment);
              }
            });

            this.queueReviewDiscussionTranslations();
          });
        } else {
          console.log("[ProductView] No reviews to translate", {
            hasProduct: !!this.product,
            hasLatestReviews: !!this.product?.latest_reviews,
            isArray: Array.isArray(this.product?.latest_reviews),
            length: this.product?.latest_reviews?.length,
          });
        }
      }
    },
    formatPrice(price) {
      return parseFloat(price).toFixed(2);
    },
    selectVariant(variantKey, option) {
      this.selectedVariants[variantKey] = option;
    },
    areAllVariantsSelected() {
      if (!this.product.variants || Object.keys(this.product.variants).length === 0) {
        return true;
      }
      for (let key in this.product.variants) {
        if (!this.selectedVariants[key]) {
          return false;
        }
      }
      return true;
    },
    addToCart() {
      const cartItem = {
        id: this.product.id,
        product_id: this.product.id,
        title: this.product.title || this.product.name,
        // Use calculated_price (discount-applied) so cart stores the actual charged price
        price: this.product.calculated_price ?? this.product.price,
        variant_price: this.calculateCurrentPrice(),
        image: this.product.image,
        description: this.product.description,
        quantity: 1,
      };

      // Add variant_options and variants data if any variants exist
      if (this.product.variants && Object.keys(this.product.variants).length > 0) {
        cartItem.variant_options = this.selectedVariants;
        cartItem.variants = this.product.variants;
      }

      console.log("[ProductView] Adding to cart:", cartItem);
      this.cartStore.addToCart(cartItem);
      alert(`${this.product.title || this.product.name} pridaný do košíka!`);
    },
    async submitReview() {
      if (!this.user) {
        this.reviewError = "Musíte byť prihlásený, aby ste mohli písať recenzie.";
        return;
      }
      if (!this.newReview.rating) {
        this.reviewError = this.t("product.select_rating");
        return;
      }
      this.submittingReview = true;
      this.reviewError = null;
      try {
        const resp = await api.post(`/api/products/${this.product.id}/reviews`, {
          rating: this.newReview.rating,
          comment: this.newReview.comment,
        });

        // Update product aggregates and prepend new review
        if (resp.data) {
          this.product.rating = resp.data.rating ?? this.product.rating;
          this.product.reviews = resp.data.reviews ?? this.product.reviews;
          if (!this.product.latest_reviews) this.product.latest_reviews = [];
          // Remove any existing review by this user so duplicates don't appear
          if (this.user && this.user.id) {
            this.product.latest_reviews = this.product.latest_reviews.filter(
              (r) => !this.isSameUserId(r.user_id, this.user.id)
            );
          }
          this.product.latest_reviews.unshift({
            ...resp.data.review,
            comments: this.normalizeReviewCommentsTree(resp.data.review?.comments || []),
          });
          // Translate the new review comment
          if (resp.data.review && resp.data.review.comment) {
            console.log(
              "[ProductView] New review submitted, queueing translation for review",
              resp.data.review.id
            );
            this.getTranslatedComment(resp.data.review.id, resp.data.review.comment);
          }
        }
        this.resetReview();
      } catch (e) {
        console.error("Failed to submit review", e);
        this.reviewError = e.response?.data?.message || "Chyba pri odosielaní recenzie.";
      } finally {
        this.submittingReview = false;
      }
    },
    resetReview() {
      this.newReview.rating = 0;
      this.newReview.comment = "";
      this.reviewError = null;
    },
    async deleteReview(reviewId) {
      this.deleteProcessing = true;
      try {
        const resp = await api.delete(
          `/api/products/${this.product.id}/reviews/${reviewId}`
        );
        const wasSuccessful =
          resp.status >= 200 && resp.status < 300 && (resp.data?.success ?? true);

        if (wasSuccessful) {
          // Remove from list
          this.product.latest_reviews = this.product.latest_reviews.filter(
            (r) => !this.isSameUserId(r.id, reviewId)
          );
          // Update aggregates
          this.product.rating = resp.data.rating ?? this.product.rating;
          this.product.reviews = resp.data.reviews ?? this.product.reviews;
          this.$notify &&
            this.$notify({ type: "success", text: this.t("product.review_deleted") });
        }
      } catch (e) {
        console.error("Failed to delete review", e);
        this.$notify &&
          this.$notify({
            type: "error",
            text: e?.response?.data?.message || this.t("product.review_delete_failed"),
          });
      } finally {
        this.deleteProcessing = false;
        this.showDeleteModal = false;
        this.deleteTargetReviewId = null;
      }
    },
    promptDeleteReview(reviewId) {
      this.deleteTargetReviewId = reviewId;
      this.showDeleteModal = true;
    },
    async confirmDelete() {
      if (!this.deleteTargetReviewId) return;
      await this.deleteReview(this.deleteTargetReviewId);
    },
    cancelDelete() {
      this.showDeleteModal = false;
      this.deleteTargetReviewId = null;
    },
    startEditReview(review) {
      this.editingReviewId = review.id;
      this.editingReview.rating = parseFloat(review.rating) || 0;
      this.editingReview.comment = review.comment;
    },
    cancelEditReview() {
      this.editingReviewId = null;
      this.editingReview.rating = 0;
      this.editingReview.comment = "";
    },
    async submitEditReview(reviewId) {
      if (!this.editingReview.rating) {
        alert(this.t("product.select_rating"));
        return;
      }

      this.updatingReview = true;
      try {
        const resp = await api.put(
          `/api/products/${this.product.id}/reviews/${reviewId}`,
          {
            rating: this.editingReview.rating,
            comment: this.editingReview.comment,
          }
        );

        const wasSuccessful =
          resp.status >= 200 && resp.status < 300 && (resp.data?.success ?? true);

        if (wasSuccessful) {
          // Update the review in the list
          const idx = this.product.latest_reviews.findIndex((r) =>
            this.isSameUserId(r.id, reviewId)
          );
          if (idx !== -1) {
            const existingComments = this.product.latest_reviews[idx]?.comments || [];
            this.product.latest_reviews[idx] = {
              ...resp.data.review,
              comments: this.normalizeReviewCommentsTree(
                resp.data.review?.comments || existingComments
              ),
            };
            // Clear cached translation for this review so it gets re-translated
            delete this.translatedComments[reviewId];
            // Translate the updated review comment
            if (resp.data.review && resp.data.review.comment) {
              console.log(
                "[ProductView] Review updated, queueing re-translation for review",
                resp.data.review.id
              );
              this.getTranslatedComment(resp.data.review.id, resp.data.review.comment);
            }
          }
          // Update aggregates
          this.product.rating = resp.data.rating;
          this.product.reviews = resp.data.reviews;
          this.cancelEditReview();
        }
      } catch (e) {
        console.error("Failed to update review", e);
        alert(e?.response?.data?.message || "Chyba pri úprave recenzie.");
      } finally {
        this.updatingReview = false;
      }
    },
    onStarHover(e, n) {
      const rect = e.currentTarget.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const half = rect.width / 2;
      this.hoverRating = x < half ? n - 0.5 : n;
    },
    onStarLeave() {
      this.hoverRating = 0;
    },
    onStarClick(e, n) {
      const rect = e.currentTarget.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const half = rect.width / 2;
      this.newReview.rating = x < half ? n - 0.5 : n;
    },
    getFillWidth(n) {
      const value = this.hoverRating || this.newReview.rating || 0;
      const start = n - 1;
      const fill = Math.max(0, Math.min(1, value - start));
      return Math.round(fill * 100);
    },
    getEditFillWidth(n) {
      const value = this.editHoverRating || this.editingReview.rating || 0;
      const start = n - 1;
      const fill = Math.max(0, Math.min(1, value - start));
      return Math.round(fill * 100);
    },
    getCategoryLabel(category) {
      const categoryMap = {
        phones: "Telefóny",
        notebooks: "Notebooky",
        tablets: "Tablety",
        pocitace: "Počítače",
        pcs: "Počítače",
        accessories: "Príslušenstvo",
        monitors: "Monitory",
        peripherals: "Periférie",
        mobily: "Mobily",
      };
      return categoryMap[category] || category;
    },
    isSameUserId(a, b) {
      return String(a ?? "") === String(b ?? "");
    },
    isReviewOwner(review) {
      if (!this.user || !review) return false;
      return this.isSameUserId(this.user.id, review.user_id);
    },
    canDeleteReview(review) {
      // User can delete their own review, or if they are admin/owner
      if (!this.user) return false;
      if (this.isReviewOwner(review)) return true;
      if (this.user.role === "admin" || this.user.role === "owner") return true;
      return false;
    },
    isReviewCommentOwner(comment) {
      if (!this.user || !comment) return false;
      return this.isSameUserId(this.user.id, comment.user_id);
    },
    canEditReviewComment(comment) {
      if (!this.user) return false;
      return this.isReviewCommentOwner(comment);
    },
    canDeleteReviewComment(comment) {
      if (!this.user) return false;
      if (this.isReviewCommentOwner(comment)) return true;
      if (this.user.role === "admin" || this.user.role === "owner") return true;
      return false;
    },
    isEditingReviewComment(reviewId, commentId) {
      return (
        this.isSameUserId(this.editingReviewCommentReviewId, reviewId) &&
        this.isSameUserId(this.editingReviewCommentId, commentId)
      );
    },
    isReviewCommentUpdating(commentId) {
      return !!this.reviewCommentUpdating[commentId];
    },
    isReviewCommentDeleting(commentId) {
      return !!this.reviewCommentDeleting[commentId];
    },
    setReviewCommentEditTextareaRef(commentId, element) {
      const key = String(commentId ?? "");
      if (element) {
        this.reviewCommentEditTextareaRefs[key] = element;
        return;
      }

      delete this.reviewCommentEditTextareaRefs[key];
    },
    focusReviewCommentEditTextarea(commentId) {
      this.$nextTick(() => {
        const key = String(commentId ?? "");
        const textarea = this.reviewCommentEditTextareaRefs[key];
        if (!textarea || typeof textarea.focus !== "function") {
          return;
        }

        textarea.focus();

        if (typeof textarea.value === "string" && textarea.setSelectionRange) {
          const cursorPosition = textarea.value.length;
          textarea.setSelectionRange(cursorPosition, cursorPosition);
        }
      });
    },
    startEditReviewComment(reviewId, comment) {
      if (!this.canEditReviewComment(comment)) {
        return;
      }

      this.clearReplyTarget(reviewId);
      this.reviewCommentComposerOpen[reviewId] = false;
      this.editingReviewCommentReviewId = reviewId;
      this.editingReviewCommentId = comment.id;
      this.editingReviewCommentText = comment.comment || "";
      this.focusReviewCommentEditTextarea(comment.id);
    },
    cancelEditReviewComment() {
      this.editingReviewCommentReviewId = null;
      this.editingReviewCommentId = null;
      this.editingReviewCommentText = "";
    },
    normalizeSingleReviewComment(comment) {
      if (!comment || typeof comment !== "object") {
        return {
          id: null,
          review_id: null,
          parent_id: null,
          comment: "",
          created_at: null,
          updated_at: null,
          user_id: null,
          user_name: "",
          user_avatar: null,
          replies: [],
        };
      }

      return {
        id: comment.id,
        review_id: comment.review_id,
        parent_id: comment.parent_id ?? null,
        comment: comment.comment || "",
        created_at: comment.created_at,
        updated_at: comment.updated_at,
        user_id: comment.user_id ?? null,
        user_name: comment.user_name || "",
        user_avatar: comment.user_avatar || null,
        replies: this.normalizeReviewCommentsTree(comment.replies || []),
      };
    },
    normalizeReviewCommentsTree(comments) {
      if (!Array.isArray(comments)) return [];
      return comments.map((comment) => this.normalizeSingleReviewComment(comment));
    },
    flattenReviewComments(comments, level = 0, result = []) {
      if (!Array.isArray(comments) || comments.length === 0) {
        return result;
      }

      comments.forEach((comment) => {
        const normalized = this.normalizeSingleReviewComment(comment);
        result.push({ ...normalized, level });
        if (normalized.replies && normalized.replies.length > 0) {
          this.flattenReviewComments(normalized.replies, level + 1, result);
        }
      });

      return result;
    },
    isReviewCommentSubmitting(reviewId) {
      return !!this.reviewCommentSubmitting[reviewId];
    },
    getReviewCommentTextareaRefKey(reviewId, parentCommentId = null) {
      if (parentCommentId) {
        return `${reviewId}:${parentCommentId}`;
      }

      return `${reviewId}:root`;
    },
    setReviewCommentTextareaRef(reviewId, element, parentCommentId = null) {
      const key = this.getReviewCommentTextareaRefKey(reviewId, parentCommentId);
      if (element) {
        this.reviewCommentTextareaRefs[key] = element;
        return;
      }

      delete this.reviewCommentTextareaRefs[key];
    },
    focusReviewCommentComposer(reviewId) {
      this.$nextTick(() => {
        const parentCommentId = this.reviewReplyTargets[reviewId]?.parentId || null;
        const key = this.getReviewCommentTextareaRefKey(reviewId, parentCommentId);
        const textarea = this.reviewCommentTextareaRefs[key];
        if (!textarea || typeof textarea.focus !== "function") {
          return;
        }

        textarea.focus();

        if (typeof textarea.value === "string" && textarea.setSelectionRange) {
          const cursorPosition = textarea.value.length;
          textarea.setSelectionRange(cursorPosition, cursorPosition);
        }
      });
    },
    openReviewCommentComposer(reviewId) {
      this.cancelEditReviewComment();
      this.reviewCommentComposerOpen[reviewId] = true;
      this.focusReviewCommentComposer(reviewId);
    },
    closeReviewCommentComposer(reviewId) {
      this.reviewCommentComposerOpen[reviewId] = false;
      this.clearReplyTarget(reviewId);
    },
    toggleReviewCommentComposer(reviewId) {
      if (this.isReviewCommentOpen(reviewId)) {
        this.closeReviewCommentComposer(reviewId);
        return;
      }

      this.openReviewCommentComposer(reviewId);
    },
    isReviewCommentOpen(reviewId) {
      return !!this.reviewCommentComposerOpen[reviewId];
    },
    setReplyTarget(reviewId, parentCommentId, parentUserName) {
      this.cancelEditReviewComment();
      this.reviewReplyTargets[reviewId] = {
        parentId: parentCommentId,
        parentUserName: parentUserName || this.t("product.anonymous_user"),
      };
      this.reviewCommentComposerOpen[reviewId] = true;
      this.focusReviewCommentComposer(reviewId);
    },
    cancelReply(reviewId) {
      this.clearReplyTarget(reviewId);
      this.reviewCommentComposerOpen[reviewId] = false;
    },
    clearReplyTarget(reviewId) {
      delete this.reviewReplyTargets[reviewId];
    },
    isReplyingTo(reviewId) {
      return !!this.reviewReplyTargets[reviewId]?.parentId;
    },
    isReplyingToComment(reviewId, commentId) {
      return this.isSameUserId(this.reviewReplyTargets[reviewId]?.parentId, commentId);
    },
    getReplyTargetName(reviewId) {
      return (
        this.reviewReplyTargets[reviewId]?.parentUserName ||
        this.t("product.anonymous_user")
      );
    },
    appendCommentToTree(tree, newComment) {
      if (!Array.isArray(tree) || !newComment?.parent_id) {
        return false;
      }

      for (const item of tree) {
        if (this.isSameUserId(item.id, newComment.parent_id)) {
          if (!Array.isArray(item.replies)) {
            item.replies = [];
          }
          item.replies.push(newComment);
          return true;
        }

        if (this.appendCommentToTree(item.replies || [], newComment)) {
          return true;
        }
      }

      return false;
    },
    appendCommentToReview(reviewId, comment) {
      const review = (this.product.latest_reviews || []).find((item) =>
        this.isSameUserId(item.id, reviewId)
      );

      if (!review) {
        return;
      }

      if (!Array.isArray(review.comments)) {
        review.comments = [];
      }

      const normalizedComment = this.normalizeSingleReviewComment(comment);
      if (!normalizedComment.parent_id) {
        review.comments.push(normalizedComment);
        return;
      }

      const inserted = this.appendCommentToTree(review.comments, normalizedComment);
      if (!inserted) {
        normalizedComment.parent_id = null;
        review.comments.push(normalizedComment);
      }
    },
    updateCommentInTree(tree, commentId, updatedComment) {
      if (!Array.isArray(tree)) {
        return false;
      }

      for (const item of tree) {
        if (this.isSameUserId(item.id, commentId)) {
          item.comment = updatedComment.comment ?? item.comment;
          item.updated_at = updatedComment.updated_at ?? item.updated_at;
          item.user_id = updatedComment.user_id ?? item.user_id;
          item.user_name = updatedComment.user_name ?? item.user_name;
          if (Object.prototype.hasOwnProperty.call(updatedComment, "user_avatar")) {
            item.user_avatar = updatedComment.user_avatar;
          }
          return true;
        }

        if (this.updateCommentInTree(item.replies || [], commentId, updatedComment)) {
          return true;
        }
      }

      return false;
    },
    updateCommentInReview(reviewId, commentId, updatedComment) {
      const review = (this.product.latest_reviews || []).find((item) =>
        this.isSameUserId(item.id, reviewId)
      );

      if (!review || !Array.isArray(review.comments)) {
        return false;
      }

      return this.updateCommentInTree(review.comments, commentId, updatedComment);
    },
    collectCommentIds(tree, result = []) {
      if (!Array.isArray(tree)) {
        return result;
      }

      tree.forEach((item) => {
        if (item?.id !== null && item?.id !== undefined) {
          result.push(item.id);
        }
        this.collectCommentIds(item?.replies || [], result);
      });

      return result;
    },
    removeCommentFromTree(tree, commentId, removedIds = []) {
      if (!Array.isArray(tree)) {
        return [];
      }

      const nextTree = [];
      tree.forEach((item) => {
        if (this.isSameUserId(item.id, commentId)) {
          removedIds.push(item.id);
          this.collectCommentIds(item.replies || [], removedIds);
          return;
        }

        item.replies = this.removeCommentFromTree(item.replies || [], commentId, removedIds);
        nextTree.push(item);
      });

      return nextTree;
    },
    removeCommentFromReview(reviewId, commentId) {
      const review = (this.product.latest_reviews || []).find((item) =>
        this.isSameUserId(item.id, reviewId)
      );

      if (!review || !Array.isArray(review.comments)) {
        return [];
      }

      const removedIds = [];
      review.comments = this.removeCommentFromTree(review.comments, commentId, removedIds);
      return removedIds;
    },
    findCommentInTree(tree, commentId) {
      if (!Array.isArray(tree)) {
        return null;
      }

      for (const item of tree) {
        if (this.isSameUserId(item.id, commentId)) {
          return item;
        }

        const nested = this.findCommentInTree(item.replies || [], commentId);
        if (nested) {
          return nested;
        }
      }

      return null;
    },
    hasCommentInReview(reviewId, commentId) {
      const review = (this.product.latest_reviews || []).find((item) =>
        this.isSameUserId(item.id, reviewId)
      );

      if (!review || !Array.isArray(review.comments)) {
        return false;
      }

      return !!this.findCommentInTree(review.comments, commentId);
    },
    clearCommentTranslationCache(commentIds = []) {
      if (!Array.isArray(commentIds)) {
        return;
      }

      commentIds.forEach((commentId) => {
        delete this.translatedReviewDiscussionComments[commentId];
        delete this.translatingReviewDiscussionComments[commentId];
      });
    },
    async submitEditReviewComment(reviewId, commentId) {
      if (!this.isEditingReviewComment(reviewId, commentId)) {
        return;
      }

      const text = this.editingReviewCommentText.trim();
      if (!text) {
        this.$notify &&
          this.$notify({ type: "error", text: this.t("product.review_comment_empty") });
        return;
      }

      if (this.isReviewCommentUpdating(commentId)) {
        return;
      }

      this.reviewCommentUpdating[commentId] = true;
      try {
        const response = await api.put(
          `/api/products/${this.product.id}/reviews/${reviewId}/comments/${commentId}`,
          {
            comment: text,
          }
        );

        if (response.data?.comment) {
          const updatedComment = this.normalizeSingleReviewComment(response.data.comment);
          this.updateCommentInReview(reviewId, commentId, updatedComment);
          this.clearCommentTranslationCache([commentId]);
          this.getTranslatedReviewDiscussionComment(commentId, updatedComment.comment);
          this.cancelEditReviewComment();
          this.$notify &&
            this.$notify({
              type: "success",
              text: this.t("product.review_comment_updated"),
            });
        }
      } catch (error) {
        console.error("Failed to update review comment", error);
        this.$notify &&
          this.$notify({
            type: "error",
            text:
              error?.response?.data?.message ||
              this.t("product.review_comment_update_failed"),
          });
      } finally {
        this.reviewCommentUpdating[commentId] = false;
      }
    },
    async deleteReviewComment(reviewId, commentId) {
      if (!this.user || this.isReviewCommentDeleting(commentId)) {
        return;
      }

      if (!window.confirm(this.t("product.review_comment_delete_confirm"))) {
        return;
      }

      this.reviewCommentDeleting[commentId] = true;
      try {
        const response = await api.delete(
          `/api/products/${this.product.id}/reviews/${reviewId}/comments/${commentId}`
        );

        const wasSuccessful =
          response.status >= 200 &&
          response.status < 300 &&
          (response.data?.success ?? true);

        if (wasSuccessful) {
          const removedIds = this.removeCommentFromReview(reviewId, commentId);
          this.clearCommentTranslationCache(removedIds);

          const activeReplyTarget = this.reviewReplyTargets[reviewId]?.parentId;
          if (activeReplyTarget && !this.hasCommentInReview(reviewId, activeReplyTarget)) {
            this.clearReplyTarget(reviewId);
            this.reviewCommentComposerOpen[reviewId] = false;
          }

          if (
            this.editingReviewCommentId &&
            this.editingReviewCommentReviewId &&
            !this.hasCommentInReview(
              this.editingReviewCommentReviewId,
              this.editingReviewCommentId
            )
          ) {
            this.cancelEditReviewComment();
          }

          this.$notify &&
            this.$notify({
              type: "success",
              text:
                response.data?.message || this.t("product.review_comment_deleted"),
            });
        }
      } catch (error) {
        console.error("Failed to delete review comment", error);
        this.$notify &&
          this.$notify({
            type: "error",
            text:
              error?.response?.data?.message ||
              this.t("product.review_comment_delete_failed"),
          });
      } finally {
        this.reviewCommentDeleting[commentId] = false;
      }
    },
    async submitReviewComment(reviewId) {
      if (!this.user) {
        this.reviewError = this.t("product.login_to_comment_review");
        return;
      }

      const text = (this.reviewCommentForms[reviewId] || "").trim();
      if (!text || this.isReviewCommentSubmitting(reviewId)) {
        return;
      }

      this.reviewCommentSubmitting[reviewId] = true;
      try {
        const replyTarget = this.reviewReplyTargets[reviewId] || null;
        const response = await api.post(
          `/api/products/${this.product.id}/reviews/${reviewId}/comments`,
          {
            comment: text,
            parent_id: replyTarget?.parentId || null,
          }
        );

        if (response.data?.comment) {
          this.appendCommentToReview(reviewId, response.data.comment);
          this.getTranslatedReviewDiscussionComment(
            response.data.comment.id,
            response.data.comment.comment
          );
          this.reviewCommentForms[reviewId] = "";
          this.clearReplyTarget(reviewId);
          this.reviewCommentComposerOpen[reviewId] = false;
        }
      } catch (error) {
        console.error("Failed to submit review comment", error);
        this.$notify &&
          this.$notify({
            type: "error",
            text:
              error?.response?.data?.message || this.t("product.review_comment_submit_failed"),
          });
      } finally {
        this.reviewCommentSubmitting[reviewId] = false;
      }
    },
    getAvatarUrl(avatar) {
      if (!avatar) return "";
      if (typeof avatar === "string" && avatar.startsWith("http")) return avatar;
      const base = (import.meta.env.VITE_API_URL || "https://backend.techstore.lznet.work").replace(
        /\/+$/,
        ""
      );
      const normalized = String(avatar).replace(/^\/+/, "");
      if (normalized.startsWith("storage/")) {
        return `${base}/${normalized}`;
      }
      return `${base}/storage/${normalized}`;
    },
    getInitials(name) {
      if (!name) return "?";
      const parts = String(name).split(" ").filter(Boolean);
      if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
      }
      return parts[0].substring(0, 2).toUpperCase();
    },
    async getTranslatedDescription(descriptionText) {
      // Don't translate if text is empty
      if (!descriptionText || !descriptionText.trim()) {
        this.translatedDescription = descriptionText;
        return descriptionText;
      }

      // Check if already cached
      if (this.translatedDescription && this.translatedDescription.trim()) {
        return this.translatedDescription;
      }

      // Check if already translating
      if (this.translatingDescription) {
        return descriptionText; // Return original while translating
      }

      // Mark as translating
      this.translatingDescription = true;

      try {
        // Get current interface language
        const currentLocale = this.$i18n?.locale || "sk";

        // Don't translate if current language is Slovak (source)
        if (currentLocale === "sk") {
          this.translatedDescription = descriptionText;
          return descriptionText;
        }

        console.log("[ProductView] Translating description", {
          from: "sk",
          to: currentLocale,
          text: descriptionText.substring(0, 50) + "...",
        });

        const response = await api.post("/api/translate", {
          text: descriptionText,
          targetLanguage: currentLocale,
        });

        if (response.data && response.data.translatedText) {
          this.translatedDescription = response.data.translatedText;
          console.log("[ProductView] Description translated successfully");
          return this.translatedDescription;
        }
      } catch (error) {
        console.error("[ProductView] Error translating description:", error);
      } finally {
        this.translatingDescription = false;
      }

      // Return original text on error or if translation failed
      this.translatedDescription = descriptionText;
      return descriptionText;
    },
    async getTranslatedComment(commentId, commentText) {
      // Don't translate if text is empty
      if (!commentText || !commentText.trim()) {
        this.translatedComments[commentId] = commentText;
        return commentText;
      }

      // Check if already cached
      if (this.translatedComments[commentId]) {
        return this.translatedComments[commentId];
      }

      // Check if already translating
      if (this.translatingComments[commentId]) {
        return commentText; // Return original while translating
      }

      // Mark as translating
      this.translatingComments[commentId] = true;

      try {
        // Get current interface language
        const currentLocale = this.$i18n?.locale || "sk";

        // Don't translate if current language is Slovak (source)
        if (currentLocale === "sk") {
          this.translatedComments[commentId] = commentText;
          return commentText;
        }

        console.log("[ProductView] Translating comment", {
          commentId,
          from: "sk",
          to: currentLocale,
          text: commentText.substring(0, 50) + "...",
        });

        const response = await api.post("/api/translate", {
          text: commentText,
          targetLanguage: currentLocale,
        });

        console.log("[ProductView] Translation response", {
          commentId,
          translatedText: response.data.translatedText
            ? response.data.translatedText.substring(0, 50) + "..."
            : "empty",
        });

        // Cache the translation
        this.translatedComments[commentId] = response.data.translatedText || commentText;
        return this.translatedComments[commentId];
      } catch (error) {
        console.warn("[ProductView] Translation API error:", error);
        // Return original text on error
        this.translatedComments[commentId] = commentText;
        return commentText;
      } finally {
        this.translatingComments[commentId] = false;
      }
    },
    queueReviewDiscussionTranslations() {
      if (!Array.isArray(this.product?.latest_reviews)) {
        return;
      }

      this.product.latest_reviews.forEach((review) => {
        const discussionComments = this.flattenReviewComments(review?.comments || []);
        discussionComments.forEach((item) => {
          if (item?.id && item?.comment) {
            this.getTranslatedReviewDiscussionComment(item.id, item.comment);
          }
        });
      });
    },
    async getTranslatedReviewDiscussionComment(commentId, commentText) {
      if (!commentText || !commentText.trim()) {
        this.translatedReviewDiscussionComments[commentId] = commentText;
        return commentText;
      }

      if (this.translatedReviewDiscussionComments[commentId]) {
        return this.translatedReviewDiscussionComments[commentId];
      }

      if (this.translatingReviewDiscussionComments[commentId]) {
        return commentText;
      }

      this.translatingReviewDiscussionComments[commentId] = true;

      try {
        const currentLocale = this.$i18n?.locale || "sk";

        if (currentLocale === "sk") {
          this.translatedReviewDiscussionComments[commentId] = commentText;
          return commentText;
        }

        const response = await api.post("/api/translate", {
          text: commentText,
          targetLanguage: currentLocale,
        });

        this.translatedReviewDiscussionComments[commentId] =
          response.data.translatedText || commentText;
        return this.translatedReviewDiscussionComments[commentId];
      } catch (error) {
        console.warn("[ProductView] Discussion translation API error:", error);
        this.translatedReviewDiscussionComments[commentId] = commentText;
        return commentText;
      } finally {
        this.translatingReviewDiscussionComments[commentId] = false;
      }
    },
    calculateCurrentPrice() {
      // Use Product.calculated_price (discount-applied) when available
      let basePrice = parseFloat(this.product.calculated_price ?? this.product.price);
      let modifier = 0;

      // Check if product has variant_pricing and selected variants
      if (this.product.variant_pricing && Object.keys(this.selectedVariants).length > 0) {
        for (let variantType in this.selectedVariants) {
          const selectedValue = this.selectedVariants[variantType];
          if (
            this.product.variant_pricing[variantType] &&
            this.product.variant_pricing[variantType][selectedValue] !== undefined
          ) {
            modifier += parseFloat(
              this.product.variant_pricing[variantType][selectedValue]
            );
          }
        }
      }

      const finalPrice = basePrice + modifier;
      console.log("[ProductView] calculateCurrentPrice:", {
        basePrice,
        selectedVariants: this.selectedVariants,
        variant_pricing: this.product.variant_pricing,
        modifier,
        finalPrice,
      });

      return finalPrice;
    },
  },
};
</script>
