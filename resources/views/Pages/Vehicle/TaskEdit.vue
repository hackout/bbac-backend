<template>
    <Layout>
        <el-form class="printer-box" ref="form" v-model="form">
            <div class="page-search">
                <div class="page-search-buttons">
                    <el-button @click="goList">返回</el-button>
                </div>
                <div class="page-search-form" style="flex: 0;">
                    <el-form-item inline>
                        <el-button type="primary" v-loading="loading" @click="onSubmit" icon="el-icon-edit">
                            <span>保存</span>
                        </el-button>
                    </el-form-item>
                </div>
            </div>

            <ReportBox modeName="vehicle" :title="`${$status('engine_type', item.engine)} Engine Audit Report`"
                footer="1/1 QM E&B | AFS">
                <table class="report-table" border="1">
                    <tbody>
                        <tr>
                            <td class="report-table-label" align="right">
                                <span>Finder</span>
                            </td>
                            <td>
                                <span>{{ item.auditor }}</span>
                            </td>
                            <td class="report-table-label" align="right">
                                <span>Assembly Line</span>
                            </td>
                            <td>
                                <el-select v-model="form.line" placeholder="请选择生产线">
                                    <el-option v-for="(line, index) in assembly_line" :key="index" :value="line.value"
                                        :label="line.name"></el-option>
                                </el-select>
                            </td>
                            <td colspan="4" class="report-table-label" align="center">
                                <span>Issue Description</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="report-table-label" align="right">
                                <span>Engine Type</span>
                            </td>
                            <td colspan="3">
                                <el-radio-group v-model="form.engine">
                                    <el-radio v-for="(engine, index) in engine_type" :key="index"
                                        :label="engine.value">{{
                                            engine.name }}</el-radio>
                                </el-radio-group>
                            </td>
                            <td colspan="4" rowspan="3" valign="top">
                                <el-input style="height:130px;" type="textarea" maxlength="700" show-word-limit
                                    v-model="form.description" placeholder="Issue Description"></el-input>
                            </td>
                        </tr>
                        <tr>
                            <td class="report-table-label" align="right">
                                <span>Purpose</span>
                            </td>
                            <td colspan="3">
                                <el-radio-group v-model="form.purpose">
                                    <el-radio v-for="(pur, index) in purpose" :key="index" :label="pur.value">{{
                                        pur.name }}</el-radio>
                                </el-radio-group>
                            </td>
                        </tr>
                        <tr>
                            <td class="report-table-label" align="right">
                                <span>8D No.</span>
                            </td>
                            <td>
                                <el-input v-model="form.eight" placeholder="8D No"></el-input>
                            </td>
                            <td class="report-table-label" align="right">
                                <span>Date</span>
                            </td>
                            <td>
                                <span>{{ $tool.dateFormat(item.created_at) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="report-table-label between" align="right">
                                <span>Related Pictures</span>
                                <el-icon-picture></el-icon-picture>
                            </td>
                            <td class="report-table-label" align="right">
                                <span>Issue Level</span>
                            </td>
                            <td>
                                <el-select v-model="form.level" placeholder="请选择缺陷等级">
                                    <el-option v-for="(level, index) in defect_level" :key="index" :value="level.value"
                                        :label="level.name"></el-option>
                                </el-select>
                            </td>
                            <td class="report-table-label" align="right">
                                <span>Engine Number</span>
                            </td>
                            <td>
                                <el-input v-model="form.eb_number" placeholder="Engine Number"></el-input>
                            </td>
                            <td class="report-table-label" colspan="3" align="center">
                                <span>Resp. Dept./Issue Manager</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" rowspan="3">
                                <el-upload :file-list="form.thumbnails"
                                    :action="$route('vehicle.task_upload', { id: item.id })" list-type="picture-card"
                                    :on-preview="handleThumbnailPreview" :on-remove="handleThumbnailRemove"
                                    :headers="uploadHeaders" :on-success="importThumbnailSuccess" multiple :limit="5"
                                    accept="image/*">
                                    <el-icon-plus>
                                    </el-icon-plus>
                                </el-upload>
                            </td>
                            <td colspan="3" class="report-table-manager" valign="top">
                                <el-input type="textarea" maxlength="500" show-word-limit v-model="form.resp"
                                    placeholder="Resp. Dept./Issue Manager"></el-input>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="report-table-label" align="center">
                                <span>Next Step</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="report-table-next" valign="top">
                                <el-input type="textarea" maxlength="700" show-word-limit v-model="form.next"
                                    placeholder="Next Step"></el-input>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </ReportBox>
            <el-image-viewer v-if="showViewer" @close="showViewer = false" infinite :initial-index="viewerIndex"
                :url-list="viewerList" />
        </el-form>
    </Layout>
</template>
<script>
import { router } from "@inertiajs/vue3";
export default {
    props: {
        item: {
            type: Object,
            default: {}
        },
        defect_level: {
            type: Array,
            default: []
        },
        defect_category: {
            type: Array,
            default: []
        },
        task_status: {
            type: Array,
            default: []
        },
        engine_type: {
            type: Array,
            default: []
        },
        purpose: {
            type: Array,
            default: []
        },
        assembly_line: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            loading: false,
            showViewer: false,
            viewerList: [],
            viewerIndex: 0,
            form: {
                line: '',
                engine: '',
                purpose: '',
                eight: '',
                level: '',
                eb_number: '',
                description: '',
                resp: '',
                next: '',
                thumbnails: []
            },
            uploadHeaders: {
                'X-XSRF-TOKEN': ''
            },
            editable: false,
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.uploadHeaders['X-XSRF-TOKEN'] = this.$tool.cookies.get('XSRF-TOKEN')
            this.form = {
                line: this.item.line,
                engine: this.item.engine,
                purpose: this.item.purpose,
                eight: this.item.eight,
                level: this.item.level,
                eb_number: this.item.eb_number,
                description: this.item.description,
                resp: this.item.resp,
                next: this.item.next,
                thumbnails: this.item.thumbnails
            }
        })
    },
    methods: {
        goList() {
            this.$goTo('vehicle.task')
        },
        onSubmit() {
            this.$confirm('确定修改此内容?', '操作提示').then(async () => {
                this.loading = true
                let form = this.$tool.objCopy(this.form)
                form.media = []
                if (this.item.thumbnails.length > 0) {
                    this.item.thumbnails.forEach(n => {
                        if (form.thumbnails.filter(x => x.uuid == n.uuid).length == 0) {
                            form.media.push(n.uuid)
                        }
                    })
                }
                const res = await this.$axios.put(this.$route('vehicle.task_edit', { id: this.item.id }), form)
                this.loading = false
                if (res.code == this.$config.successCode) {
                    this.$message.success('修改信息成功');
                    setTimeout(()=>{
                        this.$goTo('vehicle.task_detail',{id:this.item.id})
                    },2000)
                } else {
                    this.$message.error(res.message)
                }
            }).catch(() => { })
        },
        handleThumbnailPreview(e) {
            this.viewerList = this.form.thumbnails.map(n => n.url)
            this.viewerIndex = 0
            this.form.thumbnails.forEach((n, i) => {
                if (n.uuid == e.uuid) {
                    this.viewerIndex = i
                }
            })
            this.showViewer = true
        },
        handleThumbnailRemove(e) {
            let uuid = e.uuid ? e.uuid : e.response.data.uuid
            let thumbnails = this.form.thumbnails.filter(n => n.uuid != uuid)
            this.form.thumbnails = thumbnails
        },
        importThumbnailSuccess(e) {
            if (e.errno == 0) {
                this.form.thumbnails.push(e.data)
            }
        },
    }
}
</script>

<style lang="scss" scoped>
.report-table {
    width: 100%;
    border-color: var(--el-report-primary);

    :deep(.el-textarea__inner) {
        height: 100%;
    }

    td {
        border-color: var(--el-report-primary);
        height: 50px;
        padding: 10px;
        color: var(--el-text-color-regular);
    }

    &-label {
        background-color: var(--el-report-main);
        color: var(--el-color-white) !important;

        &.between {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 0;
        }
    }

    &-next {
        height: 150px !important;
    }

    &-manager {
        height: 90px !important;
    }

    :deep(.el-upload-list--picture-card) {
        width: 100%;
    }

    :deep(.el-upload--picture-card) {
        width: 148px !important;
        height: 148px !important;
    }
}
</style>