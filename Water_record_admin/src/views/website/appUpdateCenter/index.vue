<template>
  <div>
<!--    <el-card style="margin-bottom: 20px">-->
<!--    </el-card>-->
    <el-card>
      <div style="margin-bottom: 10px" v-show="MixinScene === 0">
        <el-button type="primary" class="el-icon-plus" @click="changeScene(1)">添加App</el-button>
      </div>
      <div v-show="MixinScene === 0">
        <el-table :data="appInfoObj.data" border lazy v-loading="MixinTableLoading" :row-class-name="tableRowClassName">
          <el-table-column type="index" label="序列" align="center" width="60px"></el-table-column>
          <el-table-column prop="version" label="版本" align="center" width="80px"></el-table-column>
          <el-table-column prop="update_url" label="更新链接" align="center" show-overflow-tooltip></el-table-column>
          <el-table-column prop="silent" label="静默更新" align="center" width="80px"></el-table-column>
          <el-table-column prop="force" label="强制更新" align="center" width="80px"></el-table-column>
          <el-table-column prop="net_check" label="非WIFI提醒" align="center" width="100px"></el-table-column>
          <el-table-column label="更新内容" align="center" show-overflow-tooltip>
            <template slot-scope="{row, $index}">
              <el-button size="mini" type="primary" @click="seeNote(row)">查看</el-button>
            </template>
          </el-table-column>
          <el-table-column prop="issue" label="发布" align="center" width="60px"></el-table-column>
          <el-table-column prop="created_at" label="创建时间" align="center" width="160px"></el-table-column>
          <el-table-column prop="updated_at" label="修改时间" align="center" width="160px"></el-table-column>
          <el-table-column label="操作" align="center" width="200px">
            <template slot-scope="{row,$index}">
              <el-tooltip content="发布中" placement="left">
                <el-button v-show="row.issue === '是'" size="mini" class="el-icon-top" type="success"
                           @click="changeIssue(row.id, false)"></el-button>
              </el-tooltip>
              <el-tooltip content="未发布" placement="left">
                <el-button v-show="row.issue === '否'" size="mini" class="el-icon-bottom" type="info"
                           @click="changeIssue(row.id, true)"></el-button>
              </el-tooltip>
              <el-button size="mini" class="el-icon-edit" type="warning" @click="editAppInfo(row.id)"></el-button>
              <el-button size="mini" class="el-icon-delete" type="danger" @click="deleteAppInfo(row)"></el-button>
            </template>
          </el-table-column>
        </el-table>

        <el-pagination style="text-align: center;margin-top: 20px" background :total="MixinTotal"
                       :current-page="MixinCurrent_page" :page-size="MixinCurrent_limit"
                       @current-change="changeCurrentPage" @size-change="changeSize"
                       hide-on-single-page
                       :layout="MixinLayout">
        </el-pagination>
      </div>
      <div v-show="MixinScene === 1">
        <app-from @changeScene="changeScene" ref="appFrom"></app-from>
      </div>
    </el-card>
    <el-dialog title="更新信息" :visible.sync="dialogFormVisible">
      <span style="text-align: center" v-html="appFromNote"></span>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="dialogFormVisible = false">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import {mapActions, mapState} from "vuex";
import appFrom from "@/views/website/appUpdateCenter/appFrom/index.vue";
import mixin from "@/mixin";

export default {
  components: {appFrom},
  mixins:[mixin],
  data() {
    return {
      // 显示dialog
      dialogFormVisible: false,
      // 更新内容
      appFromNote: ''
    }
  },
  created() {
    this.changeCurrentPage()
  },
  computed: {
    ...mapState('appInfo', ['appInfoObj'])
  },
  methods: {
    ...mapActions('appInfo', ['getAppInfo', 'getAppInfoById', 'updateAppStatus', 'deleteApp']),
    // 高亮表格中正在发布的app的一行
    tableRowClassName({row, rowIndex}) {
      if (row.issue === '是'){
        return 'success-row'
      }
    },
    /**
     * 切换场景
     * @param type 切换场景的数字
     * @param flag 'update':修改时 | 'add':添加时 '':为空时
     */
    changeScene(type, flag = '') {
      this.MixinScene = type
      if (flag === 'update') {
        this.changeCurrentPage(this.MixinCurrent_page)
      } else if (flag === 'add') {
        this.changeCurrentPage()
      }
    },
    // 查看内容
    seeNote(row) {
      this.dialogFormVisible = true
      this.appFromNote = JSON.parse(row.note)
    },
    // 当前页数改变时
    changeCurrentPage(currentPage) {
      let data = this.initCurrentData(currentPage)
      this.getAppInfo(data).then(res => {
        let {appInfoObj} = this
        this.MixinCurrent_page = appInfoObj.currentPage
        this.MixinCurrent_limit = appInfoObj.limit
        this.MixinTotal = appInfoObj.total
        this.MixinTableLoading = false
      }).catch(error => {
      })
    },
    // 当前页码改变时
    changeSize(current_limit) {
      this.MixinCurrent_limit = current_limit
      this.changeCurrentPage()
    },

    // 修改是否发布
    changeIssue(id, flag) {
      let msg = flag === true ? '发布' : '取消发布'
      this.$confirm(`确定要${msg}吗？`, '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.updateAppStatus({id, flag}).then(res => {
          this.$message({
            type: 'success',
            message: '修改成功!'
          })
          this.changeCurrentPage()
        }).catch(error => {
        })
      }).catch(() => {
      })
    },
    // 修改app信息
    editAppInfo(id) {
      this.getAppInfoById(id).then(res => {
        this.MixinScene = 1
        this.$refs.appFrom.initAppInfo()
      }).catch(error => {
      })
    },
    // 删除app信息
    deleteAppInfo(data) {
      this.$confirm('此操作将永久删除该信息, 是否继续?', '警告', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.deleteApp(data.id).then(res=>{
          this.changeCurrentPage(this.appInfoObj.data.length > 1 ? this.current_page : this.current_page > 1 ? this.current_page - 1 : this.current_page)
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

<style lang="scss">
.el-table .success-row {
  background: #cceebd;
}
</style>
