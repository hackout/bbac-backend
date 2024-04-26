<template>
    <el-dialog :title="`${item.name}的零件列表`" v-model="visitable" width="1000" @closed="$emit('closed')">
        <div class="DataTable" style="width:100%;">
            <el-table :data="items" style="width:100%;" v-loading="loading" border size="small" stripe highlightCurrentRow>
                <el-table-column label="序号" align="center" prop="id" width="65">
                    <template #default="scope">
                        <span>{{ scope.$index + 1 < 10 ? '0' + (scope.$index + 1) : scope.$index + 1 }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="用户" align="center" prop="user" width="100"></el-table-column>
                <el-table-column label="总成号" align="center" prop="assembly"></el-table-column>
                <el-table-column label="发动机号" align="center" prop="product"></el-table-column>
                <el-table-column label="零件编号" align="center" prop="number"></el-table-column>
                <el-table-column label="更新时间" align="center" prop="updated_at" width="135">
                    <template #default="scope">
                        <el-tag size="small">{{ $tool.dateFormat(scope.row.updated_at) }}</el-tag>
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </el-dialog>
</template>

<script>
export default {
    emits: ['closed'],
    data() {
        return {
            item: {
                name: ''
            },
            items: [],
            visitable: false,
            loading: false,
        }
    },
    methods: {
        async getData() {
            this.loading = true
            var res = await this.$axios.get(this.$route('part.item', { id: this.item.id }))
            this.loading = false;
            if (res.code == this.$config.successCode) {
                this.items = res.data
                this.visitable = true
            } else {
                this.$message.error(res.message)
                this.$emit('close')
                this.visitable = false;
            }
        },
        open(item) {
            this.item = item
            this.getData()
        }
    }
}
</script>

<style scoped lang="scss">
.DataTable :deep(.el-table) {
    --el-table-border-color: var(--el-border-light);
}

.DataTable :deep(th.el-table__cell) {
    background-color: var(--el-table-th-bg);
}

.DataTable :deep(thead) {
    color: var(--el-table-th);
}

.DataTable :deep(thead th) {
    font-weight: 200;
}

.DataTable :deep(.el-table__footer) .cell {
    font-weight: bold;
}

.DataTable :deep(.el-table__body-wrapper) .el-scrollbar__bar.is-horizontal {
    height: 12px;
    border-radius: 12px;
}

.DataTable :deep(.el-table__body-wrapper) .el-scrollbar__bar.is-vertical {
    width: 12px;
    border-radius: 12px;
}
</style>