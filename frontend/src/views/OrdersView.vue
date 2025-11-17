<template>
	<div class="min-h-screen bg-white dark:bg-gray-900">
		<div class="max-w-6xl mx-auto px-6 py-12">
			<!-- Header -->
			<div class="mb-8">
				<h1 class="text-3xl font-bold text-gray-900 dark:text-white">História objednávok</h1>
				<p class="text-gray-600 dark:text-gray-400 mt-2">
					Celkovo {{ orders.length }} objednávok
				</p>
			</div>

			<!-- Filters -->
			<div class="flex gap-2 mb-6 overflow-x-auto pb-2">
				<button 
					v-for="filter in filters" 
					:key="filter.value"
					@click="activeFilter = filter.value"
					class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-colors"
					:class="activeFilter === filter.value 
						? 'bg-indigo-600 text-white' 
						: 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
				>
					{{ filter.label }}
				</button>
			</div>

			<!-- Orders List -->
			<div class="space-y-4">
				<div 
					v-for="order in filteredOrders" 
					:key="order.id"
					class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow"
				>
					<div class="p-6">
						<!-- Order Header -->
						<div class="flex items-start justify-between mb-4">
							<div>
								<div class="flex items-center gap-3 mb-2">
									<h3 class="text-lg font-bold text-gray-900 dark:text-white">
										Objednávka #{{ order.id }}
									</h3>
									<span 
										class="px-3 py-1 rounded-full text-xs font-semibold"
										:class="getStatusClass(order.status)"
									>
										{{ order.status }}
									</span>
								</div>
								<p class="text-sm text-gray-600 dark:text-gray-400">
									{{ order.date }}
								</p>
							</div>
							<div class="text-right">
								<p class="text-2xl font-bold text-gray-900 dark:text-white">
									{{ formatPrice(order.total) }} €
								</p>
								<p class="text-sm text-gray-600 dark:text-gray-400">
									{{ order.itemsCount }} {{ order.itemsCount === 1 ? 'produkt' : order.itemsCount < 5 ? 'produkty' : 'produktov' }}
								</p>
							</div>
						</div>

						<!-- Order Items Preview -->
						<div class="flex gap-3 mb-4 overflow-x-auto pb-2">
							<img 
								v-for="(item, index) in order.items.slice(0, 4)" 
								:key="index"
								:src="item.image" 
								:alt="item.name"
								class="w-16 h-16 object-cover rounded-lg flex-shrink-0"
							/>
							<div 
								v-if="order.items.length > 4"
								class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0"
							>
								<span class="text-sm font-semibold text-gray-600 dark:text-gray-400">
									+{{ order.items.length - 4 }}
								</span>
							</div>
						</div>

						<!-- Actions -->
						<div class="flex gap-3">
							<button 
								@click="viewOrderStatus(order)"
								class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors"
							>
								{{ order.status === 'Doručené' ? 'Zobraziť detail' : 'Sledovať zásielku' }}
							</button>
							<button 
								v-if="order.status === 'Doručené'"
								class="px-4 py-2 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded-lg font-medium transition-colors"
							>
								Kúpiť znova
							</button>
							<button 
								v-if="order.status === 'Doručené'"
								class="px-4 py-2 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded-lg font-medium transition-colors"
							>
								Stiahnuť faktúru
							</button>
							<button 
								v-if="order.status === 'Spracováva sa'"
								@click="cancelOrder(order.id)"
								class="px-4 py-2 border border-red-300 dark:border-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg font-medium transition-colors"
							>
								Zrušiť
							</button>
                            <button 
                                v-if="order.status === 'Doručené'"
                                @click="refundOrder(order.id)"
                                class="px-4 py-2 border border-purple-300 dark:border-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg font-medium transition-colors"
                            >
                                Reklamovať
                            </button>
						</div>
					</div>
				</div>
			</div>

			<!-- Empty State -->
			<div v-if="filteredOrders.length === 0" class="text-center py-16">
				<svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
				</svg>
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
					Žiadne objednávky
				</h3>
				<p class="text-gray-600 dark:text-gray-400 mb-6">
					{{ activeFilter === 'all' ? 'Zatiaľ ste nevytvorili žiadnu objednávku' : 'Žiadne objednávky v tejto kategórii' }}
				</p>
				<button 
					@click="$router.push('/')"
					class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors"
				>
					Začať nakupovať
				</button>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	data() {
		return {
			activeFilter: 'all',
			filters: [
				{ label: 'Všetky', value: 'all' },
				{ label: 'V procese', value: 'processing' },
				{ label: 'Doručené', value: 'delivered' },
				{ label: 'Zrušené', value: 'cancelled' }
			],
			orders: [
				{
					id: '10234',
					date: '15. november 2024',
					status: 'V preprave',
					total: 1582.99,
					itemsCount: 2,
					items: [
						{
							name: 'iPhone 15 Pro Max',
							image: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=200&h=200&fit=crop'
						},
						{
							name: 'AirPods Pro',
							image: 'https://images.unsplash.com/photo-1606841837239-c5a1a4a07af7?w=200&h=200&fit=crop'
						}
					]
				},
				{
					id: '10198',
					date: '02. november 2024',
					status: 'Spracováva sa',
					total: 89.90,
					itemsCount: 1,
					items: [
						{
							name: 'iPhone Case',
							image: 'https://images.unsplash.com/photo-1591337676887-a217a6970a8a?w=200&h=200&fit=crop'
						}
					]
				},
				{
					id: '10156',
					date: '28. október 2024',
					status: 'Doručené',
					total: 456.50,
					itemsCount: 3,
					items: [
						{
							name: 'Wireless Keyboard',
							image: 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=200&h=200&fit=crop'
						},
						{
							name: 'Mouse',
							image: 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=200&h=200&fit=crop'
						},
						{
							name: 'USB-C Cable',
							image: 'https://images.unsplash.com/photo-1625948515291-69613efd103f?w=200&h=200&fit=crop'
						}
					]
				},
				{
					id: '10089',
					date: '15. október 2024',
					status: 'Doručené',
					total: 2299.00,
					itemsCount: 1,
					items: [
						{
							name: 'MacBook Pro 14"',
							image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=200&h=200&fit=crop'
						}
					]
				},
				{
					id: '10034',
					date: '28. september 2024',
					status: 'Zrušené',
					total: 349.00,
					itemsCount: 1,
					items: [
						{
							name: 'Headphones',
							image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=200&h=200&fit=crop'
						}
					]
				}
			]
		}
	},
	computed: {
		filteredOrders() {
			if (this.activeFilter === 'all') {
				return this.orders
			}
			
			const statusMap = {
				'processing': ['Spracováva sa', 'V preprave'],
				'delivered': ['Doručené'],
				'cancelled': ['Zrušené']
			}
			
			return this.orders.filter(order => 
				statusMap[this.activeFilter].includes(order.status)
			)
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
		},
		viewOrderStatus(order) {
			// Presmerovanie na detail objednávky
			this.$router.push(`/orders/${order.id}`)
		},
		cancelOrder(orderId) {
			if (confirm('Naozaj chcete zrušiť túto objednávku?')) {
				const order = this.orders.find(o => o.id === orderId)
				if (order) {
					order.status = 'Zrušené'
				}
			}
		}
	}
}
</script>