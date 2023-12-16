<template>
	<view id="keyboard" class="container" @click="change_number" @touchstart="change_down_num" @touchend="change_up_num"
		@mousedown="change_down_num" @mouseup="change_up_num">
		<view class="remark-content">
			<view v-show="!isSeleteDatetime">
				<button type="default" size="mini" @click="chang_datetime">选择时间</button>
			</view>
			<view class="datetime-right" v-show="isSeleteDatetime">
				<uni-datetime-picker type="datetime" :value="datetime" ref="datetime" @change="changeDatetime"/>
			</view>
			<view class="remakr-left">
				<button type="default" size="mini" @click="chang_remark">备注</button>
				<uni-popup ref="popupDialog" type="dialog">
					<uni-popup-dialog ref="inputClose" mode="input" title="输入备注(1-15)" :value="remark"  placeholder="请输入备注"
						@confirm="dialogInputConfirm" @close="closeDialog"></uni-popup-dialog>
				</uni-popup>
			</view>
		</view>
		<view>
			<uni-row>
				<uni-col :span="6" v-for="(n,index) in 3" :key="index">
					<view class="number-font" :class="{active:index===currentIndex}" :data-num="index+1">{{index+1}}
					</view>
				</uni-col>
				<uni-col :span="6" :pull='0'>
					<view class="number-font red" :class="{active:'D' === currentIndex}" data-num="D">
						<image src="/static/img/delete.png" mode="" data-num="D"
							style="width: 30px;height: 30px;margin-top: 15px;">
						</image>
					</view>
				</uni-col>
			</uni-row>
			<uni-row>
				<uni-col :span="6" v-for="(n1,index) in 6" :key="index" v-if="index+1>3">
					<view class="number-font" :class="{active:index===currentIndex}" :data-num="index+1">{{index+1}}
					</view>
				</uni-col>
				<view class="btn-save number-font">
					<view class="save-btn red" :class="{active:'S' === currentIndex}" @touchstart="change_down_save"
						@touchend="change_up_save" @longtap="change_up_save" @mousedown="change_down_save"
						@mouseup="change_up_save" @click.stop="save">
						<view>保</view>
						<view>存</view>
					</view>
				</view>
			</uni-row>
			<uni-row>
				<uni-col :span="6" v-for="(n2,index) in 9" :key="index" v-if="index+1>6">
					<view class="number-font" :class="{active:index===currentIndex}" :data-num="index+1">{{index+1}}
					</view>
				</uni-col>
			</uni-row>
			<uni-row>
				<uni-col :span="6">
					<view class="number-font" :class="{active:'dot'===currentIndex}" data-num="dot">.</view>
				</uni-col>
				<uni-col :span="6">
					<view class="number-font" :class="{active:'0'===currentIndex}" data-num="0">0</view>
				</uni-col>
				<uni-col :span="6">
					<view class="number-font" :class="{active:'C'===currentIndex}" data-num="C">C</view>
				</uni-col>
			</uni-row>
		</view>
	</view>
</template>

<script>
	export default {
		props:{
			remark:{
				type: String,
				default: ''
			},
			datetime:{
				type: String,
				default: ''
			}
		},
		data() {
			return {
				currentIndex: -1,
				delete_active: false,
				save_active: false,
				//显示备注的input
				isShowRemark: false,
				//获取焦点
				isFocus: false,
				isSeleteDatetime: false
			}
		},
		mounted() {
			uni.createSelectorQuery().in(this).select("#keyboard").boundingClientRect(data => {
				uni.$emit('getKeyboardHeight', parseInt(data.height + 40))
			}).exec()
		},
		methods: {
			//改变时间
			chang_datetime(){
				this.isSeleteDatetime = true
				this.$refs.datetime.show()
			},
			changeDatetime(time){
				uni.$emit('changeTime',time)
			},
			//改变备注
			chang_remark() {
				this.$refs.popupDialog.open()
			},
			//取消时
			closeDialog(){
				this.$refs.popupDialog.close()
			},
			//确定时
			dialogInputConfirm(value) {
				this.$refs.popupDialog.close()
				uni.$emit('changeRemark',value)
			},
			//改变金额
			change_number(e) {
				let num = e.target.dataset.num
				if (num) {
					uni.$emit('change_price', num)
				}
			},
			//保存按钮
			save() {
				uni.$emit('save_price')
			},
			change_down_save() {
				this.currentIndex = 'S'
			},
			change_up_save() {
				this.currentIndex = -1
			},
			change_down_num(e) {
				let num = e.target.dataset.num
				if (num === 'dot' || num === 'C' || num === 'D' || num === '0') {
					this.currentIndex = num
				} else if (num) {
					this.currentIndex = num - 1
				}
			},
			change_up_num() {
				this.currentIndex = -1
			}
		}
	}
</script>

<style scoped>
	.container {
		position: absolute;
		bottom: 0%;
		margin-bottom: 30px;
		margin-top: 10px;
		width: 100%;
	}

	.number-font {
		text-align: center;
		line-height: 60px;
		height: 60px;
		background-color: rgba(0, 0, 0, .05);
		box-shadow: -5px -5px 15px 0px rgba(255, 255, 255, 0.5);
	}

	.btn-save {
		width: 25%;
		height: 180px;
		position: absolute;
		right: 0;
		z-index: 2;
	}

	.red {
		background-color: #e86d34;
		border-radius: 5px;
	}

	.save-btn {
		height: 100%;
		font-size: 30px;
		color: white;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
	}

	.remark-text {
		border: 1px solid;
		padding: 5px;
		border-radius: 5px;
		opacity: .8;
	}

	.active {
		background-color: rgba(0, 0, 0, .2);
	}
	.remark-content{
		display: flex;
		justify-content: space-between;
		align-items: center;
		background-color: rgba(0, 0, 0, .05);
	}
	.remakr-left{
		
	}
	.datetime-right{
		width: 60%;
	}
</style>