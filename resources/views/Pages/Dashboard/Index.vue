<template>
    <Layout>
      <el-card title="控制台面板">
      <span>控制台面板</span>
      </el-card>
    </Layout>
  </template>
  
  <script>
  import { router } from '@inertiajs/vue3'
  export default {
    props: {
      departments: {
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
          parent_id: 0,
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
          this.$refs.DepartmentDialog.open('add')
        })
      },
      editItem() {
        this.editable = true
        this.$nextTick(() => {
          this.$refs.DepartmentDialog.open('edit', this.items[0])
        })
      },
      deleteItem() {
        this.$confirm('确定删除所选项?', '操作提示').then(async () => {
          this.deleting = true
          var res = await this.$axios.post(this.$route('organization.batch_delete'), { ids: this.items.map(n => n.id) })
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
          only: ['departments'],
          onSuccess: () => {
            this.data = []
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