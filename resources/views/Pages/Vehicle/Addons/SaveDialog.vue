<template>
    <el-dialog title="考核分配" v-model="visitable" width="750px" @closed="$emit('closed')">
        <el-form :model="form" :rules="rules" label-width="80px" ref="form" @submit.native.prevent="onSave">
            <el-form-item label="分配员工" prop="user_id">
                <el-select v-model="form.user_id" style="width:100%;" placeholder="请选择员工" clearable>
                    <el-option v-for="(item, index) in users" :key="index" :label="item.name"
                        :value="item.value"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="分配工时" prop="period">
                <el-input v-model="form.period" placeholder="请输入分配工时" clearable></el-input>
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="visitable = false">取消</el-button>
                <el-button type="primary" v-loading="loading" @click="onSave">提交分配</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script>
export default {
    props: {
        users: {
            type: Array,
            default: []
        }
    },
    emits: ['success', 'closed'],
    data() {
        return {
            assemblies: [],
            visitable: false,
            loading: false,
            form: {
                task_id: '',
                user_id: '',
                period: '',
            },
            rules: {
                user_id: [
                    { required: true, message: '请选择员工', trigger: 'change' }
                ],
                period: [
                    { required: true, message: '请填写分配工时', trigger: 'blur' }
                ]
            }
        }
    },
    methods: {
        onSave() {
            this.$refs.form.validate(async valid => {
                if (valid) {
                    let res = await this.$axios.post(this.$route('vehicle.task_assign'), this.form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success('分配任务成功')
                        this.$emit('success')
                        this.visitable = false
                    } else {
                        this.$message.error(res.message)
                    }
                }
            })
        },
        open(item) {
            this.form = {
                task_id: item.id,
                period: item.period,
                user_id: ''
            }
            this.visitable = true
        }
    }
}
</script>

<style scoped lang="scss"></style>