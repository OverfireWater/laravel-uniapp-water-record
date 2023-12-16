import {
    reqGetUserConsumeType,
    reqGetUserConsumeTypeShowData,
    reqGetUserRelatedType,
    reqDeleteUserType,
    reqAddUserType,
    reqUpdateUserType
} from "@/api/module/userType";
import store from "@/store";

export default {
    namespaced: true,
    state: {
        consumeType: {},
        userTypeExpendPrice: [],
        userTypeIncomePrice: [],
        userRelatedToExpendParentType: [],
        userRelatedToIncomeParentType: []
    },
    mutations: {
        SET_USER_CONSUME_TYPE(state, data) {
            state.consumeType = data
        },
        SET_USER_TYPE_PRICE(state, data) {
            if (data) {
                state.userTypeExpendPrice = data.expendData
                state.userTypeIncomePrice = data.incomeData
            } else {
                state.userTypeExpendPrice = []
                state.userTypeIncomePrice = []
            }
        },
        SET_USER_RELATED_TO_PARENT_TYPE(state, data) {
            if (data){
                state.userRelatedToExpendParentType = data.ExpendType
                state.userRelatedToIncomeParentType = data.IncomeType
            }else {
                state.userRelatedToExpendParentType = []
                state.userRelatedToIncomeParentType = []
            }
        }
    },
    actions: {
        //获取消费类型
        getUserConsumeType({commit}) {
            let res = reqGetUserConsumeType()
            return res.then(res => {
                commit('SET_USER_CONSUME_TYPE', res.data)
                return Promise.resolve(res.code)
            }).catch(error => {
                commit('SET_USER_CONSUME_TYPE', '')
                return Promise.reject(error.code)
            })
        },
        //获取分类展示的数据
        getUserTypeShowData({commit}) {
            return new Promise((resolve, reject) => {
                reqGetUserConsumeTypeShowData().then(res => {
                    commit('SET_USER_TYPE_PRICE', res.data)
                    resolve('ok')
                }).catch(error => {
                    commit('SET_USER_TYPE_PRICE', '')
                    reject('error')
                })
            })
        },
        // 获取父类型下的子类型
        getUserRelatedToParentType({commit}) {
            return new Promise((resolve, reject) => {
                reqGetUserRelatedType().then(res => {
                    commit('SET_USER_RELATED_TO_PARENT_TYPE', res.data)
                    resolve('ok')
                }).catch(err => {
                    commit('SET_USER_RELATED_TO_PARENT_TYPE', '')
                    reject('error')
                })
            })
        },
        // 添加类型
        addOrEditUserType({commit, dispatch}, data) {
            return new Promise((resolve, reject) => {
                let temp= ''
                if (data.isAdd){
                    temp = reqAddUserType
                }else {
                    temp = reqUpdateUserType
                }
                temp(data).then(res=>{
                    dispatch('getUserRelatedToParentType')
                    uni.showToast({
                        icon: 'success'
                    })
                    resolve('ok')
                }).catch(error=>{
                    reject('error')
                })
            })
        },

        // 点击删除时
        deleteUserType({commit, dispatch}, typeId) {
            return new Promise((resolve, reject) => {
                reqDeleteUserType(typeId).then(res => {
                    dispatch('getUserRelatedToParentType')
                    resolve('ok')
                    uni.showToast({
                        icon: 'success'
                    })
                }).catch(err => {
                    reject('error')
                })
            })
        }
    },
    getters: {
        // 获取支出全部类型
        getExpendAllType(state) {
            let common = state.consumeType.expend || {}
            return common.commonType || []
        },
        // 获取支出用户类型
        getExpendUserType(state) {
            let user = state.consumeType.expend || {}
            return user.userType || []
        },
        // 获取收入全部类型
        getIncomeAllType(state) {
            let common = state.consumeType.income || {}
            return common.commonType || []
        },
        // 获取收入用户类型
        getIncomeUserType(state) {
            let user = state.consumeType.income || {}
            return user.userType || []
        },

        // 合并支出
        processedExpendData(state) {
            return state.userTypeExpendPrice.map((item) => {
                const percent = ((item.price / parseFloat(store.state.consume.userMonthAllExpend.replace(/,/g, '')) * 100) || 0).toFixed(2);
                const price = parseFloat(item.price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                return {...item, percent, price};
            });
        },
        // 合并收入
        processedIncomeData(state) {
            return state.userTypeIncomePrice.map((item) => {
                const percent = ((item.price / parseFloat(store.state.consume.userMonthAllIncome.replace(/,/g, '')) * 100) || 0).toFixed(2);
                const price = parseFloat(item.price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                return {...item, percent, price};
            });
        },
    }
}