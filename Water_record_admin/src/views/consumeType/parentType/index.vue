<template>
  <div>
    <el-card v-show="MixinScene === 0">
      <div style="margin-bottom: 10px">
        <el-button type="primary" class="el-icon-plus" @click="changeScene(1)">添加父类型</el-button>
        <el-select @change="changeIsExpendSelect" v-model="isExpend" style="margin-left: 10px;width: 100px">
          <el-option v-for="item in options"
                     :key="item.value"
                     :label="item.label"
                     :value="item.value">
          </el-option>
        </el-select>
      </div>
      <el-table :data="typeList" border lazy v-loading="MixinTableLoading">
        <el-table-column type="index" label="序列" align="center" width="60px"></el-table-column>
        <el-table-column label="图片" align="center" width="80px">
          <template slot-scope="{row, $index}">
            <img style="width: 30px;height: 30px;" :src="row.t_imgUrl" alt="">
          </template>
        </el-table-column>
        <el-table-column prop="t_name" label="名称" align="center"></el-table-column>
        <el-table-column label="类型" align="center">
          <template slot-scope="{row, $index}">
            <el-tag v-show="row.isExpend === 0">支出</el-tag>
            <el-tag v-show="row.isExpend === 1" type="success">收入</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="子类型数量" align="center">
          <template slot-scope="{row, $index}">
            <el-tag type="info">{{ row.w_type.length }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="created_at" label="创建时间" align="center"></el-table-column>
        <el-table-column prop="updated_at" label="修改时间" align="center"></el-table-column>
        <el-table-column label="操作" align="center">
          <template slot-scope="{row, $index}">
            <el-tooltip content="添加子类型" placement="left">
              <el-button size="mini" class="el-icon-plus" type="success" @click="addChildType(row)"></el-button>
            </el-tooltip>
            <el-button size="mini" class="el-icon-edit" type="warning" @click="editParentType(row)"></el-button>
            <el-button size="mini" class="el-icon-delete" type="danger" @click="deleteParentType(row)"></el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
    <el-card v-show="MixinScene === 1">
      <parent-type-form :options="options" @changeScene="changeScene"></parent-type-form>
    </el-card>
    <el-card v-show="MixinScene === 2">
      <child-type-form @changeScene="changeScene" ref="childTypeForm"></child-type-form>
    </el-card>
  </div>
</template>

<script>
import {mapActions, mapState} from "vuex";
import mixin from "@/mixin";
import parentTypeForm from "@/views/consumeType/parentType/parentTypeForm/index.vue";
import childTypeForm from "@/views/consumeType/childrenType/childTypeForm/index.vue";

export default {
  components: {
    parentTypeForm,
    childTypeForm
  },
  mixins: [mixin],
  data() {
    return {
      typeList: [],
      options: [
        {
          value: 0,
          label: '支出',
        },
        {
          value: 1,
          label: '收入'
        }
      ],
      isExpend: 0
    }
  },
  created() {
    this.initData()
  },
  computed: {
    ...mapState('type', ['expendParentAndChildrenType', 'incomeParentAndChildrenType']),
  },
  methods: {
    ...mapActions('type', ['getUserParentAndChildrenType', 'vxDeleteParentType']),
    // 切换类型
    changeIsExpendSelect(e) {
      this.typeList = !e ? this.expendParentAndChildrenType : this.incomeParentAndChildrenType
    },
    //切换场景
    changeScene(type, flag = '') {
      this.MixinScene = type
      if (flag === 'update') {
        this.initData(this.MixinCurrent_page)
      } else if (flag === 'add') {
        this.initData()
      }
    },
    // 初始数据
    initData(currentPage) {
      let data = this.initCurrentData(currentPage)
      this.getUserParentAndChildrenType().then(() => {
        this.changeIsExpendSelect(this.isExpend)
        this.MixinTableLoading = false
      }).catch(() => {
      })
    },
    // 添加子类
    addChildType(row){
      this.$refs.childTypeForm.initParentInfo(row)
      this.changeScene(2)
    },
    // 修改
    editParentType(row){
      this.$bus.$emit('editParentType', row)
      this.changeScene(1)
    },
    // 删除
    deleteParentType(row){
      this.$confirm('此操作将永久删除该信息, 是否继续?', '警告', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.vxDeleteParentType(row.id).then(res=>{
          this.initData(this.typeList.length > 1 ? this.MixinCurrent_page : this.MixinCurrent_page > 1 ? this.MixinCurrent_page - 1 : this.MixinCurrent_page)
          this.$message({
            type: 'success',
            message: '删除成功'
          })
        }).catch(error=>{})

      }).catch(() => {
      })
    },
  },
}
</script>

<style scoped lang="less">

</style>
