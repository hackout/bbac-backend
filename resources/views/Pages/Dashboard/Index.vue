<template>
  <Layout>
    <el-row :gutter="20">
      <el-col :span="12">
        <el-card header="整车服务" style="height:375px">
          <center>
            <el-statistic title="待处理问题" :value="items.vehicle.processing" />
            <el-divider></el-divider>
            <el-statistic title="已处理问题" :value="items.vehicle.finish" />
            <el-divider></el-divider>
            <el-statistic title="动态考核" :value="items.vehicle.dynamic" />
          </center>
        </el-card>
      </el-col>
      <el-col :span="12">
        <el-card header="产品考核" style="height:375px">
          <center>
            <el-statistic title="未完成考核单" :value="items.product.processing" />
            <el-divider></el-divider>
            <el-statistic title="已完成考核单" :value="items.product.finish" />
            <el-divider></el-divider>
            <el-statistic title="动态考核" :value="items.product.dynamic" />
          </center>
        </el-card>
      </el-col>
      <el-col :span="12">
        <el-card header="在线考核" style="margin-top: 20px;height:375px">
          <center>
            <el-statistic title="未完成考核单" :value="items.inline.processing" />
            <el-divider></el-divider>
            <el-statistic title="已完成考核单" :value="items.inline.finish" />
            <el-divider></el-divider>
            <el-statistic title="动态考核" :value="items.inline.dynamic" />
          </center>
        </el-card>
      </el-col>
      <el-col :span="12">
        <el-card header="客户端下载" style="margin-top: 20px;height:375px">
          <center>
            <qrcode-vue value="/app.apk" :size="200" level="H" />
            <el-divider></el-divider>
            <el-link href="/app.apk" target="_blank">点击下载APP</el-link>
          </center>
        </el-card>
      </el-col>
    </el-row>
  </Layout>
</template>

<script>
import QrcodeVue from 'qrcode.vue'
export default {
  components: {
    QrcodeVue
  },
  props: {
    items: {
      type: Object,
      default: {
        inline: {},
        product: {},
        vehicle: {}
      }
    }
  },
  mounted() {
    this.$nextTick(() => {

    })
  }
}
</script>

<style scoped lang="scss">
:deep(.el-card__header) {
  font-size: 18px;
}
</style>