<template>
    <el-dialog title="实际测量项列表" v-model="visitable" width="800px" @closed="$emit('closed')">
        <el-alert title="所有修改删除等操作均需要提交保存,否则不生效" description="编辑时可双击输入框或点击编辑" type="error" />
        <el-table v-loading="loading" class="DataTable" :data="items" border style="width: 100%;margin-top: 5px">
            <el-table-column label="实际测量项(中文)" prop="name_zh">
                <template #default="scope">
                    <el-input v-model="items[scope.$index].name_zh" @dblclick="handleEdit(scope.$index)"
                        :readonly="!scope.row.editable" placeholder="实际测量项(中文)"></el-input>
                </template>
            </el-table-column>
            <el-table-column label="实际测量项(英文)" prop="name_en">
                <template #default="scope">
                    <el-input v-model="items[scope.$index].name_en" @dblclick="handleEdit(scope.$index)"
                        :readonly="!scope.row.editable" placeholder="实际测量项(英文)"></el-input>
                </template>
            </el-table-column>
            <el-table-column label="顺序号" prop="sort_order">
                <template #default="scope">
                    <el-input v-model="items[scope.$index].sort_order" @dblclick="handleEdit(scope.$index)"
                        :readonly="!scope.row.editable" placeholder="顺序号"></el-input>
                </template>
            </el-table-column>
            <el-table-column align="right" width="175px">
                <template #header>
                    <el-button type="primary" size="small" @click="handleAdd" icon="el-icon-plus">增加一行</el-button>
                </template>
                <template #default="scope">
                    <el-button link type="primary" @click="toTop(scope.$index)"
                        v-if="scope.$index > 0 && items.length > 1" icon="el-icon-caret-top"></el-button>
                    <el-button link type="primary" @click="toBottom(scope.$index)"
                        v-if="scope.$index < items.length - 1 && items.length > 1"
                        icon="el-icon-caret-bottom"></el-button>
                    <el-button size="small" type="primary" link @click="handleEdit(scope.$index)">编辑</el-button>
                    <el-button size="small" type="danger" link @click="handleDelete(scope.$index)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="visitable = false">取消</el-button>
                <el-button type="primary" @click="onSave">保存编辑</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script>
export default {
    emits: ['closed'],
    data() {
        return {
            item: { id: '' },
            commit: { id: '' },
            visitable: false,
            items: [],
            templateItem: {
                name_zh: '',
                name_en: '',
                sort_order: '0',
                editable: true
            }
        }
    },
    methods: {
        handleAdd() {
            let template = this.$tool.objCopy(this.templateItem)
            this.items.push(template)
        },
        handleEdit(index) {
            this.items = this.items.map((n, i) => {
                if (i == index) {
                    n.editable = true
                }
                return n
            })
        },
        handleDelete(index) {
            this.items = this.items.map((n, i) => i != index)
        },
        async onSave() {
            this.loading = true
            let res = await this.$axios.post(this.$route('commit_item_option.save', { id: this.commit.id, item_id: this.item.id }), { items: this.items })
            this.loading = false
            if (res.code == this.$config.successCode) {
                this.$message.success("保存实际测量项成功")
                this.visitable = false
            } else {
                this.$message.error(res.message)
            }
        },
        open(commit, item) {
            this.commit = commit
            this.item = item
            this.getData()
            this.visitable = true
        },
        toTop(index) {
            let items = this.items
            let _item = items[index];
            let _after = items[index - 1];
            items[index - 1] = _item
            items[index] = _after
            this.items = items;
        },
        toBottom(index) {
            let items = this.items
            let _item = items[index];
            let _after = items[index + 1];
            items[index + 1] = _item
            items[index] = _after
            this.items = items;
        },
        async getData() {
            this.loading = true
            let res = await this.$axios.get(this.$route('commit_item_option.list', { id: this.commit.id, item_id: this.item.id }))
            this.loading = false
            if (res.code == this.$config.successCode) {
                this.items = res.data.map(n => {
                    return {
                        id: n.id,
                        name_zh: n.name_zh,
                        name_en: n.name_en,
                        sort_order: n.sort_order,
                        editable: false
                    }
                })
            }
        }
    }
}
</script>

<style scoped lang="scss">
.DataTable.el-table {
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

.col-text {
    width: 100%;
    height: 200px;
    @extend .flexColumn;
    font-size: 18px;
    border: var(--el-border-light) 1px solid;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
}
</style>