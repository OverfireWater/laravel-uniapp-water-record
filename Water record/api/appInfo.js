import request from '@/utils/request.js'

// 获取最新app信息
export const reqGetNewAppInfo = () => {
	return request('/app/getNewAppInfo', 'get', {}, false)
}