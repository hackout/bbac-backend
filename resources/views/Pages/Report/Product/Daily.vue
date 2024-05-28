<template>
    <Layout>
        <div class="printer-box">
            <div class="page-search">
                <div class="page-search-buttons">
                    <el-button @click="goList">返回</el-button>
                    <el-date-picker style="margin-left:10px;" v-model="daily" type="date" @change="changeDate"
                        placeholder="选择日期">
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
            <div class="report" id="printer">
                <el-carousel :autoplay="false" height="689px" :loop="false" indicator-position="none">
                    <el-carousel-item>
                        <ReportBox height="689px" :title="title" :footer="`1/${sliderTotal} QM E&B | AFS`"
                            modeName="product">
                            <ReportBlock :blockStyle="{ width: '100%', height: 'auto', marginTop: '10px' }"
                                title="Daily Engine Product Audit Overview">
                                <div class="report-table-box">
                                    <table class="report-table-box-item">
                                        <colgroup>
                                            <col width="22.8%" />
                                            <col width="22.8%" />
                                            <col width="22.8%" />
                                            <col width="5.8%" />
                                            <col width="22.8%" />
                                            <col width="3%" />
                                        </colgroup>
                                        <thead>
                                            <tr>
                                                <th>Engine Number</th>
                                                <th>Audit Progress</th>
                                                <th>Assembly Progress</th>
                                                <th>Result</th>
                                                <th>Brief issue description</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(overview, index) in report.overviews" :key="index">
                                                <td align="center">{{ overview.number }}</td>
                                                <td align="center">
                                                    <div class="report-item-progress">
                                                        <span class="active"
                                                            :style="{ width: `${overview.audit_progress}%` }"></span>
                                                        <span>{{ overview.audit_progress }}%</span>
                                                    </div>
                                                </td>
                                                <td align="center">
                                                    <div class="report-item-progress">
                                                        <span class="active"
                                                            :style="{ width: `${overview.assembly_progress}%` }"></span>
                                                        <span>{{ overview.assembly_progress }}%</span>
                                                    </div>
                                                </td>
                                                <td align="center" v-if="overview.is_ok"><img
                                                        style="width:16px;height:16px;" src="/assets/imgs/checkin.png">
                                                </td>
                                                <td align="center" v-else>
                                                    <img style="width:16px;height:16px;"
                                                        src="/assets/imgs/checkout.png">
                                                    <span>{{ $status('defect_level', overview.defect_level) }}</span>
                                                </td>
                                                <td align="center">{{ overview.description }}
                                                </td>
                                                <td align="center">
                                                    <div class="report-status-point"
                                                        :class="{ success: overview.status == 3, warning: overview.status == 2, warning: overview.status == 4 || overview.status == 1 }"
                                                        style="margin:0;"></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="report-table-tip">
                                        <div class="report-table-tip-item">
                                            <span>Status</span>
                                        </div>
                                        <div class="report-table-tip-item">
                                            <span class="report-status-point border"></span>
                                            <span>Close</span>
                                        </div>
                                        <div class="report-table-tip-item">
                                            <span class="report-status-point preHighlight border"></span>
                                            <span>On Going</span>
                                        </div>
                                        <div class="report-table-tip-item">
                                            <span class="report-status-point highlight border"></span>
                                            <span>Open</span>
                                        </div>
                                    </div>
                                </div>
                            </ReportBlock>
                            <ReportBlock :blockStyle="{ width: '100%', height: 'auto', marginTop: '0px' }"
                                title="Score card trend">
                                <table border="1" class="report-table-box-item">
                                    <colgroup>
                                        <col width="150px" />
                                        <col />
                                        <col width="150px" />
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th>Engine Type</th>
                                            <th>Score Card Trend</th>
                                            <th>Score Card</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-for="(trend, index) in report.trends">
                                            <tr :key="index" v-if="index < 3">
                                                <td align="center">{{ trend.engine }}</td>
                                                <td align="center">
                                                    <div class="report-table-chart">
                                                        <Chart :ref="`trendChart${index}`" height="65px" width="100%"
                                                            :option="trendChart(trend.trend)"></Chart>
                                                    </div>
                                                </td>
                                                <td align="center">{{ trend.score }}</td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </ReportBlock>
                        </ReportBox>
                    </el-carousel-item>
                    <template v-for="(item, index) in report.items" :key="index">
                        <el-carousel-item>
                            <ReportBox height="689px"
                                :title="`${$status('engine_type', item.engine)} Engine Audit Report`"
                                :footer="`${(index * 2) + 2}/${sliderTotal} QM E&B | AFS`" modeName="product">
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
                                                        :checked="engine.value == item.engine" :key="index"
                                                        :label="engine.value">{{
                                                            engine.name }}</el-checkbox>
                                                </el-checkbox-group>
                                            </td>
                                            <td colspan="4" rowspan="3" valign="top">
                                                <span>{{ $status('defect_category', item.defect_description) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="report-table-label" align="right">
                                                <span>Purpose</span>
                                            </td>
                                            <td colspan="3">
                                                <el-checkbox-group readonly>
                                                    <el-checkbox v-for="(pur, index) in purpose"
                                                        :checked="pur.value == item.purpose" :key="index"
                                                        :label="pur.value">{{
                                                            pur.name }}</el-checkbox>
                                                </el-checkbox-group>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="report-table-label" align="right">
                                                <span>8D No.</span>
                                            </td>
                                            <td>
                                                <span>{{ item.eight_disciplines }}</span>
                                            </td>
                                            <td class="report-table-label" align="right">
                                                <span>Date</span>
                                            </td>
                                            <td>
                                                <span>{{ $tool.dateFormat(item.created_at, 'YYYY/MM/DD') }}</span>
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
                                                <span>{{ $status('defect_level', item.defect_level) }}</span>
                                            </td>
                                            <td class="report-table-label" align="right">
                                                <span>Engine Number</span>
                                            </td>
                                            <td>
                                                <span>{{ item.number }}</span>
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
                                                <span>{{ item.ira }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="report-table-label" align="center">
                                                <span>SOMA</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="report-table-next" valign="top">
                                                <span>{{ item.soma }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </ReportBox>
                        </el-carousel-item>
                        <el-carousel-item>
                            <ReportBox height="689px" title="M254-S2-E20-Miller PT1 Engine Product Audit Issue Overview"
                                :footer="`${(index * 2) + 3}/${sliderTotal} QM E&B | AFS`" modeName="product">
                                <ReportBlock :blockStyle="{ width: '100%', height: '280px', marginBottom: '0px' }"
                                    title="PA Quantity">
                                    <div class="report-table-box">
                                        <div class="report-items">
                                            <div class="report-items-item">
                                                <div class="report-items-item-title">PT2</div>
                                                <div class="report-items-item-content">
                                                    <div class="report-items-item-content-progress">
                                                        <div style="width:100px;"></div>
                                                        <span>1,999</span>
                                                    </div>
                                                    <div class="report-items-item-content-progress">
                                                        <div style="width:100px;"></div>
                                                        <span>1,39</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="report-items-item">
                                                <div class="report-items-item-title">PT1</div>
                                                <div class="report-items-item-content">
                                                    <div class="report-items-item-content-progress">
                                                        <div style="width:100px;"></div>
                                                        <span>1,999</span>
                                                    </div>
                                                    <div class="report-items-item-content-progress">
                                                        <div style="width:100px;"></div>
                                                        <span>1,39</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="report-table-tip flex-start">
                                            <div class="report-table-tip-item">
                                                <span class="report-status-point border purple"></span>
                                                <span>Audit Quantity</span>
                                            </div>
                                            <div class="report-table-tip-item">
                                                <span class="report-status-point primary border"></span>
                                                <span>On Going</span>
                                            </div>
                                        </div>
                                    </div>
                                </ReportBlock>
                                <ReportBlock :blockStyle="{ width: '100%', height: '300px' }"
                                    title="Issue summary during M254-S2-E20-Miller PT1  phase">
                                    <div class="report-table-box">
                                        <div class="report-season-box">
                                            <div class="report-season-box-items">
                                                <div class="report-season-box-items-item">
                                                    <div class="report-season-box-items-item-label">
                                                        <span>1. Thermostat screw torque low</span>
                                                    </div>
                                                    <div class="report-season-box-items-item-progress">
                                                        <div class="progress"></div>
                                                        <span>1</span>
                                                        <div class="report-status-point primary border"></div>
                                                    </div>
                                                </div>
                                                <div class="report-season-box-items-item">
                                                    <div class="report-season-box-items-item-label">
                                                        <span>1. Thermostat screw torque low</span>
                                                    </div>
                                                    <div class="report-season-box-items-item-progress">
                                                        <div class="progress"></div>
                                                        <span>1</span>
                                                        <div class="report-status-point primary border"></div>
                                                    </div>
                                                </div>
                                                <div class="report-season-box-items-item">
                                                    <div class="report-season-box-items-item-label">
                                                        <span>1. Thermostat screw torque low</span>
                                                    </div>
                                                    <div class="report-season-box-items-item-progress">
                                                        <div class="progress"></div>
                                                        <span>1</span>
                                                        <div class="report-status-point primary border"></div>
                                                    </div>
                                                </div>
                                                <div class="report-season-box-items-item">
                                                    <div class="report-season-box-items-item-label">
                                                        <span>1. Thermostat screw torque low</span>
                                                    </div>
                                                    <div class="report-season-box-items-item-progress">
                                                        <div class="progress"></div>
                                                        <span>1</span>
                                                        <div class="report-status-point primary border"></div>
                                                    </div>
                                                </div>
                                                <div class="report-season-box-items-item">
                                                    <div class="report-season-box-items-item-label">
                                                        <span>1. Thermostat screw torque low</span>
                                                    </div>
                                                    <div class="report-season-box-items-item-progress">
                                                        <div class="progress"></div>
                                                        <span>1</span>
                                                        <div class="report-status-point primary border"></div>
                                                    </div>
                                                </div>
                                                <div class="report-season-box-items-item">
                                                    <div class="report-season-box-items-item-label">
                                                        <span>1. Thermostat screw torque low</span>
                                                    </div>
                                                    <div class="report-season-box-items-item-progress">
                                                        <div class="progress"></div>
                                                        <span>1</span>
                                                        <div class="report-status-point primary border"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="report-season-box-pictures">
                                                <div class="report-season-box-pictures-item">
                                                    <img src="/assets/imgs/avatar.jpg" />
                                                    <span>1</span>
                                                </div>
                                                <div class="report-season-box-pictures-item">
                                                    <img src="/assets/imgs/avatar.jpg" />
                                                    <span>2</span>
                                                </div>
                                                <div class="report-season-box-pictures-item">
                                                    <img src="/assets/imgs/avatar.jpg" />
                                                    <span>3</span>
                                                </div>
                                                <div class="report-season-box-pictures-item">
                                                    <img src="/assets/imgs/avatar.jpg" />
                                                    <span>4</span>
                                                </div>
                                                <div class="report-season-box-pictures-item">
                                                    <img src="/assets/imgs/avatar.jpg" />
                                                    <span>5</span>
                                                </div>
                                                <div class="report-season-box-pictures-item">
                                                    <img src="/assets/imgs/avatar.jpg" />
                                                    <span>6</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="report-table-tip"
                                            style="height: 30px; padding: 10px 0;  margin-top: -10px;">
                                            <div class="report-table-tip-item">
                                                <span class="report-status-point square danger"></span>
                                                <span>A&B</span>
                                            </div>
                                            <div class="report-table-tip-item">
                                                <span class="report-status-point square warning"></span>
                                                <span>C</span>
                                            </div>
                                            <div class="report-table-tip-item">
                                                <span class="report-status-point square primary"></span>
                                                <span>U</span>
                                            </div>
                                            <div class="report-table-tip-item">
                                                <span>Status</span>
                                            </div>
                                            <div class="report-table-tip-item">
                                                <span class="report-status-point border success"></span>
                                                <span>Close</span>
                                            </div>
                                            <div class="report-table-tip-item">
                                                <span class="report-status-point border warning"></span>
                                                <span>On going</span>
                                            </div>
                                            <div class="report-table-tip-item">
                                                <span class="report-status-point border danger"></span>
                                                <span>Open</span>
                                            </div>
                                            <div class="report-table-tip-item">
                                                <span class="report-status-point border light"></span>
                                                <span>Verify</span>
                                            </div>
                                        </div>
                                    </div>
                                </ReportBlock>
                            </ReportBox>
                        </el-carousel-item>
                    </template>
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
        eb_type: {
            type: Array,
            default: []
        },
        engine_type: {
            type: Array,
            default: []
        },
        defect_level: {
            type: Array,
            default: []
        },
        defect_category: {
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
        },
        issue_status: {
            type: Array,
            default: []
        },
        date: {
            type: String,
            default: ''
        },
        report: {
            type: Object,
            default: {
                overviews: [],
                trends: [],
                items: [],
                issues: []
            }
        }
    },
    data() {
        return {
            title: `AFS _ PA Daily Report`,
            daily: '',
            sliderTotal: 1,
            assemblies: []
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.title = `AFS _ PA Daily Report – ${this.$tool.dateFormat(this.date, 'YYYY/MM/DD')}`
            this.daily = this.date
            this.assemblies = Object.keys(this.report.items)
            this.sliderTotal += this.assemblies.length * 2
            this.changeDate()
        })
    },
    methods: {
        trendChart(data) {
            return {
                grid: {
                    top: '5px',
                    left: '3%',
                    right: '15px',
                    bottom: '0px'
                },
                xAxis: {
                    show: false,
                    data: data
                },
                yAxis: {
                    type: 'value',
                    splitNumber: 2
                },
                series: [
                    {
                        data: data,
                        showSymbol: false,
                        type: 'line'
                    }
                ]
            }
        },
        goList() {
            this.$goTo('report.product')
        },
        changeDate() {
            router.reload({
                data: {
                    date: this.daily
                }
            })
        },
        exportPPT() {

            let pres = ppt.init('product');
            let theadStyle = { text: 'No.', options: { fill: { color: '335CA1' }, color: 'FFFFFF' } };
            let valueStyle = { text: '-', options: {} };
            pres.title(this.title)
            let x = 0, y = 0.925;
            let defaultStyle = { w: 10, x: 0, y: y, h: 0.2, color: '27406A', align: 'center', fontSize: '7', valign: 'middle', border: { type: 'solid', pt: '1', color: '27406A' } };
            let rows = []
            pres.addBlock('Daily Engine Product Audit Overview', { x: 0, y: 0.56, w: 10 })
            rows = [
                [
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Engine Number' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Audit Progress' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Assembly Progress' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Result' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Brief issue description' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Status' }),
                ]
            ]
            this.report.overviews.forEach(overview => {
                rows.push([
                    Object.assign(this.$tool.objCopy(valueStyle), { text: overview.number }),
                    Object.assign(this.$tool.objCopy(valueStyle), { text: '' }),
                    Object.assign(this.$tool.objCopy(valueStyle), { text: '' }),
                    Object.assign(this.$tool.objCopy(valueStyle), { text: overview.is_ok ? '' : this.$status('defect_level', overview.defect_level) }),
                    Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('defect_category', overview.description) }),
                    Object.assign(this.$tool.objCopy(valueStyle), { text: '' }),
                ])
            })
            rows.push([
                { text: ' ', options: { colspan: 6, fill: { color: 'FFFFFF' } } }
            ])
            ppt.table(rows, Object.assign(defaultStyle, { x: x, y: y, colW: [2.2, 2.2, 2.2, 0.6, 2.2, 0.6] }))

            this.report.overviews.forEach((overview, index) => {
                ppt.addSquare({
                    w: 2,
                    h: 0.14,
                    x: 2.3,
                    y: 1.1811024 + (index * 0.215),
                    fill: {
                        color: '335CA1'
                    }
                })
                if (overview.audit_progress > 0) {
                    ppt.addSquare({
                        w: 2 * overview.audit_progress / 100,
                        h: 0.14,
                        x: 2.3,
                        y: 1.1811024 + (index * 0.215),
                        fill: {
                            color: '67c23a'
                        }
                    })
                }
                ppt.addText(overview.audit_progress + '%', {
                    w: 2,
                    h: 0.14,
                    x: 2.3,
                    y: 1.1811024 + (index * 0.215),
                    color: 'FFFFFF',
                    align: 'center',
                    valign: 'middle',
                    fontSize: '6'
                })
                ppt.addSquare({
                    w: 2,
                    h: 0.14,
                    x: 4.5,
                    y: 1.1811024 + (index * 0.215),
                    fill: {
                        color: '335CA1'
                    }
                })
                if (overview.assembly_progress > 0) {
                    ppt.addSquare({
                        w: 2 * overview.assembly_progress / 100,
                        h: 0.14,
                        x: 4.5,
                        y: 1.1811024 + (index * 0.215),
                        fill: {
                            color: '67c23a'
                        }
                    })
                }
                ppt.addText(overview.assembly_progress + '%', {
                    w: 2,
                    h: 0.14,
                    x: 4.5,
                    y: 1.1811024 + (index * 0.215),
                    color: 'FFFFFF',
                    align: 'center',
                    valign: 'middle',
                    fontSize: '6'
                })
                ppt.addImage({
                    path: overview.is_ok ? '/assets/imgs/checkin.png' : '/assets/imgs/checkout.png',
                    w: 0.12,
                    h: 0.12,
                    x: overview.is_ok ? 6.85 : 6.65,
                    y: 1.1911024 + (index * 0.22)
                })
                ppt.addCircle({
                    w: 0.1,
                    h: 0.1,
                    x: 9.65,
                    y: 1.2011024 + (index * 0.225),
                    fill: { color: overview.status == 2 ? 'ED4235' : (overview.status == 1 ? 'e6a23c' : '67c23a') }
                })
                y += 0.33
            });
            ppt.addText("Status", {
                y: y,
                x: 4.4,
                w: 0.6,
                h: 0.2,
                fontSize: '7',
                color: '313131'
            });
            ppt.addText("Close", {
                y: y,
                x: 5,
                w: 0.6,
                h: 0.2,
                fontSize: '7',
                color: '313131'
            });
            ppt.addCircle({
                w: 0.1,
                h: 0.1,
                x: 4.95,
                y: y + 0.05,
                fill: { color: 'ED4235' }
            })
            ppt.addText("On Going", {
                y: y,
                x: 5.6,
                w: 0.6,
                h: 0.2,
                fontSize: '7',
                color: '313131'
            });
            ppt.addCircle({
                w: 0.1,
                h: 0.1,
                y: y + 0.05,
                x: 5.55,
                fill: { color: 'e6a23c' }
            })
            ppt.addText("Open", {
                y: y,
                x: 6.3,
                w: 0.6,
                h: 0.2,
                fontSize: '7',
                color: '313131'
            });
            ppt.addCircle({
                w: 0.1,
                h: 0.1,
                y: y + 0.05,
                x: 6.15,
                fill: { color: '67c23a' }
            })
            y += 0.3
            pres.addBlock('Score card trend', { x: 0, y: y, w: 10 })
            y += 0.35
            let rowH = [0.2]
            rows = [
                [
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Engine Type' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Score Card Trend' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Score Card' }),
                ]
            ]
            this.report.trends.forEach((trend, index) => {
                if (index < 3) {
                    this.$refs['trendChart' + index][0].initUrl()
                    rows.push([
                        Object.assign(this.$tool.objCopy(valueStyle), { text: trend.engine }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: '' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: trend.score + '' }),
                    ])
                    rowH.push(0.5)
                }
            })
            ppt.table(rows, Object.assign(defaultStyle, { x: x, y: y, rowH: rowH, colW: [1.5, 7, 1.5] }))
            this.report.trends.forEach((trend, index) => {
                if (index < 3) {
                    this.$refs['trendChart' + index][0].getUrl()
                    ppt.addImage({
                        data: this.$refs['trendChart' + index][0].getUrl(),
                        w: 6.8,
                        h: 0.393700787402,
                        x: 1.6,
                        y: y + 0.25 + (index * 0.5)
                    })
                }
            })
            ppt.table([
                [
                    { text: ' ', options: { fill: { color: 'FFFFFF' } } },
                ]
            ], Object.assign(defaultStyle, { x: 0, y: 0.55, rowH: 1.8 }))

            ppt.addText('PT1', {
                x: 0.1,
                y: 0.65,
                w: 1,
                h: 0.4,
                fontSize: '8'
            })
            ppt.addText('PT2', {
                x: 0.1,
                y: 1.1,
                w: 1,
                h: 0.4,
                fontSize: '8'
            })
            ppt.addText('PT3', {
                x: 0.1,
                y: 1.55,
                w: 1,
                h: 0.4,
                fontSize: '8'
            })
            ppt.addText("Audit Quantity", {
                y: 2,
                x: 6.3,
                w: 0.6,
                h: 0.1,
                fontSize: '7',
                color: '313131'
            });
            ppt.addCircle({
                w: 0.1,
                h: 0.1,
                y: 0.3,
                x: 6.15,
                fill: { color: '7030a0' }
            })
            ppt.addText("Audit Quantity", {
                y: 2,
                x: 6.3,
                w: 0.6,
                h: 0.1,
                fontSize: '7',
                color: '313131'
            });
            ppt.addCircle({
                w: 0.1,
                h: 0.1,
                y: 0.3,
                x: 6.15,
                fill: { color: '0e58c4' }
            })


            ppt.footer('1/' + this.sliderTotal + ' QM E&B | AFS')
            let i = 1;
            this.report.items.forEach((item, index) => {
                i++;
                ppt.addSlide()
                pres.title(this.$status('engine_type', item.engine) + " Engine Audit Report")
                rows = [
                    [
                        { text: 'Finder', options: { valign: 'middle', align: 'right', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                        { text: item.auditor, options: { valign: 'middle', align: 'left' } },
                        { text: 'Assembly Line', options: { valign: 'middle', align: 'right', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                        { text: this.$status('assembly_line', item.line), options: { valign: 'middle', align: 'left' } },
                        { text: 'Issue Description', options: { colspan: 4, valign: 'middle', align: 'center', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                    ],
                    [
                        { text: 'Engine Type', options: { valign: 'middle', align: 'right', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                        { text: this.$status('engine_type', item.engine), options: { colspan: 3, valign: 'middle', align: 'left' } },
                        { text: this.$status('defect_category', item.defect_description), options: { colspan: 4, rowspan: 3, valign: 'top', align: 'left', color: 'FFFFFF' } },
                    ],
                    [
                        { text: 'Purpose', options: { valign: 'middle', align: 'right', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                        { text: this.$status('purpose', item.purpose), options: { colspan: 3, valign: 'middle', align: 'left' } },
                    ],
                    [
                        { text: '8D No.', options: { valign: 'middle', align: 'right', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                        { text: item.eight_disciplines, options: { valign: 'middle', align: 'left' } },
                        { text: 'Date', options: { valign: 'middle', align: 'right', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                        { text: this.$tool.dateFormat(item.created_at), options: { valign: 'middle', align: 'left' } },
                    ],
                    [
                        { text: 'Related Pictures', options: { valign: 'middle', align: 'left', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                        { text: 'Issue Level', options: { valign: 'middle', align: 'right', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                        { text: this.$status('defect_level', item.defect_level), options: { valign: 'middle', align: 'left' } },
                        { text: 'Engine Number', options: { valign: 'middle', align: 'right', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                        { text: item.number, options: { valign: 'middle', align: 'left' } },
                        { text: 'Resp. Dept./Issue Manager', options: { colspan: 3, valign: 'middle', align: 'center', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                    ],
                    [
                        { text: '', options: { colspan: 5, rowspan: 3, valign: 'middle', align: 'right', color: 'FFFFFF' } },
                        { text: item.ira, options: { valign: 'top', align: 'left', colspan: 3 } },
                    ],
                    [
                        { text: 'SOMA', options: { colspan: 3, valign: 'middle', align: 'center', fill: { color: '335CA1' }, color: 'FFFFFF' } },
                    ],
                    [
                        { text: item.soma, options: { colspan: 3, valign: 'top', align: 'left' } },
                    ],
                ]
                y = 0.75

                ppt.table(rows, Object.assign(defaultStyle, { x: x, y: y, rowH: [0.2, 0.2, 0.2, 0.2, 0.2, 0.6, 0.2, 1.2], colW: '12.5%', border: { type: 'solid', pt: '1', color: '27406A' }, color: '27406A', fontSize: '7' }))
                item.thumbnails.forEach((n, i) => {
                    ppt.addImage({
                        path: n.url,
                        w: 1,
                        h: 1,
                        x: 0.1968504 + (i * 0.1) + (i * 1),
                        y: i >= 4 ? 3.15 : 2
                    })
                })
                ppt.footer(i + '/' + this.sliderTotal + ' QM E&B | AFS')
                ppt.addSlide()
                i++;
                pres.title(index + " Engine Product Audit Issue Overview")
                ppt.addShape({
                    w: 10,
                    h: 1,
                    fill: { color: 'FFFFFF' },
                    border: {
                        color: '313131',
                        pt: 1,
                        style: 'solid'
                    }, border: { type: 'solid', pt: '0.5', color: '27406A' }
                })
                ppt.footer(i + '/' + this.sliderTotal + ' QM E&B | AFS')
            })
            ppt.save()
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

.report-list {
    width: 100%;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    margin-top: 15px;
}

.report-item-statusBox {
    @extend .flexColumn;
    flex-direction: row;
    width: 100%;
    height: 100%;

    span {
        margin-left: 5px;
    }
}

.report-item-progress {
    width: 300px;
    height: 20px;
    background-color: var(--el-report-main);
    position: relative;
    @extend .flexColumn;
    color: var(--el-color-white);

    span {
        position: relative;
        z-index: 2;
    }

    .active {
        position: absolute;
        left: 0;
        top: 0;
        z-index: 1;
        width: var(--progress-width);
        height: 100%;
        background-color: var(--el-vehicle-success)
    }
}

.report-picture {
    width: 15%;
    height: 160px;
    margin-right: 2%;
    margin-bottom: 10px;

    &:nth-child(5n) {
        margin-right: 0;
    }

    &-title {
        background: var(--el-report-main);
        border: var(--el-report-primary) 1px solid;
        height: 30px;
        text-align: center;
        vertical-align: middle;
        color: var(--el-color-white);
        font-size: 14px;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
    }

    &-image {
        background: var(--el-report-main);
        border: var(--el-report-primary) 1px solid;
        border-top: 0;
        height: 130px;

        img {
            width: 100%;
            height: 100%;
        }
    }
}

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

.report-table-chart {
    width: 100%;
    height: 70px;
    padding: 5px;
    box-sizing: border-box;
}

.report-items {
    width: 100%;
    height: auto;
    padding: 5px 10px;
    box-sizing: border-box;
    background-color: var(--el-color-white);
    display: flex;
    flex-direction: column;

    &-item {
        width: 100%;
        height: 70px;
        @extend .flexColumn;
        flex-direction: row;

        &-title {
            width: 100px;
            height: 70px;
            @extend .flexColumn;
        }

        &-content {
            flex: 1;
            height: 80px;
            @extend .flexColumn;

            &-progress {
                width: 100%;
                height: 20px;
                position: relative;
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: flex-start;

                >div {
                    width: 20px;
                    height: 20px;
                    border-top-right-radius: 10px;
                    border-bottom-right-radius: 10px;
                    background-color: var(--el-report-purple);
                }

                &:last-child {
                    margin-top: 10px;

                    >div {
                        background-color: var(--el-report-main);
                    }
                }

                >span {
                    margin-left: 20px;
                    line-height: 20px;
                    height: 20px;
                }
            }
        }
    }
}

.report-season-box {
    height: 220px;
    width: 100%;
    @extend .flexColumn;
    flex-direction: row;
    background-color: var(--el-color-white);
    padding: 10px;

    &-items {
        flex: 1;
        height: 100%;
        box-sizing: border-box;
        @extend .flexColumn;

        &-item {
            width: 100%;
            height: 30px;
            padding: 5px;
            display: flex;
            box-sizing: border-box;
            flex-direction: row;

            &-label {
                width: 400px;
                height: 20px;
                line-height: 20px;
            }

            &-progress {
                flex: 1;
                height: 20px;
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: center;
                padding-right: 30px;

                .progress {
                    flex: 1;
                    height: 20px;
                    background-color: var(--el-report-main);
                    border-top-right-radius: 10px;
                    border-bottom-right-radius: 10px;

                    &.danger {
                        background-color: var(--el-color-danger);
                    }

                    &.warning {
                        background-color: var(--el-color-warning);
                    }
                }

                span {
                    margin-left: 15px;
                    text-align: center;
                    width: 40px;
                }
            }
        }
    }

    &-pictures {
        width: 320px;
        height: 100%;
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;

        &-item {
            width: 95px;
            height: 95px;
            margin-right: 10px;
            margin-bottom: 10px;
            position: relative;

            img {
                width: 100%;
                height: 100%;
            }

            span {
                position: absolute;
                bottom: 10px;
                left: 0;
                width: 100%;
                text-align: center;
                color: var(--el-color-white);
            }

            &:nth-child(3n) {
                margin-right: 0;
            }

            &:nth-child(4),
            &:nth-child(5),
            &:nth-child(6) {
                margin-bottom: 0;
            }
        }
    }
}

:deep(.report-block-title) {
    height: 40px;
}

:deep(.report-block-box) {
    margin-top: 5px;
    height: calc(100% - 40px);
}

:deep(.report-table-tip) {
    padding: 5px !important;
}
</style>