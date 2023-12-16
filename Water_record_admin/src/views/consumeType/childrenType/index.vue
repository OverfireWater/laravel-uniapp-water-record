<template>
  <div>
    <el-card>
      <el-form inline>
        <el-form-item label="类型">
          <el-select :disabled="selectDisAble" @change="changeIsExpend" v-model="isExpend">
            <el-option v-for="item in expendAndIncomeOptions"
                       :key="item.value"
                       :label="item.label"
                       :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="父类型" style="margin-left: 10px">
          <el-select :disabled="selectDisAble" v-model="parentTypeId" placeholder="请选择">
            <el-option v-for="item in parentTypeOptions"
                       :label="item.t_name"
                       :value="item.id"
                       :key="item.id"></el-option>
          </el-select>
        </el-form-item>
      </el-form>
    </el-card>
    <el-card style="margin-top: 10px" v-show="MixinScene === 0">
      <el-table :data="typeList" border lazy v-loading="MixinTableLoading" :row-class-name="tableRowClassName">
        <el-table-column type="index" label="序列" align="center" width="60px"></el-table-column>
        <el-table-column label="图片" align="center" width="80px">
          <template slot-scope="{row, $index}">
            <img style="width: 30px;height: 30px;" :src="row.t_imgUrl" alt="">
          </template>
        </el-table-column>
        <el-table-column prop="t_name" label="名称" align="center"></el-table-column>
        <el-table-column label="说明" align="center">
          <template slot-scope="{row, $index}">
            <el-tag v-show="row.isDelete === 0" effect="dark">系统自带</el-tag>
            <el-tag v-show="row.isDelete === 1" type="warning" effect="dark">用户所有</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="类型" align="center">
          <template slot-scope="{row, $index}">
            <el-tag v-show="row.isExpend === 0">支出</el-tag>
            <el-tag v-show="row.isExpend === 1" type="success">收入</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="created_at" label="创建时间" align="center"></el-table-column>
        <el-table-column prop="updated_at" label="修改时间" align="center"></el-table-column>
        <el-table-column label="操作" align="center">
          <template slot-scope="{row, $index}">
            <el-button size="mini" class="el-icon-edit" type="warning" @click="editChildrenType(row)"></el-button>
            <el-button size="mini" class="el-icon-delete" type="danger" @click="deleteChildrenTypeMethod(row)"></el-button>
          </template>
        </el-table-column>
      </el-table>
      <el-pagination style="text-align: center;margin-top: 20px" background :total="MixinTotal"
                     :current-page="MixinCurrent_page" :page-size="MixinCurrent_limit"
                     @current-change="initPaginateAndSearchRelatedToParentChildrenType" @size-change="changeSize"
                     hide-on-single-page
                     :layout="MixinLayout">
      </el-pagination>
    </el-card>
    <el-card style="margin-top: 10px" v-show="MixinScene === 1">
      <!--      <type-form :options="options" @changeScene="changeScene"></type-form>-->
      <child-type-form @changeSelectDisAble="changeSelectDisAble" @changeScene="changeScene" ></child-type-form>
    </el-card>
  </div>
</template>

<script>
import {mapActions, mapGetters, mapState} from "vuex";
import mixin from "@/mixin";
import childTypeForm from "@/views/consumeType/childrenType/childTypeForm/index.vue";

export default {
  components: {childTypeForm},
  mixins: [mixin],
  data() {
    return {
      typeList: [],
      expendAndIncomeOptions: [
        {
          value: 0,
          label: '支出',
        },
        {
          value: 1,
          label: '收入'
        }
      ],
      // 是否显示选择框
      selectDisAble: false,
      parentTypeOptions: [],
      parentTypeId: '',
      isExpend: 0
    }
  },
  created() {
    this.initData()
  },
  computed: {
    ...mapState('type', ['expendParentAndChildrenType', 'incomeParentAndChildrenType', 'searchConsumeTypeList']),
  },
  methods: {
    ...mapActions('type', ['getUserParentAndChildrenType', 'getPaginateAndSearchRelatedToParentChildrenType', 'deleteChildrenType']),
    // 初始化父类型的数据
    initData(currentPage) {
      this.getUserParentAndChildrenType().then(() => {
        this.changeIsExpend(this.isExpend)
        this.initPaginateAndSearchRelatedToParentChildrenType(currentPage)
      }).catch(() => {
      })
    },
    // 初始化子类型的分页数据
    initPaginateAndSearchRelatedToParentChildrenType(currentPage = 1) {
      let data = this.initCurrentData(currentPage)
      data.parentTypeId = this.parentTypeId
      this.getPaginateAndSearchRelatedToParentChildrenType(data).then(() => {
        let {searchConsumeTypeList} = this
        this.MixinCurrent_page = searchConsumeTypeList.currentPage
        this.MixinCurrent_limit = searchConsumeTypeList.limit
        this.MixinTotal = searchConsumeTypeList.total
        this.typeList = searchConsumeTypeList.data
        this.MixinTableLoading = false
      }).catch(() => {
      })
    },
    // 改变场景
    changeScene(type, flag = '') {
      this.MixinScene = type
      this.changeSelectDisAble(true)
      if (flag === 'update') {
        this.initPaginateAndSearchRelatedToParentChildrenType(this.MixinCurrent_page)
      } else if (flag === 'add') {
        this.initPaginateAndSearchRelatedToParentChildrenType()
      }
    },
    // 改变选项栏
    changeSelectDisAble(flag){
      this.selectDisAble = flag
    },
    // 支出 | 收入 类型改变时
    changeIsExpend(e) {
      this.parentTypeOptions = !e ? this.expendParentAndChildrenType : this.incomeParentAndChildrenType
      this.parentTypeId = this.parentTypeOptions[0].id
    },
    // 当前页码改变时
    changeSize(current_limit) {
      this.MixinCurrent_limit = current_limit
      this.changeCurrentPage()
    },
    //表单改变颜色
    tableRowClassName({row, rowIndex}){
      if (row.isDelete){
        return 'success-row'
      }
    },

    //修改时
    editChildrenType(row){
      this.$bus.$emit('editChildrenType',row)
      this.changeScene(1)
    },
    //删除时
    deleteChildrenTypeMethod(row){
      this.$confirm('此操作将永久删除该信息, 是否继续?', '警告', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.deleteChildrenType(row.id).then(res=>{
          this.initPaginateAndSearchRelatedToParentChildrenType(this.typeList.length > 1 ? this.MixinCurrent_page : this.MixinCurrent_page > 1 ? this.MixinCurrent_page - 1 : this.MixinCurrent_page)
          this.$message({
            type: 'success',
            message: '删除成功'
          })
        }).catch(error=>{})

      }).catch(() => {})
    },
  },
  watch: {
    parentTypeId: {
      handler(newValue, oldValue) {
        if (newValue !== oldValue) {
          this.initPaginateAndSearchRelatedToParentChildrenType()
        }
      }
    }
  },
}
</script>

<style>
.el-table .success-row {
  background: #f0f9eb;
}
</style>
