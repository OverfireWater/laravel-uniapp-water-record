import App from './App.vue'
import orangeUtil from './js_sdk/orange-util/orange-util.js'
import store from './store'
import updateTitle from './mixins/updateTitle.js'
// #ifndef VUE3
import Vue from 'vue'
Vue.use(orangeUtil)
Vue.mixin(updateTitle)
Vue.config.productionTip = false
App.mpType = 'app'
const app = new Vue({
	store,
	...App
})
app.$mount()
// #endif

// #ifdef VUE3
import {
	createSSRApp
} from 'vue'
export function createApp() {
	const app = createSSRApp(App)
	return {
		store,
		app
	}
}
// #endif
