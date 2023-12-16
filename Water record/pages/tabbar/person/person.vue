<template>
  <view class="content">
    <uni-card shadow="5px 5px 15px 1px rgba(0,0,0,.3)" style="text-align: center;">
      <image class="avatar-img" src="/static/avatar/author.jpg" mode="" style="height: 80px;width: 80px;"></image>
      <view class="login-btn" @click="open_login" v-show="!token">点击登陆</view>
      <view class="login-btn" style="color: black;" v-show="token">{{ userInfo.name }}</view>
    </uni-card>
    <uni-list>
      <uni-list-item title="设置" :clickable="true" :show-arrow="true" thumb="/static/icon/setting.png"
                     thumb-size="sm"></uni-list-item>
      <uni-list-item v-show="token" title="退出登陆" :clickable="true" :show-arrow="true"
                     thumb="/static/icon/logout.png" thumb-size="sm" @click="logout"></uni-list-item>
      <uni-list-item title="分类管理" thumb="/static/icon/typeManage.png" thumb-size="sm" link
                     to="/pages/ConsumeTypeFrom/ConsumeTypeFrom"></uni-list-item>
    </uni-list>
    <!-- #ifdef H5 || APP-PLUS -->
    <web-icpInfo></web-icpInfo>
    <!-- #endif -->
  </view>
</template>

<script>
import {
  mapState,
  mapActions
} from 'vuex'
import {
  destoryToken
} from '@/utils/tokenService.js'

export default {
  data() {
    return {}
  },
  created() {
    this.initUserInfo()
  },
  computed: {
    ...mapState('user', ['userInfo', 'token']),
  },
  methods: {
    ...mapActions('user', ['getUserInfo']),
    ...mapActions('consume', ['getUserTodayPrice']),
    ...mapActions('userType', ['getUserTypeShowData']),
    open_login() {
      uni.navigateTo({
        url: '/pages/PopupLogin/PopupLogin'
      })
    },
    initUserInfo() {
      //TODO:
      if (this.token) {
        this.getUserInfo()
      }
    },
    //退出登陆
    logout() {
      destoryToken()
      this.initUserInfo()
      this.getUserTodayPrice()
      this.getUserTypeShowData()
    },
  }
}
</script>

<style scoped>
.avatar-img {
  border-radius: 50%;
  box-shadow: 5px 5px 15px 3px rgba(0, 0, 0, .3);
}

.login-btn {
  font-size: 16px;
  margin: 10px;
  color: #5979c3;
}
</style>