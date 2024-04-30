<template>
    <el-dialog :title="titles[mode]" v-model="visitable" width="808px" :close-on-click-modal="false"
        :close-on-press-escape="false" @closed="$emit('closed')">
        <el-form :model="form" :rules="{}" label-width="150px" ref="form" @submit.native.prevent="onSave">
            <el-form-item label="工位" prop="station">
                <el-input v-model="form.station" placeholder="请输入工位" clearable></el-input>
            </el-form-item>
            <template v-if="form.type == 1">
                <el-form-item label="螺栓编号" :rules="[{ required: true, message: '螺栓编号不能为空', trigger: 'blur' }]"
                    prop="bolt_number">
                    <el-input v-model="form.bolt_number" placeholder="请输入螺栓编号" clearable></el-input>
                </el-form-item>
                <el-form-item label="中文描述" prop="content"
                    :rules="[{ required: true, message: '中文描述不能为空', trigger: 'blur' }]">
                    <el-input type="textarea" v-model="form.content" placeholder="请输入中文描述" clearable></el-input>
                </el-form-item>
                <el-form-item label="英文描述" prop="content_en">
                    <el-input type="textarea" v-model="form.content_en" placeholder="请输入英文描述" clearable></el-input>
                </el-form-item>
                <el-form-item label="螺栓数量" :rules="[{ required: true, message: '螺栓数量不能为空', trigger: 'blur' }]"
                    prop="number">
                    <el-input v-model="form.number" placeholder="请输入螺栓数量" clearable></el-input>
                </el-form-item>
                <el-form-item label="螺栓种类1" prop="bolt_model">
                    <el-select style="width:100%" v-model="form.bolt_model" placeholder="螺栓种类1" clearable>
                        <el-option v-for="(item, index) in bolt_model" :key="index" :value="item.value"
                            :label="item.name"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="螺栓种类2" prop="bolt_type">
                    <el-select style="width:100%" v-model="form.bolt_type" placeholder="螺栓种类2" clearable>
                        <el-option v-for="(item, index) in bolt_type" :key="index" :value="item.value"
                            :label="item.name"></el-option>
                    </el-select>
                </el-form-item>
            </template>
            <template v-else-if="form.type > 4">
                <el-form-item label="测量内容" prop="content"
                    :rules="[{ required: true, message: '测量内容不能为空', trigger: 'blur' }]">
                    <el-input type="textarea" v-model="form.content" placeholder="请输入测量内容" clearable></el-input>
                </el-form-item>
                <el-form-item label="测量内容(英文)" prop="content_en">
                    <el-input type="textarea" v-model="form.content_en" placeholder="请输入测量内容(英文)" clearable></el-input>
                </el-form-item>
            </template>
            <template v-else>
                <el-form-item label="检查内容" prop="content"
                    :rules="[{ required: true, message: '检查内容不能为空', trigger: 'blur' }]">
                    <el-input type="textarea" v-model="form.content" placeholder="请输入检查内容" clearable></el-input>
                </el-form-item>
                <el-form-item label="检查内容(英)" prop="content_en">
                    <el-input type="textarea" v-model="form.content_en" placeholder="请输入检查内容(英)" clearable></el-input>
                </el-form-item>
            </template>
            <template v-if="form.type > 1">
                <el-form-item label="检查标准" prop="standard">
                    <el-input type="textarea" v-model="form.standard" placeholder="请输入检查标准" clearable></el-input>
                </el-form-item>
                <el-form-item label="检查标准(英)" prop="standard_en">
                    <el-input type="textarea" v-model="form.standard_en" placeholder="请输入检查标准(英)" clearable></el-input>
                </el-form-item></template>
            <el-form-item label="墨水型号" v-if="form.type == 5" prop="gluing">
                <el-input v-model="form.gluing" placeholder="请输入墨水型号" clearable></el-input>
            </el-form-item>
            <el-form-item label="检查数量" v-if="form.type > 1"
                :rules="[{ required: true, message: '检查数量不能为空', trigger: 'blur' }]" prop="number">
                <el-input v-model="form.number" placeholder="请输入检查数量" clearable></el-input>
            </el-form-item>
            <el-form-item label="特殊特性" v-if="form.type != 6 && form.type != 6" prop="special">
                <el-select style="width:100%" v-model="form.special" placeholder="特殊特性" clearable>
                    <el-option v-for="(item, index) in special" :key="index" :value="item.value"
                        :label="item.name"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="放行状态" v-if="form.type == 1" prop="bolt_status">
                <el-select style="width:100%" v-model="form.bolt_status" placeholder="放行状态" clearable>
                    <el-option v-for="(item, index) in bolt_status" :key="index" :value="item.value"
                        :label="item.name"></el-option>
                </el-select>
            </el-form-item>
            <template v-if="form.type == 1 || form.type == 2">
                <el-form-item label="测量下限" prop="lower_limit">
                    <el-input v-model="form.lower_limit" placeholder="请输入测量下限" clearable></el-input>
                </el-form-item>
                <el-form-item label="测量上限" prop="upper_limit">
                    <el-input v-model="form.upper_limit" placeholder="请输入测量上限" clearable></el-input>
                </el-form-item>
                <el-form-item label="测量单位" prop="unit">
                    <el-input v-model="form.unit" placeholder="请输入测量单位" clearable></el-input>
                </el-form-item>
            </template>
            <el-form-item label="实际测量值" prop="name">
                <el-input v-model="form.name" placeholder="请输入实际测量值" clearable></el-input>
            </el-form-item>
            <el-form-item label="图示" prop="thumbnails">
                <el-upload :file-list="form.thumbnail" :action="$route('commit_inline_item.upload', { id: commit.id })"
                    list-type="picture-card" :on-preview="handlePictureCardPreview" :on-remove="handleRemove"
                    :on-success="importSuccess" :headers="uploadHeaders" multiple accept="image/*">
                    <el-icon-plus>
                    </el-icon-plus>
                </el-upload>
            </el-form-item>
            <el-image-viewer v-if="showViewer" @close="showViewer = false" infinite :url-list="viewerList" />
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
        special: {
            type: Array,
            default: []
        },
        bolt_status: {
            type: Array,
            default: []
        },
        bolt_model: {
            type: Array,
            default: []
        },
        bolt_type: {
            type: Array,
            default: []
        },
        examine_inline_item_type: {
            type: Array,
            default: []
        }
    },
    emits: ['success', 'closed'],
    data() {
        return {
            mode: 'add',
            titles: {
                add: '添加考核项',
                edit: '编辑考核项'
            },
            visitable: false,
            loading: false,
            showViewer: false,
            viewerList: [],
            type: 'inline',
            sub_type: 0,
            commit: {
                id: ''
            },
            uploadHeaders: {
                'X-XSRF-TOKEN': ''
            },
            form: {
                id: '',
                station: '',
                name: '',
                content: '',
                content_en: '',
                standard: '',
                standard_en: '',
                number: '',
                special: '',
                gluing: '',
                bolt_number: '',
                bolt_model: '',
                bolt_type: '',
                bolt_status: '',
                lower_limit: '',
                upper_limit: '',
                unit: 'Nm',
                type: 1,
                sort_order: '',
                thumbnail: [],
            }
        }
    },
    created() {
        this.$nextTick(() => {
            this.uploadHeaders['X-XSRF-TOKEN'] = this.$tool.cookies.get('XSRF-TOKEN')
        })
    },
    methods: {
        onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    this.loading = true
                    const url = this.mode == 'add' ? this.$route('commit_inline_item.create', { id: this.commit.id }) : this.$route('commit_inline_item.update', { id: this.commit.id, item_id: this.form.id })
                    const method = this.mode == 'add' ? 'post' : 'put'
                    const res = await this.$axios[method](url, this.form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success((this.mode == 'add' ? '添加' : '编辑') + `考核项成功`)
                        this.$emit('success')
                        this.visitable = false
                    } else {
                        this.$message.error(res.message)
                    }
                }
            })
        },
        open(mode, type, commit, item) {
            this.mode = mode
            this.commit = commit
            if (item) {
                this.form = {
                    id: item.id,
                    station: item.station,
                    name: item.name,
                    content: item.content,
                    content_en: item.content_en,
                    standard: item.standard,
                    standard_en: item.standard_en,
                    number: item.number,
                    special: item.special > 0 ? item.special : '',
                    gluing: item.gluing,
                    bolt_number: item.bolt_number,
                    bolt_model: item.bolt_model > 0 ? item.bolt_model : '',
                    bolt_type: item.bolt_type > 0 ? item.bolt_type : '',
                    bolt_status: item.bolt_status > 0 ? item.bolt_status : '',
                    lower_limit: item.lower_limit,
                    upper_limit: item.upper_limit,
                    unit: item.unit,
                    type: item.type,
                    sort_order: item.sort_order,
                    thumbnail: item.thumbnails ? item.thumbnails : [],
                }
            } else {
                this.form = {
                    id: '',
                    station: '',
                    name: '',
                    content: '',
                    content_en: '',
                    standard: '',
                    standard_en: '',
                    number: '',
                    special: '',
                    gluing: '',
                    bolt_number: '',
                    bolt_model: '',
                    bolt_type: '',
                    bolt_status: '',
                    lower_limit: '',
                    upper_limit: '',
                    unit: 'Nm',
                    type: type,
                    sort_order: '',
                    thumbnail: [],
                }
            }
            this.visitable = true
        },
        importSuccess(e) {
            if (e.errno == 0) {
                this.form.thumbnail.push(e.data)
            }
        },
        async handleRemove(e) {
            let uuid = e.uuid ? e.uuid : e.response.data.uuid
            let thumbnail = this.form.thumbnail.filter(n => n.uuid != uuid)
            this.form.thumbnail = thumbnail
        },
        handlePictureCardPreview() {
            this.viewerList = this.form.thumbnail.map(n => n.url)
            this.showViewer = true
        }
    }
}
</script>

<style scoped lang="scss"></style>