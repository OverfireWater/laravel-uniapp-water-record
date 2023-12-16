<template>
  <div>
    <el-form v-model="typeForm" ref="form" label-width="80px">
      <el-form-item label="名称">
        <el-input v-model="typeForm.type_name" class="input-w" placeholder="请输入类型名称"></el-input>
      </el-form-item>
      <el-form-item label="类型">
        <el-select v-model="typeForm.isExpend">
          <el-option v-for="item in options"
                     :key="item.value"
                     :label="item.label"
                     :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="图片">
        <el-upload
          class="avatar-uploader"
          :action="action"
          :show-file-list="false"
          :on-success="handleAvatarSuccess"
          :before-upload="beforeAvatarUpload">
          <img v-if="imgUrl" :src="imgUrl" class="avatar" alt="">
          <i v-else class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" class="el-icon-plus" @click="saveBtn">保存</el-button>
        <el-button @click="cancel(0)">取消</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import {mapActions} from "vuex";

export default {
  props: ['options'],
  data() {
    return {
      typeForm: {
        parentTypeId: 0,
        type_name: '',
        isExpend: 0,
        t_imgUrl: '',
      },
      imgUrl: '',
    }
  },
  mounted() {
    this.$bus.$on('editParentType', (row) => {
      this.typeForm = {
        parentTypeId: row.id,
        type_name: row.t_name,
        isExpend: row.isExpend,
        t_imgUrl: row.t_imgUrl,
      }
      this.imgUrl = row.t_imgUrl
    })
  },
  computed: {
    action() {
      return process.env.VUE_APP_BASE_API + '/type/uploadParentTypeFile'
    }
  },
  methods: {
    ...mapActions('type', ['addAndUpdateParentTYpe']),
    // 保存
    saveBtn(){
      this.addAndUpdateParentTYpe(this.typeForm).then(() => {
        let type = ''
        type = this.typeForm.parentTypeId ? 'update' : 'add'
        this.cancel(0, type)
      })
    },
    // 取消
    cancel(type, flag) {
      this.$emit('changeScene', type, flag)
      setTimeout(()=>{
        this.typeForm = {
          t_name: '',
          isExpend: 0,
          t_imgUrl: '',
        }
        this.imgUrl = ''
      },500)
    },
    // 文件上传
    handleAvatarSuccess(res, file) {
      this.typeForm.t_imgUrl = res.data
      this.imgUrl = URL.createObjectURL(file.raw);
    },
    // 文件上传
    beforeAvatarUpload(file) {
      const isJPG = this.$is_some_file_suffix(file.type)
      const isLt2M = file.size / 1024 / 1024 < 2;
      if (!isJPG) {
        this.$message.error('上传头像图片只能是 JPG 格式!');
      }
      if (!isLt2M) {
        this.$message.error('上传头像图片大小不能超过 2MB!');
      }
      return isJPG && isLt2M;
    }
  },
}
</script>

<style scoped lang="less">

</style>
<style>
.avatar-uploader .el-upload {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.avatar-uploader .el-upload:hover {
  border-color: #409EFF;
}

.avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 178px;
  height: 178px;
  line-height: 178px;
  text-align: center;
}

.avatar {
  width: 178px;
  height: 178px;
  display: block;
}
</style>
