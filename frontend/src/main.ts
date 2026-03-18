import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import routes from './router'

const app = createApp(App)

app.use(createPinia())
app.use(routes);
app.mount('#app')
