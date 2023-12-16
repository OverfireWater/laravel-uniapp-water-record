import {
  reqGetUserRelatedToType,
  reqGetPaginateAndSearchRelatedToParentChildrenType,
  reqAddChildrenType,
  reqAddParentType,
  reqUpdateParentType,
  reqUpdateChildrenType,
  reqDeleteParentType,
  reqDeleteChildrenType
} from "@/api/type/type";
import {error} from "autoprefixer/lib/utils";

export default {
  namespaced: true,
  state: {
    expendParentAndChildrenType: [],
    incomeParentAndChildrenType: [],
    searchConsumeTypeList: [],
  },
  mutations: {
    SET_EXPEND_PARENT_TYPE(state, data) {
      state.expendParentAndChildrenType = data
    },
    SET_INCOME_PARENT_TYPE(state, data) {
      state.incomeParentAndChildrenType = data
    },
    SET_SEARCH_CONSUME_TYPE_LIST(state, data) {
      state.searchConsumeTypeList = data
    }
  },
  actions: {
    // 获取父类型和子类型
    getUserParentAndChildrenType({commit}) {
      return new Promise((resolve, reject) => {
        reqGetUserRelatedToType().then(res => {
          let {ExpendType, IncomeType} = res.data
          commit('SET_EXPEND_PARENT_TYPE', ExpendType)
          commit('SET_INCOME_PARENT_TYPE', IncomeType)
          resolve('ok')
        }).catch(() => {
          reject('error')
        })
      })
    },
    // 获取分页的子类型数据
    getPaginateAndSearchRelatedToParentChildrenType({commit}, data) {
      return new Promise((resolve, reject) => {
        reqGetPaginateAndSearchRelatedToParentChildrenType(data).then(res => {
          commit('SET_SEARCH_CONSUME_TYPE_LIST', res.data)
          resolve('ok')
        }).catch(() => {
          reject('error');
        })
      })
    },
    // 添加 | 修改 父类型
    addAndUpdateParentTYpe({commit}, data) {
      return new Promise((resolve, reject) => {
        let temp = ''
        temp = !data.parentTypeId ? reqAddParentType : reqUpdateParentType
        temp(data).then(()=>{
          resolve('ok')
        }).catch(()=>{})
      })
    },
    // 删除父类
    vxDeleteParentType({commit}, typeId){
      return new Promise((resolve,reject)=>{
        reqDeleteParentType(typeId).then(()=>{
          resolve('ok')
        }).catch(()=>{})
      })
    },
    // 添加 | 修改 子类型
    addAndUpdateChildrenType({commit}, data) {
      return new Promise((resolve, reject) => {
        let temp = ''
        temp = !data.typeId ? reqAddChildrenType : reqUpdateChildrenType
        temp(data).then(res => {
          resolve('ok')
        }).catch(error => {
        })
      })
    },
    // 删除子类型
    deleteChildrenType({commit}, typeId) {
      return new Promise((resolve, reject) => {
        reqDeleteChildrenType(typeId).then(() => {
          resolve("ok")
        }).catch(() => {
        })
      })
    }
  },
  getters: {}
}
