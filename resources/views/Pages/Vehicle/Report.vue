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
                            <el-button type="primary" @click="exportPpt" icon="el-icon-lightning">
                                <span>导出</span>
                            </el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            <ReportBox modeName="vehicle" title="Engine & Battery Q-Loop4 Issue Report" footer="1/1 QM E&B | AFS">
                <table class="report-table" border="1" id="printer-table">
                    <colgroup>
                        <col v-for="i in 100" width="1%" />
                    </colgroup>
                    <thead>
                        <tr class="report-border">
                            <td colspan="4" class="report-table-left-border">
                                <div><span>No.</span></div>
                            </td>
                            <td colspan="6">
                                <div><span>Date</span></div>
                            </td>
                            <td colspan="5">
                                <div><span>Shift</span></div>
                            </td>
                            <td colspan="5">
                                <div><span>Plant</span></div>
                            </td>
                            <td colspan="5">
                                <div><span>Sensor</span></div>
                            </td>
                            <td colspan="5">
                                <div><span>V-Type</span></div>
                            </td>
                            <td colspan="5">
                                <div><span>E&B Type</span></div>
                            </td>
                            <td colspan="6">
                                <div><span>PN</span></div>
                            </td>
                            <td colspan="13">
                                <div><span>EN/BN</span></div>
                            </td>
                            <td colspan="14">
                                <div><span>Issue Description</span></div>
                            </td>
                            <td colspan="14">
                                <div><span>Root Cause</span></div>
                            </td>
                            <td colspan="14">
                                <div><span>SOMA</span></div>
                            </td>
                            <td colspan="4" class="report-table-right-border">
                                <div><span>Status</span></div>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="report-border">
                            <td colspan="4" class="report-table-left-border">
                                <div><span>1</span></div>
                            </td>
                            <td colspan="6">
                                <div><span>{{ $tool.dateFormat(item.created_at, 'YYYY-MM-DD') }}</span></div>
                            </td>
                            <td colspan="5">
                                <div><span>{{ $status('service_shift', item.shift) }}</span></div>
                            </td>
                            <td colspan="5">
                                <div><span>{{ $status('service_factory', item.plant) }}</span></div>
                            </td>
                            <td colspan="5">
                                <div><span>{{ $status('sensor_point', item.sensor) }}</span></div>
                            </td>
                            <td colspan="5">
                                <div><span>{{ $status('motorcycle_type', item.car_type) }}</span></div>
                            </td>
                            <td colspan="5">
                                <div><span>{{ $status('eb_type', item.eb_type) }}</span></div>
                            </td>
                            <td colspan="6">
                                <div><span>{{ item.product_number }}</span></div>
                            </td>
                            <td colspan="13">
                                <div><span>{{ item.eb_number }}</span></div>
                            </td>
                            <td colspan="14">
                                <div><span>{{ item.description }}</span></div>
                            </td>
                            <td colspan="14">
                                <div><span>{{ item.cause }}</span></div>
                            </td>
                            <td colspan="14">
                                <div><span>{{ item.soma }}</span></div>
                            </td>
                            <td colspan="4" class="report-table-right-border">
                                <div><span>{{ $status('issue_status', item.status) }}</span></div>
                            </td>
                        </tr>
                        <tr class="report-empty-20">
                            <td colspan="100"></td>
                        </tr>
                    </tbody>
                    <tbody class="picture" v-for="(list, index) in pictures" :key="index">
                        <tr>
                            <template v-for="(n, i) in list" :key="i">
                                <td colspan="10">
                                    <div class="picture-title">
                                        <span>Picture No.{{ index + 1 }}</span>
                                    </div>
                                </td>
                                <td colspan="2"></td>
                            </template>
                            <td :colspan="100 - (list.length * 12)"></td>
                        </tr>
                        <tr class="report-empty">
                            <td colspan="100"></td>
                        </tr>
                        <tr>
                            <template v-for="(n, i) in list" :key="i">
                                <td colspan="10">
                                    <div class="picture-image">
                                        <img :src="n.url" :alt="n.name" />
                                    </div>
                                </td>
                                <td colspan="2"></td>
                            </template>
                            <td :colspan="100 - (list.length * 12)"></td>
                        </tr>
                        <tr class="report-empty-20">
                            <td colspan="100"></td>
                        </tr>
                    </tbody>
                </table>
            </ReportBox>
        </div>
    </Layout>
</template>
<script>
import ppt from '@/utils/ppt'
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
            print: {
                id: 'printer',
                popTitle: 'Engine & Battery Q-Loop4 Issue Report'
            }
        }
    },
    mounted() {
        this.$nextTick(() => {
        })
    },
    computed: {
        pictures() {
            let pictures = []
            if (this.item.overview_attaches) {
                pictures = pictures.concat(this.item.overview_attaches)
            }
            if (this.item.master_overview_attaches) {
                pictures = pictures.concat(this.item.master_overview_attaches)
            }
            if (this.item.detail_attaches) {
                pictures = pictures.concat(this.item.detail_attaches)
            }
            if (this.item.master_detail_attaches) {
                pictures = pictures.concat(this.item.master_detail_attaches)
            }
            let result = new Array()
            let limit = 6
            let count = Math.ceil(pictures.length / limit)
            let skip = 0
            for (var i = 0; i < count; i++) {
                skip = i * limit
                result.push(pictures.slice(skip, skip + limit))
            }
            return result
        }
    },
    methods: {
        goList() {
            this.$goTo('vehicle.index')
        },
        exportExcel() {
            this.exportItem('excel')
        },
        exportPpt() {
            let pres = ppt.init();
            pres.title("Engine & Battery Q-Loop4 Issue Report")
            let theadStyle = { text: 'No.', options: { valign: 'middle', fill: { color: '335CA1' }, border: { type: 'solid', pt: '1', color: '27406A' }, color: 'FFFFFF', w: '5%', h: 0.2, align: 'center', fontSize: '7' } };
            let valueStyle = { text: '-', options: { valign: 'middle', border: { type: 'solid', pt: '1', color: '27406A' }, color: '27406A', w: '5%', h: 0.2, align: 'center', fontSize: '7' } };
            let rows = [
                [
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'No.', w: '4%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Date', w: '6%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Shift' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Plant' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Sensor' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'V-Type' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'E&B Type' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'PN', w: '6%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'EN/BN', w: '13%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Issue Description', w: '14%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Root Cause', w: '14%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'SOMA', w: '14%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Status', w: '4%' }),
                ],
                [
                    Object.assign(this.$tool.objCopy(valueStyle), { text: '1', w: '4%' }),
                    Object.assign(this.$tool.objCopy(valueStyle), { text: this.$tool.dateFormat(this.item.created_at, 'YYYY-MM-DD'), w: '6%' }),
                    Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('service_shift', this.item.shift) }),
                    Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('service_factory', this.item.plant) }),
                    Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('sensor_point', this.item.sensor) }),
                    Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('motorcycle_type', this.item.car_type) }),
                    Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('eb_type', this.item.eb_type) }),
                    Object.assign(this.$tool.objCopy(valueStyle), { text: this.item.product_number, w: '6%' }),
                    Object.assign(this.$tool.objCopy(valueStyle), { text: this.item.eb_number, w: '13%' }),
                    Object.assign(this.$tool.objCopy(valueStyle), { text: this.item.description, w: '14%' }),
                    Object.assign(this.$tool.objCopy(valueStyle), { text: this.item.cause, w: '14%' }),
                    Object.assign(this.$tool.objCopy(valueStyle), { text: this.item.soma, w: '14%' }),
                    Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('issue_status', this.item.status), w: '4%' }),
                ]
            ]
            ppt.table(rows, { w: 10, x: 0, y: 0.75 })
            let x = 0, y = 1.7716535;
            this.pictures.forEach((z) => {
                z.forEach((n, i) => {
                    ppt.item('Picture No.' + (i + 1), {
                        x: x, y: y, w: 1.5, h: 0.1968504,
                        valign: 'middle', fill: { color: '335CA1' }, color: 'FFFFFF', fontSize: '7',
                        border: { type: 'solid', pt: '1', color: '27406A' }, align: 'center'
                    }, {
                        path: n.url, h: 0.984252, w: 1.5, x: x, y: y + 0.1968504,
                        fill: { color: '335CA1' }, border: { type: 'solid', pt: '1', color: '27406A' }
                    })
                    x += 1.7
                })
                y += 2
            })
            ppt.footer('1/1 QM E&B | AFS')
            ppt.save()
        }
    }
}
</script>

<style scoped lang="scss">
.picture {
    .picture-title {
        height: 30px;
        width: 100%;
        background-color: var(--el-report-main);
        color: var(--el-color-white);
        font-size: 14px;
        border: 1px double var(--el-report-primary);
        display: flex;
        align-items: center;
        justify-content: center;

        td {
            background-color: var(--el-report-main);
        }
    }

    .picture-image {
        width: 100%;
        padding-bottom: 60%;
        position: relative;

        img {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            border: var(--el-report-primary) 1px solid;
        }
    }
}
</style>