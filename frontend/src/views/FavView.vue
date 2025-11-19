<template>
	<div class="min-h-screen bg-white dark:bg-gray-900">
		<div class="max-w-7xl mx-auto px-6 py-12">
			<div class="mb-8">
				<div class="flex items-center justify-between mb-6">
					<div>
						<h1 class="text-3xl font-bold text-gray-900 dark:text-white">Obľúbené</h1>
						<p class="text-gray-600 dark:text-gray-400 mt-2">{{ favorites.length }} produktov</p>
					</div>
					
					<div class="flex gap-3">
						<button 
							@click="viewMode = 'grid'"
							class="p-2 rounded-lg transition-colors"
							:class="viewMode === 'grid' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'"
						>
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
							</svg>
						</button>
						<button 
							@click="viewMode = 'list'"
							class="p-2 rounded-lg transition-colors"
							:class="viewMode === 'list' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'"
						>
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
							</svg>
						</button>
					</div>
				</div>

				<!-- Total Price Bar -->
				<div v-if="favorites.length > 0" class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border border-green-200 dark:border-green-800 rounded-lg p-4 flex items-center justify-between">
					<div class="flex items-center gap-6">
						<div>
							<p class="text-sm text-gray-600 dark:text-gray-400">Celkovo bez DPH</p>
							<p class="text-xl font-bold text-gray-900 dark:text-white">{{ formatPrice(totalPriceWithoutVAT) }} €</p>
						</div>
						<div>
							<p class="text-sm text-gray-600 dark:text-gray-400">Celkovo s DPH</p>
							<p class="text-2xl font-bold text-green-700 dark:text-green-400">{{ formatPrice(totalPriceWithVAT) }} €</p>
						</div>
					</div>
					<button 
						@click="addAllToCart"
						class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-colors flex items-center gap-2 shadow-lg"
					>
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
						</svg>
						Pridať všetko do košíka
					</button>
				</div>
			</div>

			<!-- Empty State -->
			<div v-if="favorites.length === 0" class="bg-gray-50 dark:bg-gray-800 rounded-lg p-12 text-center">
				<svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
				</svg>
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Žiadne obľúbené produkty</h3>
				<p class="text-gray-600 dark:text-gray-400 mb-6">Pridajte si produkty do obľúbených pre rýchly prístup</p>
				<button class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
					<router-link to="/" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold">

					Prejsť na nákup
					</router-link>
				</button>
			</div>

			<!-- Grid View -->
			<div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
				<div 
					v-for="item in favorites" 
					:key="item.id"
					class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden group hover:shadow-lg transition-shadow"
				>
					<div class="relative">
						<img :src="item.image" :alt="item.name" class="w-full h-48 object-cover" />
						<button 
							@click="removeFromFavorites(item.id)"
							class="absolute top-3 right-3 w-10 h-10 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center shadow-lg hover:bg-red-50 dark:hover:bg-red-900 transition-colors"
						>
							<svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
								<path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
							</svg>
						</button>
						<span 
							v-if="!item.inStock"
							class="absolute top-3 left-3 px-3 py-1 bg-red-600 text-white text-xs font-semibold rounded-full"
						>
							Vypredané
						</span>
					</div>
					
					<div class="p-4">
						<h3 class="font-semibold text-gray-900 dark:text-white mb-1 line-clamp-2">{{ item.name }}</h3>
						<p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-1">{{ item.category }}</p>
						
						<div class="flex items-center justify-between">
							<div>
								<p class="text-xl font-bold text-gray-900 dark:text-white">{{ item.price }} €</p>
								<p v-if="item.originalPrice" class="text-sm text-gray-500 dark:text-gray-400 line-through">{{ item.originalPrice }} €</p>
							</div>
							<button 
								@click="addToCart(item.id)"
								:disabled="!item.inStock"
								class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed dark:disabled:bg-gray-700"
							>
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
								</svg>
							</button>
						</div>
					</div>
				</div>
			</div>

			<!-- List View -->
			<div v-else class="space-y-4">
				<div 
					v-for="item in favorites" 
					:key="item.id"
					class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow"
				>
					<div class="flex gap-6">
						<div class="relative flex-shrink-0">
							<img :src="item.image" :alt="item.name" class="w-32 h-32 object-cover rounded-lg" />
							<span 
								v-if="!item.inStock"
								class="absolute top-2 left-2 px-2 py-1 bg-red-600 text-white text-xs font-semibold rounded"
							>
								Vypredané
							</span>
						</div>
						
						<div class="flex-1 min-w-0">
							<div class="flex items-start justify-between">
								<div class="flex-1">
									<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ item.name }}</h3>
									<p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ item.category }}</p>
									<p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">{{ item.description }}</p>
								</div>
								<button 
									@click="removeFromFavorites(item.id)"
									class="ml-4 p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900 rounded-lg transition-colors"
								>
									<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
										<path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
									</svg>
								</button>
							</div>
							
							<div class="flex items-center justify-between mt-4">
								<div class="flex items-center gap-3">
									<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ item.price }} €</p>
									<p v-if="item.originalPrice" class="text-lg text-gray-500 dark:text-gray-400 line-through">{{ item.originalPrice }} €</p>
								</div>
								<button 
									@click="addToCart(item.id)"
									:disabled="!item.inStock"
									class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed dark:disabled:bg-gray-700 flex items-center gap-2"
								>
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
									</svg>
									Pridať do košíka
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	data() {
		return {
			viewMode: 'grid', // 'grid' or 'list'
			vatRate: 0.20, // 20% DPH
			favorites: [
				{
					id: 1,
					name: 'iPhone 15 Pro Max',
					description: '256GB, Natural Titanium, najpokročilejší iPhone s titánovým dizajnom',
					category: 'Mobilné telefóny',
					price: 1299.00,
					originalPrice: 1499.00,
					image: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=400&h=400&fit=crop',
					inStock: true
				},
				{
					id: 2,
					name: 'MacBook Pro 14"',
					description: 'M3 Pro chip, 18GB RAM, 512GB SSD, Space Black',
					category: 'Notebooky',
					price: 2299.00,
					originalPrice: null,
					image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400&h=400&fit=crop',
					inStock: true
				},
				{
					id: 3,
					name: 'AirPods Pro (2. generácia)',
					description: 'S USB-C nabíjaním, adaptívne potlačenie hluku',
					category: 'Audio',
					price: 279.00,
					originalPrice: null,
					image: 'https://images.unsplash.com/photo-1606841837239-c5a1a4a07af7?w=400&h=400&fit=crop',
					inStock: true
				},
				{
					id: 4,
					name: 'Apple Watch Series 9',
					description: '45mm, GPS + Cellular, Midnight Aluminum',
					category: 'Smart hodinky',
					price: 499.00,
					originalPrice: 549.00,
					image: 'https://images.unsplash.com/photo-1434494878577-86c23bcb06b9?w=400&h=400&fit=crop',
					inStock: false
				},
				{
					id: 5,
					name: 'iPad Air 11"',
					description: 'M2 chip, 128GB, Wi-Fi, Space Gray',
					category: 'Tablety',
					price: 699.00,
					originalPrice: null,
					image: 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400&h=400&fit=crop',
					inStock: true
				},
				{
					id: 6,
					name: 'Sony WH-1000XM5',
					description: 'Prémiové bezdrôtové slúchadlá s najlepším ANC',
					category: 'Audio',
					price: 349.00,
					originalPrice: 399.00,
					image: 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=400&h=400&fit=crop',
					inStock: true
				},
				{
					id: 7,
					name: 'Samsung Galaxy S24 Ultra',
					description: '512GB, Titanium Gray, S Pen v balení',
					category: 'Mobilné telefóny',
					price: 1399.00,
					originalPrice: null,
					image: 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=400&h=400&fit=crop',
					inStock: true
				},
				{
					id: 8,
					name: 'DJI Mini 4 Pro',
					description: '4K video, 34 min letového času, obstacle avoidance',
					category: 'Drony',
					price: 759.00,
					originalPrice: 849.00,
					image: 'https://images.unsplash.com/photo-1473968512647-3e447244af8f?w=400&h=400&fit=crop',
					inStock: false
				}
			]
		}
	},
	computed: {
		totalPriceWithVAT() {
			return this.favorites
				.filter(item => item.inStock)
				.reduce((sum, item) => sum + item.price, 0)
		},
		totalPriceWithoutVAT() {
			return this.totalPriceWithVAT / (1 + this.vatRate)
		}
	},
	methods: {
		formatPrice(price) {
			return price.toFixed(2)
		},
		removeFromFavorites(itemId) {
			this.favorites = this.favorites.filter(item => item.id !== itemId)
		},
		addToCart(itemId) {
			console.log('Adding to cart:', itemId)
			// Tu pridáš logiku pre pridanie do košíka
		},
		addAllToCart() {
			const availableItems = this.favorites.filter(item => item.inStock)
			console.log('Adding all to cart:', availableItems)
			// Tu pridáš logiku pre pridanie všetkých dostupných produktov do košíka
			alert(`Pridaných ${availableItems.length} produktov do košíka!`)
		}
	}
}
</script>