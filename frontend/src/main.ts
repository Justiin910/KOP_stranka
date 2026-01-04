//import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import '../src/assets/main.css'

import App from './App.vue'
import router from './router'


const app = createApp(App)

import { library, dom } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { fas } from '@fortawesome/free-solid-svg-icons'
import { fab } from '@fortawesome/free-brands-svg-icons';
import { far } from '@fortawesome/free-regular-svg-icons';
library.add(fas, far, fab)
dom.watch();

const pinia = createPinia()
app.use(pinia)
app.use(router)
.component("font-awesome-icon", FontAwesomeIcon)

// Preload server favorites once on app start when logged in to ensure
// product cards can reflect favorite status immediately after refresh.
if (localStorage.getItem('token')) {
	import('./api').then(({ default: api }) => {
		api.get('/api/favorites').then(resp => {
			const favs = resp.data || [];
			try { localStorage.setItem('favorites', JSON.stringify(favs)); } catch(e) {}
			try { window.__favorites_cache = favs.map(f => (typeof f === 'object' ? f.id : f)); } catch(e) { window.__favorites_cache = []; }
			window.dispatchEvent(new CustomEvent('favorites-remote-updated', { detail: window.__favorites_cache }));
		}).catch(() => {});
	}).catch(() => {});
}

// Setup global UI helpers (override native alert and provide async confirm)
import { useUiStore } from './stores/uiStore'

(window as any).alert = (msg?: any) => {
	try {
		const ui = useUiStore()
		ui.notify(typeof msg === 'string' ? msg : JSON.stringify(msg))
	} catch (e) {
		// fallback
		console.log(msg)
	}
}

// async confirm replacement: returns a Promise<boolean>
(window as any).appConfirm = (msg: string): Promise<boolean> => {
	const ui = useUiStore()
	return ui.confirm(msg)
}

app.mount('#app')
