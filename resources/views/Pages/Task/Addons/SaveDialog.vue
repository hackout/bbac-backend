<template>
    <el-dialog title="新增考核单" v-model="visitable" width="850px" @closed="$emit('closed')">
        <el-form :model="form" ref="form" label-position="top" :rules="rules" v-loading="loading" label-width="120px"
            @submit.native.prevent="onSave">

            <el-row :gutter="20">
                <el-col :span="24">
                    <el-form-item label="考核类型" prop="type">
                        <el-select style="width:100%" v-model="form.type" @change="changeType" placeholder="请选择考核类型"
                            clearable>
                            <el-option v-for="(item, index) in examine_type" :key="index" :value="item.value"
                                :label="item.name"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="24">
                    <el-form-item label="考核单模板" prop="examine_id">
                        <el-select style="width:100%" v-model="form.examine_id" @change="changeExamine"
                            placeholder="请选择考核单模板" clearable>
                            <el-option v-for="(item, index) in examines" :key="index" :value="item.value"
                                :label="`[${$status('engine_type', item.engine)}-${item.period}工时]${item.name}(V${item.version})`"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="24">
                    <el-form-item label="考核名称" prop="name">
                        <el-input v-model="form.name" placeholder="请输入考核名称" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="4">
                    <el-form-item label="工厂" prop="plant">
                        <el-select style="width:100%" v-model="form.plant" @change="getAssembly" placeholder="请选择工厂"
                            clearable>
                            <el-option v-for="(item, index) in plant" :key="index" :value="item.value"
                                :label="item.name"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="4">
                    <el-form-item label="产线" prop="line">
                        <el-select style="width:100%" :disabled="form.plant == ''" @change="getAssembly"
                            v-model="form.line" placeholder="请选择产线" clearable>
                            <el-option v-for="(item, index) in line" :key="index" :value="item.value"
                                :label="item.name"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="4">
                    <el-form-item label="发动机机型" prop="engine">
                        <el-select style="width:100%" :disabled="form.line == ''" @change="getAssembly"
                            v-model="form.engine" placeholder="请选择发动机机型" clearable>
                            <el-option v-for="(item, index) in engine_type" :key="index" :value="item.value"  :label="item.name"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="总成号" prop="assembly_id">
                        <el-select style="width:100%" :disabled="form.engine == ''" v-model="form.assembly_id"
                            placeholder="请选择总成号" clearable>
                            <el-option :key="index" v-for="(item, index) in assemblies" :value="item.value"
                                :label="item.name"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="4">
                    <el-form-item label="项目阶段" prop="status">
                        <el-select style="width:100%" :disabled="form.assembly == ''" v-model="form.status"
                            placeholder="请选择项目阶段" clearable>
                            <el-option v-for="(item, index) in status" :key="index" :value="item.value"
                                :label="item.name"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="24">
                    <el-form-item label="标准工时" prop="period">
                        <el-input type="number" step="0.1" v-model="form.period" placeholder="标准工时"
                            clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="24">
                    <el-form-item label="生成单数" prop="number">
                        <el-input type="number" v-model="form.number" placeholder="生成单数" clearable></el-input>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="visitable = false">取消</el-button>
                <el-button type="primary" v-loading="saving" @click="onSave">确定</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script>
export default {
    props: {
        examine_type: {
            type: Array,
            default: []
        },
        status: {
            type: Array,
            default: []
        },
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
        task_status: {
            type: Array,
            default: []
        },
    },
    emits: ['success', 'closed'],
    data() {
        return {
            visitable: false,
            loading: false,
            saving: false,
            form: {
                name: '',
                examine_id: '',
                type: '',
                plant: '',
                line: '',
                engine: '',
                status: '',
                assembly_id: '',
                period: '',
                number: ''
            },
            examines: [],
            assemblies: [],
            chooseItem: null,
            rules: {
                type: [
                    { required: true, message: '请选择考核类型', trigger: 'change' }
                ],
                name: [
                    { required: true, message: '请输入考核名称', trigger: 'blur' }
                ],
                number: [
                    { required: true, message: '请输入生成单数', trigger: 'blur' }
                ],
                period: [
                    { required: true, message: '请输入标准工时', trigger: 'blur' }
                ],
                plant: [
                    { required: true, message: '请选择工厂', trigger: 'change' }
                ],
                line: [
                    { required: true, message: '请选择产线', trigger: 'change' }
                ],
                engine: [
                    { required: true, message: '请选择发动机机型', trigger: 'change' }
                ],
                status: [
                    { required: true, message: '请选择阶段状态', trigger: 'change' }
                ],
                assembly_id: [
                    { required: true, message: '请选择总成号', trigger: 'change' }
                ],
                examine_id: [
                    { required: true, message: '请选择考核单模板', trigger: 'change' }
                ]
            },
            typeList: {
                1: 'inline',
                2: 'product',
                3: 'vehicle'
            }
        }
    },
    methods: {
        changeExamine(value) {
            let res = this.examines.filter(n => n.value == value)
            if (res.length > 0) {
                this.form.period = res[0].period
                this.form.engine = res[0].engine
                this.form.name = res[0].name
            }
        },
        async changeType() {
            const res = await this.$axios.get(this.$route('examine.option',{ type: this.typeList[this.form.type] }))
            if (res.code == this.$config.successCode) {
                this.examines = res.data
            } else {
                this.examines = []
            }
        },
        async getAssembly() {
            const res = await this.$axios.get(this.$route('assembly.option'), { plant: this.form.plant, line: this.form.line, engine: this.form.engine })
            if (res.code == this.$config.successCode) {
                this.assemblies = res.data
            } else {
                this.assemblies = []
            }
        },
        onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    this.saving = true
                    let res = await this.$axios.post(this.$route('task.create'), this.form)
                    this.saving = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success(`新增生产任务成功`)
                        this.$emit('success')
                        this.visitable = false
                    } else {
                        this.$message.error(res.message)
                    }
                }
            })
        },
        open() {
            this.form = {
                name: '',
                examine_id: '',
                type: '',
                plant: '',
                line: '',
                engine: '',
                status: '',
                assembly_id: '',
                period: '',
                number: ''
            }
            this.visitable = true
        },
        changeTemplate(val) {
            let examine = val.split('-')
            let item = this.options.filter(n => n.type == examine[0] && n.id == examine[1])
            if (item.length > 0) {
                this.chooseItem = item[0]
                this.form.name = this.chooseItem.name
                this.form.period = this.chooseItem.period
                this.form.engine = ''
                this.form.assembly = ''
                this.form.status = ''
            }
        },
        async getOptions() {
            this.loading = true
            var res = await this.$axios.get(this.$route('task.template'))
            this.loading = false
            if (res.code == this.$config.successCode) {
                this.options = res.data
            } else {
                this.$message.error(res.message)
                this.$emit('closed')
            }
        }
    }
}
</script>

<style scoped lang="scss"></style>