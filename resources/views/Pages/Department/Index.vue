<template>
  <Layout>
    <TopNav></TopNav>
    <div class="page-block" style="height:650px;">
      <div class="page-search">
        <div class="page-search-form">
          <el-button type="primary" @click="addItem" icon="el-icon-plus">新增</el-button>
          <el-button type="success" :disabled="items.length != 1" @click="editItem" icon="el-icon-edit">编辑</el-button>
          <el-divider direction="vertical" />
          <el-button type="danger" :disabled="items.length == 0" @click="deleteItem" icon="el-icon-delete"
            :loading="deleting">删除</el-button>
          <el-divider direction="vertical" />
          <el-upload :action="$route('department.import')" ref="importUpload" :on-success="refreshData"
            class="page-search-buttons-upload" :limit="1" :show-file-list="false" :headers="uploadHeaders">
            <el-button icon="el-icon-upload" type="primary">导入</el-button>
          </el-upload>
          <el-button type="primary" @click="$goTo('department.template')" link icon="el-icon-download">模板</el-button>
        </div>
      </div>
      <el-container class="department">
        <el-aside style="width:300px;">
          <el-input v-model="filterText" style="width: 300px" placeholder="通过关键词过滤" />
          <el-scrollbar>
            <el-tree ref="treeRef" class="filter-tree" :data="data" :props="defaultProps" default-expand-all
              :filter-node-method="filterNode" @node-click="clickNode">
              <template #default="{ node, data }">
                <el-icon-briefcase />
                <span>{{ node.label }}</span>
              </template>
            </el-tree>
          </el-scrollbar>
        </el-aside>
        <el-main style="padding:0;">
          <DataTable ref="table" :apiName="$route('department.list')" @change-page="query.page = $event"
            @change-page-size="query.limit = $event" height="100%" :params="query" stripe highlightCurrentRow remoteSort
            @selection-change="selectionChange" remoteFilter>
            <el-table-column label="选择" type="selection" align="center" width="55" />
            <el-table-column label="序号" align="center" prop="id" width="100">

              <template #default="scope">
                <span>{{ scope.$index + 1 < 10 ? '0' + (scope.$index + 1) : scope.$index + 1 }}</span>
              </template>
            </el-table-column>
            <el-table-column label="部门名称" align="center" prop="name"></el-table-column>
            <el-table-column label="负责人" align="center" prop="contact" width="150px">
              <template #default="scope">
                <el-tooltip effect="dark" placement="left">
                  <template #content>{{ scope.row.mobile }}<br />{{ scope.row.email }}</template>
                  <el-text>{{ scope.row.contact }}</el-text>
                </el-tooltip>
              </template>
            </el-table-column>
            <el-table-column label="成员数" align="center" prop="user_count" width="100px"></el-table-column>
          </DataTable>
        </el-main>
      </el-container>
    </div>
    <SaveDialog ref="SaveDialog" v-if="editable" @closed="editable = false" @success="refreshData"
      :departments="departments" :department_role="department_role" :users="users">
    </SaveDialog>
  </Layout>
</template>

<script>
import SaveDialog from './Addons/SaveDialog.vue'
import { router } from '@inertiajs/vue3'
export default {
  components: {
    SaveDialog
  },
  props: {
    department_role: {
      type: Array,
      default: []
    },
    departments: {
      type: Array,
      default: []
    },
    users: {
      type: Array,
      default: []
    }
  },
  data() {
    return {
      filterText: '',
      data: this.departments,
      defaultProps: {
        children: 'children',
        label: 'name',
        value: 'id'
      },
      filterNode: (value, data) => {
        if (!value) return true
        return data.name.includes(value)
      },
      editable: false,
      query: {
        parent_id: '',
        page: 1,
        limit: 20
      },
      uploadHeaders: {
        'X-XSRF-TOKEN': this.$tool.cookies.get('XSRF-TOKEN')
      },
      items: [],
      deleting: false
    }
  },
  watch: {
    filterText(val) {
      this.$refs.treeRef.filter(val)
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.refreshData()
    })
  },
  methods: {
    selectionChange(items) {
      this.items = items
    },
    clickNode(e) {
      this.query.parent_id = e.id
      this.refreshData()
    },
    addItem() {
      this.editable = true
      this.$nextTick(() => {
        this.$refs.SaveDialog.open('add')
      })
    },
    editItem() {
      this.editable = true
      this.$nextTick(() => {
        this.$refs.SaveDialog.open('edit', this.items[0])
      })
    },
    deleteItem() {
      this.$confirm('确定删除所选项?', '操作提示').then(async () => {
        this.deleting = true
        var res = await this.$axios.post(this.$route('department.batch_delete'), { ids: this.items.map(n => n.id) })
        this.deleting = false
        if (res.code == this.$config.successCode) {
          this.$message.success("批量删除成功")
          this.refreshData()
        } else {
          this.$message.error(res.message)
        }
      }).catch(() => { })
    },
    refreshData() {
      router.reload({
        only: ['departments', 'users'],
        onSuccess: () => {
          this.$refs.importUpload.clearFiles();
          this.data = [
            {
              name: '顶部信息',
              id: '',
              children: []
            },
          ]
          this.$page.props.departments.forEach(n => {
            this.data.push({
              name: n.name,
              id: n.id,
              children: this.makeChildren(n)
            })
          })
          this.itemIds = []
          this.$refs.table.refresh()
        }
      })
    },
    makeChildren(options) {
      let result = []
      if (options.children) {
        options.children.forEach(n => {
          result.push({
            name: n.name,
            id: n.id,
            children: this.makeChildren(n)
          })
        })
      }
      return result.length > 0 ? result : null
    }
  }
}
</script>

<style scoped lang="scss">
.department {
  border: var(--el-link-color) 1px solid;
  border-radius: var(--el-input-border-radius, var(--el-border-radius-base));
  height: calc(100% - 100px);

  :deep(.el-input__wrapper) {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
    border-color: var(--el-link-color);
  }

  :deep(.el-aside) {
    border-right: var(--el-link-color) 1px solid;
    height: 100%;
    overflow: hidden;

    .el-tree {
      height: calc(100% - 30px);
      width: 100%;
    }
  }
}

.el-form-item-msg {
  color: var(--el-link-color)
}
</style>