import { createApp } from 'vue'
import axios from 'axios'
import Cookies from 'js-cookie';
import App from './App.vue'
import store from './store'
import bus from './bus'

const app = createApp(App)
app.config.globalProperties.$axios = axios;
app.config.globalProperties.$Cookies = Cookies;
app.config.globalProperties.$bus = bus

app.config.devtools = true


// moment js
import moment from "moment"

// fontawesome
import '@fortawesome/fontawesome-free/css/all.css'
import '@fortawesome/fontawesome-free/js/all.js'

app.use(store,axios,moment,Cookies).mount('#app')
