<template>
    <el-dialog :title="titles[mode]" v-model="visitable" width="550px" :close-on-click-modal="false" :close-on-press-escape="false" @closed="$emit('closed')">
        <el-form :model="form" :rules="rules" label-width="100px" ref="form" @submit.native.prevent="onSave">
            <el-form-item label="模板名称" prop="name">
                <el-input v-model="form.name" placeholder="请输入模板名称" clearable></el-input>
            </el-form-item>
            <el-form-item label="考核机型" prop="engine">
                <el-select v-model="form.engine" style="width:100%;" placeholder="请选择考核机型" @change="getVersion"
                    clearable>
                    <el-option v-for="(item, index) in engine_type" :key="index" :label="item.name"
                        :value="item.value"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="上一个版本" prop="parent_id" v-if="mode == 'add'">
                <el-select v-model="form.parent_id" @change="changeParent" style="width:100%;" placeholder="请选择上一个版本"
                    clearable>
                    <el-option label="全新版本" value=""></el-option>
                    <el-option v-for="(item, index) in commits" :key="index"
                        :label="`[${$status('engine_type', item.engine)}]${item.name}(${item.version})`"
                        :value="item.value"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="版本号" prop="version">
                <el-input v-model="form.version" :placeholder="lastVersion" @keydown.tab="form.version = lastVersion"
                    clearable></el-input>
            </el-form-item>
            <el-form-item label="备注信息" prop="description">
                <el-input type="textarea" maxlength="200" show-word-limit v-model="form.description"
                    placeholder="请输入备注信息" clearable></el-input>
            </el-form-item>
            <el-form-item label="标准工时" prop="period">
                <el-input type="number" v-model="form.period" placeholder="请输入标准工时" clearable></el-input>
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
                add: '添加考核模板',
                edit: '编辑考核模板'
            },
            commits: [],
            visitable: false,
            loading: false,
            form: {
                id: '',
                parent_id: '',
                version: '',
                name: '',
                description: '',
                engine: '',
                period: '',
                type: ''
            },
            rules: {
                name: [
                    { required: true, message: '请输入模板标题', trigger: 'blur' }
                ],
                engine: [
                    { required: true, message: '请选择考核机型', trigger: 'change' }
                ],
                version: [
                    { required: true, message: '请输入版本号', trigger: 'blur' }
                ],
                period: [
                    { required: true, message: '请输入标准工时', trigger: 'blur' }
                ]
            }
        }
    },
    computed: {
        lastVersion() {
            if (!this.form.version || parseInt(this.form.version)) return '1.0.1'
            var version = this.form.version.split('.')
            version[version.length - 1] = parseInt(version[version.length - 1]) + 1
            return version.join('.')
        }
    },
    methods: {
        async getVersion() {
            let res = await this.$axios.get(this.$route('commit_product.option'), { engine: this.form.engine })
            if (res.code == this.$config.successCode) {
                this.commits = res.data
                this.visitable = true
            }
        },
        onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    this.loading = true
                    const url = this.mode == 'add' ? this.$route('commit_product.create') : this.$route('commit_product.update', { id: this.form.id })
                    const method = this.mode == 'add' ? 'post' : 'put'
                    const res = await this.$axios[method](url, this.form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success((this.mode == 'add' ? '添加' : '编辑') + `考核模板成功`)
                        this.$emit('success')
                        this.visitable = false
                    } else {
                        this.$message.error(res.message)
                    }
                }
            })
        },
        changeParent(e) {
            let find = this.commits.filter(n => n.value == e);
            if (find.length > 0) {
                this.form.version = find[0].version
                this.form.period = find[0].period,
                    this.form.description = find[0].description
            } else {
                this.form.version = ''
                this.form.period = '',
                    this.form.description = ''
            }
        },
        open(mode,type, item) {
            this.mode = mode
            if (item) {
                this.form = {
                    id: item.id,
                    parent_id: item.parent_id,
                    version: item.version,
                    name: item.name,
                    description: item.description,
                    engine: item.engine,
                    period: item.period,
                    type: item.type
                }
            } else {
                this.form = {
                    id: '',
                    parent_id: '',
                    version: '',
                    name: '',
                    description: '',
                    engine: '',
                    period: '',
                    type: type
                }
            }
            this.getVersion()
        }
    }
}
</script>

<style scoped lang="scss"></style>