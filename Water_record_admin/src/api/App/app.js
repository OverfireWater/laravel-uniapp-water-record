import request from "@/utils/request";

// 获取所有的app的信息
export function reqGetAppInfo({page, limit}) {
  return request({
    url: `/app/getAppInfo/${page}/${limit}`,
    method: "GET"
  })
}

// 添加app更新
export function reqAddAppInfo(data) {
  return request({
    url: '/app/addAppInfo',
    method: 'POST',
    data
  })
}

// 获取app信息
export function reqGetAppInfoById(id) {
  return request({
    url: `/app/getAppInfoById/${id}`,
    method: 'get'
  })
}

// 修改app信息
export function reqEditAppInfo(data) {
  return request({
    url: '/app/updateAppInfo',
    method: 'put',
    data
  })
}

// 修改app状态
export function reqUpdateAppStatus({id, flag}){
  return request({
    url: `/app/updateStatus/${id}/${flag}`,
    method: 'put'
  })
}

// 删除app
export function reqDeleteAppInfo(id){
  return request({
    url: `/app/delete/${id}`,
    method: 'delete'
  })
}
