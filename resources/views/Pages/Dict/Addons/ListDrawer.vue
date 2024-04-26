<template>
    <el-drawer v-model="visitable" destroy-on-close @closed="$emit('closed')" title="字典项配置" size="780px">
        <div class="page-search">
            <div class="page-search-buttons">
                <el-button size="small" @click="addItem" type="primary">新增</el-button>
                <el-button size="small" @click="deleteItem" :disabled="itemIds.length == 0" type="danger">删除</el-button>
            </div>
        </div>
        <DataTable ref="table" :apiName="$route('dict_item.list', { code: code })" @change-page="listQuery.page = $event"
            @change-page-size="listQuery.limit = $event" height="calc(100vh - 150px)" :params="listQuery" stripe
            highlightCurrentRow remoteSort @selection-change="selectionChange" remoteFilter>
            <el-table-column type="selection" align="center" width="55" />
            <el-table-column label="字典编码" align="center" prop="id" width="100"></el-table-column>
            <el-table-column label="字典标签" align="center" prop="name" width="100"></el-table-column>
            <el-table-column label="键值" align="center" prop="content" width="100"></el-table-column>
            <el-table-column label="排序" align="center" prop="sort_order" width="100"></el-table-column>
            <el-table-column label="创建时间" align="center" prop="created_at" width="180">
                <template #default="scope">
                    <span>{{ $tool.dateFormat(scope.row.created_at) }}</span>
                </template>
            </el-table-column>
            <el-table-column label="操作" align="center" prop="action">
                <template #default="scope">
                    <el-button type="primary" @click="editItem(scope.row)" link>
                        <span>修改</span>
                    </el-button>
                </template>
            </el-table-column>
        </DataTable>
        <ItemDialog v-if="dialogVisit" ref="formDialog" @success="refreshData" @closed="dialogVisit = false">
        </ItemDialog>
    </el-drawer>
</template>
<script>
import ItemDialog from './ItemDialog.vue';
export default {
    components: {
        ItemDialog
    },
    emits: ['closed'],
    data() {
        return {
            listQuery: {
                page: 1,
                limit: 20
            },
            code: 0,
            visitable: false,
            dialogVisit: false,
            itemIds: []
        }
    },
    methods: {
        open(item) {
            this.code = item.code
            this.visitable = true
        },
        selectionChange(items) {
            let itemIds = []
            items.forEach(n => {
                itemIds.push(n.id)
            })
            this.itemIds = itemIds
        },
        async onSearch() {
            var validate = await this.$refs.queryForm.validate().catch(() => { })
            if (!validate) return false;
            this.$nextTick(() => {
                this.$refs.table.upData(this.queryForm)
            })
        },
        refreshData() {
            this.$nextTick(() => {
                this.$refs.table.refresh()
            })
        },
        addItem() {
            this.dialogVisit = true
            this.$nextTick(() => {
                this.$refs.formDialog.open('add', this.code)
            })
        },
        editItem(item) {
            this.dialogVisit = true
            this.$nextTick(() => {
                this.$refs.formDialog.open('edit', this.code, item)
            })
        },
        deleteItem(item) {
            this.$confirm('确定删除所选项？', '系统提示').then(async () => {
                var res = await this.$axios.post(this.$route('dict_item.batch_delete', { code: this.code }), { ids: this.itemIds })
                if (res.code == this.$config.successCode) {
                    this.$message.success('删除字典项成功')
                    this.refreshData()
                } else {
                    this.$message.error(res.message)
                }
            })
        }
    }
}
</script>

<style lang="scss" scoped></style>