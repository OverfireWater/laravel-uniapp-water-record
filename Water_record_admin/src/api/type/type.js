import request from "@/utils/request";

//获取父类型和子类型
// /type/getUserRelatedToType
export const reqGetUserRelatedToType = () => {
  return request({
    url: '/type/getUserRelatedToType',
    method: 'get'
  })
}

// 获取分页可查询父类的子类型
// /admin/type/getConsumeType/{page}/{limit}/{parentTypeId}
export const reqGetPaginateAndSearchRelatedToParentChildrenType = (data) => {
  return request({
    url: `/admin/type/getConsumeType/${data.page}/${data.limit}/${data.parentTypeId}`,
    method: 'get'
  })
}

/*
* 添加父分类
* post
* */
export const reqAddParentType = (data) => {
  return request({
    url: '/admin/type/addParentType',
    method: 'post',
    data
  })
}
/*
* 修改父类
* /admin/type/updateParentType
* put
* */
export const reqUpdateParentType = (data) =>{
  return request({
    url: '/admin/type/updateParentType',
    method: 'put',
    data
  })
}

/*
* 删除父类型
* */

export const reqDeleteParentType = (id) => {
  return request({
    url: `/admin/type/deleteParentType/${id}`,
    method: 'delete'
  })
}

/*
* 添加子类型
* /type/addUserType
* post
* */
export const reqAddChildrenType = (data) =>{
  return request({
    url: '/type/addUserType',
    method: 'post',
    data
  })
}

/*
* 修改子类型
* */
export const reqUpdateChildrenType = (data) =>{
  return request({
    url: '/type/updateUserType',
    method: 'put',
    data
  })
}

/*
* 删除子类型
* */
export const reqDeleteChildrenType = (typeId) =>{
  return request({
    url: `/type/deleteUserType/${typeId}`,
    method: 'delete'
  })
}
