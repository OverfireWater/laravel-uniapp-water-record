<template>
  <uni-card padding="0" spacing="0" margin="5">
    <view class="card-container-type">
      <view class="card-container-type-header">
        <text class="card-container-type-header-month">{{ month }}月分类{{ consumeTitle }}</text>
        <view style="font-size: 14px">
          <text style="padding-right: 10px">总{{ consumeTitle}}</text>
          <text class="red">{{ userMonthAllPrice }}</text>
        </view>
      </view>
      <view class="card-container-type-content" v-for="(consumeData,index) in processedConsumeData"
            :key="consumeData.w_type.id">
        <view class="card-container-type-content-left">
          <text>{{ index + 1 }}</text>
          <image :src="consumeData.w_type.t_imgUrl" style="width: 25px;height: 25px"></image>
          <!--          <text>{{ consumeData.w_type.t_name }}</text>-->
        </view>
        <view class="card-container-type-content-center">
          <view class="card-container-type-content-center-tips font-14">
            <view class="card-container-type-content-center-tips-left">
              <text>{{ consumeData.w_type.t_name }}</text>
            </view>
            <view class="card-container-type-content-center-tips-right">
              <text style="padding-right: 10px">{{ consumeData.percent }}%
              </text>
              <text class="red">{{ consumeData.price }}</text>
            </view>
          </view>
          <progress style="width: 100%;padding-bottom: 10px" border-radius="10" active :duration="10"
                    :percent="consumeData.percent"
                    stroke-width="5"></progress>
        </view>
      </view>
    </view>
  </uni-card>
</template>
<script>
export default {
  props: {
    month: {
      type: Number,
      default: ''
    },
    userMonthAllPrice: {
      type: String,
      default: 0.00
    },
    processedConsumeData: {
      type: Array
    }
  },
  computed: {
    consumeTitle() {
      if (!this.processedConsumeData.length) return ''
      return this.processedConsumeData[0].isExpend === 0 ? '支出' : '收入'
    }
  },
}
</script>
<style scoped lang="less">
@fontSize: 16px;
@cardPadding: 10px;
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
      width: 20%;
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

      .card-container-type-content-center-tips {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        margin-bottom: 5px;
        align-items: center;
        flex: 1;

        .card-container-type-content-center-tips-left {

        }
        .card-container-type-content-center-tips-right {
          display: flex;
          flex-direction: row;
          align-items: center;
        }
      }
    }
  }
}

</style>