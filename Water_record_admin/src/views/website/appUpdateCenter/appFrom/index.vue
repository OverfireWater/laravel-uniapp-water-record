<template>
  <div>
    <el-form style="width: 60%" ref="from" label-width="120px" :rules="rules" :model="appFrom" hide-required-asterisk>
      <el-form-item label="版本号V" prop="version">
        <el-input v-model="appFrom.version" placeholder="请输入版本号，例：1.0.0，1.0.1"></el-input>
      </el-form-item>
      <el-form-item label="文件上传" :error="fileErrorInfo">
        <el-upload ref="upload" :action="action" accept=".apk,.wgt" :before-upload="beforeUpload"
                   :on-success="uploadFileSuccess" :on-change="fileChange" :on-remove="onRemove" :file-list="fileList">
          <el-button type="primary" class="el-icon-plus">添加文件</el-button>
        </el-upload>
      </el-form-item>
      <el-form label-width="120px" inline>
        <el-form-item label="是否静默更新">
          <el-switch v-model="appFrom.silent">
          </el-switch>
        </el-form-item>
        <el-form-item label="是否强制更新">
          <el-switch v-model="appFrom.force">
          </el-switch>
        </el-form-item>
        <el-form-item label="非WIFI是否提醒">
          <el-switch v-model="appFrom.net_check">
          </el-switch>
        </el-form-item>
      </el-form>
      <el-form-item label="更新内容" prop="note">
        <el-input v-model="appFrom.note" type="textarea" :rows="5" placeholder="请输入需要更新的内容"></el-input>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" class="el-icon-plus" @click="saveBtn">保存</el-button>
        <el-button @click="cancel(0)">取消</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import {mapActions, mapState, mapMutations} from "vuex";

let loading = '';
export default {
  data() {
    const checkVersion = (rule, value, callback) => {
      if (!value) {
        callback(new Error('请出入版本号'))
      } else {
        let regex = /^(\d+)\.(\d+)\.(\d+)$/
        if (regex.test(value)) {
          callback()
        } else {
          callback(new Error('版本号错误，请重新输入'))
        }
      }
    }
    return {
      // 文件上传
      fileList: [],
      // 文件为空时的报错信息
      fileErrorInfo: '',
      appFrom: {
        version: '', // 版本
        update_url: '', //更新链接
        silent: true, // 静默更新
        force: true, // 强制更新
        net_check: true, //非WIFI检测
        issue: false, // 是否发布
        note: ''
      },
      rules: {
        version: [
          {validator: checkVersion, trigger: 'blur'}
        ],
        note: [
          {required: true, message: '请输入更新内容', trigger: 'blur'}
        ]
      }
    }
  },
  computed: {
    ...mapState('appInfo', ['appInfoById']),
    action() {
      return process.env.VUE_APP_BASE_API + '/app/uploadFile'
    }
  },
  methods: {
    ...mapActions('appInfo', ['addAppInfo']),
    ...mapMutations('appInfo', ['SET_APP_INFO_BY_ID']),
    // 父组件点击修改获取app信息
    initAppInfo() {
      let info = this.appInfoById
      info.silent = info.silent === '是'
      info.force = info.force === '是'
      info.net_check = info.net_check === '是'
      info.issue = info.issue === '是'
      info.note = this.backPreText(JSON.parse(info.note))
      this.appFrom = info
      let fileName = info.update_url.split('//')[1].split('/')[2]
      this.fileList = [{
        name: fileName,
        url: info.update_url
      }]
    },
    // 文件发生改变时，只保存一个文件
    fileChange(file, fileList) {
      if (fileList.length > 0) {
        this.fileList = [fileList[fileList.length - 1]]
      }
    },
    // 上传文件之前的回调
    beforeUpload(file) {
      this.appFrom.update_url = ''
      let regex = /\.(apk|wgt)$/
      if (regex.test(file.name)) {
        return true
      } else {
        this.$message({
          message: '图片格式错误',
          type: 'error'
        })
        return false
      }
    },
    // 移除文件列表时的回调
    onRemove(file) {
      this.fileList = []
      this.appFrom.update_url = ''
    },
    // 文件上传成功的回调
    uploadFileSuccess(response) {
      if (response.code === 500) {
        this.$message({
          message: response.msg,
          type: 'error'
        })
        return false
      }
      this.$message({
        message: '文件上传成功',
        type: 'success'
      })
      this.appFrom.update_url = response.data
      if (loading) {
        loading.close()
        this.saveBtn(this.type)
      }
    },
    saveBtn() {
      this.$refs.from.validate((valid) => {
        //校验失败
        if (!valid) {
          console.log('error')
          return false;
        }
        if (this.fileList.length <= 0) {
          this.fileErrorInfo = '请上传文件后缀为（apk，wgt）'
          return false;
        }
        if (!this.appFrom.update_url) {
          loading = this.$loading({
            lock: true,
            text: '文件正在上传中，请稍后',
            spinner: 'el-icon-loading',
            background: 'rgba(0, 0, 0, 0.7)'
          });
          return false
        }
        this.fileErrorInfo = ''
        this.appFrom.note = JSON.stringify(this.preText(this.appFrom.note))
        this.addAppInfo(this.appFrom).then(res => {
          this.$message({
            message: '保存成功',
            type: 'success'
          })
          let flag = ''
          if (this.appFrom.id){
            flag = 'update'
          }else {
            flag = 'add'
          }
          this.cancel(0, flag)
        }).catch(error => {
        })
      })
    },
    /**
     * 取消时
     * @param type 0 首页，1 添加页面，2 修改页面
     * @param flag 'update':修改时 | 'add':添加时 '':为空时
     */
    cancel(type, flag) {
      this.fileErrorInfo = ''
      this.fileList = []
      this.appFrom = {
        version: '', // 版本
          update_url: '', //更新链接
          silent: true, // 静默更新
          force: true, // 强制更新
          net_check: true, //非WIFI检测
          issue: false, // 是否发布
          note: ''
      }
      this.SET_APP_INFO_BY_ID('')
      this.$refs.from.resetFields()
      this.$emit('changeScene', type, flag)
    },
    // 保存时文本域换行问题解决
    preText(pretext) {
      return pretext.replace(/\r\n/g, '<br/>').replace(/\n/g, '<br/>').replace(/\s/g, '&nbsp;')
    },
    // 回显时
    backPreText(pretext) {
      return pretext.replace(/<br\/>/g, '\n').replace(/<br\/>/g, '\n').replace(/&nbsp;/g, ' ')
    },
  },
}
</script>

<style scoped lang="scss">

</style>
