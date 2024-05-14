<template>
    <Layout>
        <div class="printer-box">
            <div class="page-search">
                <div class="page-search-buttons">
                    <el-button @click="goList">返回</el-button>
                </div>
                <div class="page-search-form">
                    <el-form ref="query" inline>
                        <el-form-item>
                            <el-button type="primary" @click="editReport" icon="el-icon-edit">
                                <span>编辑</span>
                            </el-button>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" @click="exportPpt" icon="el-icon-lightning">
                                <span>导出</span>
                            </el-button>
                        </el-form-item>
                    </el-form>
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
                                <span>{{ $status('assembly_line', item.line) }}</span>
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
                                <el-checkbox-group readonly>
                                    <el-checkbox v-for="(engine, index) in engine_type"
                                        :checked="engine.value == item.engine" :key="index" :label="engine.value">{{
                                            engine.name }}</el-checkbox>
                                </el-checkbox-group>
                            </td>
                            <td colspan="4" rowspan="3" valign="top">
                                <span>{{ item.description }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="report-table-label" align="right">
                                <span>Purpose</span>
                            </td>
                            <td colspan="3">
                                <el-checkbox-group readonly>
                                    <el-checkbox v-for="(pur, index) in purpose" :checked="pur.value == item.purpose"
                                        :key="index" :label="pur.value">{{ pur.name }}</el-checkbox>
                                </el-checkbox-group>
                            </td>
                        </tr>
                        <tr>
                            <td class="report-table-label" align="right">
                                <span>8D No.</span>
                            </td>
                            <td>
                                <span>{{ item.eight }}</span>
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
                                <span>{{ $status('defect_level', item.level) }}</span>
                            </td>
                            <td class="report-table-label" align="right">
                                <span>Engine Number</span>
                            </td>
                            <td>
                                <span>{{ item.eb_number }}</span>
                            </td>
                            <td class="report-table-label" colspan="3" align="center">
                                <span>Resp. Dept./Issue Manager</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" rowspan="3" valign="top">
                                <div class="report-table-image">
                                    <div class="report-table-image-item" @click="previewImage(index)"
                                        v-for="(thumb, index) in item.thumbnails" :key="index">
                                        <img :src="thumb.url" :alt="thumb.name">
                                    </div>
                                </div>
                            </td>
                            <td colspan="3" class="report-table-manager" valign="top">
                                <span>{{ item.resp }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="report-table-label" align="center">
                                <span>Next Step</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="report-table-next" valign="top">
                                <span>{{ item.next }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </ReportBox>
            <el-image-viewer v-if="showViewer" @close="showViewer = false" infinite :initial-index="viewerIndex"
                :url-list="viewerList" />
        </div>
    </Layout>
</template>
<script>
import ppt from '@/utils/ppt'
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
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.viewerList = this.item.thumbnails.map(n => n.url)
        })
    },
    methods: {
        editReport() {
            this.$goTo('vehicle.task_edit', { id: this.item.id })
        },
        goList() {
            this.$goTo('vehicle.task')
        },
        previewImage(i) {
            this.showViewer = true;
            this.viewerIndex = i
        },
        exportPpt() {

            let pres = ppt.init();
            pres.title(`${this.$status('engine_type', this.item.engine)} Engine Audit Report`)
            let rows = [
                [
                    { text: 'Finder', options: { valign: 'middle', align: 'right', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                    { text: this.item.auditor, options: { valign: 'middle', align: 'left' } },
                    { text: 'Assembly Line', options: { valign: 'middle', align: 'right', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                    { text: this.$status('assembly_line', this.item.line), options: { valign: 'middle', align: 'left' } },
                    { text: 'Issue Description', options: { colspan: 4, valign: 'middle', align: 'center', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                ],
                [
                    { text: 'Engine Type', options: { valign: 'middle', align: 'right', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                    { text: this.$status('engine_type', this.item.engine), options: { colspan: 3, valign: 'middle', align: 'left' } },
                    { text: this.item.description, options: { colspan: 4, rowspan: 3, valign: 'top', align: 'left', color: 'FFFFFF' } },
                ],
                [
                    { text: 'Purpose', options: { valign: 'middle', align: 'right', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                    { text: this.$status('purpose', this.item.purpose), options: { colspan: 3, valign: 'middle', align: 'left' } },
                ],
                [
                    { text: '8D No.', options: { valign: 'middle', align: 'right', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                    { text: this.item.eight, options: { valign: 'middle', align: 'left' } },
                    { text: 'Date', options: { valign: 'middle', align: 'right', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                    { text: this.$tool.dateFormat(this.item.created_at), options: { valign: 'middle', align: 'left' } },
                ],
                [
                    { text: 'Related Pictures', options: { valign: 'middle', align: 'left', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                    { text: 'Issue Level', options: { valign: 'middle', align: 'right', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                    { text: this.$status('defect_level', this.item.level), options: { valign: 'middle', align: 'left' } },
                    { text: 'Engine Number', options: { valign: 'middle', align: 'right', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                    { text: this.item.eb_number, options: { valign: 'middle', align: 'left' } },
                    { text: 'Resp. Dept./Issue Manager', options: { colspan: 3, valign: 'middle', align: 'center', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                ],
                [
                    { text: '', options: { colspan: 5, rowspan: 3, valign: 'middle', align: 'right', color: 'FFFFFF' } },
                    { text: this.item.resp, options: { valign: 'top', align: 'left', colspan: 3 } },
                ],
                [
                    { text: 'Next Step', options: { colspan: 3, valign: 'middle', align: 'center', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                ],
                [
                    { text: this.item.next, options: { colspan: 3, valign: 'top', align: 'left' } },
                ],
            ]
            ppt.table(rows, { w: 10, x: 0, y: 0.75, rowH: [0.2, 0.2, 0.2, 0.2, 0.2, 0.6, 0.2, 1.2], border: { type: 'solid', pt: '1', color: '27406A' }, color: '27406A', fontSize: '7' })
            this.item.thumbnails.forEach((n, i) => {
                ppt.addImage({
                    path: n.url,
                    w: 1,
                    h: 1,
                    x: 0.1968504 + (i * 0.1) + (i * 1),
                    y: 2
                })
            })
            ppt.footer('1/1 QM E&B | AFS')
            ppt.save()
        }
    }
}
</script>

<style lang="scss" scoped>
.report-table {
    width: 100%;
    border-color: var(--el-report-primary);

    td {
        border-color: var(--el-report-primary);
        height: 30px;
        padding: 0 10px;
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
        height: 120px !important;
    }

    &-manager {
        height: 60px !important;
    }

    &-image {
        width: 100%;
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: flex-start;
        height: 168px;

        &-item {
            width: 148px;
            height: 148px;
            overflow: hidden;
            cursor: pointer;
            margin-top: 12px;
            box-shadow: 1px 3px 5px var(--el-box-shadow);
            margin-right: 12px;

            img {
                width: 100%;
                height: 100%;
            }
        }
    }
}
</style>