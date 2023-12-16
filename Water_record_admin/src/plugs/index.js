export default {
  install: (vue, options) => {
    // 判断文件的后缀是否相同
    vue.prototype.$is_some_file_suffix = (suffix, suffixArray = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']) => {
      return suffixArray.includes(suffix)
    }
  }
}
