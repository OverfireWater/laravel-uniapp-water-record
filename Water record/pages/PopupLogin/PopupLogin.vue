<template>
	<view class="container">
		<view class="header-title">
			<text>欢迎来到灵思智财云登陆</text>
		</view>
		<uni-forms ref="form" style="width: 75%;margin: 0 auto;" :model="formData" :rules="rules">
			<uni-forms-item name="email">
				<uni-easyinput prefix-icon="email-filled" v-model="formData.email" :focus="false" type="text"
					:trim="true" placeholder="请输入邮箱" :cursorSpacing="10" confirm-type="next" />
			</uni-forms-item>
			<template v-if="isLogin">
				<uni-forms-item name="password">
					<uni-easyinput prefix-icon="locked-filled" maxlength="20" v-model="formData.password" :trim="true"
						type="password" placeholder="请输入密码" :cursorSpacing="10" />
				</uni-forms-item>
			</template>
			<template v-else>
				<uni-forms-item name="captcha">
					<view class="captcha_input">
						<uni-easyinput maxlength="5" v-model="formData.captcha" prefix-icon="chatbubble-filled"
							:trim="true" type="number" placeholder="请输入验证码" :cursorSpacing="10" />
					</view>
					<button class="captcha_btn" type="primary" @click="send_mail_btn" :disabled="isDisable">发送</button>
				</uni-forms-item>
			</template>
			<button type="primary" style="margin-bottom: 10px;" @click="submit">登陆</button>
			<view class="footer">
				<text class="captcha_login blue" v-if="isLogin" @click="change_is_login_captcha('captcha')">没有账号？验证码登陆</text>
				<text class="captcha_login blue" v-else @click="change_is_login_captcha('login')">密码登陆</text>				
				<checkbox-group @change="changeCheck">
					<label>
						<checkbox value="saveAccount" :checked="isSave" style="transform: scale(0.8);"/><text class="captcha_login blue">记住账号</text>
					</label>
				</checkbox-group>
			</view>
		</uni-forms>
		<!-- #ifdef H5 -->
			<web-icpInfo></web-icpInfo>
		<!-- #endif -->
	</view>
</template>

<script>
	import {mapActions} from 'vuex'
	export default {
		name:'PopupLogin',
		data() {
			return {
				isLogin: false,
				formData: {
					email: '',
					password: '',
					captcha: '',
					ispwd: false
				},
				// 是否禁用发送按钮
				isDisable: false,
				//记住账号按钮
				isSave: false,
				rules: {
					email: {
						rules: [{
								required: true,
								errorMessage: "请填写邮箱"
							},
							{
								format: 'email',
								errorMessage: '邮箱格式错误'
							},
						]
					},
					password: {
						rules: [{
							required: true,
							errorMessage: '请填写密码'
						}]
					},
					captcha: {
						rules: [{
							required: true,
							errorMessage: '请填写验证码'
						}]
					}
				}
			}
		},
		created() {
			let account = uni.getStorageSync('account')			
			if(account){
				account = JSON.parse(account)
				this.formData.email = account.account
				this.isSave = account.isSave
			}
		},
		methods: {
			...mapActions('user', ['getUserInfo','getUserToken','sendMail']),
			...mapActions('consume', ['getUserTodayPrice']),
      ...mapActions('userType', ['getUserTypeShowData']),
			//改变登陆方式
			change_is_login_captcha(type) {
				this.formData.password = ''
				this.formData.captcha = ''
				this.$refs.form.clearValidate()
				if (type === 'login') {
					this.isLogin = true
					this.formData.ispwd = true
				} else {
					this.isLogin = false
					this.formData.ispwd = false
				}
			},
			//登陆
			submit() {
				let vali = 'password'
				if (!this.isLogin) {
					vali = 'captcha'
				}
				this.$refs.form.validateField(['email', vali]).then(res => {
					let res2 = this.getUserToken(this.formData)
					res2.then(res => {
						this.setAccount()
						this.PageGetUserInfo()
						uni.navigateBack()
						uni.showToast({
							icon: 'success',
							title: '登陆成功'
						})
					}).catch(error => {
						console.log(error);
					})
				}).catch(err => {})
			},
			//发送邮箱
			send_mail_btn() {
				this.$refs.form.validateField(['email']).then(res => {
					this.sendMail(this.formData.email).then(res => {
						this.isDisable = true
						uni.showToast({
							icon: 'success',
							title: '发送成功'
						})
					}).catch(err => {
						uni.showToast({
							icon: 'error',
							title: err.msg
						})
					})
				}).catch(err => {})
			},
      // 获取数据
			PageGetUserInfo() {
				this.email_focus = false
				this.getUserInfo().then(res => {
					this.getUserTodayPrice()
          this.getUserTypeShowData()
				}).catch(error => {
					console.log(error);
				})
			},
			// 记住账号框改变时
			changeCheck(e){
				let value = e.detail.value
				this.isSave = !!value.length;
			},
			// 存储账号
			setAccount(){
				if(this.isSave){
					let arr =JSON.stringify({'account' : this.formData.email, isSave : this.isSave})
					uni.setStorageSync('account',arr)
				}else{
					uni.removeStorageSync('account')
				}
			}
		},
		mounted() {
			this.email_focus = true
		},
	}
</script>

<style>
	.container {
		margin: 20px;
		margin-bottom: 120px;
	}
	.captcha_login {
		font-size: 14px;
	}
	
	.captcha_input {
		width: 65%;
		float: left;
		margin-right: 5%;
	}
	
	.captcha_btn {
		width: 30%;
		float: left;
		font-size: 15px;
	}
	.close_icon_btn {
		margin: 10px;
		text-align: right;
	}
	.header-title{
		text-align: center;
		font-size: 18px;
		margin: 20px 0;
	}
	.footer{
		display: flex;
		justify-content: space-between;
	}
</style>