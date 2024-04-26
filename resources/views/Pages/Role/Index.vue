<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block">
            <div class="page-search">
                <div class="page-search-buttons">
                    <el-button type="primary" @click="addItem" icon="el-icon-plus">新增</el-button>
                    <el-button type="success" :disabled="items.length != 1" @click="editItem"
                        icon="el-icon-edit">编辑</el-button>
                    <el-divider direction="vertical" />
                    <el-button type="danger" :disabled="items.length == 0" @click="deleteItem" icon="el-icon-delete"
                        :loading="deleting">删除</el-button>
                </div>
            </div>
            <DataTable ref="table" :apiName="$route('role.list')" @change-page="query.page = $event"
                @change-page-size="query.limit = $event" height="100%" :params="query" stripe highlightCurrentRow
                remoteSort @selection-change="selectionChange" remoteFilter>
                <el-table-column label="选择" type="selection" align="center" width="55" />
                <el-table-column label="序号" align="center" prop="id" width="100">

                    <template #default="scope">
                        <span>{{ scope.$index + 1 < 10 ? '0' + (scope.$index + 1) : scope.$index + 1 }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="角色名称" align="center" prop="name"></el-table-column>
                <el-table-column label="角色状态" align="center" prop="is_valid" width="150">
                    <template #default="scope">
                        <el-switch v-model="scope.row.is_valid" v-loading="saving" @change="changeValid(scope.row)" />
                    </template>
                </el-table-column>
                <el-table-column label="成员数" align="center" prop="user_count" width="100"></el-table-column>
            </DataTable>
        </div>
        <SaveDialog ref="SaveDialog" v-if="editable" @closed="editable = false" @success="refreshData"
            :permissions="permissions">
        </SaveDialog>
    </Layout>
</template>

<script>
import SaveDialog from './Addons/SaveDialog.vue'
export default {
    components: {
        SaveDialog
    },
    props: {
        permissions: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            saving: false,
            editable: false,
            query: {
                page: 1,
                limit: 20
            },
            items: [],
            deleting: false
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
        async changeValid(item) {
            this.saving = true
            let res = await this.$axios.patch(this.$route('role.valid', { id: item.id }))
            this.saving = false
            if (res.code == this.$config.successCode) {
                this.$message.success('修改状态成功')
                this.refreshData()
            } else {
                this.$message.error(res.message)
            }

        },
        deleteItem() {
            this.$confirm('确定删除所选项?', '操作提示').then(async () => {
                this.deleting = true
                var res = await this.$axios.post(this.$route('role.batch_delete'), { ids: this.items.map(n => n.id) })
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
            this.itemIds = []
            this.$refs.table.refresh()
        }
    }
}
</script>

<style scoped lang="scss"></style>