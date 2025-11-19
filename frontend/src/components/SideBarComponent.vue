<template>

	<aside class="w-64 bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 h-full top-15">
		<nav aria-label="Kategórie" >
			<ul class="space-y-1 pt-5 px-3">
				<li v-for="item in items" :key="item.key">
					<button
						@click="select(item.key)"
						class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
						:title="item.label"
					>
						<span class="w-6 h-6 flex-shrink-0" aria-hidden="true" v-html="item.icon"></span>
						<span class="truncate">{{ item.label }}</span>
					</button>
				</li>
			</ul>
		</nav>
	</aside>

</template>

<script>
export default {
	name: 'SideBarComponent',
	emits: ['select'],
	data() {
		return {
			items: [
				{
					key: 'phones',
					label: 'Mobily, smartfóny',
					icon: `
						<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' class='w-6 h-6'>
							<rect x='7' y='2' width='10' height='20' rx='2' ry='2' stroke-width='1.5'/>
							<circle cx='12' cy='18' r='0.5' fill='currentColor'/>
						</svg>`
				},
				{
					key: 'notebooks',
					label: 'Notebooky',
					icon: `
						<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' class='w-6 h-6'>
							<path d='M3 6h18v10H3z' stroke-width='1.5'/>
							<path d='M2 18h20' stroke-width='1.5' stroke-linecap='round'/>
						</svg>`
				},
				{
					key: 'tablets',
					label: 'Tablety',
					icon: `
						<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' class='w-6 h-6'>
							<rect x='6' y='2' width='12' height='18' rx='2' ry='2' stroke-width='1.5'/>
							<path d='M12 18v.01' stroke-width='1.5' stroke-linecap='round' />
						</svg>`
				},
				{
					key: 'pcs',
					label: 'PC a stolné počítače',
					icon: `
						<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' class='w-6 h-6'>
							<rect x='2' y='3' width='20' height='12' rx='1' ry='1' stroke-width='1.5'/>
							<path d='M8 21h8' stroke-width='1.5' stroke-linecap='round'/>
							<path d='M12 15v6' stroke-width='1.5' stroke-linecap='round'/>
						</svg>`
				},
				{
					key: 'powerbanks',
					label: 'Powerbanky & batérie',
					icon: `
						<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' class='w-6 h-6'>
							<rect x='3' y='6' width='14' height='12' rx='2' ry='2' stroke-width='1.5' />
							<path d='M19 10v4' stroke-width='1.5' stroke-linecap='round' />
							<path d='M7 9v6' stroke-width='1.5' stroke-linecap='round' />
						</svg>`
				}
			]
		}
	},
	methods: {
		select(key) {
			this.$emit('select', key)
		}
	}
}
</script>

<style scoped>
/* keep it compact and drive look from Tailwind; custom overrides minimal */
.truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>