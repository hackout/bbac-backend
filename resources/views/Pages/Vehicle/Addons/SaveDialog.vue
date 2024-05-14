<template>
    <el-dialog title="考核维护" v-model="visitable" width="750px" @closed="$emit('closed')">
        <el-form :model="form" :rules="rules" label-width="80px" ref="form" @submit.native.prevent="onSave">
            <el-row :gutter="20">

                <el-col :span="24">
                    <el-divider content-position="left">目录项</el-divider>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="工厂" prop="plant">
                        <el-select v-model="form.plant" style="width:100%;" placeholder="请选择工厂" @change="getAssembly"
                            clearable>
                            <el-option v-for="(item, index) in plant" :key="index" :label="item.name"
                                :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="产线" prop="line">
                        <el-select v-model="form.line" style="width:100%;" placeholder="请选择产线" @change="getAssembly"
                            clearable>
                            <el-option v-for="(item, index) in line" :key="index" :label="item.name"
                                :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="机型" prop="engine">
                        <el-select v-model="form.engine" style="width:100%;" placeholder="请选择机型" @change="getAssembly"
                            clearable>
                            <el-option v-for="(item, index) in engine_type" :key="index" :label="item.name"
                                :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="24">
                    <el-form-item label="总成号" prop="assembly_id">
                        <el-select v-model="form.assembly_id" style="width:100%;" placeholder="请选择总成号" clearable>
                            <el-option v-for="(item, index) in assemblies" :key="index" :label="item.name"
                                :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="24">
                    <el-divider content-position="left">螺栓信息</el-divider>
                </el-col>
                <el-col :span="16">
                    <el-form-item label="螺栓编号" prop="number">
                        <el-input v-model="form.number" placeholder="请输入螺栓编号" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="车型" prop="vehicle_type">
                        <el-select v-model="form.vehicle_type" style="width:100%;" placeholder="请选择车型" clearable>
                            <el-option v-for="(item, index) in motorcycle_type" :key="index" :label="item.name"
                                :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="中文描述" prop="content_zh">
                        <el-input type="textarea" maxlength="200" show-word-limit v-model="form.content_zh"
                            placeholder="请输入中文描述" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="英文描述" prop="content_en">
                        <el-input type="textarea" maxlength="200" show-word-limit v-model="form.content_en"
                            placeholder="请输入英文描述" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="螺栓数量" prop="quantity">
                        <el-input type="number" v-model="form.quantity" placeholder="请输入螺栓数量" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="分类1" prop="model">
                        <el-select v-model="form.model" style="width:100%;" placeholder="请选择分类1" clearable>
                            <el-option v-for="(item, index) in model" :key="index" :label="item.name"
                                :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="分类2" prop="type">
                        <el-select v-model="form.type" style="width:100%;" placeholder="请选择分类2" clearable>
                            <el-option v-for="(item, index) in type" :key="index" :label="item.name"
                                :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="放行状态" prop="status">
                        <el-select v-model="form.status" style="width:100%;" placeholder="请选择放行状态" clearable>
                            <el-option v-for="(item, index) in status" :key="index" :label="item.name"
                                :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="项目阶段" prop="stage">
                        <el-select v-model="form.stage" style="width:100%;" placeholder="请选择项目阶段" clearable>
                            <el-option v-for="(item, index) in stage" :key="index" :label="item.name"
                                :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="特殊特性" prop="special">
                        <el-select v-model="form.special" style="width:100%;" placeholder="请选择特殊特性" clearable>
                            <el-option v-for="(item, index) in special" :key="index" :label="item.name"
                                :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="工位1" prop="station">
                        <el-input v-model="form.station" placeholder="请输入工位1" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="工位2" prop="sub_station">
                        <el-input v-model="form.sub_station" placeholder="请输入工位2" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="螺栓参数" prop="param">
                        <el-input v-model="form.param" placeholder="请输入螺栓参数" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="目标扭矩" prop="torque_target">
                        <el-input v-model="form.torque_target" placeholder="请输入目标扭矩" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="扭矩下限" prop="torque_lower">
                        <el-input v-model="form.torque_lower" placeholder="请输入扭矩下限" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="扭矩上限" prop="torque_upper">
                        <el-input v-model="form.torque_upper" placeholder="请输入扭矩上限" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="角度标准" prop="angle_target">
                        <el-input v-model="form.angle_target" placeholder="请输入角度标准" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="角度下限" prop="angle_lower">
                        <el-input v-model="form.angle_lower" placeholder="请输入角度下限" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="角度上限" prop="angle_upper">
                        <el-input v-model="form.angle_upper" placeholder="请输入角度上限" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="上次放行" prop="lasted_at">
                        <el-date-picker v-model="form.lasted_at" type="date" placeholder="请输入上一次放行时间" clearable />
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="预计放行" prop="expected_at">
                        <el-date-picker v-model="form.expected_at" type="date" placeholder="请输入预计放行时间" clearable />
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="最终放行" prop="final_at">
                        <el-date-picker v-model="form.final_at" type="date" placeholder="请输入最终放行时间" clearable />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="起始扭矩" prop="start_torque">
                        <el-input v-model="form.start_torque" placeholder="请输入起始扭矩" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="转矩角" prop="residual_torque">
                        <el-input v-model="form.residual_torque" placeholder="请输入转矩角" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="24">
                    <el-divider content-position="left">PFU 信息参数</el-divider>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="测试值" prop="pfu_test">
                        <el-input v-model="form.pfu_test" placeholder="请输入测试值" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="考核下限" prop="pfu_lower">
                        <el-input v-model="form.pfu_lower" placeholder="请输入考核下限" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="考核上限" prop="pfu_upper">
                        <el-input v-model="form.pfu_upper" placeholder="请输入考核上限" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="预警上限" prop="pfu_early_lower">
                        <el-input v-model="form.pfu_early_lower" placeholder="请输入预警上限" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="预警下限" prop="pfu_early_upper">
                        <el-input v-model="form.pfu_early_upper" placeholder="请输入预警下限" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="24">
                    <el-divider content-position="left">L-PFU 信息参数</el-divider>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="测试值" prop="l_pfu_test">
                        <el-input v-model="form.l_pfu_test" placeholder="请输入测试值" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="考核下限" prop="l_pfu_lower">
                        <el-input v-model="form.l_pfu_lower" placeholder="请输入考核下限" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="考核上限" prop="l_pfu_upper">
                        <el-input v-model="form.l_pfu_upper" placeholder="请输入考核上限" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="预警上限" prop="l_pfu_early_lower">
                        <el-input v-model="form.l_pfu_early_lower" placeholder="请输入预警上限" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="预警下限" prop="l_pfu_early_upper">
                        <el-input v-model="form.l_pfu_early_upper" placeholder="请输入预警下限" clearable></el-input>
                    </el-form-item>
                </el-col>
            </el-row>
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
        motorcycle_type: {
            type: Array,
            default: []
        },
        model: {
            type: Array,
            default: []
        },
        type: {
            type: Array,
            default: []
        },
        status: {
            type: Array,
            default: []
        },
        stage: {
            type: Array,
            default: []
        },
        special: {
            type: Array,
            default: []
        },
    },
    emits: ['success', 'closed'],
    data() {
        return {
            assemblies: [],
            visitable: false,
            loading: false,
            form: {
                id: 0,
                plant: '',
                line: '',
                engine: '',
                vehicle_type: '',
                assembly_id: '',
                number: '',
                content_zh: '',
                content_en: '',
                quantity: '',
                model: '',
                type: '',
                status: '',
                stage: '',
                station: '',
                sub_station: '',
                special: '',
                param: '',
                torque_target: '',
                torque_lower: '',
                torque_upper: '',
                angle_target: '',
                angle_lower: '',
                angle_upper: '',
                lasted_at: '',
                expected_at: '',
                final_at: '',
                start_torque: '',
                residual_torque: '',
                pfu_test: '',
                pfu_lower: '',
                pfu_upper: '',
                pfu_early_lower: '',
                pfu_early_upper: '',
                l_pfu_test: '',
                l_pfu_lower: '',
                l_pfu_upper: '',
                l_pfu_early_lower: '',
                l_pfu_early_upper: '',
                is_io: 0
            },
            rules: {
                plant: [
                    { required: true, message: '请选择工厂', trigger: 'change' }
                ],
                line: [
                    { required: true, message: '请选择产线', trigger: 'change' }
                ],
                engine: [
                    { required: true, message: '请选择机型', trigger: 'change' }
                ],
                vehicle_type: [
                    { required: true, message: '请选择车型', trigger: 'change' }
                ],
                assembly_id: [
                    { required: true, message: '请选择总成号', trigger: 'change' }
                ],
                number: [
                    { required: true, message: '请填写螺栓编号', trigger: 'blur' }
                ],
                content_zh: [
                    { required: true, message: '请填写中文描述', trigger: 'blur' }
                ],
                content_en: [
                    { required: true, message: '请填写英文描述', trigger: 'blur' }
                ],
                quantity: [
                    { required: true, message: '请输入螺栓数量', trigger: 'blur' }
                ],
                model: [
                    { required: true, message: '请选择分类1', trigger: 'change' }
                ],
                type: [
                    { required: true, message: '请选择分类2', trigger: 'change' }
                ],
                status: [
                    { required: true, message: '请选择开放状态', trigger: 'change' }
                ],
                stage: [
                    { required: true, message: '请选择项目阶段', trigger: 'change' }
                ],
                station: [
                    { required: true, message: '请填写工位', trigger: 'blur' }
                ],
                sub_station: [
                    { required: true, message: '请填写工位2', trigger: 'blur' }
                ],
                special: [
                    { required: true, message: '请选择特殊特性', trigger: 'change' }
                ],
                param: [
                    { required: true, message: '请填写参数', trigger: 'blur' }
                ],
                torque_target: [
                    { required: true, message: '请填写目标扭矩', trigger: 'blur' }
                ],
                torque_lower: [
                    { required: true, message: '请填写扭矩下限', trigger: 'blur' }
                ],
                torque_upper: [
                    { required: true, message: '请填写扭矩上限', trigger: 'blur' }
                ],
                angle_target: [
                    { required: true, message: '请填写角度标准', trigger: 'blur' }
                ],
                angle_lower: [
                    { required: true, message: '请填写角度下限', trigger: 'blur' }
                ],
                angle_upper: [
                    { required: true, message: '请填写角度上限', trigger: 'blur' }
                ],
                lasted_at: [
                    { required: true, message: '请填写最近放行时间', trigger: 'change' }
                ],
                expected_at: [
                    { required: true, message: '请填写预计放行时间', trigger: 'change' }
                ],
                final_at: [
                    { required: true, message: '请填写最终放行时间', trigger: 'change' }
                ],
                start_torque: [
                    { required: true, message: '请填写起始扭矩', trigger: 'blur' }
                ],
                residual_torque: [
                    { required: true, message: '请填写转矩角', trigger: 'blur' }
                ],
                pfu_test: [
                    { required: true, message: '请填写PFU测试值', trigger: 'blur' }
                ],
                pfu_lower: [
                    { required: true, message: '请填写PFU考核下限', trigger: 'blur' }
                ],
                pfu_upper: [
                    { required: true, message: '请填写PFU考核上限', trigger: 'blur' }
                ],
                pfu_early_lower: [
                    { required: true, message: '请填写PFU预警上限', trigger: 'blur' }
                ],
                pfu_early_upper: [
                    { required: true, message: '请填写PFU预警下限', trigger: 'blur' }
                ],
                l_pfu_test: [
                    { required: true, message: '请填写L-PFU测试值', trigger: 'blur' }
                ],
                l_pfu_lower: [
                    { required: true, message: '请填写L-PFU考核下限', trigger: 'blur' }
                ],
                l_pfu_upper: [
                    { required: true, message: '请填写L-PFU考核上限', trigger: 'blur' }
                ],
                l_pfu_early_lower: [
                    { required: true, message: '请填写L-PFU预警上限', trigger: 'blur' }
                ],
                l_pfu_early_upper: [
                    { required: true, message: '请填写L-PFU预警下限', trigger: 'blur' }
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
            this.$refs.form.validate(valid => {
                if (valid) {
                    this.form.is_io = 1;
                    this.$confirm("变更涉及IO,需要向上级领导审批?审批通过后生效", "IO变更").then(async () => {
                        this.loading = true
                        let res = await this.$axios.put(this.$route('torque.update', { id: this.form.id }), this.form)
                        this.loading = false
                        if (res.code == this.$config.successCode) {
                            this.$message.success('变更信息提交成功')
                            this.$emit('success')
                            this.visitable = false
                        } else {
                            this.$message.error(res.message)
                        }
                    }).catch(() => { })
                }
            })
        },
        open(item) {
            this.form = {
                id: item.id,
                plant: item.plant,
                line: item.line,
                engine: item.engine,
                vehicle_type: item.vehicle_type,
                assembly_id: item.assembly_id,
                number: item.number,
                content_zh: item.content_zh,
                content_en: item.content_en,
                quantity: item.quantity,
                model: item.model,
                type: item.type,
                status: item.status,
                stage: item.stage,
                station: item.station,
                sub_station: item.sub_station,
                special: item.special,
                param: item.param,
                torque_target: item.torque_target,
                torque_lower: item.torque_lower,
                torque_upper: item.torque_upper,
                angle_target: item.angle_target,
                angle_lower: item.angle_lower,
                angle_upper: item.angle_upper,
                lasted_at: item.lasted_at,
                expected_at: item.expected_at,
                final_at: item.final_at,
                start_torque: item.start_torque,
                residual_torque: item.residual_torque,
                pfu_test: item.pfu_test,
                pfu_lower: item.pfu_lower,
                pfu_upper: item.pfu_upper,
                pfu_early_lower: item.pfu_early_lower,
                pfu_early_upper: item.pfu_early_upper,
                l_pfu_test: item.l_pfu_test,
                l_pfu_lower: item.l_pfu_lower,
                l_pfu_upper: item.l_pfu_upper,
                l_pfu_early_lower: item.l_pfu_early_lower,
                l_pfu_early_upper: item.l_pfu_early_upper,
                is_io: 0
            }
            this.getAssembly()
            this.visitable = true
        }
    }
}
</script>

<style scoped lang="scss"></style>