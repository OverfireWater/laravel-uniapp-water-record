<template>
  <view>
    <view id="consumePrice">
      <uni-segmented-control :current="current" :values="items" @clickItem="clickItem" styleType="text"
                             activeColor="#007aff"></uni-segmented-control>
      <uni-card :border="false" :is-full="true" :is-shadow="true"
                :shadow="'0px 10px 15px -15px rgba(0, 0, 0, 0.5)'">
        <view class="content">
          <view class="center" v-show="current === 0">
            <text class="active-price-type">{{ consume_type.t_name }}</text>
            <!-- 支出按钮 -->
            <text class="expend-price-font">{{ consume_price }}</text>
          </view>
          <view class="center" v-show="current === 1">
            <text class="active-price-type">{{ consume_type.t_name }}</text>
            <!-- 收入按钮 -->
            <text class="income-price-font">{{ consume_price }}</text>
          </view>
          <view class="center" style="margin-top: 5px;" v-show="remark">
            <image src="/static/icon/remark.png" mode=""
                   style="width: 15px;height: 15px;margin-right: 5px;"></image>
            <text class="font-14-opcity">{{ remark }}</text>
          </view>
        </view>
      </uni-card>
    </view>

    <ConsumeType :isExpendAllType="isExpendAllType" :isExpendUserType="isExpendUserType"
                 :consumeTypeHeight="height"></ConsumeType>
    <KeyboardNumber :remark="remark" :datetime="datetime" ref="keyboard"></KeyboardNumber>
  </view>
</template>

<script>
import ConsumeType from './consume-type/consume-type.vue'
import KeyboardNumber from './keyboard-number/keyboard-number.vue'
import {
  mapActions,
  mapState,
  mapGetters,
  mapMutations
} from 'vuex'

export default {
  components: {
    ConsumeType,
    KeyboardNumber
  },
  data() {
    return {
      items: ['支出', '收入'],
      current: 0,
      // 金钱
      consume_price: '0.00',
      // 消费类型
      consume_type: {},
      //备注
      remark: '',
      id: '',
      datetime: this.$util.getNowDT(),
      consumePriceHeight: '',
      statusBar: '',
      customBar: ''
    }
  },
  onLoad(options) {
    let {id} = options
    if (id) {
      this.id = id
      let res = this.getUserConsumeDetail(id)
      res.then(result => {
        let detail = this.userPriceDetail
        this.consume_price = detail.price
        this.consume_type = detail.w_type
        detail.isExpend === 1 ? this.current = 1 : this.current = 0
        this.remark = detail.remark
        this.datetime = detail.updated_at.replace(/CST/g, ' ')
      }).catch(error => {
      })
    }
    // 获取消费类型
    this.getUserConsumeType(),
    // 键盘输入时的数字
    uni.$on('change_price', this.change_price),
    // 点击保存
    uni.$on('save_price', this.save_price),
    //点击类型时的回调函数
    uni.$on('changeConsumeType', this.change_consume_type)
    //改变备注的文本
    uni.$on('changeRemark', (remark) => {
      this.remark = remark
    })
    //改变时间
    uni.$on('changeTime', (time) => {
      this.datetime = time
    })
  },
  // 页面卸载时，销毁所有通信事件
  onUnload() {
    uni.$off('change_price')
    uni.$off('save_price')
    uni.$off('changeConsumeType')
    uni.$off('changeRemark')
    uni.$off('changeTime')
  },
  mounted() {
    // 获取金额的高度
    uni.createSelectorQuery().in(this).select("#consumePrice").boundingClientRect(data => {
      this.consumePriceHeight = parseInt(data.height)
    }).exec()
    // 动态获取导航栏高度
    uni.getSystemInfo({
      success: (e) => {
        let statusBar = 0
        let customBar = 0
        // #ifdef MP
        statusBar = e.statusBarHeight
        customBar = e.statusBarHeight + 45
        if (e.platform === 'android') {
          customBar = e.statusBarHeight + 50
        }
        // #endif


        // #ifdef MP-WEIXIN
        statusBar = e.statusBarHeight
        // @ts-ignore
        const custom = wx.getMenuButtonBoundingClientRect()
        customBar = custom.bottom + custom.top - e.statusBarHeight - 44
        // #endif


        // #ifdef MP-ALIPAY
        statusBar = e.statusBarHeight
        customBar = e.statusBarHeight + e.titleBarHeight
        // #endif


        // #ifdef APP-PLUS
        statusBar = e.statusBarHeight
        customBar = e.statusBarHeight + 44
        // #endif


        // #ifdef H5
        statusBar = 0
        customBar = e.statusBarHeight + 44
        // #endif
        this.statusBar = statusBar
        this.customBar = customBar
      }
    })
  },
  computed: {
    ...mapGetters('userType', ['getExpendAllType', 'getExpendUserType', 'getIncomeAllType', 'getIncomeUserType']),
    ...mapState('consume', ['userPriceDetail']),
    // 判断是否为共有的类型 判断是否为 消费类型
    isExpendAllType() {
      if (this.current === 0) {
        return this.getExpendAllType || []
      } else {
        return this.getIncomeAllType || []
      }
    },
    // 是否为用户所添加的类型
    isExpendUserType() {
      if (this.current === 0) {
        return this.getExpendUserType || []
      } else {
        return this.getIncomeUserType || []
      }
    },
    height() {
      let {
        consumePriceHeight,
        customBar
      } = this
      return consumePriceHeight + customBar

    }
  },
  methods: {
    ...mapActions('consume', ['addConsumePrice', 'getUserConsumeDetail', 'getUserTodayPrice']),
    ...mapActions('userType', ['getUserConsumeType']),
    ...mapMutations('consume', ['SET_USER_PRICE_DETAIL']),
    // 点击切换收入和支出
    clickItem(e) {
      if (this.current !== e.currentIndex) {
        this.current = e.currentIndex;
      }
      if (Object.keys(this.userPriceDetail).length <= 0) return
      for (let item of this.isExpendAllType) {
        if (item.id === this.userPriceDetail.w_type.id) {
          this.consume_type = this.userPriceDetail.w_type
          break
        } else {
          this.consume_type = this.isExpendAllType[0]
        }
      }

    },
    //点击类型时的回调函数
    change_consume_type(obj) {
      this.consume_type = obj
    },
    // 金额改变时
    change_price(num) {
      let {
        consume_price
      } = this
      switch (num) {
        case 'dot':
          if (this.consume_price === '0.00') {
            this.consume_price = '0.'
          } else if (this.consume_price.indexOf('.') === -1) {
            this.consume_price += '.'
          }
          break;
        case 'D':
          if (consume_price === '0.00') return
          let price = consume_price.slice(0, -1)
          price = price.length != 0 ? price : '0.00'
          this.consume_price = price
          break;
        case 'C':
          this.consume_price = '0.00'
          break;
        default:
          if (this.id != "" && this.consume_price === this.userPriceDetail.price) {
            consume_price = ''
          }
          if (consume_price === '0.00' || consume_price === '0') {
            if (num === '0') break
            consume_price = ''
          }
          if (consume_price.indexOf('.') !== -1) {
            let angle = consume_price.split('.')[1]
            if (angle.length >= 2) {
              break
            }
          }

          this.consume_price = consume_price + num
          break;
      }
    },
    // 提交
    save_price() {
      if (this.consume_price !== '0.00') {
        let content = {
          id: parseInt(this.id),
          price: this.consume_price,
          typeId: this.consume_type.id,
          isExpend: this.current,
          remark: this.remark,
          datetime: this.datetime
        }
        this.addConsumePrice(content).then(res => {
          this.getUserTodayPrice().then(() => {
            uni.$emit('getUserTypeShowData')
            uni.switchTab({
              url: '/pages/tabbar/index/index',
              success: () => {
                this.SET_USER_PRICE_DETAIL('')
              }
            })
          }).catch(error => {
          })
        }).catch(error => {

        })
      }
    },
  },
  watch: {
    isExpendAllType(newVal, oldVal) {
      if (newVal.length > 0 && this.id === "") {
        uni.$emit('changeConsumeType', newVal[0])
      }
    }
  }
}
</script>

<style scoped>
.content {
  margin: 10rpx 0;
}

.font-14-opcity {
  font-size: 14px;
  opacity: .7;
}

.expend-price-font {
  font-size: 40px;
  color: #ff1d1d;
  font-weight: 500;
}

.income-price-font {
  font-size: 40px;
  color: #4747c3;
}

.active-price-type {
  margin: 0 10px;
  font-size: 18px;
}

.center {
  text-align: center;
}
</style>