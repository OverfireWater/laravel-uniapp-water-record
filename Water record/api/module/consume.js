// 发送消费的操作api
import request from '@/utils/request.js'

//添加消费金钱的api
export const reqAddConsume = (content) => {
    if (content.id == "" || content.id == null || isNaN(content.id)) {
        return request('/consume/add', 'post', content)
    } else {
        return request('/consume/update', 'put', content)
    }
}

// 获取用户今日消费
export const reqGetUserTodayPrice = () => {
	return request('/consume/getTodayPrice', 'get')
}

//获取所有用户消费金额的api
export const reqAllGetUserConsumePrice = () => {
    return request('/consume/getUserAllConsume', 'get')
}

//获取某个用户的某个消费
// /getUserConsume/{consumeId} get
export const reqGetUserConsume = (consumeId) => {
    return request(`/consume/getUserConsume/${consumeId}`, 'get')
}

// 删除某个消费
export const reqDeleteConsume = (consumeId) => {
    return request(`/consume/delete/${consumeId}`, 'delete')
}
