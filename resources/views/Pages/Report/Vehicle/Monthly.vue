<template>
    <Layout>
        <div class="printer-box">
            <div class="page-search">
                <div class="page-search-buttons">
                    <el-button @click="goList">返回</el-button>
                    <el-date-picker style="margin-left:10px;" v-model="month" type="month"
                        @change="changeDate" placeholder="选择日期">
                    </el-date-picker>
                </div>
                <div class="page-search-form">
                    <el-form ref="query" inline>
                        <el-form-item>
                            <el-button type="primary" @click="exportPPT" icon="el-icon-lightning">
                                <span>导出</span>
                            </el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            <div class="report" id="printer" v-loading="exporting" :element-loading-text="exportingText">
                <el-carousel @change="changeTab" ref="carousel" :initial-index="currentIndex" :autoplay="false"
                    height="689px" :loop="false" indicator-position="none">
                    <el-carousel-item v-for="(item, index) in items" :key="index">
                        <ReportBox modeName="vehicle" :title="`Q-Loop 4-Monthly Summary-CW${item.current}`"
                            :footer="`${index + 1}/${items.length} QM E&B | AFS`" direction="row">
                            <ReportBlock :blockStyle="{ width: '300px', marginRight: '10px' }" title="Summary">
                                <div class="report-left">
                                    <div class="report-left-tab">
                                        <span class="report-left-tab-item"
                                            :class="{ highlight: item.y == 2, preHighlight: item.y == 1 }">Y</span>
                                        <span class="report-left-tab-item"
                                            :class="{ highlight: item.m == 2, preHighlight: item.m == 1 }">M</span>
                                        <span class="report-left-tab-item"
                                            :class="{ highlight: item.w == 2, preHighlight: item.w == 1 }">W</span>
                                    </div>
                                    <div class="report-left-image">
                                        <span>{{ item.name }}</span>
                                        <img src="/assets/imgs/frame.png" alt="">
                                    </div>
                                    <div class="report-left-list">
                                        <div class="report-left-list-item">
                                            <span>Total Outbound Quantity</span>
                                            <span>{{ $tool.groupSeparator(item.ay4) }}</span>
                                        </div>
                                        <div class="report-left-list-item">
                                            <span>Monthly Outbound Quantity</span>
                                            <span>{{ $tool.groupSeparator(item.ax4) }}</span>
                                        </div>
                                        <div class="report-left-list-item">
                                            <span>PPM Target</span>
                                            <span>{{ $tool.groupSeparator(item.bb4) }}</span>
                                        </div>
                                        <div class="report-left-list-item">
                                            <span>PPM Issue Amount</span>
                                            <span>{{ $tool.groupSeparator(item.ar4) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </ReportBlock>
                            <div class="report-right">
                                <div class="report-right-top" style="height:288px;">
                                    <ReportBlock :blockStyle="{ width: '900px', marginRight: '10px', height: '285px' }"
                                        title="PPM Status">
                                        <Chart :ref="`ppmChart${index}`" :option="ppmOptions"></Chart>
                                    </ReportBlock>
                                    <ReportBlock :blockStyle="{ width: '360px', height: '285px' }"
                                        title="Root Cause Type">
                                        <Chart :ref="`typeChart${index}`" :option="typeOptions"></Chart>
                                    </ReportBlock>
                                </div>

                                <div class="report-table-box" style="margin-top:20px;border-bottom: 0;">
                                    <table class="report-table-box-item">
                                        <thead>
                                            <tr>
                                                <th width="7%" align="center">No.</th>
                                                <th width="13%" align="center">Qty</th>
                                                <th width="50%" align="center">Description</th>
                                                <th width="15%" align="center">Issue Type</th>
                                                <th width="15%" align="center">Root Cause</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(val, i) in item.eight" :key="i">
                                                <td width="7%" align="center">{{ i + 1 }}</td>
                                                <td width="13%" align="center">{{ val.qty }}</td>
                                                <td width="50%" align="center">{{ val.description }}</td>
                                                <td width="15%" align="center">{{ $status('issue_type', val.issue_type)
                                                    }}
                                                </td>
                                                <td width="15%" align="center">
                                                    {{ $status('root_cause_type', val.cause_type) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </ReportBox>
                    </el-carousel-item>
                </el-carousel>
            </div>
        </div>
    </Layout>
</template>
<script>
import { router } from "@inertiajs/vue3"
import ppt from "../../../../js/utils/ppt"
export default {
    props: {
        date: {
            type: String,
            default: ''
        },
        items: {
            type: Array,
            default: []
        },
        eb_type: {
            type: Array,
            default: []
        },
        root_cause_type: {
            type: Array,
            default: []
        },
        issue_type: {
            type: Array,
            default: []
        },
    },
    data() {
        return {
            month: '',
            currentIndex: 0,
            exporting: false,
            exportingText: '正在导出数据'
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.month = this.date
            this.changeDate()
        })
    },
    computed: {
        ppmOptions() {
            let result = {
                xAxis: {
                    type: 'category',
                    data: this.items[this.currentIndex].cwList.map(n => {
                        return n.name
                    })
                },
                yAxis: [{
                    type: 'value',
                }, {
                    type: 'value',
                    show: false
                }
                ],
                series: [
                    {
                        data: this.items[this.currentIndex].cwList.map(n => {
                            return n.sum
                        }),
                        type: 'bar',
                        itemStyle: {
                            color: '#335CA1',
                            borderRadius: [5, 5, 0, 0]
                        },
                        label: {
                            show: true,
                            color: '#FFF'
                        }
                    },
                    {
                        name: 'Temperature',
                        type: 'line',
                        yAxisIndex: 1,
                        tooltip: {
                            valueFormatter: function (value) {
                                return value + ' °C';
                            }
                        },
                        data: this.items[this.currentIndex].cwList.map(n => {
                            return n.count
                        }),
                        markLine: {
                            label: {
                                show: true
                            },
                            lineStyle: {
                                color: '#ED4235',
                            },
                            data: [{
                                type: 'average',
                                name: '平均值'
                            }]
                        }
                    }
                ]
            }

            return result;
        },
        typeOptions() {
            let result = {
                tooltip: {
                    trigger: 'item'
                },
                legend: {
                    show: false
                },
                series: [
                    {
                        name: 'Root Cause Type',
                        type: 'pie',
                        radius: ['40%', '70%'],
                        avoidLabelOverlap: false,
                        selectedMode: 'single',
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        },
                        label: {
                            textStyle: {
                                lineBreak: 10
                            },
                            show: true,
                            formatter: "{b|{d}%}\n{a|{b}}",
                            rich: {
                                a: {
                                    color: '#7f7f7f',
                                    lineHeight: 12,
                                    align: 'center',
                                    fontSize: 8
                                },
                                b: {
                                    fontSize: 14,
                                    fontWeight: 'bold',
                                    lineHeight: 16
                                }
                            },
                            color: 'inherit'
                        },
                        data: this.items[this.currentIndex].causeTypeList.map(n => {
                            return {
                                value: n.count,
                                name: n.name
                            }
                        })
                    }
                ]
            };
            return result;
        }
    },
    methods: {
        changeTab(val) {
            this.currentIndex = val
        },
        goList() {
            this.$goTo('report.vehicle')
        },
        changeDate() {
            router.reload({
                data: {
                    date: this.month
                }
            })
        },
        exportSlide(n, i, pres, theadStyle, valueStyle, rows) {
            this.$refs.carousel.setActiveItem(i)
            setTimeout(() => {
                this.$refs['ppmChart' + i][0].initUrl()
                this.$refs['typeChart' + i][0].initUrl()
                setTimeout(() => {
                    if (i > 0) {
                        ppt.addSlide()
                    }
                    pres.title("Q-Loop 4-Monthly Summary-CW"+ n.current)
                    ppt.addBlock('Summary', { x: 0, y: 0.562, w: 1.97 })
                    ppt.table([[
                        { text: ' ', options: { border: { type: 'solid', pt: '1', color: '27406A' }, w: '100%', h: 4.24 } }
                    ]], {
                        x: 0,
                        y: 0.9252,
                        w: 1.97,
                        h: 4.24,
                        fill: {
                            color: 'FFFFFF'
                        },
                        border: { type: 'solid', pt: '1', color: '27406A' }
                    })
                    ppt.addText('Y', {
                        shape: pres.pres.ShapeType.rect,
                        x: 0.4330708661,
                        y: 1.1811023622,
                        w: 0.1968503937,
                        h: 0.1968503937,
                        color: 'FFFFFF',
                        fontSize: '10',
                        align: 'center',
                        valign: 'middle',
                        rectRadius: 2,
                        fill: {
                            color: n.y == 1 ? 'e6a23c' : (n.y == 2 ? 'ED4235' : '34A755')
                        },
                    })
                    ppt.addText('M', {
                        shape: pres.pres.ShapeType.rect,
                        x: 0.8661417323,
                        y: 1.1811023622,
                        w: 0.1968503937,
                        h: 0.1968503937,
                        color: 'FFFFFF',
                        fontSize: '12',
                        align: 'center',
                        valign: 'middle',
                        rectRadius: 2,
                        fill: {
                            color: n.m == 1 ? 'e6a23c' : (n.m == 2 ? 'ED4235' : '34A755')
                        },
                    })
                    ppt.addText('W', {
                        shape: pres.pres.ShapeType.rect,
                        x: 1.2992125984,
                        y: 1.1811023622,
                        w: 0.1968503937,
                        h: 0.1968503937,
                        color: 'FFFFFF',
                        fontSize: '12',
                        align: 'center',
                        valign: 'middle',
                        rectRadius: 2,
                        fill: {
                            color: n.w == 1 ? 'e6a23c' : (n.w == 2 ? 'ED4235' : '34A755')
                        },
                    })
                    ppt.addText(n.name, {
                        y: 1.5748031496,
                        x: 0,
                        w: 1.97,
                        h: 0.1968503937,
                        fontSize: '10',
                        valign: 'middle',
                        align: 'center',
                        color: '313131'
                    })
                    ppt.addImage({
                        path: '/assets/imgs/frame.png',
                        x: 0.1968503937,
                        y: 1.7716535433,
                        w: 1.5748031496,
                        h: 1.1811023622
                    })
                    ppt.addShape({
                        w: 1.7716535433,
                        h: 0.3937007874,
                        x: 0.1181102362,
                        y: 3.1496062992,
                        border: { type: 'solid', pt: '1', color: '27406A' }
                    })
                    ppt.table([
                        [
                            { text: [{ text: 'Total Outbound Quantity\n', options: { fontSize: '8' } }, { text: this.$tool.groupSeparator(n.ay4), options: { fontSize: '9' } }], options: { border: { type: 'solid', pt: '0.5', color: '27406A' }, valign: "middle", w: '100%', h: 3 } },

                        ],
                        [
                            { text: [{ text: 'Monthly Outbound Quantity\n', options: { fontSize: '8' } }, { text: this.$tool.groupSeparator(n.ax4), options: { fontSize: '9' } }], options: { border: { type: 'solid', pt: '0.5', color: '27406A' }, valign: "middle", w: '100%', h: 3 } },

                        ],
                        [
                            { text: [{ text: 'PPM Target\n', options: { fontSize: '8' } }, { text: this.$tool.groupSeparator(n.bb4), options: { fontSize: '9' } }], options: { border: { type: 'solid', pt: '0.5', color: '27406A' }, valign: "middle", w: '100%', h: 3 } },
                        ],
                        [
                            { text: [{ text: 'PPM Issue Amount\n', options: { fontSize: '8' } }, { text: this.$tool.groupSeparator(n.ar4), options: { fontSize: '9' } }], options: { border: { type: 'solid', pt: '0.5', color: '27406A' }, valign: "middle", w: '100%', h: 3 } },
                        ]
                    ], {
                        x: 0.1023622,
                        y: 3.3937008,
                        w: 1.8,
                        h: 1.7,
                        fill: {
                            color: 'FFFFFF'
                        },
                        border: { type: 'solid', pt: '1', color: '27406A' }
                    })

                    ppt.addBlock('PPM Status', { x: 2, y: 0.562, w: 5.67 })
                    ppt.addImage({
                        data: this.$refs['ppmChart' + i][0].getUrl(),
                        x: 2,
                        y: 1,
                        w: 5.67,
                        h: 1.5590551,
                    })
                    ppt.addBlock('Root Cause Type', { x: 7.7, y: 0.562, w: 2.3 })
                    ppt.addImage({
                        data: this.$refs['typeChart' + i][0].getUrl(),
                        x: 7.7,
                        y: 1,
                        w: 2.3,
                        h: 1.5590551,
                    })
                    rows = [
                        [
                            Object.assign(this.$tool.objCopy(theadStyle), { text: 'No.', w: '7%' }),
                            Object.assign(this.$tool.objCopy(theadStyle), { text: 'Qty', w: '13%' }),
                            Object.assign(this.$tool.objCopy(theadStyle), { text: 'Description', w: '50%' }),
                            Object.assign(this.$tool.objCopy(theadStyle), { text: 'Issue Type', w: '15%' }),
                            Object.assign(this.$tool.objCopy(theadStyle), { text: 'Root Cause', w: '15%' }),
                        ]
                    ]
                    n.eight.forEach((x, z) => {
                        rows.push([
                            Object.assign(this.$tool.objCopy(valueStyle), { text: z + 1, w: '7%' }),
                            Object.assign(this.$tool.objCopy(valueStyle), { text: x.qty, w: '13%' }),
                            Object.assign(this.$tool.objCopy(valueStyle), { text: x.description, w: '50%' }),
                            Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('issue_type', x.issue_type), w: '15%' }),
                            Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('root_cause_type', x.cause_type), w: '15%' }),
                        ])
                    })
                    ppt.table(rows, {
                        y: 2.8,
                        x: 2,
                        w: 8
                    })
                    ppt.footer(`${i + 1}/${this.items.length} QM E&B | AFS`)
                    if (i + 1 == this.items.length) {
                        ppt.save()
                        this.exporting = false;
                        this.$refs.carousel.setActiveItem(0)
                        this.$message.success('导出数据成功')
                    }
                }, 300)
            }, 200)
        },
        exportPPT() {
            if(!this.exporting)
            {
                this.exporting = true
                let pres = ppt.init();
                let theadStyle = { text: 'No.', options: { valign: 'middle', fill: { color: '335CA1' }, border: { type: 'solid', pt: '1', color: '27406A' }, color: 'FFFFFF', w: '5%', h: 0.2, align: 'center', fontSize: '7' } };
                let valueStyle = { text: '-', options: { valign: 'middle', border: { type: 'solid', pt: '1', color: '27406A' }, color: '27406A', w: '5%', h: 0.2, align: 'center', fontSize: '7' } };
                let rows;
                this.items.forEach((n, i) => {
                    setTimeout(() => {
                        this.exportSlide(n, i, pres, theadStyle, valueStyle, rows)
                    }, 600 * (i + 1))
                })
            }
        }
    }
}
</script>

<style scoped lang="scss">
.report .el-carousel {
    --el-carousel-arrow-size: 48px;
    --el-carousel-arrow-font-size: 24px;
    --el-carousel-arrow-background: rgba(0, 0, 0, .75);
}

.report-right {
    flex: 1;
    display: flex;
    flex-direction: column;

    &-top {
        height: 380px;
        display: flex;
        flex-direction: row;
    }
}

.report-left {
    width: 100%;
    height: 100%;
    @extend .flexColumn;
    justify-content: space-between;
    background-color: var(--el-color-white);
    border: var(--el-report-primary) 1px solid;
    padding: 10px;

    &-tab {
        width: 100%;
        height: 60px;
        @extend .flexColumn;
        flex-direction: row;

        &-item {
            width: 40px;
            height: 30px;
            margin: 0 5px;
            border-radius: 5px;
            @extend .flexColumn;
            background-color: var(--el-color-success);
            color: var(--el-color-white);
            font-size: 16px;
            font-weight: 600;

            &.highlight {
                background-color: var(--el-color-danger);
            }

            &.preHighlight {
                background-color: var(--el-color-warning);
            }
        }
    }

    &-image {
        width: 100%;
        height: 220px;
        @extend .flexColumn;

        span {
            width: 100%;
            text-align: center;
            font-size: 16px;
            line-height: 24px;
        }

        img {
            width: 180px;
            height: 180px;
        }
    }

    &-list {
        width: 100%;
        @extend .flexColumn;

        &-item {
            height: 50px;
            width: 100%;
            border: var(--el-report-primary) 1px solid;
            border-top-width: 0;
            padding: 5px 10px;
            @extend .flexColumn;

            span {
                width: 100%;
                font-size: 15px;

                &:first-child {
                    font-size: 13px;
                }
            }

            &:first-child {
                border-top-width: 1px;
            }
        }
    }
}
</style>