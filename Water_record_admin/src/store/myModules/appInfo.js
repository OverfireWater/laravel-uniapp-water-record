import {
  reqGetAppInfo,
  reqAddAppInfo,
  reqGetAppInfoById,
  reqEditAppInfo,
  reqUpdateAppStatus,
  reqDeleteAppInfo
} from '@/api/App/app'

export default {
  namespaced: true,
  state: {
    appInfoObj: {},
    appInfoById: {}
  },
  mutations: {
    SET_APP_INFO(state, data) {
      state.appInfoObj = data
    },
    SET_APP_INFO_BY_ID(state, data) {
      state.appInfoById = data
    }
  },
  actions: {
    // 获取app信息
    getAppInfo({commit}, data) {
      return reqGetAppInfo(data).then(res => {
        commit('SET_APP_INFO', res.data)
        return Promise.resolve('ok')
      }).catch(error => {
        console.log(error)
        return Promise.reject('error')
      })
    },
    // 添加app信息 | 修改app信息
    addAppInfo({commit}, data) {
      let res = ''
      if (data.id) {
        res = reqEditAppInfo(data)
      } else {
        res = reqAddAppInfo(data)
      }
      return res.then(res => {
        return Promise.resolve('ok')
      }).catch(error => {
        console.log(error)
        return Promise.reject('error')
      })
    },
    // 修改app状态
    updateAppStatus({commit}, data) {
      return reqUpdateAppStatus(data).then(res => {
        return Promise.resolve('ok')
      }).catch(error => {
        console.log(error)
        return Promise.reject('error')
      })
    },

    // 获取app信息
    getAppInfoById({commit}, data) {
      return reqGetAppInfoById(data).then(res => {
        commit('SET_APP_INFO_BY_ID', res.data)
        return Promise.resolve('ok')
      }).catch(error => {
        console.log(error)
        return Promise.reject(error)
      })
    },
    // 删除
    deleteApp({commit}, data) {
      return reqDeleteAppInfo(data).then(res=>{
        return Promise.resolve('ok')
      }).catch(error=>{
        return Promise.reject(error)
      })
    }
  }
}
