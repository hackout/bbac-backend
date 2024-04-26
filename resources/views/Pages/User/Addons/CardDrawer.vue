<template>
    <el-drawer title="生日卡列表" v-model="visitable" :size="800" @closed="$emit('closed')">
        <DataTable ref="table" :apiName="$route('birthday_card.list')" @change-page="query.page = $event"
            @change-page-size="query.limit = $event" height="100%" :params="query" stripe highlightCurrentRow remoteSort
            remoteFilter>
            <el-table-column label="序号" align="center" prop="id" width="100">
                <template #default="scope">
                    <span>{{ scope.$index + 1 < 10 ? '0' + (scope.$index + 1) : scope.$index + 1 }}</span>
                </template>
            </el-table-column>
            <el-table-column label="示例图" align="center" prop="example">
                <template #default="scope">
                    <el-image style="width: 120px; height: 60px" :src="scope.row.example" :zoom-rate="1.2"
                        :max-scale="7" :min-scale="0.2" preview-teleported :preview-src-list="[scope.row.example]" fit="cover" />
                </template>
            </el-table-column>
            <el-table-column label="名称" align="center" prop="name" width="115"></el-table-column>
            <el-table-column label="操作" align="center" prop="action" width="185">
                <template #default="scope">
                    <el-button size="small" @click="editItem(scope.row)" type="primary" link>编辑</el-button>
                    <el-popconfirm title="确定删除此项?" @confirm="deleteItem(scope.row)">
                        <template #reference>
                            <el-button type="danger" link>
                                <span>删除</span>
                            </el-button>
                        </template>
                    </el-popconfirm>
                </template>
            </el-table-column>
        </DataTable>
        <CardDrawerDialog ref="CardDrawerDialog" v-if="editable" @success="loadRefresh" @closed="editable = false">
        </CardDrawerDialog>
        <template #footer>
            <div class="drawer-footer">
                <el-button type="primary" @click="addItem">添加新样式</el-button>
            </div>
        </template>
    </el-drawer>
</template>

<script>

import CardDrawerDialog from './CardDrawerDialog.vue'
export default {
    components: {
        CardDrawerDialog
    },
    emits: ['success', 'closed'],
    data() {
        return {
            editable: false,
            visitable: false,
            loading: false,
        }
    },
    methods: {
        addItem() {
            this.editable = true
            this.$nextTick(() => {
                this.$refs.CardDrawerDialog.open('add')
            })
        },
        editItem(item) {
            this.editable = true
            this.$nextTick(() => {
                this.$refs.CardDrawerDialog.open('edit', item)
            })
        },
        open() {
            this.visitable = true
        },
        async deleteItem(item) {
            let res = await this.$axios.delete(this.$route('birthday_card.delete', { id: item.id }))
            if (res.code == this.$config.successCode) {
                this.$message.success('删除样式成功')
                this.loadRefresh()
            } else {
                this.$message.error(res.message)
            }
        },
        loadRefresh() {
            this.refreshData()
            this.$emit('success')
        },
        refreshData() {
            this.$refs.table.refresh()
        }
    }
}
</script>

<style scoped lang="scss">
</style>