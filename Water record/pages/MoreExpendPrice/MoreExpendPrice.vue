<template>
  <view>
    <PriceList :day-data="dayData"></PriceList>
  </view>
</template>

<script>
import PriceList from "@/pages/tabbar/index/PriceList/PriceList.vue";
import {mapGetters, mapActions} from 'vuex'

export default {
  components: {
    PriceList,
  },
  data() {
    return {};
  },
  onLoad() {
    this.getAllUserConsume()
  },
  computed: {
    ...mapGetters('consume', ['dayData'])
  },
  methods: {
    ...mapActions('consume', ['getAllUserConsume']),
  },
}
</script>

<style lang="scss">

</style>
