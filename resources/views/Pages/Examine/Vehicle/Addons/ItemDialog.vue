<template>
    <el-dialog :title="titles[mode]" v-model="visitable" width="808px" :close-on-click-modal="false"
        :close-on-press-escape="false" @closed="$emit('closed')">
        <el-form :model="form" :rules="{}" label-width="150px" ref="form" @submit.native.prevent="onSave">
            <el-form-item label="工作序号" prop="sort_order"
                :rules="[{ required: true, message: '请输入工作序号', trigger: 'blur' }]">
                <el-input type="number" v-model="form.sort_order" placeholder="请输入工作序号" clearable></el-input>
            </el-form-item>
            <el-form-item label="检查类型" prop="type" :rules="[{ required: true, message: '请输入检查类型', trigger: 'blur' }]">
                <el-select style="width:100%" v-model="form.type" placeholder="检查类型" clearable filterable>
                    <el-option v-for="(item, index) in examine_vehicle_item_type" :key="index" :value="item.value"
                        :label="item.name"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="工作内容(中文)" prop="content"
                :rules="[{ required: true, message: '请输入工作内容(中文)', trigger: 'blur' }]">
                <el-input type="textarea" v-model="form.content" placeholder="请输入工作内容(中文)" clearable></el-input>
            </el-form-item>
            <el-form-item label="工作内容(英文)" prop="content_en">
                <el-input type="textarea" v-model="form.content_en" placeholder="请输入工作内容(英文)" clearable></el-input>
            </el-form-item>
            <el-form-item label="检查标准(中文)" prop="standard">
                <el-input type="textarea" v-model="form.standard" placeholder="请输入检查标准(中文)" clearable></el-input>
            </el-form-item>
            <el-form-item label="检查标准(英文)" prop="standard_en">
                <el-input type="textarea" v-model="form.standard_en" placeholder="请输入检查标准(英文)" clearable></el-input>
            </el-form-item>
            <el-form-item label="其他要求(中文)" prop="other">
                <el-input type="textarea" v-model="form.other" placeholder="请输入其他要求(中文)" clearable></el-input>
            </el-form-item>
            <el-form-item label="其他要求(英文)" prop="other_en">
                <el-input type="textarea" v-model="form.other_en" placeholder="请输入其他要求(英文)" clearable></el-input>
            </el-form-item>
            <el-form-item label="图示" prop="thumbnails">
                <el-upload :file-list="form.thumbnail" :action="$route('commit_vehicle_item.upload', { id: commit.id })"
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
        examine_vehicle_item_type: {
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
            commit: {
                id: ''
            },
            uploadHeaders: {
                'X-XSRF-TOKEN': ''
            },
            form: {
                id: '',
                content: '',
                content_en: '',
                standard: '',
                standard_en: '',
                other: '',
                other_en: '',
                type: '',
                sort_order: '',
                thumbnail: []
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
                    const url = this.mode == 'add' ? this.$route('commit_vehicle_item.create', { id: this.commit.id }) : this.$route('commit_vehicle_item.update', { id: this.commit.id, item_id: this.form.id })
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
        open(mode, commit, item) {
            this.mode = mode
            this.commit = commit
            if (item) {
                item = this.$tool.objCopy(item)
                this.form = {
                    id: item.id,
                    content: item.content,
                    content_en: item.content_en,
                    standard: item.standard,
                    standard_en: item.standard_en,
                    other: item.other,
                    other_en: item.other_en,
                    type: item.type,
                    sort_order: item.sort_order,
                    thumbnail: item.thumbnails ? item.thumbnails : []
                }
            } else {
                this.form = {
                    id: '',
                    content: '',
                    content_en: '',
                    standard: '',
                    standard_en: '',
                    other: '',
                    other_en: '',
                    type: '',
                    sort_order: '',
                    thumbnail: []
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