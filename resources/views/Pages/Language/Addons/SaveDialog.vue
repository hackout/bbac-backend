<template>
    <el-dialog :title="titles[mode]" v-model="visitable" width="450px" @closed="$emit('closed')">
        <el-form :model="form" ref="form" :rules="rules" @submit.native.prevent="onSave">
            <el-form-item label="标识代码" prop="code">
                <el-input v-model="form.code" placeholder="请输入标识代码" clearable></el-input>
            </el-form-item>
            <el-form-item label="中文内容" prop="content_zh">
                <el-input type="textarea" v-model="form.content_zh" maxlength="500" show-word-limit placeholder="请输入中文内容"
                    clearable></el-input>
            </el-form-item>
            <el-form-item label="英文内容" prop="content_en">
                <el-input type="textarea" v-model="form.content_en" maxlength="500" show-word-limit placeholder="请输入英文内容"
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
            mode: 'add',
            titles: {
                add: '添加语言',
                edit: '编辑语言'
            },
            visitable: false,
            loading: false,
            form: {
                id: 0,
                code: '',
                content_zh: '',
                content_en: ''
            },
            rules: {
                code: [
                    { required: true, message: '请输入标识代码', trigger: 'blur' }
                ]
            }
        }
    },
    methods: {
        onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    let method = this.mode == 'add' ? 'post' : 'put'
                    let url = this.mode == 'add' ? this.$route('locale_package.create') : this.$route('locale_package.update', { id: this.form.id })
                    this.loading = true
                    let res = await this.$axios[method](url, this.form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success(`${this.mode == 'add' ? '添加' : '修改'}语言成功`)
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
                    code: item.code,
                    content_zh: item.content_zh,
                    content_en: item.content_en
                }
            } else {
                this.form = {
                    id: 0,
                    code: '',
                    content_zh: '',
                    content_en: ''
                }
            }
            this.visitable = true
        }
    }
}
</script>

<style scoped lang="scss"></style>