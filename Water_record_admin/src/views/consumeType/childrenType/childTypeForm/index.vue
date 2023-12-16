<template>
  <div>
    <!--    TODO: 1：当前选择的父类型 ，提示语 2：表单验证 2023/12/7-->
    <el-form v-model="typeForm" ref="form" label-width="80px">
      <el-form-item label="名称">
        <el-input v-model="typeForm.type_name" class="input-w" placeholder="请输入类型名称"></el-input>
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
  data() {
    return {
      typeForm: {
        typeId: '',
        type_name: '',
        t_imgUrl: '',
        parentTypeId: 0,
        isExpend: 0
      },
      imgUrl: '',
      parentTypeObj: {},
    }
  },
  mounted() {
    this.$bus.$on('editChildrenType', (row, type = '') => {
      this.typeForm = {
        typeId: row.id,
        type_name: row.t_name,
        t_imgUrl: row.t_imgUrl,
        parentTypeId: row.parent_type_id,
        isExpend: row.isExpend
      }
      this.imgUrl = row.t_imgUrl
    })
  },
  computed: {
    action() {
      return process.env.VUE_APP_BASE_API + '/type/uploadChildrenTypeFile'
    }
  },
  methods: {
    ...mapActions('type', ['addAndUpdateChildrenType']),
    initParentInfo(data) {
      this.typeForm.parentTypeId = data.id
      this.typeForm.isExpend = data.isExpend
      this.parentTypeObj = data
    },
    // 保存
    saveBtn() {
      this.addAndUpdateChildrenType(this.typeForm).then(() => {
        let type = ''
        type = this.typeForm.typeId ? 'update' : 'add'
        this.cancel(0, type)
      })
    },
    // 取消
    cancel(type = 0, flag) {
      this.$emit('changeScene', type, flag)
      this.$emit('changeSelectDisAble', false)
      setTimeout(() => {
        this.typeForm = {
          typeId: '',
          type_name: '',
          t_imgUrl: '',
          parentTypeId: 0,
          isExpend: 0
        }
        this.imgUrl = ''
        this.parentTypeObj = {}
      }, 500)

    },
    // 文件上传
    handleAvatarSuccess(res, file) {
      this.typeForm.t_imgUrl = res.data
      this.imgUrl = URL.createObjectURL(file.raw);
    },
    // 文件上传
    beforeAvatarUpload(file) {
      let isJPG = this.$is_some_file_suffix(file.type)
      const isLt2M = file.size / 1024 / 1024 < 2;

      if (!isJPG) {
        this.$message.error('上传头像图片只能是 JPG、png、gif 格式!');
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
