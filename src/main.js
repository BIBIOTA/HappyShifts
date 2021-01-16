import { createApp } from 'vue'
import App from './App.vue'
import store from './store'
import bus from './bus'

const app = createApp(App)
app.config.globalProperties.$bus = bus

// moment js
import moment from "moment"

// fontawesome
import '@fortawesome/fontawesome-free/css/all.css'
import '@fortawesome/fontawesome-free/js/all.js'

app.use(store,moment).mount('#app')

