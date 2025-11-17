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
			<h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">O nás</h1>

			<!-- Content Sections -->
			<div class="space-y-6">
				<!-- Hero Section -->
				<section class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg p-8 text-white">
					<h2 class="text-2xl font-bold mb-3">TechShop - Váš partner v technológiách</h2>
					<p class="text-indigo-100">
						Prinášame kvalitné technológie za férové ceny od roku 2015. Veríme, že každý si zaslúži prístup k najnovším technologickým produktom.
					</p>
				</section>

				<!-- Our Story -->
				<section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
					<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Naša história</h2>
					<div class="space-y-3 text-gray-600 dark:text-gray-400">
						<p>
							TechShop vznikol v roku 2025 s jasnou víziou - sprístupniť moderné technológie všetkým. Začali sme ako malý e-shop s niekoľkými produktmi a dnes ponúkame tisíce produktov od svetových značiek.
						</p>
						<p>
							Za tie roky sme si získali dôveru viac ako <strong class="text-gray-900 dark:text-white">100 000 spokojných zákazníkov</strong> na Slovensku. Neustále sa snažíme zlepšovať naše služby a prinášať najlepšie riešenia pre našich klientov.
						</p>
						<p>
							Naša firma je certifikovaná a všetky produkty pochádzajú z overených zdrojov s plnou zárukou a podporou.
						</p>
					</div>
				</section>

				<!-- Values -->
				<section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
					<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Naše hodnoty</h2>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div 
							v-for="value in values" 
							:key="value.id"
							class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4"
						>
							<div class="flex items-start gap-3">
								<div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
									<div v-html="value.icon" class="text-indigo-600 dark:text-indigo-400"></div>
								</div>
								<div>
									<h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ value.title }}</h3>
									<p class="text-sm text-gray-600 dark:text-gray-400">{{ value.description }}</p>
								</div>
							</div>
						</div>
					</div>
				</section>

				<!-- Stats -->
				<section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
					<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 text-center">TechShop v číslach</h2>
					<div class="grid grid-cols-2 md:grid-cols-4 gap-6">
						<div 
							v-for="stat in stats" 
							:key="stat.id"
							class="text-center"
						>
							<div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mb-1">{{ stat.value }}</div>
							<div class="text-sm text-gray-600 dark:text-gray-400">{{ stat.label }}</div>
						</div>
					</div>
				</section>

				<!-- Team -->
				<section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
					<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Náš tím</h2>
					<div class="space-y-3 text-gray-600 dark:text-gray-400">
						<p>
							Za TechShop stojí tím profesionálov s dlhoročnými skúsenosťami v IT a elektronike. Naši odborníci sú pripravení poradiť vám s výberom produktov a poskytnúť technickú podporu.
						</p>
						<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mt-4">
							<p class="text-sm text-blue-800 dark:text-blue-200">
								<strong>Pridajte sa k nám!</strong> Hľadáme nových členov do tímu. Pozrite si naše voľné pozície na kariérnej stránke.
							</p>
						</div>
					</div>
				</section>

				<!-- Certifications -->
				<section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
					<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Certifikácie a partneri</h2>
					<div class="space-y-3 text-gray-600 dark:text-gray-400">
						<p>Sme autorizovaní predajcovia prémiových značiek:</p>
						<div class="flex flex-wrap gap-4 mt-4">
							<div 
								v-for="partner in partners" 
								:key="partner"
								class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm font-semibold text-gray-900 dark:text-white"
							>
								{{ partner }}
							</div>
						</div>
					</div>
				</section>

				<!-- Mission -->
				<section class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg p-6 text-white">
					<h2 class="text-xl font-bold mb-3">Naša misia</h2>
					<p class="text-purple-100">
						Našou misiou je byť najdôveryhodnejším obchodom s elektronikou na Slovensku. Chceme, aby nakupovanie technológií bolo jednoduché, bezpečné a príjemné. Veríme v dlhodobé vzťahy s našimi zákazníkmi postavené na dôvere a kvalite.
					</p>
				</section>

				<!-- Contact CTA -->
				<section class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-6 text-center">
					<h3 class="font-semibold text-gray-900 dark:text-white mb-2">Chcete vedieť viac?</h3>
					<p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
						Neváhajte nás kontaktovať. Radi vám povieme viac o našej firme a našich hodnotách.
					</p>
					<button 
						@click="$router.push('/contact')"
						class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors"
					>
						Kontaktujte nás
					</button>
				</section>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'AboutView',
	data() {
		return {
			values: [
				{
					id: 1,
					title: 'Kvalita',
					description: 'Ponúkame len overené produkty od renomovaných výrobcov.',
					icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>'
				},
				{
					id: 2,
					title: 'Zákaznícky servis',
					description: 'Náš tím je tu pre vás kedykoľvek potrebujete pomoc alebo radu.',
					icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>'
				},
				{
					id: 3,
					title: 'Transparentnosť',
					description: 'Všetky informácie o produktoch a cenách sú jasné a zrozumiteľné.',
					icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>'
				},
				{
					id: 4,
					title: 'Inovácie',
					description: 'Sledujeme najnovšie trendy a prinášame moderné riešenia.',
					icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>'
				}
			],
			stats: [
				{ id: 1, value: '100k+', label: 'Zákazníkov' },
				{ id: 2, value: '10+', label: 'Rokov skúseností' },
				{ id: 3, value: '5000+', label: 'Produktov' },
				{ id: 4, value: '99%', label: 'Spokojnosť' }
			],
			partners: [
				'Apple',
				'Samsung',
				'Sony',
				'LG',
				'Xiaomi',
				'Huawei',
				'Dell',
				'HP'
			]
		}
	},
	mounted() {
		window.scrollTo({ top: 0, behavior: 'smooth' })
	}
}
</script>