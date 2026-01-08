<template>
    <div class="min-h-screen bg-white dark:bg-gray-900">
        <div class="max-w-5xl mx-auto px-6 py-12">
            <!-- Back Button -->
            <button 
                @click="$router.push('/orders')"
                class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-6 transition-colors"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ $t("pages.refund.back_button") }}
            </button>

            <!-- Refund Header -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ $t("pages.refund.title") }} #{{ refund.id }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ $t("pages.refund.created_label") }} {{ refund.date }}
                        </p>
                    </div>
                    <span 
                        class="px-4 py-2 rounded-lg font-semibold text-sm"
                        :class="getStatusClass(refund.status)"
                    >
                        {{ refund.status }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ $t("pages.refund.payment_method") }}</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ refund.payment }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ $t("pages.refund.amount_label") }}</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ formatPrice(refund.amount) }} €</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ $t("pages.refund.reason_label") }}</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ refund.reason }}</p>
                    </div>
                </div>
            </div>

            <!-- Refund Status Timeline -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">{{ $t("pages.refund.status_title") }}</h2>
                
                <div class="space-y-4">
                    <div 
                        v-for="(step, index) in refundSteps" 
                        :key="index"
                        class="flex gap-4"
                    >
                        <!-- Timeline Line -->
                        <div class="flex flex-col items-center">
                            <div 
                                class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                                :class="step.completed ? 'bg-green-500 text-white' : step.active ? 'bg-blue-500 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400'"
                            >
                                <svg v-if="step.completed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span v-else class="text-sm font-semibold">{{ index + 1 }}</span>
                            </div>
                            <div 
                                v-if="index < refundSteps.length - 1"
                                class="w-0.5 h-full min-h-[40px] mt-2"
                                :class="step.completed ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600'"
                            ></div>
                        </div>

                        <!-- Step Content -->
                        <div class="flex-1 pb-8">
                            <h3 
                                class="font-semibold mb-1"
                                :class="step.completed || step.active ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
                            >
                                {{ step.title }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ step.description }}</p>
                            <p v-if="step.time" class="text-xs text-gray-500 dark:text-gray-500">{{ step.time }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Refund Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">{{ $t("pages.refund.actions_title") }}</h2>
                
                <div class="flex gap-3">
                    <button class="flex-1 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors">
                        {{ $t("pages.refund.contact_support") }}
                    </button>
                    <button class="px-6 py-3 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded-lg font-semibold transition-colors">
                        {{ $t("pages.refund.download_confirmation") }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            refund: {
                id: 'R-2025-001',
                date: '16. november 2025, 09:45',
                status: this.$t("pages.refund.status_processing"),
                payment: 'Kreditná karta',
                amount: 279.00,
                reason: 'Vrátenie tovaru - AirPods Pro'
            },
            refundSteps: [
                {
                    title: this.$t("pages.refund.step1_title"),
                    description: this.$t("pages.refund.step1_desc"),
                    time: '16. november 2025, 09:45',
                    completed: true,
                    active: false
                },
                {
                    title: this.$t("pages.refund.step2_title"),
                    description: this.$t("pages.refund.step2_desc"),
                    time: '16. november 2025, 11:00',
                    completed: false,
                    active: true
                },
                {
                    title: this.$t("pages.refund.step3_title"),
                    description: this.$t("pages.refund.step3_desc"),
                    time: null,
                    completed: false,
                    active: false
                },
                {
                    title: this.$t("pages.refund.step4_title"),
                    description: this.$t("pages.refund.step4_desc"),
                    time: null,
                    completed: false,
                    active: false
                }
            ]
        }
    },
    mounted() {
        window.scrollTo({ top: 0, behavior: 'smooth' })
    },
    methods: {
        formatPrice(price) {
            return price.toFixed(2)
        },
        getStatusClass(status) {
            const classes = {
                [this.$t("pages.refund.status_processing")]: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                [this.$t("pages.refund.status_sent")]: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                [this.$t("pages.refund.status_completed")]: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                [this.$t("pages.refund.status_cancelled")]: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
            }
            return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
        }
    }
}
</script>
