import { reqGetNewAppInfo } from '@/api/appInfo.js'
import { resolve } from 'url'
import { checkUpdate } from "@/components/app-update/js/app-update-check.js";

export default {
	namespaced: true,
	state: {
		appInfo: {}
	},
	mutations: {
		SET_APP_INFO(state, data) {
			state.appInfo = data
		}
	},
	actions: {
		getNewAppInfo({commit, state}, type) {
			return reqGetNewAppInfo().then(res=>{
				let {data} = res
				data.silent = data.silent === '是' ? 1 : 0
				data.force = data.force === '是' ? 1 : 0
				data.net_check = data.net_check === '是' ? 1 : 0
				data.issue = data.issue === '是' ? 1 : 0
				commit('SET_APP_INFO', data)
				checkUpdate(state.appInfo, type).then(res => {
					if (res.msg) {
						plus.nativeUI.toast(res.msg);
					}
				});
				return Promise.resolve('ok')
			}).catch(error=>{
				return Promise.reject(error)
			})
		}
	},
	getters: {}
}