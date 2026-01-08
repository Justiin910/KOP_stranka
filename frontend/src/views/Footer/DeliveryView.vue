<template>
	<div class="min-h-screen bg-white dark:bg-gray-900">
		<div class="max-w-4xl mx-auto px-6 py-12">
			<!-- Back Button -->
			<button 
				@click="$router.push('/')"
				class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-6 transition-colors"
			>
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
				</svg>
				{{ $t("pages.delivery.back") }}
			</button>

			<!-- Header -->
			<h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">{{ $t("pages.delivery.title") }}</h1>

			<!-- Content Sections -->
			<div class="space-y-6">
				<!-- Delivery Methods -->
				<section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
					<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ $t("pages.delivery.methods_title") }}</h2>
					
					<div class="space-y-6">
						<div 
							v-for="method in deliveryMethods" 
							:key="method.id"
							class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0 last:pb-0"
						>
							<div class="flex justify-between items-start mb-2">
								<h3 class="font-semibold text-gray-900 dark:text-white">{{ method.name }}</h3>
								<span class="font-bold text-indigo-600 dark:text-indigo-400">{{ method.price }}</span>
							</div>
							<p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
								{{ method.description }}
							</p>
							<ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1 ml-4">
								<li v-for="(feature, index) in method.features" :key="index">
									• {{ feature }}
								</li>
							</ul>
						</div>
					</div>
				</section>

				<!-- Delivery Time -->
				<section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
					<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ $t("pages.delivery.time_title") }}</h2>
					<div class="space-y-3 text-gray-600 dark:text-gray-400">
						<p>{{ $t("pages.delivery.time_before") }} <strong class="text-gray-900 dark:text-white">15:00</strong> {{ $t("pages.delivery.time_after") }}</p>
						<p>{{ $t("pages.delivery.time_weekend") }}</p>
						<p class="text-sm bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
							<strong>{{ $t("pages.delivery.time_tip_label") }}</strong> {{ $t("pages.delivery.time_tip") }}
						</p>
					</div>
				</section>

				<!-- Free Delivery -->
				<section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
					<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ $t("pages.delivery.free_title") }}</h2>
					<div class="space-y-3 text-gray-600 dark:text-gray-400">
						<p>{{ $t("pages.delivery.free_amount") }} <strong class="text-gray-900 dark:text-white">50 €</strong> {{ $t("pages.delivery.free_text") }}</p>
						<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
							<div class="flex items-start gap-3">
								<svg class="w-6 h-6 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
								</svg>
								<div>
									<p class="font-semibold text-green-900 dark:text-green-100 mb-1">{{ $t("pages.delivery.free_title") }}</p>
									<p class="text-sm text-green-800 dark:text-green-200">{{ $t("pages.delivery.free_description") }}</p>
								</div>
							</div>
						</div>
					</div>
				</section>

				<!-- Tracking -->
				<section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
					<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ $t("pages.delivery.tracking_title") }}</h2>
					<div class="space-y-3 text-gray-600 dark:text-gray-400">
						<p>{{ $t("pages.delivery.tracking_text") }}</p>
						<p>{{ $t("pages.delivery.tracking_status") }} <router-link to="/orders/status" class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">{{ $t("pages.delivery.tracking_status_link") }}</router-link>.</p>
					</div>
				</section>

				<!-- Contact -->
				<section class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-6">
					<h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $t("pages.delivery.help_title") }}</h3>
					<p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
						{{ $t("pages.delivery.help_text") }}
					</p>
					<button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors">
						{{ $t("pages.delivery.help_button") }}
					</button>
				</section>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'DeliveryView',
	computed: {
		deliveryMethods() {
			const msgs = this.$i18n && this.$i18n.getLocaleMessage
				? this.$i18n.getLocaleMessage(this.$i18n.locale)
				: null

			const getFeatures = (methodKey) => {
				try {
					return msgs.pages.delivery.delivery_methods[methodKey].features || []
				} catch (e) {
					// fallback to $t which may return a string
					const val = this.$t(`pages.delivery.delivery_methods.${methodKey}.features`)
					return Array.isArray(val) ? val : (typeof val === 'string' ? [] : val || [])
				}
			}

			return [
				{
					id: 1,
					name: this.$t('pages.delivery.delivery_methods.method1.name'),
					price: this.$t('pages.delivery.delivery_methods.method1.price'),
					description: this.$t('pages.delivery.delivery_methods.method1.description'),
					features: getFeatures('method1')
				},
				{
					id: 2,
					name: this.$t('pages.delivery.delivery_methods.method2.name'),
					price: this.$t('pages.delivery.delivery_methods.method2.price'),
					description: this.$t('pages.delivery.delivery_methods.method2.description'),
					features: getFeatures('method2')
				},
				{
					id: 3,
					name: this.$t('pages.delivery.delivery_methods.method3.name'),
					price: this.$t('pages.delivery.delivery_methods.method3.price'),
					description: this.$t('pages.delivery.delivery_methods.method3.description'),
					features: getFeatures('method3')
				}
			]
		}
	},
	mounted() {
		window.scrollTo({ top: 0, behavior: 'smooth' })
	}
}
</script>