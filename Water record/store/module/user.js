import {login, sendMail, captcha_login, resGetUserInfo} from '@/api/login.js'
import {setToken,getToken} from '@/utils/tokenService.js'

export default {
	namespaced: true,
	state: {
		token:getToken(),
		userInfo: {},
	},
	mutations: {
		RESSET_TOKEN(state){
			state.token = ''
		},
		GET_USER_TOKEN(state, token){
			state.token = token
		},
		GET_USER_INFO(state, userInfo){
			state.userInfo = userInfo
		}
	},
	actions: {
		//登陆，获取用户token
		getUserToken({commit}, user) {
			let res = ''
			if (user.ispwd) {
				res = login(user)
			} else {
				res = captcha_login(user)
			}
			return res.then(res => {
				commit('GET_USER_TOKEN',res.data)
				setToken(res.data)
				return Promise.resolve(res.code)
			}).catch(err => {
				if (err.code === 404){
					uni.showToast({
						icon:'error',
						title: '验证码错误'
					});
				}
				return Promise.reject(new Error(err.code))
			})
		},
		//点击发送验证码
		sendMail({commit},email){
			let res = sendMail(email)
			return res.then(res=>{
				return Promise.resolve(res.code)
			}).catch(error=>{
				return Promise.reject(new Error(error))
			})
		},
		//获取用户信息
		getUserInfo({commit}){
			let res = resGetUserInfo()
			return res.then(res=>{
				commit('GET_USER_INFO',res.data)
				return Promise.resolve(res.code)
			}).catch(error=>{
				commit('GET_USER_INFO','')
				return Promise.reject(new Error(error.code))
			})
		}
	},
	getters: {}
}