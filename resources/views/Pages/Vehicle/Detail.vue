<template>
    <Layout>
        <el-form ref="form" :model="form" label-position="top">
            <div class="page-block margin-bottom">
                <el-descriptions direction="vertical" :column="2">
                    <template #title>
                        <div class="form-block-title">
                            <span>基础信息</span>
                        </div>
                    </template>
                    <template #extra>
                        <el-tag type="danger" effect="dark" size="large" v-if="item.is_block">滞留中</el-tag>
                        <el-tag type="success" effect="dark" size="large" v-else>已放行</el-tag>
                    </template>
                    <el-descriptions-item width="50%" label="Shift/班次:">{{ $status('service_shift',
                        item.shift) }}</el-descriptions-item>
                    <el-descriptions-item width="50%" label="E&B Type发动机/电池号:">{{ $status('eb_type',
                        item.eb_type) }}</el-descriptions-item>
                    <el-descriptions-item width="50%" label="Plant/厂区:">{{ $status('service_factory',
                        item.plant) }}</el-descriptions-item>
                    <el-descriptions-item width="50%" label="PN/生产订单号:">{{ item.product_number }}</el-descriptions-item>
                    <el-descriptions-item width="50%" label="Sensor/问题发现点:">{{ $status('sensor_point',
                        item.sensor) }}</el-descriptions-item>
                    <el-descriptions-item width="50%" label="Engine/Battery SN/发动机/电池号:">{{ item.eb_number
                        }}</el-descriptions-item>
                    <el-descriptions-item width="50%" label="Car Line/车系:">{{ $status('car_series',
                        item.car_line) }}</el-descriptions-item>
                    <el-descriptions-item width="50%" label="V-Type/车型:">{{ $status('motorcycle_type',
                        item.car_type) }}</el-descriptions-item>
                    <el-descriptions-item :span="2" label="Description/问题描述:">{{ item.description
                        }}</el-descriptions-item>
                </el-descriptions>
                <el-row :gutter="40">
                    <el-col :span="24" class="margin-bottom"><el-divider border-style="dotted" /></el-col>
                    <el-col :span="24">
                        <el-form-item prop="initial_analysis">
                            <template #label>
                                <div class="el-descriptions__header">
                                    <div class="el-descriptions__title">Initial Analysis/现场分析</div>
                                    <div class="el-descriptions__extra">
                                        <el-button size="small" @click="analysisVisit = true">变更记录</el-button>
                                    </div>
                                </div>
                            </template>
                            <el-input type="textarea" rows="6" :readonly="!editable" v-model="form.initial_analysis"
                                placeholder="请输入Initial Analysis/现场分析" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="24" class="margin-bottom"><el-divider border-style="dotted" /></el-col>
                    <el-col :span="12">
                        <el-form-item label="Overview Picture/整体图片" prop="overview_attaches">
                            <el-upload :file-list="form.overview_attaches" :disabled="!editable"
                                :action="$route('vehicle.upload', { id: item.id })" list-type="picture-card"
                                :on-preview="handleOverviewPreview" :on-remove="handleOverviewRemove"
                                :headers="uploadHeaders" :on-success="importOverviewSuccess" multiple :limit="3"
                                accept="image/jpg,image/jpeg,image/png,image/bmp,image/tif,image/tiff">
                                <el-icon-plus>
                                </el-icon-plus>
                                <template #tip>
                                    <div class="el-upload__tip">注:最多上传3张</div>
                                </template>
                            </el-upload>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="工程师整体图片" prop="master_overview_attaches">
                            <el-upload :file-list="form.master_overview_attaches" :disabled="!editable"
                                :action="$route('vehicle.upload', { id: item.id })" list-type="picture-card"
                                :on-preview="handleMasterOverviewPreview" :on-remove="handleMasterOverviewRemove"
                                :headers="uploadHeaders" :on-success="importMasterOverviewSuccess" multiple :limit="3"
                                accept="image/jpg,image/jpeg,image/png,image/bmp,image/tif,image/tiff">
                                <el-icon-plus>
                                </el-icon-plus>
                                <template #tip>
                                    <div class="el-upload__tip">注:最多上传3张</div>
                                </template>
                            </el-upload>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Detail Picture/细节图片" prop="detail_attaches">
                            <el-upload :file-list="form.detail_attaches" :disabled="!editable"
                                :action="$route('vehicle.upload', { id: item.id })" list-type="picture-card"
                                :on-preview="handleDetailPreview" :on-remove="handleDetailRemove"
                                :headers="uploadHeaders" :on-success="importDetailSuccess" multiple :limit="3"
                                accept="image/jpg,image/jpeg,image/png,image/bmp,image/tif,image/tiff">
                                <el-icon-plus>
                                </el-icon-plus>
                                <template #tip>
                                    <div class="el-upload__tip">注:最多上传3张</div>
                                </template>
                            </el-upload>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="工程师细节图片" prop="master_detail_attaches">
                            <el-upload :file-list="form.master_detail_attaches" :disabled="!editable"
                                :action="$route('vehicle.upload', { id: item.id })" list-type="picture-card"
                                :on-preview="handleMasterDetailPreview" :on-remove="handleMasterDetailRemove"
                                :headers="uploadHeaders" :on-success="importMasterDetailSuccess" multiple :limit="3"
                                accept="image/jpg,image/jpeg,image/png,image/bmp,image/tif,image/tiff">
                                <el-icon-plus>
                                </el-icon-plus>
                                <template #tip>
                                    <div class="el-upload__tip">注:最多上传3张</div>
                                </template>
                            </el-upload>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Video/视频" prop="videos">
                            <el-upload :file-list="form.videos" ref="videoUpload" :disabled="!editable"
                                :action="$route('vehicle.upload', { id: item.id })" list-type="picture-card"
                                :on-preview="handleVideoPreview" :on-remove="handleVideoRemove" :headers="uploadHeaders"
                                :on-success="importVideoSuccess" multiple :limit="2"
                                accept="video/mp4,video/avi,video/mov,video/wma,video/mkv,video/mpg,video/rm">
                                <el-icon-plus>
                                </el-icon-plus>
                                <template #file="{ file }">
                                    <img v-if="file.response && file.response.data" :src="file.response.data.poster"
                                        alt="" class="el-upload-list__item-thumbnail" />
                                    <img v-else-if="file.poster" :src="file.poster" alt=""
                                        class="el-upload-list__item-thumbnail" />
                                    <img v-else src="/assets/imgs/404.png" alt=""
                                        class="el-upload-list__item-thumbnail" />
                                    <label class="el-upload-list__item-status-label"><i
                                            class="el-icon el-icon--upload-success el-icon--check"><svg
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                                                <path fill="currentColor"
                                                    d="M406.656 706.944 195.84 496.256a32 32 0 1 0-45.248 45.248l256 256 512-512a32 32 0 0 0-45.248-45.248L406.592 706.944z">
                                                </path>
                                            </svg></i></label>
                                    <span class="el-upload-list__item-actions">
                                        <span class="el-upload-list__item-preview" @click="handleVideoPreview(file)">
                                            <el-icon-zoom-in></el-icon-zoom-in>
                                        </span>
                                        <span class="el-upload-list__item-preview" @click="handleVideoPlay(file)">
                                            <el-icon-video-play></el-icon-video-play>
                                        </span>
                                        <span v-if="editable" class="el-upload-list__item-delete"
                                            @click="handleVideoRemove(file)">
                                            <el-icon-delete></el-icon-delete>
                                        </span>
                                    </span>
                                </template>
                                <template #tip>
                                    <div class="el-upload__tip">注:最多上传2个</div>
                                </template>
                            </el-upload>
                        </el-form-item>
                    </el-col>
                </el-row>
            </div>
            <div class="page-block margin-bottom">
                <div class="form-block-title margin-bottom">
                    <span>工程师分析</span>
                </div>
                <el-row :gutter="40">
                    <el-col :span="12">
                        <el-form-item label="分析行动Analysis Action" prop="initial_action">
                            <el-input type="textarea" rows="8" :disabled="!editable" v-model="form.initial_action"
                                placeholder="请输入分析行动Analysis Action" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">

                        <el-form-item label="Issue Status" prop="status">
                            <el-select style="width:100%" :disabled="!editable" v-model="form.status"
                                placeholder="Issue Status" clearable filterable>
                                <el-option v-for="(item, index) in issue_status" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="Issue Type/问题类型" prop="type">
                            <el-select style="width:100%" :disabled="!editable" v-model="form.type"
                                placeholder="Issue Type/问题类型" clearable filterable>
                                <el-option v-for="(item, index) in issue_type" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="问题等级" prop="defect_level">
                            <el-select style="width:100%" :disabled="!editable" v-model="form.defect_level"
                                placeholder="问题等级" clearable filterable>
                                <el-option v-for="(item, index) in defect_level" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Due Date" prop="due_date">
                            <el-date-picker v-model="form.due_date" :disabled="!editable" type="datetime"
                                style="width: 100%;" placeholder="请选择Due Date" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Quantity问题数量" prop="quantity">
                            <el-input type="number" v-model="form.quantity" :disabled="!editable"
                                placeholder="请输入Quantity问题数量" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Action Status">
                            <span class="status-tag"
                                :class="{ success: item.due_end > 5, warning: item.due_end <= 3 && item.due_end > 0, danger: item.due_end < 0 }"></span>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Root Cause Type根本原因类型" prop="cause_type">
                            <el-select style="width:100%" :disabled="!editable" v-model="form.cause_type"
                                placeholder="Root Cause Type根本原因类型" clearable filterable>
                                <el-option v-for="(item, index) in root_cause_type" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Root Cause根本原因" prop="cause">
                            <el-input type="textarea" :disabled="!editable" rows="1" v-model="form.cause"
                                placeholder="请输入Root Cause根本原因" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Relate Parts" prop="relate_parts">
                            <el-input v-model="form.relate_parts" :disabled="!editable" placeholder="请输入Relate Parts"
                                clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Detect Area探测区域" prop="detect_area">
                            <el-select style="width:100%" :disabled="!editable" v-model="form.detect_area"
                                placeholder="Detect Area探测区域" clearable filterable>
                                <el-option v-for="(item, index) in detect_area" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="是否需要Pre-Highlight" prop="is_pre_highlight">
                            <el-select style="width:100%" :disabled="!editable" v-model="form.is_pre_highlight"
                                placeholder="是否需要Pre-Highlight" clearable filterable>
                                <el-option :value="true" label="YES"></el-option>
                                <el-option :value="false" label="NO"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="SOMA短期措施" prop="soma">
                            <el-input type="textarea" :disabled="!editable" rows="1" v-model="form.soma"
                                placeholder="请输入SOMA短期措施" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="是否升级为PPM问题" prop="is_ppm">
                            <el-select style="width:100%" :disabled="!editable" v-model="form.is_ppm"
                                placeholder="是否升级为PPM问题" clearable filterable>
                                <el-option :value="true" label="YES"></el-option>
                                <el-option :value="false" label="NO"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="LAMA长期措施" prop="lama">
                            <el-input type="textarea" :disabled="!editable" rows="1" v-model="form.lama"
                                placeholder="请输入LAMA长期措施" clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Delivery Confirm/E车辆交付确认/工程师" prop="is_confirm">
                            <el-select style="width:100%" :disabled="!editable" v-model="form.is_confirm"
                                placeholder="Delivery Confirm/E车辆交付确认/工程师" clearable filterable>
                                <el-option :value="true" label="YES"></el-option>
                                <el-option :value="false" label="NO"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="8D" prop="eight_disciplines">
                            <el-input v-model="form.eight_disciplines" :disabled="!editable" placeholder="请输入8D"
                                clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Issue Manager问题负责人" prop="ira">
                            <el-input v-model="form.ira" :disabled="!editable" placeholder="请输入Issue Manager问题负责人"
                                clearable></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Delivery Time交付时间" prop="delivery_at">
                            <el-input v-model="item.delivery_at" placeholder="自动生成" disabled></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="滞留天数" prop="block_days">
                            <el-input v-model="item.block_days" placeholder="自动生成" disabled></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
            </div>
            <div class="page-block margin-bottom">
                <div class="form-block-title margin-bottom">
                    <span>问题日志</span>
                </div>
                <DataTable :data="logs" :hideRefresh="true">
                    <el-table-column label="时间">
                        <template #default="scope">
                            <span>{{ $tool.dateFormat(scope.row.created_at) }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column label="操作人" prop="user"></el-table-column>
                    <el-table-column label="操作记录">
                        <template #default="scope">
                            <el-text type="success" size="small" v-if="scope.row.code == 'create'">提交了问题</el-text>
                            <el-tooltip placement="top" v-if="scope.row.code == 'updated'">
                                <template #content>
                                    <div v-for="(c, i) in scope.row.extra" :key="i">{{ i + 1 }}. {{ tags[c.field] }}</div>
                                </template>
                                <el-text type="primary" size="small" tag="ins">修改了问题</el-text>
                            </el-tooltip>

                        </template>
                    </el-table-column>
                </DataTable>
            </div>
            <div class="fixed-bottom">
                <div class="fixed-bottom-content">
                    <el-button @click="goList" size="large">返回列表</el-button>
                    <div class="empty-flex"></div>
                    <el-button @click="editable = true" v-if="!editable" size="large" type="primary">编辑</el-button>
                    <template v-else>
                        <el-button @click="editable = false" size="large">取消</el-button>

                        <el-button @click="onSubmit" :loading="loading" size="large" type="primary">保存</el-button>

                        <el-button @click="onClose" :loading="loading" size="large" type="primary">关闭问题</el-button>
                    </template>
                </div>
            </div>
        </el-form>
        <el-image-viewer v-if="showViewer" @close="showViewer = false" infinite :initial-index="viewerIndex"
            :url-list="viewerList" />
        <el-dialog v-model="videoVisit" title="视频预览" width="80%" top="5vh">
            <video controls ref="player" width="100%" :src="currentVideo.url" :poster="currentVideo.poster"></video>
        </el-dialog>
        <el-dialog v-model="analysisVisit" title="现场分析记录" width="560" top="5vh">
            <el-timeline style="max-width: 550px">
                <el-timeline-item v-for="(analysis, index) in analysisList" :key="index"
                    :timestamp="`${analysis.user} 修改于 ${$tool.dateFormat(analysis.created_at)}`">
                    <pre>{{ analysis.content }}</pre>
                </el-timeline-item>
            </el-timeline>
        </el-dialog>
    </Layout>
</template>
<script>
import { router } from "@inertiajs/vue3";
export default {
    props: {
        motorcycle_type: {
            type: Array,
            default: []
        },
        car_series: {
            type: Array,
            default: []
        },
        sensor_point: {
            type: Array,
            default: []
        },
        service_shift: {
            type: Array,
            default: []
        },
        eb_type: {
            type: Array,
            default: []
        },
        service_factory: {
            type: Array,
            default: []
        },
        block_status: {
            type: Array,
            default: []
        },
        block_content: {
            type: Array,
            default: []
        },
        issue_status: {
            type: Array,
            default: []
        },
        issue_type: {
            type: Array,
            default: []
        },
        detect_area: {
            type: Array,
            default: []
        },
        defect_level: {
            type: Array,
            default: []
        },
        root_cause_type: {
            type: Array,
            default: []
        },
        logs: {
            type: Array,
            default: []
        },
        item: {
            type: Object,
            default: {}
        }
    },
    data() {
        return {
            loading: false,
            analysisVisit: false,
            showViewer: false,
            viewerList: [],
            viewerIndex: 0,
            changeList: [],
            form: {
                id: '',
                initial_analysis: '',
                initial_action: '',
                status: '',
                type: '',
                defect_level: '',
                soma: '',
                lama: '',
                eight_disciplines: '',
                ira: '',
                is_ppm: '',
                is_pre_highlight: '',
                detect_area: '',
                quantity: '',
                cause: '',
                relate_parts: '',
                cause_type: '',
                due_date: '',
                delivery_confirm: '',
                overview_attaches: [],
                master_overview_attaches: [],
                detail_attaches: [],
                master_detail_attaches: [],
                videos: []
            },
            uploadHeaders: {
                'X-XSRF-TOKEN': ''
            },
            editable: false,
            videoVisit: false,
            currentVideo: {
                url: '',
                name: '',
                poster: '',
                uuid: ''
            },
            tags: {
                shift: '班次',
                plant: '厂区',
                eb_type: 'E&B-Type 发动机/电池型号',
                product_number: 'PN/生产订单号',
                sensor: '问题发现点',
                eb_number: 'Engine/Battery SN/发动机/电池号',
                car_line: '车系',
                car_type: '车型',
                is_block: '是否滞留',
                description: '问题描述',
                initial_analysis: '现场分析',
                initial_action: '分析行动',
                block_status: '滞留状态',
                block_content: '检测内容',
                status: '问题状态',
                type: '问题类型',
                defect_level: '问题等级',
                soma: '短期措施',
                lama: '长期措施',
                eight_disciplines: '8D',
                ira: '责任人',
                is_confirm: '放行确认',
                is_ppm: '是否PPM',
                is_pre_highlight: '是否PreHighlight',
                detect_area: '探测区域',
                quantity: '问题数量',
                cause: '根本原因',
                relate_parts: '关联零件',
                cause_type: '根本原因类型',
                delivery_at: '放行时间',
            }
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.uploadHeaders['X-XSRF-TOKEN'] = this.$tool.cookies.get('XSRF-TOKEN')
            this.form = {
                id: this.item.id,
                initial_analysis: this.item.initial_analysis,
                initial_action: this.item.initial_action,
                status: this.item.status,
                type: this.item.type,
                defect_level: this.item.defect_level,
                soma: this.item.soma,
                lama: this.item.lama,
                eight_disciplines: this.item.eight_disciplines,
                ira: this.item.ira,
                is_ppm: this.item.is_ppm,
                is_pre_highlight: this.item.is_pre_highlight,
                detect_area: this.item.detect_area,
                quantity: this.item.quantity,
                cause: this.item.cause,
                relate_parts: this.item.relate_parts,
                cause_type: this.item.cause_type,
                due_date: this.item.due_date,
                is_confirm: this.item.is_confirm,
                overview_attaches: this.item.overview_attaches,
                master_overview_attaches: this.item.master_overview_attaches,
                detail_attaches: this.item.detail_attaches,
                master_detail_attaches: this.item.master_detail_attaches,
                videos: this.item.videos,
            }
        })
    },
    computed: {
        analysisList() {
            return this.logs.filter(n => {
                return n.extra && n.extra.filter(d => d.field == 'initial_analysis').length > 0
            }).map(n => {
                return {
                    created_at: n.created_at,
                    user: n.user,
                    content: n.extra.filter(x => x.field == 'initial_analysis')[0].content
                }
            })
        }
    },
    methods: {
        goList() {
            this.$goTo('vehicle.index')
        },
        onClose() {
            this.$confirm('确定关闭此问题?', '操作提示').then(async () => {
                this.loading = true
                var res = await this.$axios.patch(this.$route('vehicle.close', { id: this.item.id }))
                this.loading = false
                if (res.code == this.$config.successCode) {
                    this.$message.success("关闭问题成功")
                    setTimeout(() => {
                        this.goList()
                    }, 2000)
                } else {
                    this.$message.error(res.message)
                }
            }).catch(() => { })
        },
        onSubmit() {

            if (this.editable) {
                this.$confirm('确定修改此内容?', '操作提示').then(async () => {
                    this.loading = true
                    let form = this.$tool.objCopy(this.form)
                    form.is_block = form.is_block ? 1 : 0
                    form.media = []
                    if (this.item.overview_attaches.length > 0) {
                        this.item.overview_attaches.forEach(n => {
                            if (form.overview_attaches.filter(x => x.uuid == n.uuid).length == 0) {
                                form.media.push(n.uuid)
                            }
                        })
                    }
                    if (this.item.master_overview_attaches.length > 0) {
                        this.item.master_overview_attaches.forEach(n => {
                            if (form.master_overview_attaches.filter(x => x.uuid == n.uuid).length == 0) {
                                form.media.push(n.uuid)
                            }
                        })
                    }
                    if (this.item.detail_attaches.length > 0) {
                        this.item.detail_attaches.forEach(n => {
                            if (form.detail_attaches.filter(x => x.uuid == n.uuid).length == 0) {
                                form.media.push(n.uuid)
                            }
                        })
                    }
                    if (this.item.master_detail_attaches.length > 0) {
                        this.item.master_detail_attaches.forEach(n => {
                            if (form.master_detail_attaches.filter(x => x.uuid == n.uuid).length == 0) {
                                form.media.push(n.uuid)
                            }
                        })
                    }
                    if (this.item.videos.length > 0) {
                        this.item.videos.forEach(n => {
                            if (form.videos.filter(x => x.uuid == n.uuid).length == 0) {
                                form.media.push(n.uuid)
                            }
                        })
                    }
                    const res = await this.$axios.put(this.$route('vehicle.update', { id: this.item.id }), form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success('修改问题成功');
                        this.editable = false
                        router.reload()
                    } else {
                        this.$message.error(res.message)
                    }
                }).catch(() => { })
            }
        },
        handleOverviewPreview(e) {
            this.viewerList = this.form.overview_attaches.map(n => n.url)
            this.viewerIndex = 0
            this.form.overview_attaches.forEach((n, i) => {
                if (n.uuid == e.uuid) {
                    this.viewerIndex = i
                }
            })
            this.showViewer = true
        },
        handleOverviewRemove(e) {
            let uuid = e.uuid ? e.uuid : e.response.data.uuid
            let overview_attaches = this.form.overview_attaches.filter(n => n.uuid != uuid)
            this.form.overview_attaches = overview_attaches
        },
        handleMasterOverviewPreview(e) {
            this.viewerList = this.form.master_overview_attaches.map(n => n.url)
            this.viewerIndex = 0
            this.form.master_overview_attaches.forEach((n, i) => {
                if (n.uuid == e.uuid) {
                    this.viewerIndex = i
                }
            })
            this.showViewer = true
        },
        handleMasterOverviewRemove(e) {
            let uuid = e.uuid ? e.uuid : e.response.data.uuid
            let master_overview_attaches = this.form.master_overview_attaches.filter(n => n.uuid != uuid)
            this.form.master_overview_attaches = master_overview_attaches
        },
        handleDetailPreview(e) {
            this.viewerList = this.form.detail_attaches.map(n => n.url)
            this.viewerIndex = 0
            this.form.detail_attaches.forEach((n, i) => {
                if (n.uuid == e.uuid) {
                    this.viewerIndex = i
                }
            })
            this.showViewer = true
        },
        handleDetailRemove(e) {
            let uuid = e.uuid ? e.uuid : e.response.data.uuid
            let detail_attaches = this.form.detail_attaches.filter(n => n.uuid != uuid)
            this.form.detail_attaches = detail_attaches
        },
        handleMasterDetailPreview(e) {
            this.viewerList = this.form.master_detail_attaches.map(n => n.url)
            this.viewerIndex = 0
            this.form.master_detail_attaches.forEach((n, i) => {
                if (n.uuid == e.uuid) {
                    this.viewerIndex = i
                }
            })
            this.showViewer = true
        },
        handleMasterDetailRemove(e) {
            let uuid = e.uuid ? e.uuid : e.response.data.uuid
            let master_detail_attaches = this.form.master_detail_attaches.filter(n => n.uuid != uuid)
            this.form.master_detail_attaches = master_detail_attaches
        },
        handleVideoPreview(e) {
            this.viewerList = this.form.videos.map(n => n.poster).filter(n => n && n.length > 0)
            this.viewerIndex = 0
            this.viewerList.forEach((n, i) => {
                if (n.uuid == e.poster) {
                    this.viewerIndex = i
                }
            })
            this.showViewer = true
        },
        handleVideoPlay(e) {
            let data = e.response ? e.response.data : e
            this.currentVideo = data
            this.videoVisit = true
            this.$nextTick(() => {
                this.$refs.player.play()
            })
        },
        handleVideoRemove(e) {
            let uuid = e.uuid ? e.uuid : e.response.data.uuid
            let videos = this.form.videos.filter(n => n.uuid != uuid)
            this.form.videos = videos
        },
        importOverviewSuccess(e) {
            if (e.errno == 0) {
                this.form.overview_attaches.push(e.data)
            }
        },
        importMasterOverviewSuccess(e) {
            if (e.errno == 0) {
                this.form.master_overview_attaches.push(e.data)
            }
        },
        importDetailSuccess(e) {
            if (e.errno == 0) {
                this.form.detail_attaches.push(e.data)
            }
        },
        importMasterDetailSuccess(e) {
            if (e.errno == 0) {
                this.form.master_detail_attaches.push(e.data)
            }
        },
        importVideoSuccess(e) {
            if (e.errno == 0) {
                this.form.videos.push(e.data)
            }
        },
    }
}
</script>

<style scoped>
.fixed-bottom {
    width: 100%;
    height: 80px;
}

.fixed-bottom-content {
    width: calc(100% - 260px);
    height: 80px;
    position: fixed;
    right: 0;
    bottom: 0;
    background-color: var(--el-color-white);
    z-index: 99;
    box-shadow: var(--el-box-shadow-lighter);
    box-sizing: border-box;
    padding: 10px 30px;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
}

.el-form-item-msg {
    color: var(--el-link-color)
}

.status-tag {
    width: 60px;
    height: 30px;
    overflow: hidden;
    display: inline-block;
    background-color: var(--el-color-primary);
}

.status-tag.success {
    background-color: var(--el-vehicle-success);
}

.status-tag.danger {
    background-color: var(--el-vehicle-danger);
}

.status-tag.warning {
    background-color: var(--el-vehicle-warning);
}

:deep(.el-descriptions__label.el-descriptions__cell.is-bordered-label) {
    font-weight: 200;
}
</style>