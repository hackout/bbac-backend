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
                        <ReportBox modeName="vehicle" title="Engine & Battery Q-Loop4 Issue Daily Report"
                            footer="1/5 QM E&B | AFS">
                            <div class="report-list">
                                <div class="report-picture" v-for="(factory, index) in report.factories" :key="index">
                                    <div class="report-picture-title">
                                        <span class="report-status-point"
                                            :class="{ preHighlight: factory.status == 1, highlight: factory.status == 2 }"></span>
                                        <span>{{ factory.name }}</span>
                                    </div>
                                    <div class="report-picture-image">
                                        <img :src="factory.thumbnail ? factory.thumbnail : '/assets/imgs/car.png'" />
                                    </div>
                                </div>
                            </div>
                            <div class="report-list">
                                <div class="report-picture" v-for="(engine, index) in report.engines" :key="index">
                                    <div class="report-picture-title">
                                        <span class="report-status-point"
                                            :class="{ preHighlight: engine.status == 1, highlight: engine.status == 2 }"></span>
                                        <span>{{ engine.name }}</span>
                                    </div>
                                    <div class="report-picture-image">
                                        <img :src="engine.thumbnail ? engine.thumbnail : '/assets/imgs/car.png'" />
                                    </div>
                                </div>
                            </div>
                            <div class="report-table-box">
                                <table class="report-table-box-item">
                                    <thead>
                                        <tr>
                                            <th width="100px"></th>
                                            <th :width="`calc((100% - 100px) / ${report.engines.length})`"
                                                v-for="(engine, index) in report.engines" :key="index">
                                                <span>{{ engine.name }}</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td width="100px" align="center">
                                                <span>Target</span>
                                            </td>
                                            <td :width="`calc((100% - 100px) / ${report.engines.length})`"
                                                align="center" v-for="(engine, index) in report.engines" :key="index">
                                                <span>{{ getCount('target', engine.value) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100px" align="center">
                                                <span>YTD</span>
                                            </td>
                                            <td :width="`calc((100% - 100px) / ${report.engines.length})`"
                                                align="center" v-for="(engine, index) in report.engines" :key="index">
                                                <span class="report-status-text"
                                                    :class="{ preHighlight: getStatus('ytd', engine.value) == 1, highlight: getStatus('ytd', engine.value) == 2 }">{{
                                                        getCount('ytd', engine.value) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100px" align="center">
                                                <span>MTD</span>
                                            </td>
                                            <td :width="`calc((100% - 100px) / ${report.engines.length})`"
                                                align="center" v-for="(engine, index) in report.engines" :key="index">
                                                <span class="report-status-text"
                                                    :class="{ preHighlight: getStatus('mtd', engine.value) == 1, highlight: getStatus('mtd', engine.value) == 2 }">{{
                                                        getCount('mtd', engine.value) }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="report-table-tip">
                                    <div class="report-table-tip-item">
                                        <span class="report-status-point border"></span>
                                        <span>No Issue/Information</span>
                                    </div>
                                    <div class="report-table-tip-item">
                                        <span class="report-status-point preHighlight border"></span>
                                        <span>Pre-Highlight</span>
                                    </div>
                                    <div class="report-table-tip-item">
                                        <span class="report-status-point highlight border"></span>
                                        <span>Highlight</span>
                                    </div>
                                </div>
                            </div>
                        </ReportBox>
                    </el-carousel-item>
                    <el-carousel-item>
                        <ReportBox modeName="vehicle" title="Engine & Battery Q-Loop4 Issue Daily Report - Highlight"
                            footer="2/5 QM E&B | AFS">
                            <div class="report-table-box" style="margin-top:20px;border-bottom: 0;">
                                <table class="report-table-box-item">
                                    <thead>
                                        <tr>
                                            <th width="4%" align="center">No.</th>
                                            <th width="8%" align="center">Date</th>
                                            <th width="5%" align="center">Shift</th>
                                            <th width="5%" align="center">Plant</th>
                                            <th width="5%" align="center">Sensor</th>
                                            <th width="5%" align="center">V-Type</th>
                                            <th width="5%" align="center">E&B Type</th>
                                            <th width="8%" align="center">PN</th>
                                            <th width="15%" align="center">Issue Description</th>
                                            <th width="15%" align="center">Root Cause</th>
                                            <th width="15%" align="center">SOMA</th>
                                            <th width="5%" align="center">Daily QTY</th>
                                            <th width="5%" align="center">12 Month QTY</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in report.highlight" :key="index">
                                            <td width="4%" align="center">{{ index + 1 }}</td>
                                            <td width="8%" align="center">{{
                                                $tool.dateFormat(item.created_at, 'YYYY-MM-DD') }}</td>
                                            <td width="5%" align="center">{{ $status('service_shift', item.shift) }}
                                            </td>
                                            <td width="5%" align="center">{{ $status('service_factory', item.plant) }}
                                            </td>
                                            <td width="5%" align="center">{{ $status('sensor_point', item.sensor) }}
                                            </td>
                                            <td width="5%" align="center">{{ $status('motorcycle_type', item.car_type)
                                                }}
                                            </td>
                                            <td width="5%" align="center">{{ $status('eb_type', item.eb_type) }}</td>
                                            <td width="8%" align="center">{{ item.product_number }}</td>
                                            <td width="15%" align="center">{{ item.description }}</td>
                                            <td width="15%" align="center">{{ item.root_cause }}</td>
                                            <td width="15%" align="center">{{ item.soma }}</td>
                                            <td width="5%" align="center">{{ item.quantity }}</td>
                                            <td width="5%" align="center">{{ item.year_quantity }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="report-list">
                                <template v-for="(item, index) in report.highlight">
                                    <div class="report-picture" v-for="(n, ix) in item.pictures"
                                        :key="index + '_' + ix">
                                        <div class="report-picture-title">
                                            <span>Highlight #{{ index + 1 + ix }}</span>
                                        </div>
                                        <div class="report-picture-image">
                                            <img :src="n.url" :alt="n.name" />
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </ReportBox>
                    </el-carousel-item>
                    <el-carousel-item>
                        <ReportBox modeName="vehicle" title="Engine & Battery Q-Loop4 Issue Daily Report - PreHighlight"
                            footer="3/5 QM E&B | AFS">
                            <div class="report-table-box" style="margin-top:20px;border-bottom: 0;">
                                <table class="report-table-box-item">
                                    <thead>
                                        <tr>
                                            <th width="4%" align="center">No.</th>
                                            <th width="8%" align="center">Date</th>
                                            <th width="5%" align="center">Shift</th>
                                            <th width="5%" align="center">Plant</th>
                                            <th width="5%" align="center">Sensor</th>
                                            <th width="5%" align="center">V-Type</th>
                                            <th width="5%" align="center">E&B Type</th>
                                            <th width="8%" align="center">PN</th>
                                            <th width="15%" align="center">Issue Description</th>
                                            <th width="15%" align="center">Initial analysis</th>
                                            <th width="15%" align="center">Analysis action</th>
                                            <th width="5%" align="center">Daily QTY</th>
                                            <th width="5%" align="center">12 Month QTY</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in report.preHighlight" :key="index">
                                            <td width="4%" align="center">{{ index + 1 }}</td>
                                            <td width="8%" align="center">{{
                                                $tool.dateFormat(item.created_at, 'YYYY-MM-DD') }}</td>
                                            <td width="5%" align="center">{{ $status('service_shift', item.shift) }}
                                            </td>
                                            <td width="5%" align="center">{{ $status('service_factory', item.plant) }}
                                            </td>
                                            <td width="5%" align="center">{{ $status('sensor_point', item.sensor) }}
                                            </td>
                                            <td width="5%" align="center">{{ $status('motorcycle_type', item.car_type)
                                                }}</td>
                                            <td width="5%" align="center">{{ $status('eb_type', item.eb_type) }}</td>
                                            <td width="8%" align="center">{{ item.product_number }}</td>
                                            <td width="15%" align="center">{{ item.description }}</td>
                                            <td width="15%" align="center">{{ item.initial_analysis }}</td>
                                            <td width="15%" align="center">{{ item.initial_action }}</td>
                                            <td width="5%" align="center">{{ item.quantity }}</td>
                                            <td width="5%" align="center">{{ item.year_quantity }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="report-list">
                                <template v-for="(item, index) in report.preHighlight">
                                    <div class="report-picture" v-for="(n, ix) in item.pictures"
                                        :key="index + '_' + ix">
                                        <div class="report-picture-title">
                                            <span>Pre-Highlight #{{ index + 1 + ix }}</span>
                                        </div>
                                        <div class="report-picture-image">
                                            <img :src="n.url" :alt="n.name" />
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </ReportBox>
                    </el-carousel-item>
                    <el-carousel-item>
                        <ReportBox modeName="vehicle" title="Engine & Battery Q-Loop4 Issue Daily Report - Information"
                            footer="4/5 QM E&B | AFS">

                            <div class="report-table-box" style="margin-top:20px;border-bottom: 0;">
                                <table class="report-table-box-item">
                                    <thead>
                                        <tr>
                                            <th width="4%" align="center">No.</th>
                                            <th width="8%" align="center">Date</th>
                                            <th width="5%" align="center">Shift</th>
                                            <th width="5%" align="center">Plant</th>
                                            <th width="5%" align="center">Sensor</th>
                                            <th width="5%" align="center">V-Type</th>
                                            <th width="5%" align="center">E&B Type</th>
                                            <th width="8%" align="center">PN</th>
                                            <th width="15%" align="center">Issue Description</th>
                                            <th width="15%" align="center">Initial analysis</th>
                                            <th width="15%" align="center">Analysis action</th>
                                            <th width="5%" align="center">Daily QTY</th>
                                            <th width="5%" align="center">12 Month QTY</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in report.information" :key="index">
                                            <td width="4%" align="center">{{ index + 1 }}</td>
                                            <td width="8%" align="center">{{
                                                $tool.dateFormat(item.created_at, 'YYYY-MM-DD') }}</td>
                                            <td width="5%" align="center">{{ $status('service_shift', item.shift) }}
                                            </td>
                                            <td width="5%" align="center">{{ $status('service_factory', item.plant) }}
                                            </td>
                                            <td width="5%" align="center">{{ $status('sensor_point', item.sensor) }}
                                            </td>
                                            <td width="5%" align="center">{{ $status('motorcycle_type', item.car_type)
                                                }}</td>
                                            <td width="5%" align="center">{{ $status('eb_type', item.eb_type) }}</td>
                                            <td width="8%" align="center">{{ item.product_number }}</td>
                                            <td width="15%" align="center">{{ item.description }}</td>
                                            <td width="15%" align="center">{{ item.initial_analysis }}</td>
                                            <td width="15%" align="center">{{ item.initial_action }}</td>
                                            <td width="5%" align="center">{{ item.quantity }}</td>
                                            <td width="5%" align="center">{{ item.year_quantity }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="report-list">
                                <template v-for="(item, index) in report.information">
                                    <div class="report-picture" v-for="(n, ix) in item.pictures"
                                        :key="index + '_' + ix">
                                        <div class="report-picture-title">
                                            <span>Information #{{ index + 1 + ix }}</span>
                                        </div>
                                        <div class="report-picture-image">
                                            <img :src="n.url" :alt="n.name" />
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </ReportBox>
                    </el-carousel-item>
                    <el-carousel-item>
                        <ReportBox modeName="vehicle" title="Engine & Battery Q-Loop4 Issue Daily Report - Ongoing"
                            footer="5/5 QM E&B | AFS">
                            <div class="report-table-box" style="margin-top:20px;border-bottom: 0;">
                                <table class="report-table-box-item">
                                    <thead>
                                        <tr>
                                            <th width="4%" align="center">No.</th>
                                            <th width="8%" align="center">Date</th>
                                            <th width="5%" align="center">Shift</th>
                                            <th width="5%" align="center">Plant</th>
                                            <th width="5%" align="center">Sensor</th>
                                            <th width="5%" align="center">V-Type</th>
                                            <th width="5%" align="center">E&B Type</th>
                                            <th width="8%" align="center">PN</th>
                                            <th width="15%" align="center">Issue Description</th>
                                            <th width="15%" align="center">Initial analysis</th>
                                            <th width="15%" align="center">Analysis action</th>
                                            <th width="5%" align="center">Due Date</th>
                                            <th width="5%" align="center">Action Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in report.ongoing" :key="index">
                                            <td width="4%" align="center">{{ index + 1 }}</td>
                                            <td width="8%" align="center">{{
                                                $tool.dateFormat(item.created_at, 'YYYY-MM-DD') }}</td>
                                            <td width="5%" align="center">{{ $status('service_shift', item.shift) }}
                                            </td>
                                            <td width="5%" align="center">{{ $status('service_factory', item.plant) }}
                                            </td>
                                            <td width="5%" align="center">{{ $status('sensor_point', item.sensor) }}
                                            </td>
                                            <td width="5%" align="center">{{ $status('motorcycle_type', item.car_type)
                                                }}</td>
                                            <td width="5%" align="center">{{ $status('eb_type', item.eb_type) }}</td>
                                            <td width="8%" align="center">{{ item.product_number }}</td>
                                            <td width="15%" align="center">{{ item.description }}</td>
                                            <td width="15%" align="center">{{ item.initial_analysis }}</td>
                                            <td width="15%" align="center">{{ item.initial_action }}</td>
                                            <td width="5%" align="center">{{ $tool.dateFormat(item.due_date,
                                                'YYYY-MM-DD') }}
                                            </td>
                                            <td width="5%" align="center">
                                                <span class="status-tag"
                                                    :class="{ success: item.due_end > 5, warning: item.due_end <= 3 && item.due_end > 0, danger: item.due_end < 0 }"></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="report-list">
                                <template v-for="(item, index) in report.ongoing">
                                    <div class="report-picture" v-for="(n, ix) in item.pictures"
                                        :key="index + '_' + ix">
                                        <div class="report-picture-title">
                                            <span>Ongoing #{{ index + 1 + ix }}</span>
                                        </div>
                                        <div class="report-picture-image">
                                            <img :src="n.url" :alt="n.name" />
                                        </div>
                                    </div>
                                </template>
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
        report: {
            type: Object,
            default: {
                factories: [],
                engines: [],
                target: [],
                ytd: [],
                mtd: [],
                highlight: [],
                preHighlight: [],
                information: [],
                ongoing: []
            }
        }
    },
    data() {
        return {
            daily: ''
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.daily = this.date
            this.changeDate()
        })
    },
    methods: {
        getCount(type, value) {
            let items = this.report[type]
            let item = items.filter(n => n.value == value)
            return item[0].count + ''
        },
        getStatus(type, value) {
            let items = this.report[type]
            let item = items.filter(n => n.value == value)
            return item[0].status
        },
        goList() {
            this.$goTo('report.vehicle')
        },
        changeDate() {
            router.reload({
                data: {
                    date: this.daily
                },
                onSuccess: page => {
                    console.log(page)
                }
            })
        },
        exportPPT() {

            let pres = ppt.init();
            let theadStyle = { text: 'No.', options: { valign: 'middle', fill: { color: '335CA1' }, border: { type: 'solid', pt: '1', color: '27406A' }, color: 'FFFFFF', w: '5%', h: 0.2, align: 'center', fontSize: '7' } };
            let valueStyle = { text: '-', options: { valign: 'middle', border: { type: 'solid', pt: '1', color: '27406A' }, color: '27406A', w: '5%', h: 0.2, align: 'center', fontSize: '7' } };
            pres.title("Engine & Battery Q-Loop4 Issue Daily Report")
            let x = 0, y = 0.6299213;
            let rows = [];
            this.report.factories.forEach(n => {
                ppt.item(n.name,
                    {
                        x: x, y: y, w: 1.5,
                        h: 0.2362205, valign: 'middle', fill: { color: '335CA1' },
                        color: 'FFFFFF', fontSize: '8', border: { type: 'solid', pt: '1', color: '27406A' },
                        align: 'center'
                    }, {
                    path: n.thumbnail || '/assets/imgs/car.png', h: 0.8267717, w: 1.5,
                    x: x, y: y + 0.2362205,
                    fill: { color: '335CA1' }, border: { type: 'solid', pt: '1', color: '27406A' }
                })
                ppt.addCircle({
                    w: 0.1,
                    h: 0.1,
                    x: x + 0.4724409,
                    y: y + 0.0669291,
                    fill: { color: n.status == 2 ? 'ED4235' : (n.status == 1 ? 'e6a23c' : '67c23a') }
                })
                x += 1.75
            })
            x = 0
            y += 1.2
            this.report.engines.forEach(n => {
                ppt.item(n.name,
                    {
                        x: x, y: y, w: 1.2,
                        h: 0.2362205, valign: 'middle', fill: { color: '335CA1' },
                        color: 'FFFFFF', fontSize: '8', border: { type: 'solid', pt: '1', color: '27406A' },
                        align: 'center'
                    }, {
                    path: n.thumbnail || '/assets/imgs/car.png', h: 0.8267717, w: 1.2,
                    x: x, y: y + 0.2362205,
                    fill: { color: '335CA1' }, border: { type: 'solid', pt: '1', color: '27406A' }
                })
                ppt.addCircle({
                    w: 0.1,
                    h: 0.1,
                    x: x + 0.15,
                    y: y + 0.0669291,
                    fill: { color: n.status == 2 ? 'ED4235' : (n.status == 1 ? 'e6a23c' : '67c23a') }
                })
                x += 1.45
            })
            y += 1.35
            rows = [
                [
                    Object.assign(this.$tool.objCopy(theadStyle), { text: ' ' })
                ],
                [
                    Object.assign(this.$tool.objCopy(valueStyle), { text: 'Target' })

                ],
                [
                    Object.assign(this.$tool.objCopy(valueStyle), { text: 'YTD' })
                ],
                [
                    Object.assign(this.$tool.objCopy(valueStyle), { text: 'MTD' })
                ]
            ]
            this.report.engines.forEach(n => {
                rows[0].push(Object.assign(this.$tool.objCopy(theadStyle), { text: n.name, w: ((100 - 5) / this.report.engines.length) + '%', fontSize: '10' }))
                rows[1].push(Object.assign(this.$tool.objCopy(valueStyle), { text: this.getCount('target', n.value), w: ((100 - 5) / this.report.engines.length) + '%', fontSize: '10', color: '303133' }))
                rows[2].push(Object.assign(this.$tool.objCopy(valueStyle), { text: this.getCount('ytd', n.value), w: ((100 - 5) / this.report.engines.length) + '%', fontSize: '10', color: this.getStatus('ytd', n.value) == 1 ? 'e6a23c' : (this.getStatus('ytd', n.value) == 2 ? 'ED4235' : '303133') }))
                rows[3].push(Object.assign(this.$tool.objCopy(valueStyle), { text: this.getCount('mtd', n.value), w: ((100 - 5) / this.report.engines.length) + '%', fontSize: '10', color: this.getStatus('mtd', n.value) == 1 ? 'e6a23c' : (this.getStatus('mtd', n.value) == 2 ? 'ED4235' : '303133') }))
            })
            ppt.table(rows, { w: 10, x: 0, y: y })
            ppt.addSquare({
                w: '100%',
                h: 0.2362205,
                x: 0,
                y: 4.003937,
                fill: { color: 'FFFFFF' },
                border: { type: 'solid', pt: '1', color: '27406A' }
            })
            ppt.addCircle({
                w: 0.1,
                h: 0.1,
                x: 3.5433071,
                y: 4.0551181,
                fill: { color: '67c23a' },
                border: {
                    type: 'solid',
                    pt: 1,
                    color: '313131'
                }
            })
            ppt.addText('No Issue/Information', {
                w: 1.0708661,
                h: 0.0787402,
                x: 3.5748031,
                y: 4.0551181,
                fontSize: 7,
                color: '313131',
                align: 'center',
                valign: 'middle'
            })
            ppt.addCircle({
                w: 0.1,
                h: 0.1,
                x: 4.8744095,
                y: 4.0551181,
                fill: { color: 'e6a23c' },
                border: {
                    type: 'solid',
                    pt: 1,
                    color: '313131'
                }
            })
            ppt.addText('Pre-highlight', {
                w: 0.8346457,
                h: 0.0787402,
                x: 4.8744095,
                y: 4.0551181,
                fontSize: 7,
                color: '313131',
                align: 'center',
                valign: 'middle'
            })
            ppt.addCircle({
                w: 0.1,
                h: 0.1,
                x: 6.15,
                y: 4.0551181,
                fill: { color: 'ED4235' },
                border: {
                    type: 'solid',
                    pt: 1,
                    color: '313131'
                }
            })
            ppt.addText('Highlight', {
                w: 0.7401575,
                h: 0.0787402,
                x: 6.15,
                y: 4.0551181,
                fontSize: 7,
                color: '313131',
                align: 'center',
                valign: 'middle'
            })
            ppt.footer('1/5 QM E&B | AFS')
            ppt.addSlide()
            pres.title("Engine & Battery Q-Loop4 Issue Daily Report - Highlight")
            y = 0.6102362
            x = 0
            rows = [
                [
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'No.', w: '4%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Date', w: '8%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Shift' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Plant' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Sensor' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'V-Type' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'E&B Type' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'PN' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Issue Description', w: '18%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Root Cause', w: '15%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'SOMA', w: '15%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Daily QTY' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: '12 Month QTY' }),
                ]
            ]
            this.report.highlight.forEach((z, i) => {
                rows.push(
                    [
                        Object.assign(this.$tool.objCopy(valueStyle), { text: i + 1, w: '4%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$tool.dateFormat(z.created_at, 'YYYY-MM-DD'), w: '8%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('service_shift', z.shift) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('service_factory', z.plant) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('sensor_point', z.sensor) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('motorcycle_type', z.car_type) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('eb_type', z.eb_type) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.product_number }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.description, w: '18%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.root_cause, w: '15%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.soma, w: '15%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.quantity, w: '5%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.year_quantity, w: '5%' }),
                    ])
            })
            ppt.table(rows, { w: 10, x: 0, y: y })
            ppt.footer('2/5 QM E&B | AFS')
            ppt.addSlide()
            pres.title("Engine & Battery Q-Loop4 Issue Daily Report - PreHighlight")
            y = 0.6102362
            x = 0
            rows = [
                [
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'No.', w: '4%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Date', w: '8%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Shift' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Plant' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Sensor' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'V-Type' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'E&B Type' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'PN' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Issue Description', w: '18%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Initial analysis', w: '15%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Analysis action', w: '15%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Due Date' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Action Status' }),
                ]
            ]
            this.report.preHighlight.forEach((z, i) => {
                rows.push(
                    [
                        Object.assign(this.$tool.objCopy(valueStyle), { text: i + 1, w: '4%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$tool.dateFormat(z.created_at, 'YYYY-MM-DD'), w: '8%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('service_shift', z.shift) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('service_factory', z.plant) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('sensor_point', z.sensor) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('motorcycle_type', z.car_type) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('eb_type', z.eb_type) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.product_number }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.description, w: '18%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.initial_analysis, w: '15%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.initial_action, w: '15%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.quantity, w: '5%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.year_quantity, w: '5%' }),
                    ])
            })
            ppt.table(rows, { w: 10, x: 0, y: y })
            ppt.footer('3/5 QM E&B | AFS')
            ppt.addSlide()
            pres.title("Engine & Battery Q-Loop4 Issue Daily Report - Information")
            y = 0.6102362
            x = 0
            rows = [
                [
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'No.', w: '4%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Date', w: '8%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Shift' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Plant' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Sensor' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'V-Type' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'E&B Type' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'PN' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Issue Description', w: '18%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Initial analysis', w: '15%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Analysis action', w: '15%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Daily QTY' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: '12 Month QTY' }),
                ]
            ]
            this.report.information.forEach((z, i) => {
                rows.push(
                    [
                        Object.assign(this.$tool.objCopy(valueStyle), { text: i + 1, w: '4%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$tool.dateFormat(z.created_at, 'YYYY-MM-DD'), w: '8%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('service_shift', z.shift) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('service_factory', z.plant) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('sensor_point', z.sensor) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('motorcycle_type', z.car_type) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('eb_type', z.eb_type) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.product_number }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.description, w: '18%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.initial_analysis, w: '15%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.initial_action, w: '15%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.quantity, w: '5%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.year_quantity, w: '5%' }),
                    ])
            })
            ppt.table(rows, { w: 10, x: 0, y: y })
            ppt.footer('4/5 QM E&B | AFS')
            ppt.addSlide()
            pres.title("Engine & Battery Q-Loop4 Issue Daily Report - Ongoing")
            y = 0.6102362
            x = 0
            rows = [
                [
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'No.', w: '4%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Date', w: '8%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Shift' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Plant' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Sensor' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'V-Type' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'E&B Type' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'PN' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Issue Description', w: '18%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Root Cause', w: '15%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'SOMA', w: '15%' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: 'Daily QTY' }),
                    Object.assign(this.$tool.objCopy(theadStyle), { text: '12 Month QTY' }),
                ]
            ]
            this.report.ongoing.forEach((z, i) => {
                var _color = 'ED4235'
                if (z.due_end > 5) {
                    _color = '67c23a'
                }
                if (z.due_end <= 3) {
                    _color = 'e6a23c'
                }
                rows.push(
                    [
                        Object.assign(this.$tool.objCopy(valueStyle), { text: i + 1, w: '4%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$tool.dateFormat(z.created_at, 'YYYY-MM-DD'), w: '8%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('service_shift', z.shift) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('service_factory', z.plant) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('sensor_point', z.sensor) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('motorcycle_type', z.car_type) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$status('eb_type', z.eb_type) }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.product_number }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.description, w: '18%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.initial_analysis, w: '15%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: z.initial_action, w: '15%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: this.$tool.dateFormat(z.due_date, 'YYYY-MM-DD'), w: '5%' }),
                        Object.assign(this.$tool.objCopy(valueStyle), { text: '■', w: '5%', fill: { color: _color } }),
                    ])
            })
            ppt.table(rows, { w: 10, x: 0, y: y })
            ppt.footer('5/5 QM E&B | AFS')
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

</style>