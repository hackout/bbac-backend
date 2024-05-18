<template>
  <Layout>
    <TopNav></TopNav>
    <div class="page-block">
      <div class="page-search">
        <div class="page-search-buttons">
          <el-button type="primary" @click="addItem">手动创建/Create</el-button>
        </div>
        <div class="page-search-form">
          <el-form :model="query" ref="query" inline @submit.native.prevent="onSearch">
            <el-form-item>
              <el-select style="width:120px" v-model="query.engine" placeholder="机型筛选" @change="onSearch" clearable
                filterable>
                <el-option v-for="(item, index) in engine_type" :key="index" :value="item.value"
                  :label="item.name"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item>
              <el-select style="width:160px" v-model="query.status" placeholder="考核状态" @change="onSearch" clearable
                filterable>
                <el-option v-for="(item, index) in task_status" :key="index" :value="item.value"
                  :label="item.name"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item>
              <el-input v-model="query.keyword" style="width:250px" placeholder="发动机号/订单号/备注描述"></el-input>
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
      <DataTable ref="table" :apiName="$route('vehicle.task_list')" @change-page="query.page = $event"
        @change-page-size="query.limit = $event" height="550px" :params="query" stripe highlightCurrentRow>
        <el-table-column align="center" prop="id" width="80">
          <template #header>
            <span>序号</span><br /><span>No.</span>
          </template>
          <template #default="scope">
            <span>{{ scope.$index + 1 }}</span>
          </template>
        </el-table-column>
        <el-table-column align="center" prop="eb_type" width="100">
          <template #header>
            <span>机型</span><br /><span>Engine</span>
          </template>
          <template #default="scope">
            <el-tag size="small">{{ $status('engine_type', scope.row.engine) }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column align="center" prop="auditor" width="150">
          <template #header>
            <span>考核员</span><br /><span>Auditor</span>
          </template>
          <template #default="scope">
            <span v-if="scope.row.auditor">{{ scope.row.auditor }}</span>
            <span v-else>-</span>
          </template>
        </el-table-column>
        <el-table-column align="center" prop="assembly" width="150">
          <template #header>
            <span>总成号</span><br /><span>Assembly</span>
          </template>
        </el-table-column>
        <el-table-column align="center" prop="product" width="220">
          <template #header>
            <span>发动机号</span><br /><span>Engine No.</span>
          </template>
        </el-table-column>
        <el-table-column align="center" prop="type" width="150">
          <template #header>
            <span>缺陷判定</span><br /><span>Finding</span>
          </template>
          <template #default="scope">
            <el-tag size="small">{{ $status('defect_category', scope.row.defect_category) }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column align="center" prop="description" width="250">
          <template #header>
            <span>动态检查</span><br /><span>Dynamic Check</span>
          </template>
          <template #default="scope">
            <el-text size="small" @click="openDetail(scope.row)" tag="ins" link>查看详情/Detail</el-text>
          </template>
        </el-table-column>
        <el-table-column align="center" prop="plant" min-width="200">
          <template #header>
            <span>备注</span><br /><span>Remark</span>
          </template>
          <template #default="scope">
            <el-text>{{ scope.row.remark }}</el-text>
          </template>
        </el-table-column>
        <el-table-column align="right" prop="action" width="235" fixed="right">
          <template #header>
            <span>操作</span><br /><span>Operation</span>
          </template>
          <template #default="scope">
            <el-button type="primary" size="small" @click="viewItem(scope.row)" link>
              <span>预览</span><br />
              <small>Preview</small>
            </el-button>
            <el-button type="primary" size="small" @click="editItem(scope.row)" link>
              <span>维护</span><br />
              <small>Edit</small>
            </el-button>
            <el-popconfirm title="确定删除此项?" @confirm="deleteItem(scope.row)">
              <template #reference>
                <el-button type="primary" size="small" link>
                  <span>删除</span><br />
                  <small>Delete</small>
                </el-button>
              </template>
            </el-popconfirm>
            <el-button type="primary" v-if="!scope.row.auditor" @click="assignItem(scope.row)" size="small" link>
              <span>分配任务</span><br />
              <small>Assign Task</small>
            </el-button>
          </template>
        </el-table-column>
      </DataTable>
      <SaveDialog ref="SaveDialog" :users="users" v-if="SaveDialogVisit" @success="refreshData" @closed="SaveDialogVisit = false"></SaveDialog>
      <DetailDialog ref="DetailDialog" :examine_vehicle_item_type="examine_vehicle_item_type" v-if="DetailDialogVisit" @success="refreshData" @closed="DetailDialogVisit = false"></DetailDialog>
      <AddDialog ref="AddDialog"
        :examine_type="examine_type"
        :status="status"
        :plant="plant"
        :line="line"
        :engine_type="engine_type"
        :task_status="task_status"
      v-if="AddDialogVisit" @success="refreshData" @closed="AddDialogVisit = false"></AddDialog>
    </div>
  </Layout>
</template>
<script>
import AddDialog from "./Addons/AddDialog.vue";
import SaveDialog from "./Addons/SaveDialog.vue";
import DetailDialog from "./Addons/DetailDialog.vue";
export default {
  components:{
    AddDialog,
    SaveDialog,
    DetailDialog
  },
  props: {
    defect_level: {
      type: Array,
      default: []
    },
    examine_vehicle_item_type: {
      type: Array,
      default: []
    },
    defect_category: {
      type: Array,
      default: []
    },
    task_status: {
      type: Array,
      default: []
    },
    engine_type: {
      type: Array,
      default: []
    },
    users: {
      type: Array,
      default: []
    },
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
  },
  data() {
    return {
      query: {
        page: 1,
        limit: 20,
        engine: '',
        status: '',
        keyword: ''
      },
      SaveDialogVisit: false,
      DetailDialogVisit: false,
      AddDialogVisit: false,
    }
  },
  mounted() {
    this.$nextTick(() => {
    })
  },
  methods: {
    viewItem(item) {
      this.$goTo('vehicle.task_detail', { id: item.id });
    },
    editItem(item) {
      this.$goTo('vehicle.task_edit', { id: item.id });
    },
    assignItem(item){
      this.SaveDialogVisit = true
      this.$nextTick(()=>{
        this.$refs.SaveDialog.open(item)
      })
    },
    addItem()
    {
      this.AddDialogVisit = true
      this.$nextTick(()=>{
        this.$refs.AddDialog.open()
      })
    },
    openDetail(item)
    {
      this.DetailDialogVisit = true
      this.$nextTick(()=>{
        this.$refs.DetailDialog.open(item)
      })
    },
    async deleteItem(item) {
      var res = await this.$axios.delete(this.$route('vehicle.task_delete', { id: item.id }))
      this.deleting = false
      if (res.code == this.$config.successCode) {
        this.$message.success("删除考核成功")
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

:deep(.el-button > span) {
  display: inline-block;
}
</style>