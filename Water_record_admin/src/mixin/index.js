export default {
  data() {
    return {
      //表单加载
      MixinTableLoading: false,
      // 当前表单页码
      MixinCurrent_page: 1,
      // 当前表单显示行数
      MixinCurrent_limit: 10,
      // 总数量
      MixinTotal: 0,
      // 当前场景
      MixinScene: 0,
      //分页器布局
      MixinLayout: 'prev, pager, next, ->, sizes, total',

    }
  },
  methods: {
    /**
     * @param currentPage 当前页
     * @return {limit: number, page: number}
     * */
    initCurrentData(currentPage = 1) {
      this.MixinTableLoading = true
      this.MixinCurrent_page = currentPage
      return {
        page: this.MixinCurrent_page,
        limit: this.MixinCurrent_limit
      }
    }
  },
}
