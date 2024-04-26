<template>
    <el-dialog v-model="visitable" destroy-on-close @closed="$emit('closed')" :title="titles[mode]" width="500">
        <el-form :model="form" ref="form" label-width="100px" :rules="rules">
            <el-form-item label="字典标签" prop="name">
                <el-input v-model="form.name" placeholder="请输入字典标签" autocomplete="off" />
            </el-form-item>
            <el-form-item label="字典键值" prop="content">
                <el-input type="number" v-model="form.content" placeholder="请输入字典键值" autocomplete="off" />
            </el-form-item>
            <el-form-item label="字典排序" prop="sort_order">
                <el-input type="number" v-model="form.sort_order" placeholder="请输入字典排序" autocomplete="off" />
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
                add: '添加字典项',
                edit: '编辑字典项'
            },
            visitable: false,
            code: '',
            form: {
                id: 0,
                name: '',
                content: '',
                sort_order: 0,
            },
            rules: {
                name: [
                    { required: true, message: '请输入字典标签', trigger: 'blur' }
                ],
                content: [
                    { required: true, message: '请输入字典键值', trigger: 'blur' }
                ]
            },
            loading: false
        }
    },
    methods: {
        open(mode, code, item) {
            this.code = code
            this.mode = mode
            if (item) {
                this.form = {
                    id: item.id,
                    name: item.name,
                    content: item.content,
                    sort_order: item.sort_order
                }
            } else {
                this.form = {
                    id: 0,
                    name: '',
                    content: '',
                    sort_order: 0
                }
            }
            this.visitable = true
        },
        onSubmit() {
            this.$refs.form.validate().then(async (valid) => {
                if (valid) {
                    this.loading = true
                    let method = this.mode == 'add' ? 'post' : 'put'
                    let url = this.mode == 'add' ? this.$route('dict_item.create', { code: this.code }) : this.$route('dict_item.update', { code: this.code, id: this.form.id })
                    var res = await this.$axios[method](url, this.form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success(`${this.mode == 'add' ? '添加' : '编辑'}字典项成功`)
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