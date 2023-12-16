<template>
  <view class="container">
    <view class="top" v-for="(priceL,index) in dayData" :key="index">
      <view class="day-margin-top">
        <template v-if="nowD == priceL[0]">
          <view class="display-inline-block">
            <text class="day-font">今</text>
            <text class="font-14">日/{{ priceL[0] }}</text>
          </view>
        </template>
        <template v-else>
          <view class="display-inline-block">
            <text class="font-14">{{ priceL[0] }}</text>
          </view>
        </template>
        <!-- 今日收入 | 今日支出 -->
        <view class="display-inline-block float-right">
          <view class="img-text" :class="nowD == priceL[0]? 'marign-top': '' ">
            <image src="/static/icon/jian.png" class="jian-jia" mode=""></image>
            <text class="font-14">{{ priceL[2].totalExpense }}</text>
            <image src="/static/icon/jia.png" class="jian-jia" mode=""></image>
            <text class="font-14">{{ priceL[2].totalIncome }}</text>
          </view>
        </view>
      </view>
      <uni-list v-for="price in priceL[1]" :key="price.id">
        <uni-list-item link :to="`/pages/ConsumeDetail/ConsumeDetail?id=${price.id}&expend=${price.isExpend}`"
                       :title="price.w_type.t_name" :thumb="price.w_type.t_imgUrl" thumb-size="medium"
                       :note="price.remark">
          <template v-slot:footer>
            <text :class="price.isExpend === 0? 'red': 'blue' " class="price-text">{{ price.price }}</text>
          </template>
        </uni-list-item>
      </uni-list>
    </view>
  </view>
</template>

<script>
export default {
  name: "PriceList",
  props: ['dayData', 'isShowAllData'],
  data() {
    return {};
  },
  computed: {
    nowD() {
      return this.$util.getNowD()
    }
  },

}
</script>

<style scoped lang="less">
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