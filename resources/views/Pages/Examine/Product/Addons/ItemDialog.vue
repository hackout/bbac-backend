<template>
    <el-dialog :title="titles[mode]" v-model="visitable" width="808px" :close-on-click-modal="false"
        :close-on-press-escape="false" @closed="$emit('closed')">
        <el-form :model="form" :rules="{}" label-width="150px" ref="form" @submit.native.prevent="onSave">
            <el-form-item label="工作序号" prop="sort_order">
                <el-input type="number" v-model="form.sort_order" placeholder="请输入工作序号" clearable></el-input>
            </el-form-item>
            <el-form-item label="工作内容" prop="content"
                :rules="[{ required: true, message: '请输入工作内容', trigger: 'blur' }]">
                <el-input type="textarea" v-model="form.content" placeholder="请输入工作内容(中文)" clearable></el-input>
            </el-form-item>
            <el-form-item label="工作内容(英文)" prop="content_en">
                <el-input type="textarea" v-model="form.content_en" placeholder="请输入工作内容(英文)" clearable></el-input>
            </el-form-item>
            <el-form-item label="操作描述" prop="standard">
                <el-input type="textarea" v-model="form.standard" placeholder="请输入操作描述(中文)" clearable></el-input>
            </el-form-item>
            <el-form-item label="操作描述(英文)" prop="standard_en">
                <el-input type="textarea" v-model="form.standard_en" placeholder="请输入操作描述(英文)" clearable></el-input>
            </el-form-item>
            <el-row :gutter="20">
                <el-col :span="16">
                    <el-form-item label="拧紧扭矩要求" prop="torque">
                        <el-input v-model="form.torque" placeholder="请输入拧紧扭矩要求" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="螺栓数量" prop="number">
                        <el-input type="number" v-model="form.number" placeholder="请输入螺栓数量" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col v-if="form.type != 2" :span="12">
                    <el-form-item label="测量项" prop="name">
                        <el-input type="textarea" v-model="form.name" placeholder="请输入测量项" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col v-if="form.type != 2" :span="12">
                    <el-form-item label="测量项(英文)" prop="name_en">
                        <el-input type="textarea" v-model="form.name_en" placeholder="请输入测量项(英文)" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col v-if="form.type != 1" :span="12">
                    <el-form-item label="目视检查" prop="eye">
                        <el-input type="textarea" v-model="form.eye" placeholder="请输入目视检查" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col v-if="form.type != 1" :span="12">
                    <el-form-item label="目视检查(英文)" prop="eye_en">
                        <el-input type="textarea" v-model="form.eye_en" placeholder="请输入目视检查(英文)" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="测量下限" prop="lower_limit">
                        <el-input type="number" v-model="form.lower_limit" placeholder="请输入测量下限" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="测量上限" prop="upper_limit">
                        <el-input type="number" v-model="form.upper_limit" placeholder="请输入测量上限" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="测量单位" prop="unit">
                        <el-input v-model="form.unit" placeholder="请输入测量单位" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="是否扫码" prop="is_scan">
                        <el-radio-group v-model="form.is_scan">
                            <el-radio :label="1" size="large" border>YES</el-radio>
                            <el-radio :label="0" size="large" border>NO</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="是否拍照" prop="is_camera">
                        <el-radio-group v-model="form.is_camera">
                            <el-radio :label="1" size="large" border>YES</el-radio>
                            <el-radio :label="0" size="large" border>NO</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="是否DS" prop="is_ds">
                        <el-radio-group v-model="form.is_ds">
                            <el-radio :label="1" size="large" border>YES</el-radio>
                            <el-radio :label="0" size="large" border>NO</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="扫码说明" prop="scan">
                        <el-input type="textarea" v-model="form.scan" placeholder="请输入扫码说明" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="拍照说明" prop="camera">
                        <el-input type="textarea" v-model="form.camera" placeholder="请输入拍照说明" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="零件编号" prop="part_number">
                        <el-select style="width:100%" v-model="form.part_id" placeholder="零件编号" clearable filterable>
                            <el-option v-for="(item, index) in parts" :key="index" :value="item.value"
                                :label="item.name"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="进度" prop="process">
                        <el-input type="number" v-model="form.process" placeholder="请输入进度" clearable></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="24">
                    <el-form-item label="记录说明" prop="record">
                        <el-input type="textarea" v-model="form.record" placeholder="请输入记录说明" clearable></el-input>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-form-item label="图示" prop="thumbnails">
                <el-upload :file-list="form.thumbnail" :action="$route('commit_product_item.upload', { id: commit.id })"
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
        parts: {
            type: Array,
            default: []
        },
        examine_product_item_type: {
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
            type: 'product',
            sub_type: 0,
            commit: {
                id: ''
            },
            uploadHeaders: {
                'X-XSRF-TOKEN': ''
            },
            form: {
                id: '',
                part_id: '',
                name: '',
                name_en: '',
                content: '',
                content_en: '',
                standard: '',
                standard_en: '',
                eye: '',
                eye_en: '',
                number: '',
                lower_limit: '',
                upper_limit: '',
                torque: '',
                is_scan: '',
                is_camera: '',
                is_ds: '',
                scan: '',
                camera: '',
                record: '',
                process: '',
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
                    const url = this.mode == 'add' ? this.$route('commit_product_item.create', { id: this.commit.id }) : this.$route('commit_product_item.update', { id: this.commit.id, item_id: this.form.id })
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
                    part_id: item.part_id,
                    name: item.name,
                    name_en: item.name_en,
                    content: item.content,
                    content_en: item.content_en,
                    standard: item.standard,
                    standard_en: item.standard_en,
                    eye: item.eye,
                    eye_en: item.eye_en,
                    number: item.number,
                    lower_limit: item.lower_limit,
                    upper_limit: item.upper_limit,
                    torque: item.torque,
                    is_scan: item.is_scan ? 1 : 0,
                    is_camera: item.is_camera ? 1 : 0,
                    is_ds: item.is_ds ? 1 : 0,
                    scan: item.scan,
                    camera: item.camera,
                    record: item.record,
                    process: item.process,
                    unit: item.unit,
                    type: item.type,
                    sort_order: item.sort_order,
                    thumbnail: item.thumbnails ? item.thumbnails : [],
                }
            } else {
                this.form = {
                    id: '',
                    part_id: '',
                    name: '',
                    name_en: '',
                    content: '',
                    content_en: '',
                    standard: '',
                    standard_en: '',
                    eye: '',
                    eye_en: '',
                    number: '',
                    lower_limit: '',
                    upper_limit: '',
                    torque: '',
                    is_scan: 0,
                    is_camera: 0,
                    is_ds: 0,
                    scan: '',
                    camera: '',
                    record: '',
                    process: '',
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