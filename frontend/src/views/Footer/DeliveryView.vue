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
				Späť
			</button>

			<!-- Header -->
			<h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Doručenie</h1>

			<!-- Content Sections -->
			<div class="space-y-6">
				<!-- Delivery Methods -->
				<section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
					<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Spôsoby doručenia</h2>
					
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
					<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Čas doručenia</h2>
					<div class="space-y-3 text-gray-600 dark:text-gray-400">
						<p>Objednávky prijaté do <strong class="text-gray-900 dark:text-white">15:00</strong> sú expedované v ten istý pracovný deň.</p>
						<p>Objednávky prijaté po 15:00 alebo cez víkend sú expedované nasledujúci pracovný deň.</p>
						<p class="text-sm bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
							<strong>Tip:</strong> Pre rýchlejšie doručenie odporúčame objednávať v pracovných dňoch do 15:00.
						</p>
					</div>
				</section>

				<!-- Free Delivery -->
				<section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
					<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Doprava zdarma</h2>
					<div class="space-y-3 text-gray-600 dark:text-gray-400">
						<p>Pri objednávke nad <strong class="text-gray-900 dark:text-white">50 €</strong> je doprava úplne zadarmo!</p>
						<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
							<div class="flex items-start gap-3">
								<svg class="w-6 h-6 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
								</svg>
								<div>
									<p class="font-semibold text-green-900 dark:text-green-100 mb-1">Doprava zdarma</p>
									<p class="text-sm text-green-800 dark:text-green-200">Pri objednávke nad 50 € neplatíte žiadne poplatky za doručenie.</p>
								</div>
							</div>
						</div>
					</div>
				</section>

				<!-- Tracking -->
				<section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
					<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Sledovanie zásielky</h2>
					<div class="space-y-3 text-gray-600 dark:text-gray-400">
						<p>Po odoslaní objednávky vám na e-mail príde sledovacie číslo zásielky.</p>
						<p>Stav objednávky môžete sledovať aj v sekcii <router-link to="/orders/status" class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">Stav objednávky</router-link>.</p>
					</div>
				</section>

				<!-- Contact -->
				<section class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-6">
					<h3 class="font-semibold text-gray-900 dark:text-white mb-2">Potrebujete pomoc?</h3>
					<p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
						Ak máte otázky k doručeniu, neváhajte nás kontaktovať.
					</p>
					<button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors">
						Kontaktovať podporu
					</button>
				</section>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'DeliveryView',
	data() {
		return {
			deliveryMethods: [
				{
					id: 1,
					name: 'Kuriér - Slovenská pošta',
					price: '4,99 €',
					description: 'Doručenie do 2-3 pracovných dní. Kuriér vás bude kontaktovať pred doručením.',
					features: [
						'Doručenie na vašu adresu',
						'Sledovanie zásielky online',
						'Možnosť platby na dobierku'
					]
				},
				{
					id: 2,
					name: 'Packeta - výdajné miesto',
					price: '3,49 €',
					description: 'Doručenie na vybraté výdajné miesto do 2-3 pracovných dní.',
					features: [
						'Viac ako 2000 výdajných miest na Slovensku',
						'Predĺžená doba na vyzdvihnutie (7 dní)',
						'Notifikácia SMS a e-mailom'
					]
				},
				{
					id: 3,
					name: 'Osobný odber',
					price: 'Zdarma',
					description: 'Vyzdvihnite si objednávku na našej predajni v Bratislave.',
					features: [
						'Pripravené do 24 hodín',
						'Otvorené Po-Pia 9:00-18:00',
						'Adresa: Hlavná 123, Bratislava'
					]
				}
			]
		}
	},
	mounted() {
		window.scrollTo({ top: 0, behavior: 'smooth' })
	}
}
</script>