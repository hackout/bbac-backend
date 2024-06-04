<template>
    <el-dialog :title="titles[mode]" v-model="visitable" width="620px" @closed="$emit('closed')">
        <el-form :model="form" ref="form" :rules="rules" label-width="100" @submit.native.prevent="onSave">

            <el-form-item label="工厂" prop="plant">
                <el-select v-model="form.plant" style="width:100%;" placeholder="请选择工厂" @change="getAssembly" clearable>
                    <el-option v-for="(item, index) in plant" :key="index" :label="item.name"
                        :value="item.value"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="产线" prop="line">
                <el-select v-model="form.line" style="width:100%;" placeholder="请选择产线" @change="getAssembly" clearable>
                    <el-option v-for="(item, index) in line" :key="index" :label="item.name"
                        :value="item.value"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="机型" prop="engine">
                <el-select v-model="form.engine" style="width:100%;" placeholder="请选择机型" @change="getAssembly"
                    clearable>
                    <el-option v-for="(item, index) in engine_type" :key="index" :label="item.name"
                        :value="item.value"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="项目阶段" prop="status">
                <el-select v-model="form.status" style="width:100%;" placeholder="请选择项目阶段"
                    clearable>
                    <el-option v-for="(item, index) in status" :key="index" :label="item.name"
                        :value="item.value"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="总成号" prop="assembly_id">
                <el-select v-model="form.assembly_id" style="width:100%;" placeholder="请选择总成号" clearable filterable>
                    <el-option v-for="(item, index) in assemblies" :key="index" :label="item.name"
                        :value="item.value"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="发动机号" prop="number">
                <el-input v-model="form.number" placeholder="请输入发动机号" clearable></el-input>
            </el-form-item>
            <el-form-item label="接机时间" prop="beginning_at">
                <el-date-picker v-model="form.beginning_at" style="width:100%;" type="datetime" placeholder="请选择接机时间" />
            </el-form-item>
            <el-form-item label="考核时间" prop="examine_at">
                <el-date-picker v-model="form.examine_at" style="width:100%;" type="datetime" placeholder="请选择考核时间" />
            </el-form-item>
            <el-form-item label="热试时间" prop="qc_at">
                <el-date-picker v-model="form.qc_at" style="width:100%;" type="datetime" placeholder="请选择热试时间" />
            </el-form-item>
            <el-form-item label="装配时间" prop="assembled_at">
                <el-date-picker v-model="form.assembled_at" style="width:100%;" type="datetime" placeholder="请选择装配时间" />
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
    props: {
        plant: {
            type: Array,
            default: []
        },
        line: {
            type: Array,
            default: []
        },
        status: {
            type: Array,
            default: []
        },
        engine_type: {
            type: Array,
            default: []
        },
    },
    emits: ['success', 'closed'],
    data() {
        return {
            mode: 'add',
            titles: {
                add: '添加发动机',
                edit: '编辑发动机'
            },
            visitable: false,
            loading: false,
            assemblies: [],
            form: {
                id: '',
                line: '',
                plant: '',
                engine: '',
                status: '',
                assembly_id: '',
                number: '',
                beginning_at: '',
                examine_at: '',
                qc_at: '',
                assembled_at: '',
            },
            rules: {
                number: [
                    { required: true, message: '请输入发动机号', trigger: 'blur' }
                ]
            }
        }
    },
    methods: {
        async getAssembly() {
            let res = await this.$axios.get(this.$route('assembly.option'), { plant: this.form.plant, line: this.form.line, type: this.form.engine })
            if (res.code == this.$config.successCode) {
                this.assemblies = res.data
            }
        },
        onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    let method = this.mode == 'add' ? 'post' : 'put'
                    let url = this.mode == 'add' ? this.$route('product.create') : this.$route('product.update', { id: this.form.id })
                    this.loading = true
                    let res = await this.$axios[method](url, this.form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success(`${this.mode == 'add' ? '添加' : '修改'}发动机成功`)
                        this.$emit('success')
                        this.visitable = false
                    } else {
                        this.$message.error(res.message)
                    }
                }
            })
        },
        open(type, item) {
            this.mode = type
            if (item) {
                this.form = {
                    id: item.id,
                    line: item.line,
                    plant: item.plant,
                    engine: item.engine,
                    status: item.status,
                    assembly_id: item.assembly_id,
                    number: item.number,
                    beginning_at: item.beginning_at,
                    examine_at: item.examine_at,
                    qc_at: item.qc_at,
                    assembled_at: item.assembled_at,
                }
                this.getAssembly()
            } else {
                this.form = {
                    id: '',
                    line: '',
                    plant: '',
                    engine: '',
                    status: '',
                    assembly_id: '',
                    number: '',
                    beginning_at: '',
                    examine_at: '',
                    qc_at: '',
                    assembled_at: '',
                }
            }
            this.visitable = true
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