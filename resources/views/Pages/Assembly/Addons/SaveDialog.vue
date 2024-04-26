<template>
    <el-dialog :title="titles[mode]" v-model="visitable" width="450px" @closed="$emit('closed')">
        <el-form :model="form" :rules="rules" label-width="120px" ref="form" @submit.native.prevent="onSave">
            <el-form-item label="工厂" prop="plant">
                <el-select v-model="form.plant" placeholder="请选择工厂" style="width: 100%" clearable>
                    <el-option v-for="item in plant" :key="item.value" :label="item.name" :value="item.value" />
                </el-select>
            </el-form-item>
            <el-form-item label="生产线" prop="line">
                <el-select v-model="form.line" placeholder="请选择生产线" style="width: 100%" clearable>
                    <el-option v-for="item in assembly_line" :key="item.value" :label="item.name" :value="item.value" />
                </el-select>
            </el-form-item>
            <el-form-item label="机型" prop="type">
                <el-select v-model="form.type" placeholder="请选择机型" style="width: 100%" clearable>
                    <el-option v-for="item in engine_type" :key="item.value" :label="item.name" :value="item.value" />
                </el-select>
            </el-form-item>
            <el-form-item label="项目阶段" prop="status">
                <el-select v-model="form.status" placeholder="请选择项目阶段" style="width: 100%" clearable>
                    <el-option v-for="item in assembly_status" :key="item.value" :label="item.name"
                        :value="item.value" />
                </el-select>
            </el-form-item>
            <el-form-item label="总成号" prop="number">
                <el-input v-model="form.number" placeholder="请输入总成号" clearable></el-input>
            </el-form-item>
            <el-form-item label="备注信息" prop="remark">
                <el-input type="textarea" v-model="form.remark" maxlength="250" show-word-limit placeholder="请输入备注信息" clearable></el-input>
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
        assembly_status: {
            type: Array,
            default: []
        },
        assembly_line: {
            type: Array,
            default: []
        },
        engine_type: {
            type: Array,
            default: []
        },
        plant: {
            type: Array,
            default: []
        },
    },
    emits: ['success', 'closed'],
    data() {
        return {
            mode: 'add',
            titles: {
                add: '添加新的总成',
                edit: '编辑总成信息'
            },
            visitable: false,
            loading: false,
            form: {
                id: 0,
                type: '',
                plant: '',
                line: '',
                status: '',
                number: '',
                remark: '',
            },
            rules: {
                type: [
                    { required: true, message: '请选择机型', trigger: 'change' }
                ],
                plant: [
                    { required: true, message: '请选择工厂', trigger: 'change' }
                ],
                line: [
                    { required: true, message: '请选择生产线', trigger: 'change' }
                ],
                status: [
                    { required: true, message: '请选择项目阶段', trigger: 'change' }
                ],
                number: [
                    { required: true, message: '请输入总成号', trigger: 'blur' }
                ]
            }
        }
    },
    methods: {
        onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    let method = this.mode == 'add' ? 'post' : 'put'
                    let url = this.mode == 'add' ? this.$route('assembly.create') : this.$route('assembly.update', { id: this.form.id })
                    this.loading = true
                    let res = await this.$axios[method](url, this.form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success(`${this.mode == 'add' ? '添加' : '修改'}总成信息成功`)
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
                    type: item.type,
                    plant: item.plant,
                    line: item.line,
                    status: item.status,
                    number: item.number,
                    remark: item.remark,
                }
            } else {
                this.form = {
                    id: 0,
                    type: '',
                    plant: '',
                    line: '',
                    status: '',
                    number: '',
                    remark: ''
                }
            }
            this.visitable = true
        }
    }
}
</script>

<style scoped lang="scss"></style>