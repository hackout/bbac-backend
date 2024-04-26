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
              <el-date-picker v-model="query.date" type="date" placeholder="开始时间" />
            </el-form-item>
            <el-form-item>
              <el-select style="width:120px" v-model="query.examine_type" placeholder="考核类型" @change="onSearch"
                clearable>
                <el-option v-for="(item, index) in examine_type" :key="index" :value="item.value"
                  :label="item.name"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item>
              <el-select style="width:120px" v-model="query.task_status" placeholder="任务状态" @change="onSearch"
                clearable>
                <el-option v-for="(item, index) in task_status" :key="index" :value="item.value"
                  :label="item.name"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item>
              <el-input v-model="query.keyword" style="width:160px" placeholder="订单号"></el-input>
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
      <DataTable ref="table" :apiName="$route('task.list')" @change-page="query.page = $event"
        @change-page-size="query.limit = $event" height="550px" :params="query" stripe highlightCurrentRow>
        <el-table-column label="序号" align="center" prop="id" width="80">
          <template #default="scope">
            <span>{{ scope.$index + 1 }}</span>
          </template>
        </el-table-column>
        <el-table-column label="考核ID" align="center" prop="number" width="400"></el-table-column>
        <el-table-column label="考核名称" align="center" prop="name" width="150"></el-table-column>
        <el-table-column label="考核对象" align="center" prop="target" min-width="250">
          <template #default="scope">
            <span>{{ getTarget(scope.row) }}</span>
          </template>
        </el-table-column>
        <el-table-column label="标准工时" align="center" prop="period" width="90"></el-table-column>
        <el-table-column label="考核数量" align="center" prop="items_count" width="90"></el-table-column>
        <el-table-column label="考核时间" align="center" prop="start_at" width="150">
          <template #default="scope">
            <span>{{ $tool.dateFormat(scope.row.start_at) }}</span>
          </template>
        </el-table-column>
        <el-table-column label="任务状态" align="center" prop="task_status" width="100">
          <template #default="scope">
            <el-tag size="small">{{ $status('task_status', scope.row.task_status) }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" align="right" prop="action" width="55" fixed="right">
          <template #default="scope">
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
    <SaveDialog v-if="editable" ref="SaveDialog" :examine_type="examine_type" :status="status" :plant="plant"
      :line="line" :engine_type="engine_type" :task_status="task_status" @success="refreshData"
      @closed="editable = false"></SaveDialog>
  </Layout>
</template>
<script>
import SaveDialog from './Addons/SaveDialog.vue'

export default {
  components: {
    SaveDialog
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
        task_status: '',
        examine_type: '',
        date: '',
        keyword: ''
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
        this.$refs.SaveDialog.open('add')
      })
    },
    async deleteItem(item) {
      var res = await this.$axios.delete(this.$route('task.delete', { id: item.id }))
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