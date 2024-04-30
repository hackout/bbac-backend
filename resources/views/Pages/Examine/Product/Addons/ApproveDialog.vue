<template>
    <el-dialog title="送审批" v-model="visitable" width="550px" @closed="$emit('closed')">
        <el-form :model="form" v-loading="loading" :rules="{}" label-width="150px" ref="form" @submit.native.prevent="onSave">
            <el-form-item label="考核模板名称" prop="name">
                <el-input v-model="item.name" readonly placeholder="请输入考核模板名称" clearable></el-input>
            </el-form-item>
            <el-form-item label="版本变动内容">
                <el-collapse accordion style="width:100%;">
                    <el-collapse-item v-if="changed.created.length > 0" :title="`新增${changed.created.length}条考核项(查看)`"
                    name="created">
                    <el-table class="DataTable" border size="small" :data="changed.created">
                        <el-table-column label="序号" align="center" prop="id" width="50">
                        <template #default="scope">
                            <span>{{ scope.$index + 1 }}</span>
                        </template>
                        </el-table-column>
                        <el-table-column label="变更项" align="center" prop="code" width="120">
                        <template #default="scope">
                            <span>{{ changeText[scope.row.code] }}</span>
                        </template>
                        </el-table-column>
                        <el-table-column label="影响内容" align="center" prop="content"></el-table-column>
                    </el-table>
                    </el-collapse-item>
                    <el-collapse-item v-if="changed.deleted.length > 0" :title="`删除${changed.deleted.length}条考核项(查看)`"
                    name="deleted">
                    <el-table border class="DataTable" size="small" :data="changed.deleted">
                        <el-table-column label="序号" align="center" prop="id" width="50">
                        <template #default="scope">
                            <span>{{ scope.$index + 1 }}</span>
                        </template>
                        </el-table-column>
                        <el-table-column label="变更项" align="center" prop="code" width="120">
                        <template #default="scope">
                            <span>{{ changeText[scope.row.code] }}</span>
                        </template>
                        </el-table-column>
                        <el-table-column label="影响内容" align="center" prop="content"></el-table-column>
                    </el-table>
                    </el-collapse-item>
                    <el-collapse-item v-if="changed.updated.length > 0" :title="`更新${changed.updated.length}条内容(查看)`"
                    name="updated">
                    <el-table border class="DataTable" size="small" :data="changed.updated">
                        <el-table-column label="序号" align="center" prop="id" width="50">
                        <template #default="scope">
                            <span>{{ scope.$index + 1 }}</span>
                        </template>
                        </el-table-column>
                        <el-table-column label="变更项" align="center" prop="code" width="120">
                        <template #default="scope">
                            <span>{{ changeText[scope.row.code] }}</span>
                        </template>
                        </el-table-column>
                        <el-table-column label="变更前" align="center" prop="before" width="120"></el-table-column>
                        <el-table-column label="变更后" align="center" prop="content"></el-table-column>
                    </el-table>
                    </el-collapse-item>
                </el-collapse>
            </el-form-item>
            <el-form-item label="变更内容" prop="content">
                <el-input type="textarea" maxlength="200" show-word-limit v-model="form.content" placeholder="请输入变更内容"
                    clearable></el-input>
            </el-form-item>
            <el-form-item label="影响范围" prop="influence">
                <el-input type="textarea" maxlength="200" show-word-limit v-model="form.influence" placeholder="请输入影响范围"
                    clearable></el-input>
            </el-form-item>
            <el-form-item label="关注事项" prop="concerns">
                <el-input type="textarea" maxlength="200" show-word-limit v-model="form.concerns" placeholder="请输入关注事项"
                    clearable></el-input>
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="visitable = false">取消</el-button>
                <el-button type="primary" v-loading="loading" @click="onSave">提交保存</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script>
export default {
    emits: ['success', 'closed'],
    data() {
        return {
            visitable: false,
            loading: false,
            item: {
                id: '',
                name: ''
            },
            form: {
                product_id: '',
                content: '',
                influence: '',
                concerns: ''
            },
            changed: {
                updated: [],
                deleted: [],
                created: []
            },
            changeText: {
                name: '名称',
                description: '备注说明',
                period: '标准工时',
                engine: '考核机型',
                items: '考核项'
            }
        }
    },
    methods: {
        onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    this.loading = true
                    const res = await this.$axios.post(this.$route('commit_approve.create'),this.form)
                    this.loading = false;
                    if(res.code == this.$config.successCode)
                    {
                        this.$message.success('提交审批成功')
                        this.$emit('success')
                        this.visitable = false
                    }else{
                        this.$message.error(res.message)
                    }
                }
            })
        },
        async getData(){
            this.loading  = true;
            const res = await this.$axios.get(this.$route('commit_product.changed',{id:this.item.id}))
            this.loading = false
            if(res.code == this.$config.successCode)
            {
                this.changed = res.data
            }
        },
        open(item) {
            this.item = {
                id: item.id,
                name: item.name
            }
            this.form = {
                product_id: item.id,
                content: '',
                influence: '',
                concerns: ''
            }
            this.getData()
            this.visitable = true
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