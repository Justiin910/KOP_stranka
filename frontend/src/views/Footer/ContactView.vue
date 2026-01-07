<template>
  <div class="page-contact-bg">
    <div class="max-w-6xl mx-auto px-6">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Contact Info Section -->
        <section class="space-y-8">
          <div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
              {{ $t("pages.contact.title") }}
            </h1>
            <p class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed">
              {{ $t("pages.contact.intro") }}
            </p>
          </div>

          <!-- Contact Details Grid -->
          <div class="space-y-6">
            <div class="flex gap-4">
              <div class="flex-shrink-0">
                <div
                  class="flex items-center justify-center h-10 w-10 rounded-md bg-indigo-500 text-white"
                >
                  <svg
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"
                    ></path>
                  </svg>
                </div>
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ $t("pages.contact.office_title") }}
                </h3>
                <p
                  class="text-gray-700 dark:text-gray-300 text-sm mt-1"
                  v-html="officeAddressHtml"
                ></p>
              </div>
            </div>

            <div class="flex gap-4">
              <div class="flex-shrink-0">
                <div
                  class="flex items-center justify-center h-10 w-10 rounded-md bg-indigo-500 text-white"
                >
                  <svg
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                    ></path>
                  </svg>
                </div>
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ $t("pages.contact.email_title") }}
                </h3>
                <p class="text-gray-700 dark:text-gray-300 text-sm mt-1">
                  <a
                    :href="'mailto:' + $t('pages.contact.email_value')"
                    class="hover:text-indigo-600 dark:hover:text-indigo-400 transition"
                    >{{ $t("pages.contact.email_value") }}</a
                  >
                </p>
              </div>
            </div>

            <div class="flex gap-4">
              <div class="flex-shrink-0">
                <div
                  class="flex items-center justify-center h-10 w-10 rounded-md bg-indigo-500 text-white"
                >
                  <svg
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                    ></path>
                  </svg>
                </div>
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ $t("pages.contact.phone_title") }}
                </h3>
                <p class="text-gray-700 dark:text-gray-300 text-sm mt-1">
                  <a
                    :href="'tel:' + $t('pages.contact.phone_value').replace(/\s+/g, '')"
                    class="hover:text-indigo-600 dark:hover:text-indigo-400 transition"
                    >{{ $t("pages.contact.phone_value") }}</a
                  >
                </p>
              </div>
            </div>

            <div class="flex gap-4">
              <div class="flex-shrink-0">
                <div
                  class="flex items-center justify-center h-10 w-10 rounded-md bg-indigo-500 text-white"
                >
                  <svg
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                    ></path>
                  </svg>
                </div>
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ $t("pages.contact.hours_title") }}
                </h3>
                <p
                  class="text-gray-700 dark:text-gray-300 text-sm mt-1"
                  v-html="hoursHtml"
                ></p>
              </div>
            </div>
          </div>
        </section>

        <!-- Contact Form Section -->
        <section
          class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 border border-gray-100 dark:border-gray-700 h-fit"
        >
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
            {{ $t("pages.contact.form_title") }}
          </h2>
          <p class="text-gray-600 dark:text-gray-400 text-sm mb-6">
            {{ $t("pages.contact.form_desc") }}
          </p>
          <form @submit.prevent="submitContact" class="space-y-5">
            <div>
              <label
                for="name"
                class="block text-sm font-semibold text-gray-800 dark:text-gray-100 mb-2"
                >{{ $t("pages.contact.name_label") }}
                <span class="text-red-600 dark:text-red-400">*</span></label
              >
              <input
                id="name"
                v-model="form.name"
                type="text"
                :placeholder="$t('pages.contact.name_placeholder')"
                @input="fieldErrors.name = null"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-500 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
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

            <div>
              <label
                for="email"
                class="block text-sm font-semibold text-gray-800 dark:text-gray-100 mb-2"
                >{{ $t("pages.contact.email_label") }}
                <span class="text-red-600 dark:text-red-400">*</span></label
              >
              <input
                id="email"
                v-model="form.email"
                type="text"
                :placeholder="$t('pages.contact.email_placeholder')"
                @input="fieldErrors.email = null"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-500 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
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
            </div>

            <div>
              <label
                for="subject"
                class="block text-sm font-semibold text-gray-800 dark:text-gray-100 mb-2"
                >{{ $t("pages.contact.subject_label") }}
                <span class="text-red-600 dark:text-red-400">*</span></label
              >
              <input
                id="subject"
                v-model="form.subject"
                type="text"
                :placeholder="$t('pages.contact.subject_placeholder')"
                @input="fieldErrors.subject = null"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-500 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
              />
              <Transition name="slideDown">
                <p
                  v-if="fieldErrors.subject"
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
                  {{ fieldErrors.subject }}
                </p>
              </Transition>
            </div>

            <div>
              <label
                for="message"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >{{ $t("pages.contact.message_label") }}
                <span class="text-red-600 dark:text-red-400">*</span></label
              >
              <textarea
                id="message"
                v-model="form.message"
                rows="5"
                @input="fieldErrors.message = null"
                :placeholder="$t('pages.contact.message_placeholder')"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition resize-none"
              ></textarea>
              <Transition name="slideDown">
                <p
                  v-if="fieldErrors.message"
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
                  {{ fieldErrors.message }}
                </p>
              </Transition>
            </div>

            <div>
              <button type="submit" :disabled="sending" class="w-full btn-primary-lg">
                {{ sending ? $t("pages.contact.sending") : $t("pages.contact.submit") }}
              </button>
            </div>

            <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
              {{ $t("pages.contact.response_time") }}
            </p>
          </form>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent } from "vue";
import { useUiStore } from "@/stores/uiStore";
import api from "@/api";

export default defineComponent({
  name: "ContactView",
  setup() {
    const ui = useUiStore();
    return { ui };
  },
  data() {
    return {
      form: {
        name: "",
        email: "",
        subject: "",
        message: "",
      },
      sending: false,
      fieldErrors: {
        name: null,
        email: null,
        subject: null,
        message: null,
      },
    };
  },
  computed: {
    officeAddressHtml() {
      return this.$t("pages.contact.office_address").replace(/\n/g, "<br/>");
    },
    hoursHtml() {
      return this.$t("pages.contact.hours_value").replace(/\n/g, "<br/>");
    },
  },
  methods: {
    clearFieldErrors() {
      this.fieldErrors = {
        name: null,
        email: null,
        subject: null,
        message: null,
      };
    },
    validateEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
    },
    async submitContact() {
      this.clearFieldErrors();

      // Client-side validation with professional messages
      if (!this.form.name.trim()) {
        this.fieldErrors.name = this.$t("pages.contact.validation.name_required");
        this.ui.notify(this.$t("pages.contact.validation_notify"), "error");
        return;
      }

      if (!this.form.email.trim()) {
        this.fieldErrors.email = this.$t("pages.contact.validation.email_required");
        this.ui.notify(this.$t("pages.contact.validation_notify"), "error");
        return;
      }

      if (!this.validateEmail(this.form.email)) {
        this.fieldErrors.email = this.$t("pages.contact.validation.email_invalid");
        this.ui.notify(this.$t("pages.contact.input_check_notify"), "error");
        return;
      }

      if (!this.form.subject.trim()) {
        this.fieldErrors.subject = this.$t("pages.contact.validation.subject_required");
        this.ui.notify(this.$t("pages.contact.validation_notify"), "error");
        return;
      }

      if (!this.form.message.trim()) {
        this.fieldErrors.message = this.$t("pages.contact.validation.message_required");
        this.ui.notify(this.$t("pages.contact.validation_notify"), "error");
        return;
      }

      if (this.form.message.trim().length < 10) {
        this.fieldErrors.message = this.$t("pages.contact.validation.message_short");
        this.ui.notify(this.$t("pages.contact.validation_notify"), "error");
        return;
      }

      try {
        this.sending = true;
        const response = await api.post("/api/contact", {
          name: this.form.name.trim(),
          email: this.form.email.trim(),
          subject: this.form.subject.trim(),
          message: this.form.message.trim(),
        });

        if (response.data.success) {
          this.ui.notify(this.$t("pages.contact.success_notify"), "success");
          // Reset form
          this.form = {
            name: "",
            email: "",
            subject: "",
            message: "",
          };
          this.clearFieldErrors();
        }
      } catch (error) {
        console.error("Error sending contact form:", error);
        console.error("Error response:", error.response);

        // Handle validation errors from backend
        if (error.response?.status === 422) {
          const errors = error.response.data.errors || {};
          Object.keys(errors).forEach((field) => {
            if (this.fieldErrors.hasOwnProperty(field)) {
              this.fieldErrors[field] = errors[field][0];
            }
          });
          this.ui.notify(this.$t("pages.contact.validation_notify"), "error");
        } else if (error.response?.status === 500) {
          this.ui.notify(
            error.response.data.message || this.$t("pages.contact.error_notify"),
            "error"
          );
        } else {
          this.ui.notify(this.$t("pages.contact.error_notify"), "error");
        }
      } finally {
        this.sending = false;
      }
    },
  },
});
</script>

<style scoped>
h1,
h2 {
  letter-spacing: -0.01em;
}

.slideDown-enter-active,
.slideDown-leave-active {
  transition: all 0.3s ease;
}

.slideDown-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.slideDown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
