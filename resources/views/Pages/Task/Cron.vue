<template>
  <Layout>
    <TopNav></TopNav>
    <div class="page-block">
      <div class="page-search">
        <div class="page-search-buttons">
          <el-button type="primary" @click="addItem" icon="el-icon-plus">新增</el-button>
        </div>
        <div class="page-search-form">
          <el-form :model="query" ref="query" inline @submit.native.prevent="onSearch">
            <el-form-item>
              <el-radio-group v-model="query.is_valid" @change="onSearch" clearable>
                <el-radio-button label="">全部</el-radio-button>
                <el-radio-button :label="1">启用</el-radio-button>
                <el-radio-button :label="0">暂停</el-radio-button>
              </el-radio-group>
            </el-form-item>
            <el-form-item>
              <el-select style="width:120px" v-model="query.examine_type" placeholder="考核类型" @change="onSearch"
                clearable>
                <el-option v-for="(item, index) in examine_type" :key="index" :value="item.value"
                  :label="item.name"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item>
              <el-input v-model="query.keyword" style="width:160px" placeholder="关键词"></el-input>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" native-type="submit">
                <span>查询</span>
              </el-button>
              <el-button native-type="reset">
                <span>重置</span>
              </el-button>
            </el-form-item>
          </el-form>
        </div>
      </div>
      <DataTable ref="table" :apiName="$route('task_cron.list')" @change-page="query.page = $event"
        @change-page-size="query.limit = $event" height="550px" :params="query" stripe highlightCurrentRow>
        <el-table-column label="序号" align="center" prop="id" width="80">
          <template #default="scope">
            <span>{{ scope.$index + 1 }}</span>
          </template>
        </el-table-column>
        <el-table-column label="考核名称" align="center" prop="name" width="150"></el-table-column>
        <el-table-column label="考核对象" align="center" prop="target" min-width="250">
          <template #default="scope">
            <span>{{ getTarget(scope.row) }}</span>
          </template>
        </el-table-column>
        <el-table-column label="标准工时" align="center" prop="period" width="90"></el-table-column>
        <el-table-column label="任务循环" align="center" prop="days" width="210">
          <template #default="scope">
            <el-tooltip placement="top" v-for="(d, i) in scope.row.days" :key="i">
              <template #content>{{ d.unit }}次/{{ d.day }}天</template>
              <el-tag size="small">{{ $tool.dateFormat(d.date[0], 'YYYY-MM-DD') }}/{{ $tool.dateFormat(d.date[1],
                'YYYY-MM-DD') }}</el-tag>
            </el-tooltip>
          </template>
        </el-table-column>
        <el-table-column label="按产量生成" align="center" prop="yield" width="150">
          <template #default="scope">
            <span>{{ scope.row.yield }}台排产{{ scope.row.yield_unit }}次</span>
          </template>
        </el-table-column>
        <el-table-column label="任务状态" align="center" prop="engine" width="100">
          <template #default="scope">
            <el-tag size="small" type="success" v-if="scope.row.is_valid">运行中</el-tag>
            <el-tag size="small" type="danger" v-else>已停止</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" align="right" prop="action" width="110" fixed="right">
          <template #default="scope">
            <el-popconfirm title="确定停用该任务?" v-if="scope.row.is_valid" @confirm="invalidItem(scope.row)">
              <template #reference>
                <el-button type="danger" size="small" link>
                  <span>停用</span>
                </el-button>
              </template>
            </el-popconfirm>
            <el-popconfirm title="确定启用该任务?" v-else @confirm="validItem(scope.row)">
              <template #reference>
                <el-button type="success" size="small" link>
                  <span>启用</span>
                </el-button>
              </template>
            </el-popconfirm>
            <el-popconfirm title="确定删除此项?" @confirm="deleteItem(scope.row)">
              <template #reference>
                <el-button type="danger" size="small" link>
                  <span>删除</span>
                </el-button>
              </template>
            </el-popconfirm>
          </template>
        </el-table-column>
      </DataTable>
    </div>
    <CronDialog v-if="editable" ref="CronDialog" :examine_type="examine_type" :status="status" :plant="plant"
      :line="line" :engine_type="engine_type" @success="refreshData" @closed="editable = false"></CronDialog>
  </Layout>
</template>
<script>
import CronDialog from './Addons/CronDialog.vue'

export default {
  components: {
    CronDialog
  },
  props: {
    examine_type: {
      type: Array,
      default: []
    },
    status: {
      type: Array,
      default: []
    },
    plant: {
      type: Array,
      default: []
    },
    line: {
      type: Array,
      default: []
    },
    engine_type: {
      type: Array,
      default: []
    },
    task_status: {
      type: Array,
      default: []
    },
    assemblies: {
      type: Array,
      default: []
    }
  },
  data() {
    return {
      query: {
        page: 1,
        limit: 20,
        is_valid: '',
        keyword: '',
        examine_type: '',
      },
      editable: false
    }
  },
  mounted() {
    this.$nextTick(() => { })
  },
  methods: {
    getTarget(item) {
      return [
        this.$status('plant', item.plant),
        this.$status('line', item.line),
        this.$status('engine_type', item.engine),
        this.$status('assemblies', item.assembly_id),
        this.$status('status', item.status),
      ].join('-')
    },
    addItem() {
      this.editable = true
      this.$nextTick(() => {
        this.$refs.CronDialog.open('add')
      })
    },
    async validItem(item) {
      await this.changeItem(item.id, 'valid')
      this.$message.success("启用任务成功")
    },
    async invalidItem(item) {
      await this.changeItem(item.id, 'invalid')
      this.$message.success("停用任务成功")
    },
    async changeItem(id, status) {
      var res = await this.$axios.patch(this.$route('task_cron.patch', { id: id, status: status }))
      if (res.code == this.$config.successCode) {
        this.refreshData()
      } else {
        this.$message.error(res.message)
      }
    },
    async deleteItem(item) {
      var res = await this.$axios.delete(this.$route('task_cron.delete', { id: item.id }))
      this.deleting = false
      if (res.code == this.$config.successCode) {
        this.$message.success("删除任务成功")
        this.refreshData()
      } else {
        this.$message.error(res.message)
      }
    },
    async onSearch() {
      var validate = await this.$refs.query.validate().catch(() => { })
      if (!validate) return false;
      this.$nextTick(() => {
        this.$refs.table.upData(this.query)
      })
    },
    refreshData() {
      this.editable = false
      this.$refs.table.refresh()
    }
  }
}
</script>

<style scoped>
.el-form-item-msg {
  color: var(--el-link-color)
}
</style>