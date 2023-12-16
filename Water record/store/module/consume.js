import {
	reqAddConsume,
	reqAllGetUserConsumePrice,
	reqDeleteConsume,
	reqGetUserConsume,
	reqGetUserTodayPrice
} from '@/api/module/consume.js'

export default {
	namespaced: true,
	state: {
		userTodayExpend: '',
		userTodayIncome: '',
		userMonthAllExpend: '0.00',
		userMonthAllIncome: '0.00',
		userTodayPrice: [],
		userAllPrice: [],
		userPriceDetail: {},
	},
	mutations: {
		SET_USER_PRICE(state, data){
			state.userAllPrice = data
		},
		SET_USER_PRICE_DETAIL(state, data){
			state.userPriceDetail = data
		},
		SET_USER_TODAY_PRICE(state, data){
			if (data){
				state.userTodayPrice = data.data
				state.userTodayExpend = data.today_expend_price
				state.userTodayIncome = data.today_income_price
				state.userMonthAllExpend = data.month_all_expend_price
				state.userMonthAllIncome = data.month_all_income_price
			}else {
				state.userTodayPrice = []
				state.userTodayExpend = ''
				state.userTodayIncome = ''
				state.userMonthAllExpend = '0.00'
				state.userMonthAllIncome = '0.00'
			}
		},
	},
	actions: {
		//添加消费金额
		addConsumePrice({commit}, data) {
			let res = reqAddConsume(data)
			return res.then(res => {
				return Promise.resolve(res.code)
			}).catch(error => {
				uni.showToast({
					icon: 'error',
					title: '保存失败'
				})
				return Promise.reject(error.code)
			})
		},

		// 获取用户今日消费
		getUserTodayPrice({commit}){
			return new Promise((resolve, reject)=>{
				reqGetUserTodayPrice().then(res=>{
					commit('SET_USER_TODAY_PRICE', res.data)
					resolve('ok')
				}).catch(error=>{
					commit('SET_USER_TODAY_PRICE', '')
					reject(error)
				})
			})
		},

		// 获取用户的所有支出和收入金额
		getAllUserConsume({commit}){
			let res = reqAllGetUserConsumePrice()
			return res.then(res => {
				commit('SET_USER_PRICE', res.data)
				return Promise.resolve(res.code)
			}).catch(error => {
				commit('SET_USER_PRICE', [])
				return Promise.reject(error.code)
			})
		},
		// 获取某个用户的某个消费
		getUserConsumeDetail({commit},consumeId){
			let res = reqGetUserConsume(consumeId)
			return res.then(res => {
				commit('SET_USER_PRICE_DETAIL', res.data)
				return Promise.resolve(res.code)
			}).catch(error => {
				commit('SET_USER_PRICE_DETAIL', {})
				return Promise.reject(error.code)
			})
		},
		// 删除某个消费
		deleteUserConsume({commit},consumeId){
			let res = reqDeleteConsume(consumeId)
			return res.then(res => {
				return Promise.resolve(res.code)
			}).catch(error => {
				uni.showToast({
					icon: 'error',
					title: '删除失败'
				})
				return Promise.reject(error.code)
			})
		},
	},
	getters: {
		//生成每日的数据
		dayData(state){
			let data = state.userAllPrice || []
			let dayData = data.reduce((acc,cur)=>{
				let time = cur.updated_at.split(' ')[0]
				if(!acc.has(time)){
					acc.set(time,[])
				}
				acc.get(time).push(cur)
				return acc
			},new Map())
			let data2 = Array.from(dayData)
			// 创建一个空对象用于存储每天的花费总额
			const dailyExpenses = {}
			const dailyIncome = {}
			// 遍历数据
			for (const [date, expenses] of data2) {
				// 初始化每天的花费总额为0
				let totalExpense = 0
				let totalIncome = 0
				// 计算每天的花费总额
				for (const expense of expenses) {
					if (expense.isExpend === 0) {
						totalExpense += parseFloat(expense.price);
					} else {
						totalIncome += parseFloat(expense.price)
					}
				}
				// 将每天的花费总额存储到dailyExpenses对象中
				dailyExpenses[date] = totalExpense.toFixed(2);
				dailyIncome[date] = totalIncome.toFixed(2)
			}
			// 将每天的花费总额添加到原始数据中
			return data2.map(([date, expenses]) => {
				return [date, expenses, {
					"totalExpense": dailyExpenses[date],
					'totalIncome': dailyIncome[date]
				}];
			})
		}
	}
}