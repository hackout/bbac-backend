<template>
    <el-drawer :title="titles[form.type][mode]" v-model="visitable" :close-on-press-escape="false"
        :close-on-click-modal="false" destroy-on-close size="800px" @closed="$emit('closed')">
        <el-form :model="form" ref="form" :rules="rules" label-position="top" @submit.native.prevent="onSave">
            <el-form-item label="消息标题" prop="title">
                <el-input v-model="form.title" placeholder="请输入消息标题" clearable></el-input>
            </el-form-item>
            <el-form-item label="消息来源" prop="from" :rules="[{ required: true, message: '请输入信息来源', trigger: 'blur' }]"
                v-if="form.type == 1">
                <el-input v-model="form.from" placeholder="请输入消息来源" clearable></el-input>
            </el-form-item>
            <template v-if="form.type == 3">
                <el-form-item label="工艺文件" :rules="[{ required: true, message: '请选择工艺文件', trigger: 'change' }]"
                    prop="document_id">
                    <el-select v-model="form.extra.document_id" style="width:100%;" v-loading="documentLoading"
                        placeholder="请选择工艺文件" clearable filterable>
                        <el-option v-for="(document, index) in documents" :key="index" :value="document.value">
                            <span style="float: left">{{ document.name }}</span>
                            <span class="extra">{{ $status('document_type', document.value) }}</span>
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="版本编号" prop="version"
                    :rules="[{ required: true, message: '请输入版本编号', trigger: 'blur' }]">
                    <el-input v-model="form.extra.version" placeholder="请输入版本编号" clearable></el-input>
                </el-form-item>
                <el-form-item label="变更项" prop="extra"
                    :rules="[{ required: true, type: 'array', message: '请选择工艺文件', trigger: 'change' }]">
                    <el-table border :data="form.extra.change">
                        <el-table-column label="变更项" prop="name">
                            <template #default="scope">
                                <el-input v-model="form.extra.change[scope.$index].name" placeholder="请输入"></el-input>
                            </template>
                        </el-table-column>
                        <el-table-column label="变更前" prop="before" width="200px">
                            <template #default="scope">
                                <el-input v-model="form.extra.change[scope.$index].before" placeholder="请输入"></el-input>
                            </template>
                        </el-table-column>
                        <el-table-column label="变更后" prop="content" width="200px">
                            <template #default="scope">
                                <el-input v-model="form.extra.change[scope.$index].content"
                                    placeholder="请输入"></el-input>
                            </template>
                        </el-table-column>
                        <el-table-column align="right" width="100px">
                            <template #header>
                                <el-button size="small" type="primary" @click="addChange">增加一项</el-button>
                            </template>
                            <template #default="scope">
                                <el-button size="small" type="danger" @click="deleteChange(scope.$index)">删除</el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-form-item>
            </template>
            <el-form-item label="消息内容" prop="content" v-else
                :rules="[{ required: true, message: '请选择消息内容', trigger: 'extra' }]">
                <Editor v-model="form.content" placeholder="请输入消息内容"></Editor>
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="visitable = false">取消</el-button>
                <el-button type="primary" v-loading="loading" @click="onSave(true)">提交保存</el-button>
                <el-button type="success" v-loading="loading" @click="onSave(false)">存草稿</el-button>
            </div>
        </template>
    </el-drawer>
</template>

<script>
export default {
    props: {
        document_type: {
            type: Array,
            default: []
        }
    },
    emits: ['success', 'closed'],
    data() {
        return {
            mode: 'add',
            titles: {
                1: {
                    add: '添加新的信息分享',
                    edit: '编辑信息分享信息'
                },
                2: {
                    add: '添加新的通知消息',
                    edit: '编辑通知消息信息'
                },
                3: {
                    add: '添加新的工艺变更',
                    edit: '编辑工艺变更信息'
                }
            },
            visitable: false,
            loading: false,
            engine_document: [],
            documentLoading: false,
            temp: {
                name: '',
                before: '',
                after: ''
            },
            form: {
                id: 0,
                type: 1,
                from: '',
                title: '',
                content: '',
                extra: {
                    version: '',
                    document_id: '',
                    change: []
                },
                is_valid: true
            },
            rules: {
                title: [
                    { required: true, message: '请输入消息标题', trigger: 'blur' }
                ]
            }
        }
    },
    methods: {
        addChange() {
            this.form.extra.change.push(this.$tool.objCopy(this.temp))
        },
        deleteChange(index) {
            let changes = this.form.extra.change.filter((n, i) => i != index);
            this.form.extra.change = changes;
        },
        async getDocuments() {
            this.documentLoading = true
            let res = await this.$axios.get(this.$route('document.option'))
            this.documentLoading = false
            if (res.code == this.$config.successCode) {
                this.engine_documents = res.data
            } else {
                this.$message.error(res.message)
            }
        },
        onSave(is_valid = true) {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    let method = this.mode == 'add' ? 'post' : 'put'
                    let url = this.mode == 'add' ? this.$route('notice.create') : this.$route('notice.update', { id: this.form.id })
                    this.loading = true
                    this.form.is_valid = is_valid
                    let res = await this.$axios[method](url, this.form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success(`${this.mode == 'add' ? '添加' : '修改'}信息成功`)
                        this.$emit('success')
                        this.visitable = false
                    } else {
                        this.$message.error(res.message)
                    }
                }
            })
        },
        async getData() {
            var res = await this.$axios.get(this.$route('notice.detail', { id: this.form.id }))
            if (res.code == this.$config.successCode) {
                if (this.form.type != res.data.type) {
                    this.$message.error('参数错误')
                    this.$emit('closed')
                } else {
                    this.form = res.data
                    this.visitable = true
                }
            } else {
                this.$message.error(res.message)
                this.$emit('closed')
            }
        },
        open(mode, form) {
            this.mode = mode
            if (form.id && mode == 'edit') {
                this.form = form
                this.getData();
            } else {
                this.form = {
                    id: 0,
                    type: form.type,
                    from: '',
                    title: '',
                    content: '',
                    extra: null,
                    is_valid: true
                }
                if(this.form.type == 3)
                {
                    this.form.extra = {
                        version: '',
                        change: [],
                        document_id: ''
                    }
                }
                this.visitable = true
            }
            if (this.form.type == 3) {
                this.getDocuments()
            }
        }
    }
}
</script>

<style scoped lang="scss">
.extra {

    float: right;
    color: var(--el-text-color-secondary);
    font-size: 13px;
}
</style>