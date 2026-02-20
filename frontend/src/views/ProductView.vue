<template>
  <div class="flex bg-white dark:bg-gray-900 min-h-screen">
    <!-- SIDEBAR -->
    <aside class="w-64 h-screen bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800">
      <SideBarComponent />
    </aside>

    <main class="flex-1 p-8">
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="text-center">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
          <p class="mt-4 text-gray-600 dark:text-gray-300">Načítavam produkt...</p>
        </div>
      </div>

      <div v-else-if="error" class="flex items-center justify-center py-20">
        <div class="text-center">
          <p class="text-red-600 dark:text-red-400 font-semibold">{{ error }}</p>
          <router-link to="/" class="mt-4 inline-block text-indigo-600 hover:underline">Späť na domov</router-link>
        </div>
      </div>

      <div v-else>
        <!-- PRODUCT HEADER -->
        <nav class="text-sm text-gray-600 dark:text-gray-300 mb-4 flex items-center gap-2 flex-wrap">
          <router-link to="/" class="hover:underline">Domov</router-link>
          <span>/</span>
          <router-link :to="`/category/${product.category}`" class="hover:underline capitalize">{{ getCategoryLabel(product.category) }}</router-link>
          <span>/</span>
          <span class="text-gray-900 dark:text-gray-100 font-semibold">{{ product.title || product.name }}</span>
        </nav>

        <div class="border-t border-gray-200 dark:border-gray-800 mb-8"></div>

        <div class="max-w-6xl grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
          <div class="flex flex-col items-center">
            <img :src="product.image" :alt="product.title || product.name" class="w-full max-w-md rounded-lg shadow-md" />
            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Kód produktu: {{ product.id }}</p>
          </div>

          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">{{ product.title || product.name }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">{{ product.description }}</p>

            <div class="mb-6">
              <div class="text-4xl font-bold text-indigo-600">{{ formatPrice(product.price) }} €</div>
              <div v-if="product.oldPrice" class="text-sm line-through text-gray-500 dark:text-gray-400">{{ formatPrice(product.oldPrice) }} €</div>
              <div v-if="product.monthlyPrice" class="text-sm text-gray-600 dark:text-gray-400 mt-1">alebo od {{ product.monthlyPrice }} € mesačne</div>
            </div>

            <div class="mb-6">
              <span :class="product.stock > 0 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'" class="inline-block px-3 py-1 rounded-full text-sm font-medium">{{ product.stock > 0 ? 'Na sklade - ' + product.stock + ' ks' : 'Vypredané' }}</span>
            </div>

            <div class="mb-8 flex items-center gap-2">
              <div class="flex items-center gap-1">
                <span class="text-2xl font-semibold text-gray-900 dark:text-white">{{ product.rating }}</span>
                <span class="text-2xl text-yellow-400">★</span>
              </div>
              <span class="text-sm text-gray-600 dark:text-gray-400">({{ product.reviews }} recenzií)</span>
            </div>

            <button @click="addToCart" :disabled="product.stock === 0" class="w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed">Pridať do košíka</button>
          </div>
        </div>

        <!-- SPECS -->
        <div class="mb-12">
          <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Parametre</h2>
          <ul v-if="product.specs && product.specs.length > 0" class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300">
            <li v-for="(spec, index) in product.specs" :key="index">{{ spec }}</li>
          </ul>
          <p v-else class="text-gray-500 dark:text-gray-400">Žiadne parametre.</p>
        </div>

        <!-- WRITE REVIEW -->
        <div class="mb-12 pb-12 border-b border-gray-200 dark:border-gray-800">
          <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Napísať recenziu</h2>

          <div v-if="!user" class="text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 p-4 rounded-lg">Pre písanie recenzií sa, prosím, <router-link to="/login" class="text-indigo-600 font-semibold hover:underline">prihláste</router-link>.</div>

          <div v-else class="max-w-2xl">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Hodnotenie</label>
              <div class="flex items-center gap-2">
                <div v-for="n in 5" :key="n" class="relative w-8 h-8 cursor-pointer" @mousemove="onStarHover($event,n)" @mouseleave="onStarLeave" @click="onStarClick($event,n)">
                  <svg class="w-8 h-8 text-gray-300 absolute inset-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.174c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.97c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.84-.197-1.54-1.118l1.287-3.97a1 1 0 00-.364-1.118L2.05 9.397c-.783-.57-.38-1.81.588-1.81h4.174a1 1 0 00.95-.69l1.287-3.97z"/></svg>
                  <div class="absolute inset-0 overflow-hidden pointer-events-none" :style="{ width: getFillWidth(n) + '%' }">
                    <svg class="w-8 h-8 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.174c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.97c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.84-.197-1.54-1.118l1.287-3.97a1 1 0 00-.364-1.118L2.05 9.397c-.783-.57-.38-1.81.588-1.81h4.174a1 1 0 00.95-.69l1.287-3.97z"/></svg>
                  </div>
                </div>
                <span v-if="newReview.rating" class="ml-2 text-lg font-semibold text-gray-900 dark:text-white">{{ newReview.rating }} z 5</span>
              </div>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Váš komentár</label>
              <textarea v-model="newReview.comment" rows="4" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Napíšte svoju recenziu..."></textarea>
            </div>

            <div class="flex gap-3">
              <button @click="submitReview" :disabled="submittingReview || newReview.rating === 0" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed">{{ submittingReview ? 'Odosielám...' : 'Odoslať recenziu' }}</button>
            </div>
            <p v-if="reviewError" class="text-red-600 dark:text-red-400 mt-3">{{ reviewError }}</p>
          </div>
        </div>

        <!-- LATEST REVIEWS -->
        <div v-if="product.latest_reviews && product.latest_reviews.length" class="mb-6">
          <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Najnovšie recenzie</h2>
          <div class="space-y-4">
            <div v-for="rev in product.latest_reviews" :key="rev.id" class="p-5 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800">
              <div v-if="editingReviewId !== rev.id">
                <div class="flex items-center justify-between mb-3">
                  <div>
                    <div class="font-semibold text-gray-900 dark:text-white">{{ rev.user_name || 'Používateľ' }}</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">{{ new Date(rev.created_at).toLocaleDateString('sk-SK') }}</div>
                  </div>
                  <div class="flex items-center gap-3">
                    <div class="text-lg font-semibold text-yellow-500">{{ rev.rating }} ★</div>
                    <div v-if="user && user.id === rev.user_id" class="flex gap-2">
                      <button @click="startEditReview(rev)" class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">Upraviť</button>
                      <button @click="promptDeleteReview(rev.id)" class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium">Vymazať</button>
                    </div>
                  </div>
                </div>
                <p class="text-gray-700 dark:text-gray-300">{{ rev.comment }}</p>
              </div>

              <div v-else>
                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Hodnotenie</label>
                  <div class="flex items-center gap-2">
                    <div v-for="n in 5" :key="n" class="relative w-6 h-6 cursor-pointer" @mousemove="(e)=>{const rect = e.currentTarget.getBoundingClientRect(); const x = e.clientX - rect.left; const half = rect.width/2; editHoverRating = x < half ? n - 0.5 : n }" @mouseleave="editHoverRating = 0" @click="(e)=>{const rect = e.currentTarget.getBoundingClientRect(); const x = e.clientX - rect.left; const half = rect.width/2; editingReview.rating = x < half ? n - 0.5 : n }">
                      <svg class="w-6 h-6 text-gray-300 absolute inset-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.174c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.97c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.84-.197-1.54-1.118l1.287-3.97a1 1 0 00-.364-1.118L2.05 9.397c-.783-.57-.38-1.81.588-1.81h4.174a1 1 0 00.95-.69l1.287-3.97z"/></svg>
                      <div class="absolute inset-0 overflow-hidden pointer-events-none" :style="{ width: getEditFillWidth(n) + '%' }"><svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.174c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.97c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.84-.197-1.54-1.118l1.287-3.97a1 1 0 00-.364-1.118L2.05 9.397c-.783-.57-.38-1.81.588-1.81h4.174a1 1 0 00.95-.69l1.287-3.97z"/></svg></div>
                    </div>
                    <span v-if="editingReview.rating" class="ml-2 text-sm font-semibold text-gray-900 dark:text-white">{{ editingReview.rating }} z 5</span>
                  </div>
                </div>

                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Váš komentár</label>
                  <textarea v-model="editingReview.comment" rows="3" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white" placeholder="Napíšte svoju recenziu..."></textarea>
                </div>

                <div class="flex gap-2">
                  <button @click="submitEditReview(rev.id)" :disabled="updatingReview || editingReview.rating === 0" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed">{{ updatingReview ? 'Ukladám...' : 'Uložiť zmeny' }}</button>
                  <button @click="cancelEditReview" type="button" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800">Zrušiť</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Delete confirmation modal (duplicate safe - ensures modal always in template hierarchy) -->
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg w-full max-w-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $t ? $t('product.confirm_delete_title') : 'Potvrdenie' }}</h3>
          <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">{{ $t ? $t('product.review_delete_confirm') : 'Naozaj chcete vymazať svoju recenziu?' }}</p>
          <div class="flex justify-end gap-3">
            <button @click="cancelDelete" class="px-4 py-2 border rounded-lg bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white">{{ $t ? $t('common.cancel') : 'Zrušiť' }}</button>
            <button @click="confirmDelete" :disabled="deleteProcessing" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed">{{ deleteProcessing ? ($t ? $t('common.deleting') : 'Maže sa...') : ($t ? $t('common.delete') : 'Vymazať') }}</button>
          </div>
        </div>
      </div>

    </main>
  </div>
</template>

<script>
import api from "@/api";
import SideBarComponent from "../components/SideBarComponent.vue";
import { useCartStore } from "../stores/cartStore";

export default {
  name: "ProductView",
  components: { SideBarComponent },
  setup() {
    const cartStore = useCartStore();
    return { cartStore };
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
      },
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
    };
  },

  async mounted() {
    await this.loadProduct();
  },

  methods: {
    async loadProduct() {
      this.loading = true;
      this.error = null;
      try {
        const id = this.$route.params.slug;
        const response = await api.get(`/api/products/${id}`);
        this.product = response.data;
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
        };
      } finally {
        this.loading = false;
      }
    },
    formatPrice(price) {
      return parseFloat(price).toFixed(2);
    },
    addToCart() {
      this.cartStore.addToCart({
        id: this.product.id,
        product_id: this.product.id,
        name: this.product.title || this.product.name,
        price: this.product.price,
        image: this.product.image,
        description: this.product.description,
        quantity: 1,
      });
      alert(`${this.product.title || this.product.name} pridaný do košíka!`);
    },
    async submitReview() {
      if (!this.user) {
        this.reviewError = "Musíte byť prihlásený, aby ste mohli písať recenzie.";
        return;
      }
      if (!this.newReview.rating) {
        this.reviewError = "Vyberte hodnotenie.";
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
            this.product.latest_reviews = this.product.latest_reviews.filter((r) => r.user_id !== this.user.id);
          }
          this.product.latest_reviews.unshift(resp.data.review);
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
        const resp = await api.delete(`/api/products/${this.product.id}/reviews/${reviewId}`);
        if (resp.data && resp.data.success) {
          // Remove from list
          this.product.latest_reviews = this.product.latest_reviews.filter((r) => r.id !== reviewId);
          // Update aggregates
          this.product.rating = resp.data.rating ?? this.product.rating;
          this.product.reviews = resp.data.reviews ?? this.product.reviews;
          this.$notify && this.$notify({ type: "success", text: this.$t?.("product.review_deleted") || "Recenzia bola vymazaná." });
        }
      } catch (e) {
        console.error("Failed to delete review", e);
        this.$notify && this.$notify({ type: "error", text: this.$t?.("product.review_delete_failed") || "Chyba pri vymazaní recenzie." });
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
      this.editingReview.rating = review.rating;
      this.editingReview.comment = review.comment;
    },
    cancelEditReview() {
      this.editingReviewId = null;
      this.editingReview.rating = 0;
      this.editingReview.comment = "";
    },
    async submitEditReview(reviewId) {
      if (!this.editingReview.rating) {
        alert("Vyberte hodnotenie.");
        return;
      }

      this.updatingReview = true;
      try {
        const resp = await api.put(`/api/products/${this.product.id}/reviews/${reviewId}`, {
          rating: this.editingReview.rating,
          comment: this.editingReview.comment,
        });

        if (resp.data.success) {
          // Update the review in the list
          const idx = this.product.latest_reviews.findIndex((r) => r.id === reviewId);
          if (idx !== -1) {
            this.product.latest_reviews[idx] = resp.data.review;
          }
          // Update aggregates
          this.product.rating = resp.data.rating;
          this.product.reviews = resp.data.reviews;
          this.cancelEditReview();
        }
      } catch (e) {
        console.error("Failed to update review", e);
        alert("Chyba pri úprave recenzie.");
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
  },
};
</script>
