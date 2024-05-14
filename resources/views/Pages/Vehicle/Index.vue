<template>
  <Layout>
    <TopNav></TopNav>
    <div class="page-block">
      <div class="page-search">
        <div class="page-search-form" style="width: 100%;">
          <el-form :model="query" ref="query" inline @submit.native.prevent="onSearch">
            <el-form-item>
              <el-select style="width:120px" v-model="query.plant" placeholder="厂区筛选" @change="onSearch" clearable
                filterable>
                <el-option v-for="(item, index) in service_factory" :key="index" :value="item.value"
                  :label="item.name"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item>
              <el-select style="width:120px" v-model="query.eb_type" placeholder="机型筛选" @change="onSearch" clearable
                filterable>
                <el-option v-for="(item, index) in eb_type" :key="index" :value="item.value"
                  :label="item.name"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item>
              <el-select style="width:160px" v-model="query.type" placeholder="问题类型" @change="onSearch" clearable
                filterable>
                <el-option v-for="(item, index) in issue_type" :key="index" :value="item.value"
                  :label="item.name"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item>
              <el-date-picker v-model="query.date" type="daterange" range-separator="至" start-placeholder="开始日期"
                end-placeholder="结束日期" clearable />
            </el-form-item>
            <el-form-item>
              <el-input v-model="query.keyword" style="width:250px" placeholder="生产单号/发动机/电池号/问题描述"></el-input>
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
      <DataTable ref="table" :apiName="$route('vehicle.list')" @change-page="query.page = $event"
        @change-page-size="query.limit = $event" height="550px" :params="query" stripe highlightCurrentRow>
        <el-table-column label="序号" align="center" prop="id" width="80">
          <template #default="scope">
            <span>{{ scope.$index + 1 }}</span>
          </template>
        </el-table-column>
        <el-table-column label="问题类型" align="center" prop="type" width="150">
          <template #default="scope">
            <el-tag size="small">{{ $status('issue_type', scope.row.type) }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="厂区" align="center" prop="plant" width="100">
          <template #default="scope">
            <el-tag size="small">{{ $status('service_factory', scope.row.plant) }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="机型" align="center" prop="eb_type" width="100">
          <template #default="scope">
            <el-tag size="small">{{ $status('eb_type', scope.row.eb_type) }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="问题描述" align="center" prop="description" min-width="250"></el-table-column>
        <el-table-column label="提交人" align="center" prop="author" width="100"></el-table-column>
        <el-table-column label="提交时间" align="center" prop="created_at" width="175">
          <template #default="scope">
            <span>{{ $tool.dateFormat(scope.row.created_at) }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Action Status" align="center" prop="due_end" width="135">
          <template #default="scope">
            <span class="status-tag"
              :class="{ success: scope.row.due_end > 5, warning: scope.row.due_end <= 3 && scope.row.due_end > 0, danger: scope.row.due_end < 0 }"></span>
          </template>
        </el-table-column>
        <el-table-column label="操作" align="right" prop="action" width="100" fixed="right">
          <template #default="scope">
            <el-button type="primary" size="small" @click="viewItem(scope.row)" link
              v-if="scope.row.status != 3">处理</el-button>
            <el-button type="primary" size="small" @click="previewItem(scope.row)" link>预览</el-button>
          </template>
        </el-table-column>
      </DataTable>
    </div>
  </Layout>
</template>
<script>

export default {
  props: {
    service_factory: {
      type: Array,
      default: []
    },
    eb_type: {
      type: Array,
      default: []
    },
    issue_type: {
      type: Array,
      default: []
    }
  },
  data() {
    return {
      query: {
        page: 1,
        limit: 20,
        plant: '',
        eb_type: '',
        type: '',
        status: 'b',
        date: ['', ''],
        keyword: ''
      }
    }
  },
  mounted() {
    this.$nextTick(() => { })
  },
  methods: {
    viewItem(item) {
      this.$goTo('vehicle.detail', { id: item.id });
    },
    previewItem(item) {
      this.$goTo('vehicle.report', { id: item.id });
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

.status-tag {
  width: 16px;
  height: 16px;
  border-radius: 8px;
  overflow: hidden;
  display: inline-block;
  background-color: var(--el-color-primary);
}

.status-tag.success {
  background-color: var(--el-vehicle-success);
}

.status-tag.danger {
  background-color: var(--el-vehicle-danger);
}

.status-tag.warning {
  background-color: var(--el-vehicle-warning);
}
</style>