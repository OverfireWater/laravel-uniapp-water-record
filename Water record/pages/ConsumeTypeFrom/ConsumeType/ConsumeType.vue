<template>
  <uni-collapse accordion ref="collapse">
    <uni-collapse-item v-for="(parentType, index) in userTypeList" :key="parentType.id" :showAnimation="true"
                       :title="parentType.t_name" :thumb="parentType.t_imgUrl" :open="index === 0">
      <view v-show="parentType.w_type.length">
        <view @click.stop="$emit('editUserType',parentType,childrenType)" class="content" hover-class="active"
              hover-stay-time="150" v-for="(childrenType, index2) in parentType.w_type" :key="childrenType.id">
          <image @click.stop="deleteConsumeType(childrenType.id)" v-show="childrenType.isDelete === 1" class="img-width"
                 src="/static/icon/-.png" mode="aspectFill"></image>
          <view v-show="childrenType.isDelete === 0" class="img-width"></view>
          <image class="img-width" :src="childrenType.t_imgUrl" mode="aspectFill"></image>
          <text class="type" style="display: flex;flex: 1">{{ childrenType.t_name }}</text>
          <uni-icons type="forward" size="15" color="#bebebe"></uni-icons>
        </view>
      </view>
      <view v-show="!parentType.w_type.length">
        <view class="nodata">
          <text class="type">没有类型~~</text>
        </view>
      </view>
    </uni-collapse-item>
  </uni-collapse>
</template>
<script>
import {mapActions} from 'vuex'

export default {
  props: ['userTypeList'],
  data() {
    return {}
  },
  methods: {
    ...mapActions('userType', ['addOrEditUserType', 'deleteUserType']),
    // 删除类型
    deleteConsumeType(id) {
      uni.showModal({
        title: '警告',
        content: '您确定要删除此类型吗？',
        success: (res) => {
          if (res.confirm) {
            this.deleteUserType(id).then(() => {
              this.closePopup()
            }).catch(() => {
            })
          } else if (res.cancel) {
            console.log('no')
          }
        }
      })
    },
  },
  watch: {
    userTypeList(newValue, oldValue) {
      // #ifdef MP
      this.$nextTick(() => {
        this.$refs.collapse.resize()
      })
      // #endif
    }
  },
}
</script>


<style scoped lang="less">
.img-width {
  width: 20px;
  height: 25px;
  margin-right: 10px;
}

.content {
  padding: 15px;
  display: flex;
  flex-direction: row;
  align-items: center;
  border-top: 1px solid #ebeef5;
  cursor: pointer;

  .type {
    // margin-left: 13px;
    font-size: 14px;
  }
}

.nodata {
  padding: 15px;
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
}
.active {
  background-color: rgba(0, 0, 0, .1) !important;
}
</style>