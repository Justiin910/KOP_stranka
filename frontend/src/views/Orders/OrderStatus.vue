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
				Späť na objednávky
			</button>

			<!-- Order Header -->
			<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
				<div class="flex items-start justify-between mb-4">
					<div>
						<h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
							Objednávka #{{ order.id }}
						</h1>
						<p class="text-gray-600 dark:text-gray-400">
							Objednaná {{ order.date }}
						</p>
					</div>
					<span 
						class="px-4 py-2 rounded-lg font-semibold text-sm"
						:class="getStatusClass(order.status)"
					>
						{{ order.status }}
					</span>
				</div>

				<div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
					<div>
						<p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Doručovacia adresa</p>
						<p class="font-medium text-gray-900 dark:text-white">{{ order.address.name }}</p>
						<p class="text-sm text-gray-600 dark:text-gray-400">{{ order.address.street }}</p>
						<p class="text-sm text-gray-600 dark:text-gray-400">{{ order.address.city }}, {{ order.address.zip }}</p>
					</div>
					<div>
						<p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Spôsob platby</p>
						<p class="font-medium text-gray-900 dark:text-white">{{ order.payment }}</p>
					</div>
					<div>
						<p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Spôsob doručenia</p>
						<p class="font-medium text-gray-900 dark:text-white">{{ order.delivery }}</p>
					</div>
				</div>
			</div>

			<!-- Order Status Timeline -->
			<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
				<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Stav objednávky</h2>
				
				<div class="space-y-4">
					<div 
						v-for="(step, index) in statusSteps" 
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
								v-if="index < statusSteps.length - 1"
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
							
							<!-- Tracking Number -->
							<div v-if="step.trackingNumber" class="mt-3 flex items-center gap-2">
								<span class="text-sm text-gray-600 dark:text-gray-400">Sledovacie číslo:</span>
								<span class="font-mono text-sm font-semibold text-gray-900 dark:text-white">{{ step.trackingNumber }}</span>
								<button class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
									</svg>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Order Items -->
			<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
				<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Produkty v objednávke</h2>
				
				<div class="space-y-4">
					<div 
						v-for="item in order.items" 
						:key="item.id"
						class="flex gap-4 pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0 last:pb-0"
					>
						<img :src="item.image" :alt="item.name" class="w-20 h-20 object-cover rounded-lg" />
						<div class="flex-1">
							<h3 class="font-semibold text-gray-900 dark:text-white">{{ item.name }}</h3>
							<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ item.variant }}</p>
							<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Množstvo: {{ item.quantity }}</p>
						</div>
						<div class="text-right">
							<p class="font-bold text-gray-900 dark:text-white">{{ formatPrice(item.price * item.quantity) }} €</p>
							<p class="text-sm text-gray-500 dark:text-gray-400">{{ formatPrice(item.price) }} € / ks</p>
						</div>
					</div>
				</div>
			</div>

			<!-- Order Summary -->
			<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
				<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Súhrn objednávky</h2>
				
				<div class="space-y-3">
					<div class="flex justify-between text-gray-600 dark:text-gray-400">
						<span>Medzisúčet</span>
						<span>{{ formatPrice(order.subtotal) }} €</span>
					</div>
					<div class="flex justify-between text-gray-600 dark:text-gray-400">
						<span>Doprava</span>
						<span>{{ formatPrice(order.shipping) }} €</span>
					</div>
					<div v-if="order.discount" class="flex justify-between text-green-600 dark:text-green-400">
						<span>Zľava</span>
						<span>-{{ formatPrice(order.discount) }} €</span>
					</div>
					<div class="border-t border-gray-200 dark:border-gray-700 pt-3 flex justify-between text-xl font-bold text-gray-900 dark:text-white">
						<span>Celkom</span>
						<span>{{ formatPrice(order.total) }} €</span>
					</div>
				</div>

				<!-- Actions -->
				<div class="mt-6 flex gap-3">
					<button class="flex-1 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors">
						Kontaktovať podporu
					</button>
					<button class="px-6 py-3 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded-lg font-semibold transition-colors">
						Stiahnuť faktúru
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
			order: {
				id: '10234',
				date: '15. november 2024, 14:30',
				status: 'V preprave',
				address: {
					name: 'Ján Novák',
					street: 'Hlavná 123',
					city: 'Bratislava',
					zip: '811 01'
				},
				payment: 'Kreditná karta',
				delivery: 'Kuriér - Slovenská pošta',
				items: [
					{
						id: 1,
						name: 'iPhone 15 Pro Max',
						variant: '256GB, Natural Titanium',
						price: 1299.00,
						quantity: 1,
						image: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=200&h=200&fit=crop'
					},
					{
						id: 2,
						name: 'AirPods Pro (2. generácia)',
						variant: 'S USB-C nabíjaním',
						price: 279.00,
						quantity: 1,
						image: 'https://images.unsplash.com/photo-1606841837239-c5a1a4a07af7?w=200&h=200&fit=crop'
					}
				],
				subtotal: 1578.00,
				shipping: 4.99,
				discount: 0,
				total: 1582.99
			},
			statusSteps: [
				{
					title: 'Objednávka prijatá',
					description: 'Vaša objednávka bola úspešne prijatá a čaká na spracovanie.',
					time: '15. november 2024, 14:30',
					completed: true,
					active: false
				},
				{
					title: 'Objednávka spracovaná',
					description: 'Vaša objednávka bola spracovaná a pripravená na odoslanie.',
					time: '15. november 2024, 16:45',
					completed: true,
					active: false
				},
				{
					title: 'Odoslaná kuriérom',
					description: 'Vaša zásielka je na ceste k vám.',
					time: '16. november 2024, 08:15',
					completed: false,
					active: true,
					trackingNumber: 'SK123456789'
				},
				{
					title: 'Doručenie',
					description: 'Zásielka bude doručená na vašu adresu.',
					time: 'Očakávaný čas: 17. november 2024, 10:00 - 14:00',
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
				'Spracováva sa': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
				'V preprave': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
				'Doručené': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
				'Zrušené': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
			}
			return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
		}
	}
}
</script>