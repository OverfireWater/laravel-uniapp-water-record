import App from './App.vue'
import orangeUtil from './js_sdk/orange-util/orange-util.js'
import store from './store'
import updateTitle from './mixins/updateTitle.js'

import Vue from 'vue'
import uView from '@/uni_modules/uview-ui'
Vue.use(orangeUtil)
Vue.use(uView)
Vue.mixin(updateTitle)
Vue.config.productionTip = false
App.mpType = 'app'
const app = new Vue({
	store,
	...App
})
app.$mount()
