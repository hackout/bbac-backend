<template>
    <el-dialog :title="titles[mode]" v-model="visitable" width="450px" @closed="$emit('closed')">
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

            <el-form-item label="机型" prop="type">
                <el-select v-model="form.type" style="width:100%;" placeholder="请选择机型" @change="getAssembly"
                    clearable>
                    <el-option v-for="(item, index) in engine_type" :key="index" :label="item.name"
                        :value="item.value"></el-option>
                </el-select>
            </el-form-item>

            <el-form-item label="总成号" prop="assembly_id">
                <el-select v-model="form.assembly_id" style="width:100%;" placeholder="请选择总成号" clearable>
                    <el-option v-for="(item, index) in assemblies" :key="index" :label="item.name"
                        :value="item.value"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="计划产量" prop="quantity">
                <el-input v-model="form.quantity" placeholder="请输入计划产量" clearable></el-input>
            </el-form-item>
            <el-form-item label="计划时间" prop="plan_at">
                <el-date-picker v-model="form.plan_at" style="width:100%;" type="date" placeholder="请选择计划时间" clearable />
            </el-form-item>
            <el-form-item label="备注信息" prop="remark">
                <el-input type="textarea" v-model="form.remark" maxlength="200" show-word-limit placeholder="请输入备注信息"
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
    props: {
        plant: {
            type: Array,
            default: []
        },
        line: {
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
                add: '添加排产计划',
                edit: '编辑排产计划'
            },
            visitable: false,
            loading: false,
            assemblies: [],
            form: {
                id: 0,
                assembly_id: '',
                plant: '',
                line: '',
                type: '',
                quantity: '',
                plan_at: '',
                remark: ''
            },
            rules: {
                assembly_id: [
                    { required: true, message: '请选择总成号', trigger: 'change' }
                ],
                plant: [
                    { required: true, message: '请选择工厂', trigger: 'change' }
                ],
                line: [
                    { required: true, message: '请选择产线', trigger: 'change' }
                ],
                type: [
                    { required: true, message: '请选择机型', trigger: 'change' }
                ],
                quantity: [
                    { required: true, message: '请输入计划产量', trigger: 'blur' }
                ],
                plan_at: [
                    { required: true, message: '请选择计划时间', trigger: 'change' }
                ],
            }
        }
    },
    methods: {
        onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    let method = this.mode == 'add' ? 'post' : 'put'
                    let url = this.mode == 'add' ? this.$route('plan.create') : this.$route('plan.update', { id: this.form.id })
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
        open(type, item) {
            this.mode = type
            if (item) {
                this.form = {
                    id: item.id,
                    assembly_id: item.assembly_id,
                    plant: item.plant,
                    line: item.line,
                    type: item.type,
                    quantity: item.quantity,
                    plan_at: item.plan_at,
                    remark: item.remark,
                }
            } else {
                this.form = {
                    id: 0,
                    assembly_id: '',
                    plant: '',
                    line: '',
                    type: '',
                    quantity: '',
                    plan_at: '',
                    remark: ''
                }
            }
            this.getAssembly()
            this.visitable = true
        },
        async getAssembly() {
            let res = await this.$axios.get(this.$route('assembly.option'), { plant: this.form.plant, line: this.form.line, type: this.form.type })
            if (res.code == this.$config.successCode) {
                this.assemblies = res.data
            }
        },
    }
}
</script>

<style scoped lang="scss">
</style>