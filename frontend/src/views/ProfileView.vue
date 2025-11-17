<template>
	<div class="min-h-screen bg-white dark:bg-gray-900">
		<div class="max-w-4xl mx-auto px-6 py-12">
			<!-- Profile Header -->
			<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 mb-6">
				<div class="flex items-start gap-6">
					<div class="relative">
						<img 
							:src="user.avatar" 
							:alt="user.name"
							class="w-24 h-24 rounded-full object-cover border-4 border-gray-200 dark:border-gray-700"
						/>
						<button 
							@click="changeAvatar"
							class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-2 shadow-lg transition-colors"
						>
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
							</svg>
						</button>
					</div>
					
					<div class="flex-1">
						<h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ user.name }}</h1>
						<p class="text-gray-600 dark:text-gray-400 mt-1">{{ user.email }}</p>
						<div class="flex gap-2 mt-3">
							<span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm font-medium rounded-full">
								{{ user.memberSince }}
							</span>
							<span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-sm font-medium rounded-full">
								{{ user.totalOrders }} objednávok
							</span>
						</div>
					</div>

					<button 
						@click="editMode = !editMode"
						class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-medium transition-colors"
					>
						{{ editMode ? 'Zrušiť' : 'Upraviť profil' }}
					</button>
				</div>
			</div>

			<!-- Profile Details -->
			<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8">
				<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Osobné údaje</h2>
				
				<div class="space-y-6">
					<!-- Name -->
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
							Meno a priezvisko
						</label>
						<input 
							v-model="user.name"
							:disabled="!editMode"
							type="text"
							class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed"
						/>
					</div>

					<!-- Email -->
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
							Email
						</label>
						<input 
							v-model="user.email"
							:disabled="!editMode"
							type="email"
							class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed"
						/>
					</div>

					<!-- Phone -->
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
							Telefón
						</label>
						<input 
							v-model="user.phone"
							:disabled="!editMode"
							type="tel"
							class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed"
						/>
					</div>

					<!-- Address -->
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
							Adresa
						</label>
						<input 
							v-model="user.address"
							:disabled="!editMode"
							type="text"
							class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed"
						/>
					</div>

					<!-- City and ZIP -->
					<div class="grid grid-cols-2 gap-4">
						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
								Mesto
							</label>
							<input 
								v-model="user.city"
								:disabled="!editMode"
								type="text"
								class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed"
							/>
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
								PSČ
							</label>
							<input 
								v-model="user.zip"
								:disabled="!editMode"
								type="text"
								class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed"
							/>
						</div>
					</div>

					<!-- Save Button -->
					<div v-if="editMode" class="flex gap-3 pt-4">
						<button 
							@click="saveProfile"
							class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors"
						>
							Uložiť zmeny
						</button>
						<button 
							@click="cancelEdit"
							class="px-6 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-medium transition-colors"
						>
							Zrušiť
						</button>
					</div>
				</div>
			</div>

			<!-- Order History -->
			<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 mt-6">
				<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">História objednávok</h2>
				
				<div class="space-y-4">
					<div 
						v-for="order in orders" 
						:key="order.id"
						class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
					>
						<div class="flex-1">
							<div class="flex items-center gap-3">
								<span class="font-semibold text-gray-900 dark:text-white">Objednávka #{{ order.id }}</span>
								<span 
									class="px-2 py-1 text-xs font-medium rounded"
									:class="{
										'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200': order.status === 'Doručené',
										'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200': order.status === 'V preprave',
										'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200': order.status === 'Spracováva sa'
									}"
								>
									{{ order.status }}
								</span>
							</div>
							<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ order.date }}</p>
						</div>
						<div class="text-right">
							<p class="font-bold text-gray-900 dark:text-white">{{ order.total }} €</p>
							<button class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 mt-1">
								Zobraziť detail
							</button>
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
			editMode: false,
			user: {
				name: '',
				email: '',
				phone: '',
				address: '',
				city: '',
				zip: '',
				avatar: '',
				memberSince: 'Člen od ',
				totalOrders: 0
			},
			originalUser: null,
			orders: []
		}
	},
	methods: {
		changeAvatar() {
			alert('Funkcia zmeny avatara - tu by sa otvoril file picker')
		},
		saveProfile() {
			// Tu by sa poslali údaje na server
			console.log('Saving profile:', this.user)
			this.editMode = false
			this.originalUser = null
			alert('Profil bol úspešne uložený!')
		},
		cancelEdit() {
			if (this.originalUser) {
				this.user = { ...this.originalUser }
			}
			this.editMode = false
			this.originalUser = null
		}
	},
	watch: {
		editMode(newValue) {
			if (newValue) {
				// Uložiť kópiu pôvodných údajov
				this.originalUser = { ...this.user }
			}
		}
	}
}
</script>