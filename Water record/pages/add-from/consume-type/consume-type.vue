<template>
	<view>
		<scroll-view scroll-y="true" class="active" :style="{'height' : height}">
			<uni-grid :column="5" :showBorder="false" @change="change_grid_item">
				<uni-grid-item v-for="t in isExpendAllType" :key="t.id" :index="t.id">
					<view class="image-font">
						<image :src="t.t_imgUrl" mode=""></image>
						<text class="text">{{t.t_name}}</text>
					</view>
				</uni-grid-item>
				<uni-grid-item v-for="u_t in isExpendUserType" :key="u_t.id" :index="u_t.id">
					<view class="image-font">
						<image :src="u_t.t_imgUrl" mode=""></image>
						<text class="text">{{u_t.t_name}}</text>
					</view>
				</uni-grid-item>
			</uni-grid>
		</scroll-view>
	</view>
</template>

<script>
	export default {
		props:['isExpendAllType','isExpendUserType','consumeTypeHeight'],
		data() {
			return {
				currentTypeObj : [],
				KeyboardHeight : ''
			}
		},
		created() {
			uni.$on('getKeyboardHeight',(height)=>{
				this.KeyboardHeight = height
			})
		},
		computed: {
			height() {
				return `calc(100vh - ${this.consumeTypeHeight}px - ${this.KeyboardHeight}px)`
			}
		},
		methods: {
			change_grid_item(e){
				let {index} = e.detail
				this.currentTypeObj = this.isExpendAllType.filter(item=>{
					return item.id === index
				})
				if(this.currentTypeObj.length ===0){
					this.currentTypeObj = this.isExpendUserType.filter(item=>{
						return item.id === index
					})
				}
				uni.$emit('changeConsumeType',this.currentTypeObj[0])
			}
		},
		
	}
</script>

<style lang="scss" scoped>
	.image-font {
		flex: 1;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
	}

	image {
		width: 40px;
		height: 40px;
	}

	.text {
		margin: 0 auto;
	}
</style>