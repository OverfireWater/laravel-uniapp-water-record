<template>
  <view>
    <uni-segmented-control @clickItem="clickItem" :current="current" styleType="text"
                           :values="items"></uni-segmented-control>
    <view>
      <template v-if="current === 0">
        <ConsumeType @editUserType="editUserType"
                     :userTypeList="userRelatedToExpendParentType"></ConsumeType>
      </template>
      <template v-else>
        <ConsumeType @editUserType="editUserType"
                     :userTypeList="userRelatedToIncomeParentType"></ConsumeType>
      </template>
    </view>
    <!--    添加类型组件 popup-->
    <uni-popup ref="popup" type="bottom" @maskClick="maskClick">
      <view class="popup-container" :style="popupHeight">
        <uni-section title="添加分类" type="line" style="border-bottom: 1px solid #e5e5e5">
          <template v-slot:right>
            <view class="section-container">
              <view class="section-container-left">
                <uni-data-select :clear="false" v-model="typeForm.isExpend" :localdata="expendOrIncomeRange"
                                 @change="changeExpendOrIncome"></uni-data-select>
              </view>
              <view class="section-container-right" hover-class="active" hover-stay-time="150" @click="saveType">
                <uni-icons type="plusempty" size="20" color="#ffffff"></uni-icons>
                <text>保存</text>
              </view>
            </view>
          </template>
        </uni-section>
        <uni-list>
          <uni-list-item clickable @click="clickTypeName" title="类型名称" show-arrow
                         thumb="https://qiniu-web-assets.dcloud.net.cn/unidoc/zh/unicloudlogo.png">
            <template v-slot:footer>
              <view class="uni-font">{{ typeForm.type_name ? typeForm.type_name : '请输入类型名称' }}</view>
            </template>
          </uni-list-item>
          <uni-list-item title="父类型选择" thumb="https://qiniu-web-assets.dcloud.net.cn/unidoc/zh/unicloudlogo.png">
            <template v-slot:footer>
              <view class="list-item-container">
                <picker mode="selector" @change="bindPickerChange" selector-type="picker" :range="parentTypeNameRange"
                        range-key="t_name">
                  <view class="list-item-picker">
                    {{ parentTypeObj.t_name }}
                    <uni-icons type="bottom" size="15"></uni-icons>
                  </view>
                </picker>
              </view>
            </template>
          </uni-list-item>
        </uni-list>
      </view>
    </uni-popup>
    <!--    填写类型名称时的dialog-->
    <uni-popup ref="dialog" type="dialog">
      <uni-popup-dialog mode="input" placeholder="2~5字左右" title="填写类型" :value="typeForm.type_name"
                        @confirm="dialogConfirm"></uni-popup-dialog>
    </uni-popup>
    <view v-show="showAddTypeBtn">
      <view class="footer-container" @click="openPopup"
            hover-class="active" hover-stay-time="150">
        <uni-icons type="compose" size="25" color="white"></uni-icons>
        <view style="margin-left: 5px">添加类型</view>
      </view>
    </view>

  </view>
</template>

<script>
import {mapActions, mapState} from 'vuex'
import ConsumeType from "@/pages/ConsumeTypeFrom/ConsumeType/ConsumeType.vue";

export default {
  components: {
    ConsumeType,
  },
  data() {
    return {
      items: ['支出', '收入'],
      current: 0,
      typeTitle: '支出',
      showAddTypeBtn: true,
      // 分类表单数据
      typeForm: {
        typeId: 0,
        isAdd: true,
        type_name: '',
        parentTypeId: 0,
        isExpend: 0
      },
      parentTypeObj: {},
      expendOrIncomeRange: [
        {value: 0, text: '支出'},
        {value: 1, text: '收入'},
      ],
      parentTypeNameRange: []
    }
  },
  onLoad() {
    // 获取类型
    this.getUserRelatedToParentType().then(() => {
      this.assignmentParentTypeRange()
    }).catch(() => {
    })
  },
  computed: {
    ...mapState('userType', ['userRelatedToExpendParentType', 'userRelatedToIncomeParentType']),
    popupHeight() {
      let windowInfo = uni.getWindowInfo()
      let height = windowInfo.screenHeight / 2
      return `height:${height}px;`
    }
  },
  methods: {
    ...mapActions('userType', ['getUserRelatedToParentType', 'addOrEditUserType', 'deleteUserType']),
    // 分段器 改变当前的选项
    clickItem(e) {
      if (this.current !== e.currentIndex) {
        this.current = e.currentIndex;
      }
    },

    // 打开弹出层
    openPopup() {
      this.showAddTypeBtn = false
      this.parentTypeObj = this.parentTypeNameRange[0]
      this.$refs.popup.open()
    },
    // 关闭弹出层
    closePopup() {
      this.$refs.popup.close()
      let {current} = this
      Object.assign(this._data, this.$options.data())
      this.current = current
      this.parentTypeNameRange = this.userRelatedToExpendParentType
    },

    // 点击弹出层遮罩时
    maskClick() {
      this.closePopup()
    },

    // 点击添加类型名称的回调
    clickTypeName() {
      this.$refs.dialog.open()
    },
    //点击确定添加类型名称的dialog
    dialogConfirm(value) {
      if (value.length > 5) {
        uni.showToast({
          icon: 'error',
          title: '字符过长'
        })
        return false
      }
      this.typeForm.type_name = value.trim()
    },

    // 点击切换分类类型 支出|收入
    changeExpendOrIncome(e) {
      this.typeForm.isExpend = e
      this.assignmentParentTypeRange()
      this.parentTypeObj = this.parentTypeNameRange[0]
    },
    // 选择picker
    bindPickerChange(e) {
      let index = e.detail.value
      this.parentTypeObj = this.parentTypeNameRange[index]
    },

    // 点击list-item时的回调
    editUserType(parentType, childType) {
      if (!childType.isDelete) {
        uni.showModal({
          title: '提示',
          content: '原生类型，不能修改和删除',
          showCancel: false
        })
        return false
      }
      this.typeForm.isAdd = false
      this.typeForm.typeId = childType.id
      this.typeForm.isExpend = parentType.isExpend
      this.typeForm.type_name = childType.t_name
      this.assignmentParentTypeRange()
      this.parentTypeNameRange.forEach(item => {
        if (item.id === parentType.id) {
          this.parentTypeObj = item
        }
      })
      this.showAddTypeBtn = false
      this.$refs.popup.open()
    },

    // 保存类型时
    saveType() {
      if (!this.typeForm.type_name) {
        uni.showToast({
          title: '请填写类型名称',
          icon: 'error'
        })
        return false
      }
      this.typeForm.parentTypeId = this.parentTypeObj.id
      this.addOrEditUserType(this.typeForm).then(() => {
        this.closePopup()
      }).catch(() => {
      })
    },

    // 赋值操作
    assignmentParentTypeRange() {
      if (this.typeForm.isExpend) {
        this.parentTypeNameRange = this.userRelatedToIncomeParentType
      } else {
        this.parentTypeNameRange = this.userRelatedToExpendParentType
      }
    },
  },
}
</script>


<style scoped lang="less">


.popup-container {
  width: 100%;
  height: 100%;
  background-color: #fff;

  .section-container {
    display: flex;
    flex: 1;
    flex-direction: row;
    align-items: center;

    .section-container-left {
      display: flex;
      flex-direction: row;
      align-items: center;
      margin-right: 20px;
      width: 121rpx;
    }

    .section-container-right {
      display: flex;
      flex: 1;
      flex-direction: row;
      align-items: center;
      border: 1px solid #e5e5e5;
      padding: 7px;
      border-radius: 5px;
      background-color: #007aff;
      color: #FFFFFF;
      cursor: pointer;
    }
  }
}

.list-item-container {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  border: 1px solid #e5e5e5;
  border-radius: 5px;
  padding: 7px;

  .list-item-picker {
    width: 150rpx;
    height: 100%;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }
}

.footer-container {
  position: fixed;
  bottom: 20px;
  left: 0;
  right: 0;
  text-align: center;
  border: 1px solid #e5e5e5;
  padding: 15px 0;
  border-radius: 5px;
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  flex: 1;
  cursor: pointer;
  background-color: #5288f1;
  color: white;
  z-index: 999;
}

.active {
  background-color: rgba(0, 0, 0, .5) !important;
}
</style>

<style lang="less">
/deep/ .uni-navbar__header-container {
  display: flex;
  flex: 1;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
</style>