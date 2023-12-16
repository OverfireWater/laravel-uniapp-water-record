import request from '@/utils/request.js'

//获取消费类型的api
export const reqGetUserConsumeType = () => {
    return request('/type/getUserConsumeType', 'get')
}
// 获取消费类型数据的api
export const reqGetUserConsumeTypeShowData = () => {
    return request('/type/getUserConsumeTypeShowData', 'get')
}

/*
* 获取父类型带子类型的api
* /type/getUserRelatedToType
* */
export const reqGetUserRelatedType = () => {
    return request('/type/getUserRelatedToType', 'get')
}

/*
* 添加类型
* /type/addUerType
* */
export const reqAddUserType = (data) => {
    return request('/type/addUserType', 'post', data)
}

/*
* 修改类型
* /type/updateUserType
* */
export const reqUpdateUserType = (data) => {
    return request('/type/updateUserType', 'put', data,)
}

/*
* 删除类型
* /type/deleteUserType/{typeId}
* */
export const reqDeleteUserType = (typeId) => {
    return request(`/type/deleteUserType/${typeId}`, 'delete')
}