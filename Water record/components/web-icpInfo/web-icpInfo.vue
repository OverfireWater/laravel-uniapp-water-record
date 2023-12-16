<template>
	<view class="footer" :class="{footer_1: isfoller}">
		<text>Copyright © 2023 GzyKzy</text>
		<text class="footer-text" @click="navtoICP">蜀ICP备2023033701号</text>
	</view>
</template>

<script>
	export default {
		name: "web-icpInfo",
		props: {
			isfoller: {
				type: Boolean,
				default: false
			}

		},
		data() {
			return {
			};
		},

		methods: {
			navtoICP() {
				// #ifdef H5
				window.open('https://beian.miit.gov.cn/')
				// #endif
			}
			
		},
	}
</script>

<style lang="less" scoped>
	.footer {
		display: flex;
		position: fixed;
		left: 0;
		right: 0;
		/* #ifdef APP-PLUS */
		bottom: 0;
		/* #endif */
		/* #ifndef APP-PLUS */
		bottom: 90rpx;
		/* #endif */		
		flex-direction: column;
		justify-content: center;
		justify-items: center;
		align-items: center;
		color: rgba(0, 0, 0, .4);
		font-size: 12px;
	}

	.footer_1 {
		position: relative;
		bottom: 0;
	}

	.footer-text {
		margin: 10px;
		cursor: pointer;
	}
</style>