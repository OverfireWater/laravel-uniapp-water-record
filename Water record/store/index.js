import Vue from 'vue'
import Vuex from 'vuex'
import user from './module/user.js'
import consume from './module/consume.js'
import userType from "./module/userType.js";
import appInfo from './module/appInfo.js'

Vue.use(Vuex)

const store = new Vuex.Store({
	modules:{
		user,
		consume,
		appInfo,
		userType
	}
})
export default store