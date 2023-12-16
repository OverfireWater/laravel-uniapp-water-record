<template>
  <view>
    <uni-card>
      <view class="marign-top-10"></view>
      <uni-row :gutter="2">
        <uni-col :span="4">
          <text class="text-font-18">{{ expendName }}</text>
        </uni-col>
        <uni-col :span="16">
          <text class="price-text-40" :class="{red:expendName === '支出',blue:expendName === '收入' }">
            {{ price }}
          </text>
        </uni-col>
      </uni-row>
      <view class="marign-top-10"></view>
      <uni-row :gutter="2">
        <uni-col :span="4">
          <text class="text-font-18">类型</text>
        </uni-col>
        <uni-col :span="16">
          <view class="type-img-text">
            <image :src="w_type.t_imgUrl" mode="" style="width: 26px;height: 26px;"></image>
            <text class="text-font-18">{{ w_type.t_name }}</text>
          </view>
        </uni-col>
      </uni-row>
      <template v-if="!!userPriceDetail.remark">
        <view class="marign-top-10"></view>
        <uni-row :gutter="2">
          <uni-col :span="4">
            <text class="text-font-18">备注</text>
          </uni-col>
          <uni-col :span="16">
            <text class="text-font-18">{{ userPriceDetail.remark }}</text>
          </uni-col>
        </uni-row>
      </template>

      <view class="marign-top-10"></view>
      <uni-row :gutter="2">
        <uni-col :span="4">
          <text class="text-font-18">时间</text>
        </uni-col>
        <uni-col :span="16">
          <text class="text-font-18">{{ datetime }}</text>
        </uni-col>
      </uni-row>
      <view slot="actions" class="card-actions">
        <button type="primary" class="btn1-size" @click="navtoAddFrom">编辑</button>
        <button type="warn" class="btn2-size" @click="deleteConsume">删除</button>
      </view>
    </uni-card>
  </view>
</template>

<script>
import {mapActions, mapState, mapGetters, mapMutations} from 'vuex'

export default {
  data() {
    return {
      id: '',
      expendName: '',
      price: ''
    }
  },
  onLoad(options) {
    let {id, expend} = options
    let title = expend === '1' ? '收入' : '支出'
    this.expendName = title
    this.id = id
    //设置导航栏标题
    uni.setNavigationBarTitle({
      title
    })

    this.getUserConsumeDetail(id).then(()=>{
      this.price = this.userPriceDetail.price
    }).catch(()=>{})
  },
  computed: {
    ...mapState('consume', ['userPriceDetail']),
    //获取详情里面的类型对象
    w_type() {
      return this.userPriceDetail.w_type || {}
    },
    datetime() {
      let time = this.userPriceDetail.created_at
      if (time) {
        return time
      }
    }
  },
  methods: {
    ...mapActions('consume', ['getUserConsumeDetail', 'deleteUserConsume', 'getAllUserConsume', 'getUserTodayPrice']),
    ...mapMutations('consume', ['SET_USER_PRICE_DETAIL']),
    ...mapActions('userType', ['getUserTypeShowData']),
    //跳转页面到金额页面
    navtoAddFrom() {
      uni.navigateTo({
        url: `/pages/add-from/add-from?id=${this.id}`
      })
    },
    //删除消费
    deleteConsume() {
      uni.showModal({
        title: '提示',
        content: '您确定要删除此消费吗？',
        success: (res) => {
          if (res.confirm) {
            this.deleteUserConsume(this.id).then(res => {
              this.getUserTodayPrice().then(()=>{
                this.getUserTypeShowData()
                uni.switchTab({
                  url: '/pages/tabbar/index/index',
                  success: () => {
                    this.SET_USER_PRICE_DETAIL('')
                  }
                })
              }).catch(error=>{})
            }).catch(error => {
            })
          } else if (res.cancel) {}
        },
      })
    },
  },
  // 返回上一级时的回调
  onBackPress() {
    this.SET_USER_PRICE_DETAIL('')
  },
  onUnload() {
    // #ifdef MP-WEIXIN
    this.SET_USER_PRICE_DETAIL('')
    // #endif
  },
}
</script>

<style scoped>
.card-actions {
  border-top: 1px #EBEEF5 solid;
  padding: 10px 0;
  display: flex;
}

.btn1-size {
  width: 60%;
}

.btn2-size {
  width: 30%;
}

.marign-top-10 {
  margin: 15px;
}

.type-img-text {
  display: flex;
  align-items: center;
}

.type-img-text > image {
  margin-right: 5px;
}
</style>