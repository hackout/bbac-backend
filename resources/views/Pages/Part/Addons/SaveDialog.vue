<template>
    <el-dialog :title="titles[mode]" v-model="visitable" width="620px" @closed="$emit('closed')">
        <el-form :model="form" ref="form" :rules="rules" label-width="100" @submit.native.prevent="onSave">

            <el-form-item label="零件名称" prop="name">
                <el-input v-model="form.name" placeholder="请输入零件名称" clearable></el-input>
            </el-form-item>
            <el-form-item label="零件名称(英)" prop="name_en">
                <el-input v-model="form.name_en" placeholder="请输入零件名称(英)" clearable></el-input>
            </el-form-item>
            <el-form-item label="工位" prop="station">
                <el-input v-model="form.station" placeholder="请输入工位" clearable></el-input>
            </el-form-item>
            <el-form-item label="零件号" prop="number">
                <el-input v-model="form.number" placeholder="请输入零件号" clearable></el-input>
            </el-form-item>
            <el-form-item label="ESD" prop="is_esd">
                <el-switch v-model="form.is_esd" active-text="YES" inactive-text="NO" :active-value="1"
                    :inactive-value="0"></el-switch>
            </el-form-item>
            <el-form-item label="追踪件" prop="is_traceability">
                <el-switch v-model="form.is_traceability" active-text="YES" inactive-text="NO" :active-value="1"
                    :inactive-value="0"></el-switch>
            </el-form-item>
            <el-form-item label="一次性零件" prop="is_one_time">
                <el-switch v-model="form.is_one_time" active-text="YES" inactive-text="NO" :active-value="1"
                    :inactive-value="0"></el-switch>
            </el-form-item>
            <el-form-item label="应用总成" class="DataTable" prop="assemblies">
                <el-table size="small" :data="form.assemblies" border>
                    <el-table-column label="总成号">
                        <template #default="scope">
                            <el-select size="small" v-model="form.assemblies[scope.$index].id" style="width:100%;"
                                placeholder="总成号" clearable filterable>
                                <el-option v-for="(item, index) in assemblies" :key="index" :label="item.name"
                                    :value="item.value"></el-option>
                            </el-select>
                        </template>
                    </el-table-column>
                    <el-table-column label="数量" align="center" width="100">
                        <template #default="scope">
                            <el-input size="small" type="number" v-model="form.assemblies[scope.$index].num"
                                placeholder="数量"></el-input>
                        </template>
                    </el-table-column>
                    <el-table-column label="操作" width="100" align="right">
                        <template #header>
                            <el-button link type="primary" size="small" @click="addItem">增加</el-button>
                        </template>
                        <template #default="scope">
                            <el-button link type="danger" size="small" @click="deleteItem(scope.$index)">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>
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
        assemblies: {
            type: Array,
            default: []
        }
    },
    emits: ['success', 'closed'],
    data() {
        return {
            mode: 'add',
            titles: {
                add: '添加零件',
                edit: '编辑零件'
            },
            visitable: false,
            loading: false,
            form: {
                id: '',
                name: '',
                name_en: '',
                station: '',
                number: '',
                is_esd: 0,
                is_traceability: 0,
                is_one_time: 0,
                assemblies: []
            },
            tempData: {
                id: '',
                num: 1
            },
            rules: {
                name: [
                    { required: true, message: '请输入零件名称', trigger: 'blur' }
                ],
                number: [
                    { required: true, message: '请输入零件号', trigger: 'blur' }
                ]
            }
        }
    },
    methods: {
        onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    let method = this.mode == 'add' ? 'post' : 'put'
                    let url = this.mode == 'add' ? this.$route('part.create') : this.$route('part.update', { id: this.form.id })
                    this.loading = true
                    let res = await this.$axios[method](url, this.form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success(`${this.mode == 'add' ? '添加' : '修改'}排产计划成功`)
                        this.$emit('success')
                        this.visitable = false
                    } else {
                        this.$message.error(res.message)
                    }
                }
            })
        },
        addItem() {
            this.form.assemblies.push(this.$tool.objCopy(this.tempData))
        },
        deleteItem(index) {
            this.form.assemblies = this.form.assemblies.filter((n, i) => i != index);
        },
        open(type, item) {
            this.mode = type
            if (item) {
                this.form = {
                    id: item.id,
                    name: item.name,
                    name_en: item.name_en,
                    station: item.station,
                    number: item.number,
                    is_esd: item.is_esd ? 1 : 0,
                    is_traceability: item.is_traceability ? 1 : 0,
                    is_one_time: item.is_one_time ? 1 : 0,
                    assemblies: item.assemblies ?? []
                }
            } else {
                this.form = {
                    id: '',
                    name: '',
                    name_en: '',
                    station: '',
                    number: '',
                    is_esd: 0,
                    is_traceability: 0,
                    is_one_time: 0,
                    assemblies: []
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