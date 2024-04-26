<template>
    <el-dialog :title="$status('work_type', form.type)" v-model="visitable" width="800px" @closed="$emit('closed')">
        <el-form :model="form" ref="form" label-position="top" :rules="rules" v-loading="loading" label-width="120px"
            @submit.native.prevent="onSave">
            <el-form-item label="考核单" :rules="[{ required: true, trigger: 'change', message: '请选择考核单' }]"
                v-if="form.type == 1 || form.type == 2" prop="task_id">
                <el-select style="width:100%" v-model="form.task_id" @change="changeTemplate" placeholder="请选择考核单"
                    clearable filterable>
                    <el-option v-for="(item, index) in options" :key="index" :value="item.value"
                        :label="`[${$status('examine_type', item.type)}]${item.name}(${item.number})-(工时:${item.period})`">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="事由理由" v-else-if="form.type == 6 || form.type == 7" prop="content">
                <el-input type="textarea" maxlength="200" show-word-limit v-model="form.content" placeholder="请输入事由理由"
                    clearable></el-input>
            </el-form-item>
            <el-form-item label="工作安排" v-else prop="content">
                <el-input type="textarea" maxlength="200" show-word-limit v-model="form.content" placeholder="请输入工作安排"
                    clearable></el-input>
            </el-form-item>
            <el-form-item label="分配工时" prop="period">
                <el-input type="number" step="0.1" v-model="form.period" placeholder="分配工时" clearable></el-input>
            </el-form-item>
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
        work_type: {
            type: Array,
            default: []
        },
        examine_type: {
            type: Array,
            default: []
        }
    },
    emits: ['success', 'closed'],
    data() {
        return {
            visitable: false,
            loading: false,
            saving: false,
            options: [],
            form: {
                user_id: '',
                task_id: '',
                content: '',
                type: '',
                period: '',
                date: ''
            },
            rules: {
                content: [
                    { required: true, message: '请输入工作安排', trigger: 'blur' }
                ],
                period: [
                    { required: true, message: '请输入分配工时', trigger: 'blur' }
                ]
            }
        }
    },
    methods: {
        onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    this.saving = true
                    let res = await this.$axios.post(this.$route('work.create'), this.form)
                    this.saving = false
                    if (res.code == this.$config.successCode) {
                        if (this.form.type == 6) {
                            this.$message.success('设置加班工时成功')
                        } else
                            if (this.form.type == 7) {
                                this.$message.success('设置休假工时成功')
                            } else {
                                this.$message.success(`分配任务成功`)
                            }
                        this.$emit('success')
                        this.visitable = false
                    } else {
                        this.$message.error(res.message)
                    }
                }
            })
        },
        open(type, date, user_id) {
            this.form = {
                user_id: user_id,
                task_id: '',
                content: '',
                type: type,
                period: '',
                date: date
            }
            if (type == 1 || type == 2) {
                this.getOptions()
            }
            this.visitable = true
        },
        changeTemplate(val) {
            let item = this.options.filter(n => n.value == val)
            if (item.length > 0) {
                this.form.content = item[0].name
                this.form.period = item[0].period
            }
        },
        async getOptions() {
            this.loading = true
            var res = await this.$axios.get(this.$route('task.option'))
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