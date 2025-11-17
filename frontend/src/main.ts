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

app.use(createPinia())
app.use(router)
.component("font-awesome-icon", FontAwesomeIcon)
app.mount('#app')
