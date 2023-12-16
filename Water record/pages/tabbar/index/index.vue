<template>
  <view>
    <uni-card padding="0" spacing="0" margin="5">
      <template v-slot:cover>
        <view class="custom-cover">
          <image class="cover-image" mode="aspectFill"
                 src="https://gzykzy.top:1011/storage/consumeType/banner.png">
          </image>
          <view class="cover-content">
            <view class="content-1">
              <view class="mouth-font">{{ month }}</view>
              <text>月总支出</text>
            </view>
            <view class="content-2-sumPrice">{{ userMonthAllExpend }}</view>
            <view class="content-3">
              <text>总收入：</text>
              <text>{{ userMonthAllIncome }}</text>
              <text v-show="userMonthAllExpend !=='0.00'">
                <text style="margin-left: 20px">结余：</text>
                <text>{{ surplus }}</text>
              </text>
            </view>
          </view>
        </view>
      </template>
    </uni-card>

    <!--    只展示今日消费-->
    <uni-card padding="0" spacing="0" margin="5" id="priceListCard" v-show="!!getUserTodayMonth">
      <view class="container">
        <view class="day-margin-top">
          <view class="display-inline-block">
            <text class="day-font">今</text>
            <text class="font-14">日/{{ getUserTodayMonth }}</text>
          </view>
          <!-- 今日收入 | 今日支出 -->
          <view class="display-inline-block float-right">
            <view class="img-text marign-top">
              <image src="/static/icon/jian.png" class="jian-jia" mode=""></image>
              <text class="font-14">{{ userTodayExpend }}</text>
              <image src="/static/icon/jia.png" class="jian-jia" mode=""></image>
              <text class="font-14">{{ userTodayIncome }}</text>
            </view>
          </view>
        </view>
        <uni-list v-for="price in userTodayPrice" :key="price.id">
          <uni-list-item link :to="`/pages/ConsumeDetail/ConsumeDetail?id=${price.id}&expend=${price.isExpend}`"
                         :title="price.w_type.t_name" :thumb="price.w_type.t_imgUrl" thumb-size="medium"
                         :note="price.remark">
            <template v-slot:footer>
              <text :class="price.isExpend === 0? 'red': 'blue' " class="price-text">{{ price.price }}</text>
            </template>
          </uni-list-item>
        </uni-list>
      </view>
      <view slot="actions" class="card-actions">
        <view class="card-actions-item" @click="actionsClick">
          <text class="card-actions-item-text">查看更多...</text>
          <uni-icons type="redo-filled" size="18" color="#5b990a"></uni-icons>
        </view>
      </view>
    </uni-card>
    <!--    没有数据展示-->
    <uni-card padding="0" spacing="0" margin="5" v-show="!getUserTodayMonth">
      <view class="card-container-nodata" @click="actionsClick">
        <view class="card-options">
          <text>{{ $util.getNowD() }}</text>
          <text>{{ $util.getTimeSplit() }}</text>
          <text>{{ $util.getNowW() }}</text>
        </view>
        <view class="card-nodata">
          <text style="font-size: 16px">暂无记录 ~~</text>
        </view>
      </view>
    </uni-card>

    <!--    本月分类支出-->
    <uni-card padding="0" spacing="0" margin="5" v-show="userTypeExpendPrice.length">
      <view class="card-container-type">
        <view class="card-container-type-header">
          <text class="card-container-type-header-month">{{ month }}月分类支出</text>
          <view style="font-size: 14px">
            <text style="padding-right: 10px">总支出</text>
            <text class="red">{{ userMonthAllExpend }}</text>
          </view>
        </view>
        <view class="card-container-type-content" v-for="(expendType,index) in processedExpendData"
              :key="expendType.w_type.id">
          <view class="card-container-type-content-left">
            <text>{{ index + 1 }}</text>
            <image :src="expendType.w_type.t_imgUrl" style="width: 25px;height: 25px"></image>
            <text>{{ expendType.w_type.t_name }}</text>
          </view>
          <view class="card-container-type-content-center">
            <view class="card-container-type-content-center-price font-14">
              <text style="padding-right: 10px">{{ expendType.percent }}%
              </text>
              <text class="red">{{ expendType.price }}</text>
            </view>
            <progress style="width: 100%;padding-bottom: 10px" border-radius="10" active :duration="10"
                      :percent="expendType.percent"
                      stroke-width="5"></progress>
          </view>
        </view>
      </view>
    </uni-card>
    <!--    本月分类收入-->
    <uni-card padding="0" spacing="0" margin="5" v-show="userTypeIncomePrice.length">
      <view class="card-container-type">
        <view class="card-container-type-header">
          <text class="card-container-type-header-month">{{ month }}月分类收入</text>
          <view style="font-size: 14px">
            <text style="padding-right: 10px">总收入</text>
            <text class="blue">{{ userMonthAllIncome }}</text>
          </view>
        </view>
        <view class="card-container-type-content" v-for="(incomeType,index) in processedIncomeData"
              :key="incomeType.w_type.id">
          <view class="card-container-type-content-left">
            <text>{{ index + 1 }}</text>
            <image :src="incomeType.w_type.t_imgUrl" style="width: 25px;height: 25px"></image>
            <text>{{ incomeType.w_type.t_name }}</text>
          </view>
          <view class="card-container-type-content-center">
            <view class="card-container-type-content-center-price font-14">
              <text style="padding-right: 10px"> {{ incomeType.percent }}%
              </text>
              <text class="blue">{{ incomeType.price }}</text>
            </view>
            <progress style="width: 100%;padding-bottom: 10px" border-radius="10"
                      :percent="incomeType.percent"
                      :duration="10" active
                      stroke-width="5"></progress>
          </view>
        </view>
      </view>
    </uni-card>

    <!-- #ifdef H5  -->
    <web-icpInfo :isfoller="!!userTypeExpendPrice.length"></web-icpInfo>
    <!-- #endif -->
  </view>
</template>

<script>

import {mapActions, mapGetters, mapState} from 'vuex'

let timer = ''
export default {
  data() {
    return {
      // 让ICP是否跟随数据
      isfoller: false,
      // 是否为数据添加
      IsDataType: false
    }
  },
  onLoad() {
    //这里可以适当延时执行
    //获取线上APP版本信息  参数type 0自动检查  1手动检查（手动检查时，之前取消更新的版本也会提示出来）
    // #ifdef APP-PLUS
    this.getNewAppInfo(0)
    // #endif
    uni.$on('getUserTypeShowData', this.getUserTypeShowData)
    this.getUserTodayPrice()
    this.getUserTypeShowData()
  },
  computed: {
    ...mapGetters('consume', ['userTodayPrice']),
    ...mapState('consume', ['userTodayExpend', 'userTodayIncome', 'userTodayPrice', 'userMonthAllExpend', 'userMonthAllIncome']),
    ...mapState('appInfo', ['appInfo']),
    ...mapState('userType', ['userTypeExpendPrice', 'userTypeIncomePrice']),
    ...mapGetters('userType', ['processedExpendData', 'processedIncomeData']),
    // 获取用户本月
    getUserTodayMonth() {
      let list = this.userTodayPrice
      if (list.length) {
        let dataMonth = list[0].updated_at.split(' ')[0]
        if (dataMonth === this.$util.getNowD()) {
          return dataMonth
        }
      } else {
        return false
      }
    },
    // 结余
    surplus(){
      let price =  ((this.userMonthAllIncome.replace(/,/g, ''))-(this.userMonthAllExpend.replace(/,/g, ''))).toFixed(2)
      return  price.toString().replace(/\d(?=(\d{3})+\.)/g, '$&,')
    },
    //获取月份
    month() {
      let date = new Date()
      return date.getMonth() + 1
    },
  },
  methods: {
    ...mapActions('consume', ['getUserTodayPrice']),
    ...mapActions('userType', ['getUserTypeShowData']),
    // #ifdef APP-PLUS
    ...mapActions('appInfo', ['getNewAppInfo']),
    // #endif

    // 点击更多时
    actionsClick() {
      uni.navigateTo({
        url: '/pages/MoreExpendPrice/MoreExpendPrice',
      })
    },
  },
};
</script>

<style>
/deep/ .uni-progress-bar {
  border-radius: 20rpx;
}

/deep/ .uni-progress-inner-bar {
  border-radius: 10px !important;
  overflow: hidden;
  background: linear-gradient(to right, #F137C8, #AA2AF9);
}

.wx-progress-inner-bar {
  border-radius: 6px !important;
  background: linear-gradient(to right, #F137C8, #AA2AF9);
}

</style>
<style scoped lang="less">
@fontSize: 16px;
@cardPadding: 10px;
.custom-cover {
  display: flex;
  flex-direction: row;
  position: relative;
  overflow: hidden;

  .cover-image {
    flex: 1;
    height: 310.34rpx;
  }

  .cover-content {
    position: absolute;
    padding-left: 15px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    // padding-left: 15px;
    font-size: 14px;
    color: #d9d9d9;
    letter-spacing: 3.5rpx;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, .4);
  }

  .content-1 {
    display: flex;
    flex-direction: row;
    align-items: center;

    .mouth-font {
      font-size: 51.72rpx;
      padding-right: 13.79rpx;
    }
  }

  .content-2-sumPrice {
    font-size: 67rpx;
  }

  .content-3 {
    display: flex;
    flex-direction: row;
    align-items: center;
    margin-top: 10px;
    margin-left: 4px;
    font-size: 15px;
  }
}

.card-actions {
  padding: 10px 0;
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;

  .card-actions-item {
    cursor: pointer;
  }

  .card-actions-item-text {
    font-size: 29.31rpx;
    margin-right: 10px;
  }
}

.card-container-nodata {
  padding: @cardPadding;
  background: linear-gradient(to bottom right, #6e4aa7c9, #8be0ee);
  color: white;
  cursor: pointer;

  .card-options {
    display: flex;
    flex-direction: row;
    font-size: @fontSize;
    justify-content: center;

    & text {
      padding: 5px;
    }
  }

  .card-nodata {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 5px;

    .card-nodata-text {
      font-size: @fontSize;
    }
  }
}

.card-container-type {
  padding: @cardPadding;
  display: flex;
  flex-direction: column;
  font-size: @fontSize;

  .card-container-type-header {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    padding-bottom: 5px;
    border-bottom: 0.5px solid rgba(0, 0, 0, .09);

    .card-container-type-header-month {
      font-weight: 550;
    }
  }

  .card-container-type-content {
    margin-top: 15px;
    display: flex;
    flex-direction: row;
    justify-content: space-around;

    .card-container-type-content-left {
      width: 30%;
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: space-around;
    }

    .card-container-type-content-center {
      width: 100%;
      display: flex;
      padding: 0 10px;
      flex-direction: column;

      .card-container-type-content-center-price {
        text-align: right;
        padding-bottom: 5px;
      }
    }

  }
}

.container {
  margin: 10px 15px;
}

.font-14 {
  font-size: 14px;
}

.display-inline-block {
  display: inline-block;
}


.float-right {
  float: right;
  margin-right: 13px;
}

.day-font {
  font-size: 23px;
}

.day-margin-top {
  margin-top: 10px;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
}

.jian-jia {
  margin: 0 10px;
  width: 20px;
  height: 20px;
  opacity: .7;
}

.img-text {
  display: flex;
  line-height: 30upx;
  align-items: center;
}

.price-text {
  display: flex;
  align-items: center;
}
</style>