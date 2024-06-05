<template>
    <el-dialog title="装配记录维护/Reassembly Record Edit" v-model="visitable" size="65vw" @closed="$emit('closed')">

        <el-form :model="form" ref="form" label-position="top" :rules="rules" v-loading="loading" label-width="120px"
            @submit.native.prevent="onSave">
            <el-form-item label="缺陷等级/Defect Level" prop="defect_level">
                <el-select style="width:100%" v-model="form.defect_level" placeholder="请选择考核类型" clearable>
                    <el-option v-for="(item, index) in defect_level" :key="index" :value="item.value"
                        :label="item.name"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="上传记录/Upload File" prop="file">
                <el-input type="file" ref="uploadFile" v-model="form.file" placeholder="请选择上传记录/Upload File"
                    clearable></el-input>
            </el-form-item>
            <el-form-item label="补充内容/Addition" prop="note">
                <el-input type="textarea" v-model="form.note" maxlength="250" show-word-limit
                    placeholder="请输入补充内容/Addition" clearable></el-input>
            </el-form-item>
            <el-form-item label="问题状态" prop="status">
                <el-select style="width:100%" v-model="form.status" placeholder="请选择考核类型" clearable>
                    <el-option v-for="(item, index) in issue_status" :key="index" :value="item.value"
                        :label="item.name"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="问题描述" prop="defect_description">
                <el-select style="width:100%" v-model="form.defect_description" placeholder="请选择考核类型" clearable>
                    <el-option v-for="(item, index) in defect_category" :key="index" :value="item.value"
                        :label="item.name"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="问题原因" prop="cause">
                <el-input type="textarea" v-model="form.cause" maxlength="250" show-word-limit placeholder="请输入问题原因"
                    clearable></el-input>
            </el-form-item>
            <el-form-item label="改善措施" prop="soma">
                <el-input type="textarea" v-model="form.soma" maxlength="250" show-word-limit placeholder="请输入改善措施"
                    clearable></el-input>
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="visitable = false">关闭</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script>
export default {
    props: {
        issue_status: {
            type: Array,
            default: []
        },
        defect_level: {
            type: Array,
            default: []
        },
        defect_category: {
            type: Array,
            default: []
        }
    },
    emits: ['success', 'closed'],
    data() {
        return {
            visitable: false,
            loading: false,
            item: {
                id: ''
            },
            form: {
                defect_level: '',
                file: '',
                note: '',
                status: '',
                defect_description: '',
                cause: '',
                soma: '',
            },
            rules: {
                defect_level: [
                    { required: true, message: '请选择缺陷等级', trigger: 'change' }
                ]
            }
        }
    },
    methods: {
        open(item) {
            this.item = item
            this.loading = false;
            this.form = {
                defect_level: item.defect_level,
                file: null,
                note: item.note,
                status: item.status,
                defect_description: item.defect_description,
                cause: item.cause,
                soma: item.soma,
            }
            this.visitable = true
        },
        async onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    this.loading = true
                    let form = new FormData()
                    Object.keys(this.form).forEach(n => {
                        if (n != 'file') {
                            form.append(n, this.form[n])
                        } else {
                            if (this.form.file) {
                                form.append('file', this.$refs.uploadFile.input.files[0])
                            }
                        }
                    })
                    let res = await this.$axios.post(this.$route('stuff.update', { id: this.item.id }), form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success('维护记录成功')
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

<style scoped lang="scss"></style>