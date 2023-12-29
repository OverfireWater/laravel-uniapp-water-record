import {
	destoryToken
} from './tokenService.js'
import store from '@/store/index.js'
// 此模块是关于uni-request的请求的封装

let BASE_URL = ''
let BASE_API = '/api'

if (process.env.NODE_ENV === 'development') {
	BASE_URL = 'http://127.0.0.1:7000'
	// BASE_URL = 'http://192.168.217.200:7000'
	// BASE_URL = 'http://172.20.10.2:7000'
} else {
	BASE_URL = 'https://water.api.gzykzy.top'
}
let URL = BASE_URL + BASE_API
/**
 * 全局请求封装
 * @param path 请求路径
 * @param method 请求类型(GET/POST/DELETE等)
 * @param data 请求体数据
 * @param loading 请求未完成是是否显示加载中，默认为true
 * @param isToken 是否需要token ture|false
 */
export default (path, method, data = {}, isToken = true, loading = true) => {
	if (loading) {
		uni.showLoading({
			title: "加载中",
			mask: true
		});
	}
	if (isToken) {
		const token = uni.getStorageSync('token');
		return tokenRequest(path, method, data, token)
	} else {
		return noTokenRequest(path, method, data)
	}
}

function tokenRequest(path, method, data, token) {

	let header = {
		"X-Requested-With": "XMLHttpRequest",
		'Authorization': 'Bearer ' + token
	}
	return new Promise((resolve, reject) => {
		uni.request({
			url: URL + path,
			data,
			method: method,
			dataType: 'json',
			header,
			success(res) {
				if (res.data.code === 200) {
					resolve(res.data)
				} else if (res.data.code === 1001 || res.data.code === 1000 || res.data.code === 1002) {
					// 判断token是否过期，如果过期了，直接删除前端本地token，同时告诉用户，重新登陆
					store.commit('user/RESSET_TOKEN')
					destoryToken()
					reject(res.data)
				}else {
					reject(res.data)
				}
			},
			fail(err) {
				uni.showToast({
					icon: 'error',
					title: "网络错误"
				})
			},
			complete() {
				uni.hideLoading()
			}
		})
	})
}

function noTokenRequest(path, method, data) {
	let header = {
		"X-Requested-With": "XMLHttpRequest"
	}
	return new Promise((resolve, reject) => {
		uni.request({
			url: URL + path,
			data,
			method: method,
			dataType: 'json',
			header,
			success(res) {
				if (res.data.code === 200) {
					resolve(res.data)
				} else {
					reject(res.data)
				}
			},
			fail(err) {
				uni.showToast({
					icon: 'error',
					title: "网络错误"
				})
			},
			complete() {
				uni.hideLoading()
			}
		})
	})
}