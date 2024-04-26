<template>
    <el-dialog :title="titles[mode]" v-model="visitable" width="950px" @closed="$emit('closed')">
        <el-form :model="form" :rules="{}" label-width="150px" ref="form" @submit.native.prevent="onSave">
            <template v-if="type == 'inline'">
                <el-form-item label="工位" prop="station" :rules="{ required: true, message: '工位不能为空', trigger: 'blur' }">
                    <el-input v-model="form.station" placeholder="请输入工位" clearable></el-input>
                </el-form-item>
                <el-form-item label="工位2" v-if="gluing_type.indexOf(form.type) == -1"
                    :rules="[{ required: true, message: '工位2不能为空', trigger: 'blur' }]" prop="sub_station">
                    <el-input v-model="form.sub_station" placeholder="请输入工位2" clearable></el-input>
                </el-form-item>
                <template v-if="form.type == 1">
                    <el-form-item label="螺栓编号" :rules="[{ required: true, message: '螺栓编号不能为空', trigger: 'blur' }]"
                        prop="bolt_number">
                        <el-input v-model="form.bolt_number" placeholder="请输入螺栓编号" clearable></el-input>
                    </el-form-item>

                    <el-form-item label="描述(英文)" :rules="[{ required: true, message: '描述(英文)不能为空', trigger: 'blur' }]"
                        prop="content_en">
                        <el-input v-model="form.content_en" placeholder="请输入描述(英文)" clearable></el-input>
                    </el-form-item>
                    <el-form-item label="描述(中文)" :rules="[{ required: true, message: '描述(中文)不能为空', trigger: 'blur' }]"
                        prop="content_zh">
                        <el-input v-model="form.content_zh" placeholder="请输入描述(中文)" clearable></el-input>
                    </el-form-item>
                    <el-form-item label="螺栓数量" :rules="[{ required: true, message: '螺栓数量不能为空', trigger: 'blur' }]"
                        prop="number">
                        <el-input v-model="form.number" placeholder="请输入螺栓数量" clearable></el-input>
                    </el-form-item>
                    <el-form-item label="螺栓分类1" :rules="[{ required: true, message: '螺栓分类1不能为空', trigger: 'change' }]"
                        prop="bolt_model">
                        <el-select style="width:100%" v-model="form.bolt_model" placeholder="螺栓分类1" clearable>
                            <el-option v-for="(item, index) in bolt_model" :key="index" :value="item.value"
                                :label="item.name"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="螺栓分类2" :rules="[{ required: true, message: '螺栓分类2不能为空', trigger: 'change' }]"
                        prop="bolt_type">
                        <el-select style="width:100%" v-model="form.bolt_type" placeholder="螺栓分类2" clearable>
                            <el-option v-for="(item, index) in bolt_type" :key="index" :value="item.value"
                                :label="item.name"></el-option>
                        </el-select>
                    </el-form-item>
                </template>
                <template v-else-if="form.type > 4">
                    <el-form-item label="测量内容(中文)"
                        :rules="[{ required: true, message: '测量内容(中文)不能为空', trigger: 'blur' }]" prop="content_zh">
                        <el-input v-model="form.content_zh" placeholder="请输入测量内容(中文)" clearable></el-input>
                    </el-form-item>
                    <el-form-item label="测量内容(英文)"
                        :rules="[{ required: true, message: '测量内容(英文)不能为空', trigger: 'blur' }]" prop="content_en">
                        <el-input v-model="form.content_en" placeholder="请输入测量内容(英文)" clearable></el-input>
                    </el-form-item>
                </template>
                <template v-else>
                    <el-form-item label="检查内容(中文)"
                        :rules="[{ required: true, message: '检查内容(中文)不能为空', trigger: 'blur' }]" prop="content_zh">
                        <el-input v-model="form.content_zh" placeholder="请输入检查内容(中文)" clearable></el-input>
                    </el-form-item>
                    <el-form-item label="检查内容(英文)"
                        :rules="[{ required: true, message: '检查内容(英文)不能为空', trigger: 'blur' }]" prop="content_en">
                        <el-input v-model="form.content_en" placeholder="请输入检查内容(英文)" clearable></el-input>
                    </el-form-item>
                </template>
                <template v-if="form.type > 1">
                    <el-form-item label="检查标准(中文)"
                        :rules="[{ required: true, message: '检查标准(中文)不能为空', trigger: 'blur' }]" prop="standard_zh">
                        <el-input v-model="form.standard_zh" placeholder="请输入检查标准(中文)" clearable></el-input>
                    </el-form-item>
                    <el-form-item label="检查标准(英文)"
                        :rules="[{ required: true, message: '检查标准(英文)不能为空', trigger: 'blur' }]" prop="standard_en">
                        <el-input v-model="form.standard_en" placeholder="请输入检查标准(英文)" clearable></el-input>
                    </el-form-item></template>
                <el-form-item label="墨水型号" v-if="form.type == 5"
                    :rules="[{ required: true, message: '墨水型号不能为空', trigger: 'blur' }]" prop="gluing">
                    <el-input v-model="form.gluing" placeholder="请输入墨水型号" clearable></el-input>
                </el-form-item>
                <el-form-item label="检查数量" v-if="form.type > 1"
                    :rules="[{ required: true, message: '检查数量不能为空', trigger: 'blur' }]" prop="number">
                    <el-input v-model="form.number" placeholder="请输入检查数量" clearable></el-input>
                </el-form-item>
                <el-form-item label="特殊特性" v-if="gluing_type.indexOf(form.type) == -1"
                    :rules="[{ required: true, message: '特殊特性不能为空', trigger: 'change' }]" prop="special">
                    <el-select style="width:100%" v-model="form.special" placeholder="特殊特性" clearable>
                        <el-option v-for="(item, index) in special" :key="index" :value="item.value"
                            :label="item.name"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="放行状态" v-if="form.type == 1"
                    :rules="[{ required: true, message: '放行状态不能为空', trigger: 'change' }]" prop="bolt_status">
                    <el-select style="width:100%" v-model="form.bolt_status" placeholder="特殊特性" clearable>
                        <el-option v-for="(item, index) in bolt_status" :key="index" :value="item.value"
                            :label="item.name"></el-option>
                    </el-select>
                </el-form-item>
                <template v-if="form.type == 1 || form.type == 2">
                    <el-form-item label="测量下限" :rules="[{ required: true, message: '测量下限不能为空', trigger: 'blur' }]"
                        prop="lower_limit">
                        <el-input v-model="form.lower_limit" placeholder="请输入测量下限" clearable></el-input>
                    </el-form-item>
                    <el-form-item label="测量上限" :rules="[{ required: true, message: '测量上限不能为空', trigger: 'blur' }]"
                        prop="upper_limit">
                        <el-input v-model="form.upper_limit" placeholder="请输入测量上限" clearable></el-input>
                    </el-form-item>
                    <el-form-item label="测量单位" :rules="[{ required: true, message: '测量单位不能为空', trigger: 'blur' }]"
                        prop="unit">
                        <el-input v-model="form.unit" placeholder="请输入测量单位" clearable></el-input>
                    </el-form-item></template>
                <el-form-item label="实际测量值(中)" prop="name_zh"
                    :rules="[{ required: true, message: '实际测量值(中)不能为空', trigger: 'blur' }]">
                    <el-input v-model="form.name_zh" placeholder="请输入实际测量值(中)" clearable></el-input>
                </el-form-item>
                <el-form-item label="实际测量值(英)" prop="name_en"
                    :rules="[{ required: true, message: '实际测量值(英)不能为空', trigger: 'blur' }]">
                    <el-input v-model="form.name_en" placeholder="请输入实际测量值(英)" clearable></el-input>
                </el-form-item>
            </template>
            <template v-if="type == 'product'">
                <el-form-item label="工作序号" prop="sort_order"
                    :rules="[{ required: true, message: '请输入工作序号', trigger: 'blur' }]">
                    <el-input type="number" v-model="form.sort_order" placeholder="请输入工作序号" clearable></el-input>
                </el-form-item>
                <el-form-item label="工作内容(中文)" prop="content_zh"
                    :rules="[{ required: true, message: '请输入工作内容(中文)', trigger: 'blur' }]">
                    <el-input v-model="form.content_zh" placeholder="请输入工作内容(中文)" clearable></el-input>
                </el-form-item>
                <el-form-item label="工作内容(英文)" prop="content_en"
                    :rules="[{ required: true, message: '请输入工作内容(英文)', trigger: 'blur' }]">
                    <el-input v-model="form.content_en" placeholder="请输入工作内容(英文)" clearable></el-input>
                </el-form-item>
                <el-form-item label="操作描述(中文)" prop="standard_zh"
                    :rules="[{ required: true, message: '请输入操作描述(中文)', trigger: 'blur' }]">
                    <el-input v-model="form.standard_zh" placeholder="请输入操作描述(中文)" clearable></el-input>
                </el-form-item>
                <el-form-item label="操作描述(英文)" prop="standard_en"
                    :rules="[{ required: true, message: '请输入操作描述(英文)', trigger: 'blur' }]">
                    <el-input v-model="form.standard_en" placeholder="请输入操作描述(英文)" clearable></el-input>
                </el-form-item>
                <el-row :gutter="20">
                    <el-col :span="16">
                        <el-form-item label="拧紧螺距要求" prop="blot_close"
                            :rules="[{ required: true, message: '请输入拧紧螺距要求', trigger: 'blur' }]">
                            <el-input v-model="form.blot_close" placeholder="请输入拧紧螺距要求" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="螺栓数量" prop="number"
                            :rules="[{ required: true, message: '请输入螺栓数量', trigger: 'blur' }]">
                            <el-input type="number" v-model="form.number" placeholder="请输入螺栓数量" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col v-if="form.type == 1 || form.type == 3" :span="12">
                        <el-form-item label="测量项(中文)" prop="name_zh"
                            :rules="[{ required: true, message: '请输入测量项(中文)', trigger: 'blur' }]">
                            <el-input v-model="form.name_zh" placeholder="请输入测量项(中文)" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col v-if="form.type == 1 || form.type == 3" :span="12">
                        <el-form-item label="测量项(英文)" prop="name_en"
                            :rules="[{ required: true, message: '请输入测量项(英文)', trigger: 'blur' }]">
                            <el-input v-model="form.name_en" placeholder="请输入测量项(英文)" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col v-if="form.type == 2 || form.type == 3" :span="12">
                        <el-form-item label="目测检查(中文)" prop="eye_zh"
                            :rules="[{ required: true, message: '请输入目测检查(中文)', trigger: 'blur' }]">
                            <el-input v-model="form.eye_zh" placeholder="请输入目测检查(中文)" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col v-if="form.type == 2 || form.type == 3" :span="12">
                        <el-form-item label="目测检查(英文)" prop="eye_en"
                            :rules="[{ required: true, message: '请输入目测检查(英文)', trigger: 'blur' }]">
                            <el-input v-model="form.eye_en" placeholder="请输入目测检查(英文)" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="测量下限" prop="lower_limit"
                            :rules="[{ required: true, message: '请输入测量下限', trigger: 'blur' }]">
                            <el-input v-model="form.lower_limit" placeholder="请输入测量下限" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="测量上限" prop="upper_limit"
                            :rules="[{ required: true, message: '请输入测量上限', trigger: 'blur' }]">
                            <el-input v-model="form.upper_limit" placeholder="请输入测量上限" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="测量单位" prop="unit"
                            :rules="[{ required: true, message: '请输入测量单位', trigger: 'blur' }]">
                            <el-input v-model="form.unit" placeholder="请输入测量单位" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="是否扫码" prop="is_scan"
                            :rules="[{ required: true, message: '请输入是否扫码', trigger: 'blur' }]">
                            <el-radio-group v-model="form.is_scan">
                                <el-radio :label="1" size="large" border>YES</el-radio>
                                <el-radio :label="0" size="large" border>NO</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="是否拍照" prop="is_camera"
                            :rules="[{ required: true, message: '请输入是否拍照', trigger: 'blur' }]">
                            <el-radio-group v-model="form.is_camera">
                                <el-radio :label="1" size="large" border>YES</el-radio>
                                <el-radio :label="0" size="large" border>NO</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="零件编号" prop="part_number"
                            :rules="[{ required: true, message: '请输入零件编号', trigger: 'blur' }]">
                            <el-select style="width:100%" v-model="form.part_number" placeholder="零件编号" clearable filterable>
                                <el-option v-for="(item, index) in torque" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="进度" prop="process"
                            :rules="[{ required: true, message: '请输入进度', trigger: 'blur' }]">
                            <el-input type="number" v-model="form.process" placeholder="请输入进度" clearable></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
            </template>
            <template v-if="type == 'service'">
                <el-form-item label="工作序号" prop="sort_order"
                    :rules="[{ required: true, message: '请输入工作序号', trigger: 'blur' }]">
                    <el-input type="number" v-model="form.sort_order" placeholder="请输入工作序号" clearable></el-input>
                </el-form-item>
                <el-form-item label="工作内容(中文)" prop="content_zh"
                    :rules="[{ required: true, message: '请输入工作内容(中文)', trigger: 'blur' }]">
                    <el-input v-model="form.content_zh" placeholder="请输入工作内容(中文)" clearable></el-input>
                </el-form-item>
                <el-form-item label="工作内容(英文)" prop="content_en"
                    :rules="[{ required: true, message: '请输入工作内容(英文)', trigger: 'blur' }]">
                    <el-input v-model="form.content_en" placeholder="请输入工作内容(英文)" clearable></el-input>
                </el-form-item>
                <el-form-item label="操作描述(中文)" prop="standard_zh"
                    :rules="[{ required: true, message: '请输入操作描述(中文)', trigger: 'blur' }]">
                    <el-input v-model="form.standard_zh" placeholder="请输入操作描述(中文)" clearable></el-input>
                </el-form-item>
                <el-form-item label="操作描述(英文)" prop="standard_en"
                    :rules="[{ required: true, message: '请输入操作描述(英文)', trigger: 'blur' }]">
                    <el-input v-model="form.standard_en" placeholder="请输入操作描述(英文)" clearable></el-input>
                </el-form-item>
                <el-row :gutter="20">
                    <el-col v-if="form.type == 1 || form.type == 3" :span="12">
                        <el-form-item label="测量项(中文)" prop="name_zh"
                            :rules="[{ required: true, message: '请输入测量项(中文)', trigger: 'blur' }]">
                            <el-input v-model="form.name_zh" placeholder="请输入测量项(中文)" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col v-if="form.type == 1 || form.type == 3" :span="12">
                        <el-form-item label="测量项(英文)" prop="name_en"
                            :rules="[{ required: true, message: '请输入测量项(英文)', trigger: 'blur' }]">
                            <el-input v-model="form.name_en" placeholder="请输入测量项(英文)" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col v-if="form.type == 2 || form.type == 3" :span="12">
                        <el-form-item label="目测检查(中文)" prop="eye_zh"
                            :rules="[{ required: true, message: '请输入目测检查(中文)', trigger: 'blur' }]">
                            <el-input v-model="form.eye_zh" placeholder="请输入目测检查(中文)" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col v-if="form.type == 2 || form.type == 3" :span="12">
                        <el-form-item label="目测检查(英文)" prop="eye_en"
                            :rules="[{ required: true, message: '请输入目测检查(英文)', trigger: 'blur' }]">
                            <el-input v-model="form.eye_en" placeholder="请输入目测检查(英文)" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="测量下限" prop="lower_limit"
                            :rules="[{ required: true, message: '请输入测量下限', trigger: 'blur' }]">
                            <el-input v-model="form.lower_limit" placeholder="请输入测量下限" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="测量上限" prop="upper_limit"
                            :rules="[{ required: true, message: '请输入测量上限', trigger: 'blur' }]">
                            <el-input v-model="form.upper_limit" placeholder="请输入测量上限" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="测量单位" prop="unit"
                            :rules="[{ required: true, message: '请输入测量单位', trigger: 'blur' }]">
                            <el-input v-model="form.unit" placeholder="请输入测量单位" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="是否扫码" prop="is_scan"
                            :rules="[{ required: true, message: '请输入是否扫码', trigger: 'blur' }]">
                            <el-radio-group v-model="form.is_scan">
                                <el-radio :label="1" size="large" border>YES</el-radio>
                                <el-radio :label="0" size="large" border>NO</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="是否拍照" prop="is_camera"
                            :rules="[{ required: true, message: '请输入是否拍照', trigger: 'blur' }]">
                            <el-radio-group v-model="form.is_camera">
                                <el-radio :label="1" size="large" border>YES</el-radio>
                                <el-radio :label="0" size="large" border>NO</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="零件编号" prop="part_number"
                            :rules="[{ required: true, message: '请输入零件编号', trigger: 'blur' }]">
                            <el-select style="width:100%" v-model="form.part_number" placeholder="零件编号" clearable filterable>
                                <el-option v-for="(item, index) in torque" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="进度" prop="process"
                            :rules="[{ required: true, message: '请输入进度', trigger: 'blur' }]">
                            <el-input type="number" v-model="form.process" placeholder="请输入进度" clearable></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
            </template>
            <el-form-item label="图示" prop="thumbnails">
                <el-upload :file-list="form.thumbnail" :action="$route('commit_item.upload', { id: commit.id })"
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
        bolt_model: {
            type: Array,
            default: []
        },
        bolt_type: {
            type: Array,
            default: []
        },
        bolt_status: {
            type: Array,
            default: []
        },
        other_type: {
            type: Array,
            default: []
        },
        standard_type: {
            type: Array,
            default: []
        },
        gluing_type: {
            type: Array,
            default: []
        },
        dynamic_type: {
            type: Array,
            default: []
        },
        torque: {
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
                'X-XSRF-TOKEN': this.$tool.cookies.get('XSRF-TOKEN')
            },
            form: {
                id: '',
                station: '',
                sub_station: '',
                name_zh: '',
                name_en: '',
                content_zh: '',
                content_en: '',
                standard_zh: '',
                standard_en: '',
                eye_zh: '',
                eye_en: '',
                number: '',
                special: '',
                gluing: '',
                bolt_number: '',
                bolt_model: '',
                bolt_type: '',
                bolt_status: '',
                blot_close: '',
                lower_limit: '',
                upper_limit: '',
                unit: '',
                is_scan: '',
                is_camera: '',
                part_number: '',
                process: '',
                type: '',
                sort_order: '',
                thumbnail: []
            }
        }
    },
    methods: {
        onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    this.loading = true
                    const url = this.mode == 'add' ? this.$route('commit_item.create', { id: this.commit.id }) : this.$route('commit_item.update', { id: this.commit.id, item_id: this.form.id })
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
        open(mode, item_type, type, sub_type, commit, item) {
            this.mode = mode
            this.type = type
            this.commit = commit
            this.sub_type = sub_type
            if (item) {
                this.form = {
                    id: item.id,
                    station: item.station,
                    sub_station: item.sub_station,
                    name_zh: item.name_zh,
                    name_en: item.name_en,
                    content_zh: item.content_zh,
                    content_en: item.content_en,
                    standard_zh: item.standard_zh,
                    standard_en: item.standard_en,
                    eye_zh: item.eye_zh,
                    eye_en: item.eye_en,
                    number: item.number,
                    special: item.special,
                    gluing: item.gluing,
                    bolt_number: item.bolt_number,
                    bolt_model: item.bolt_model,
                    bolt_type: item.bolt_type,
                    bolt_status: item.bolt_status,
                    blot_close: item.blot_close,
                    lower_limit: item.lower_limit,
                    upper_limit: item.upper_limit,
                    unit: item.unit,
                    is_scan: item.is_scan,
                    is_camera: item.is_camera,
                    part_number: item.part_number,
                    process: item.process,
                    type: item.type,
                    sort_order: item.sort_order,
                    thumbnail: item.thumbnails ? item.thumbnails : [],
                }
            } else {
                this.form = {
                    id: '',
                    station: '',
                    sub_station: '',
                    name_zh: '',
                    name_en: '',
                    content_zh: '',
                    content_en: '',
                    standard_zh: '',
                    standard_en: '',
                    eye_zh: '',
                    eye_en: '',
                    number: '',
                    special: '',
                    gluing: '',
                    bolt_number: '',
                    bolt_model: '',
                    bolt_type: '',
                    bolt_status: '',
                    blot_close: '',
                    lower_limit: '',
                    upper_limit: '',
                    unit: '',
                    is_scan: '',
                    is_camera: '',
                    part_number: '',
                    process: '',
                    type: '',
                    sort_order: '',
                    thumbnail: []
                }
            }
            this.form.type = item_type
            this.visitable = true
        },
        importSuccess(e) {
            if (e.errno == 0) {
                this.form.thumbnail.push(e.data)
            }
        },
        async handleRemove(e) {
            let uuid = e.uuid ? e.uuid : e.response.data.uuid
            var res = await this.$axios.delete(this.$route('commit_item.upload_delete', { id: this.commit.id, uuid: uuid }))
            if (res.code != this.$config.successCode) {
                this.$message.error(res.message)
            }
        },
        handlePictureCardPreview() {
            this.viewerList = this.form.thumbnail.map(n => n.url)
            this.showViewer = true
        }
    }
}
</script>

<style scoped lang="scss"></style>