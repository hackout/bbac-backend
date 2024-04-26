<template>
    <el-dialog v-model="visitable" destroy-on-close @closed="$emit('closed')" :title="titles[mode]" width="500">
        <el-form :model="form" ref="form" label-width="100px" :rules="rules">
            <el-form-item label="字典项名" prop="name">
                <el-input v-model="form.name" placeholder="请输入字典项名称" autocomplete="off" />
            </el-form-item>
            <el-form-item label="字典标识" prop="code">
                <el-input v-model="form.code" placeholder="请输入字典标识" :disabled="mode == 'edit'" autocomplete="off" />
            </el-form-item>
            <el-form-item label="字典备注" prop="description">
                <el-input type="textarea" v-model="form.description" maxlength="100" show-word-limit
                    placeholder="请输入字典备注" autocomplete="off" />
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="visitable = false">取消</el-button>
                <el-button type="primary" v-loading="loading" @click="onSubmit">提交保存</el-button>
            </div>
        </template>
    </el-dialog>
</template>
<script>
export default {
    emits: ['success', 'closed'],
    data() {
        return {
            mode: 'add',
            titles: {
                add: '添加字典',
                edit: '编辑字典'
            },
            visitable: false,
            form: {
                id: 0,
                name: '',
                code: '',
                description: ''
            },
            rules: {
                name: [
                    { required: true, message: '请输入字典名称', trigger: 'blur' }
                ],
                code: [
                    { required: true, message: '请输入字典标识', trigger: 'blur' }
                ]
            },
            loading: false
        }
    },
    methods: {
        open(mode, item) {
            this.mode = mode
            if (item) {
                this.form = {
                    id: item.id,
                    name: item.name,
                    code: item.code,
                    description: item.description
                }
            } else {
                this.form = {
                    id: 0,
                    name: '',
                    code: '',
                    description: ''
                }
            }
            this.visitable = true
        },
        onSubmit() {
            this.$refs.form.validate().then(async (valid) => {
                if (valid) {
                    this.loading = true
                    let method = this.mode == 'add' ? 'post' : 'put'
                    let url = this.mode == 'add' ? this.$route('dict.create') : this.$route('dict.update', { id: this.form.id })
                    var res = await this.$axios[method](url, this.form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success(`${this.mode == 'add' ? '添加' : '编辑'}字典成功`)
                        this.$emit('success')
                        this.visitable = false
                    } else {
                        this.$message.error(res.message)
                    }
                }
            })
        }
    }
}
</script>

<style lang="scss" scoped></style>