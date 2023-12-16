export default {
	onShow() {
		// #ifdef H5
		if (getCurrentPages().length > 0) {
			let title = document.title
			if (title.indexOf('灵思智财云') !== -1)return false
			document.title = '灵思智财云-' + title + '-科技学习分享';
		}
		// #endif
	}
}