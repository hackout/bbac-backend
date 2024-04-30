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
                            <el-button type="primary" @click="exportItem" icon="el-icon-lightning">
                                <span>导出</span>
                            </el-button>
                            <el-button type="primary" icon="el-icon-printer">
                                <span>打印</span>
                            </el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            <div class="report" id="printer">
                <table class="report-table" border="1" id="printer-table">
                    <colgroup>
                        <col v-for="i in 100" width="1%" />
                    </colgroup>
                    <thead class="report-title">
                        <tr>
                            <th></th>
                            <th colspan="3">
                                <div class="left"><img src="/assets/imgs/report_arrow.png" /></div>
                            </th>
                            <th colspan="72" align="left" valign="middle">Engine & Battery Q-Loop4 Issue Report</th>
                            <th colspan="24">
                                <div class="right"><img src="/assets/imgs/report_icon.png" /></div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="report-empty-20 report-border-bottom">
                            <td colspan="100"></td>
                        </tr>
                    </tbody>
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
                    <tfoot>
                        <tr class="report-page">
                            <td colspan="100">
                                <div>
                                    <span>1/5</span>
                                    <span>QM E&B | AFS</span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </Layout>
</template>
<script>
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
            },
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
        getPDF() {
            this.$tool.makePDF(
                document.querySelector("#pdf"),
                "Engine & Battery Q-Loop4 Issue Report"
            );
        },
        async exportItem() {
            let element = this.$tableParse.make('#printer-table')
            console.log(element)
            let res = await this.$axios.post(this.$route('custom.export'),{
                data: element
            })
            if(res.code == this.$config.successCode)
            {
                this.$message.success('报表正在打包中,3秒后自动下载');
                setTimeout(() => {
                    this.$download(res.data)
                }, 3000)
            }else{
                this.$message.error(res.message)
            }
            /**this.$tool.makeExcel('printer-table', (this.item.product_number + '-' + this.item.eb_number) + '.xlsx')**/
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