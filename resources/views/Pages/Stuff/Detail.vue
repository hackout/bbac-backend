<template>
    <Layout>
        <div class="printer-box">
            <div class="page-search">
                <div class="page-search-buttons">
                    <el-button @click="goList">返回</el-button>
                </div>
                <div class="page-search-form" style="align-items: center">
                    <el-form ref="query" inline>
                        <el-form-item>
                            <el-button type="primary" @click="goEdit" icon="el-icon-edit">
                                <span>维护报告</span>
                            </el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            <table class="report-table" border="1">
                <colgroup>
                    <col width="12.5%" />
                    <col width="12.5%" />
                    <col width="12.5%" />
                    <col width="12.5%" />
                    <col width="12.5%" />
                    <col width="12.5%" />
                    <col width="12.5%" />
                    <col width="12.5%" />
                </colgroup>
                <tbody>
                    <tr>
                        <td>
                            <span>产品/Prod.</span>
                        </td>
                        <td>
                            <span>{{ $status('engine_type', item.engine) }}</span>
                        </td>
                        <td>
                            <span>工厂/Plant.</span>
                        </td>
                        <td>
                            <span>{{ $status('plant', item.plant) }}</span>
                        </td>
                        <td>
                            <span>产线/Line.</span>
                        </td>
                        <td>
                            <span>{{ $status('assembly_line', item.line) }}</span>
                        </td>
                        <td>
                            <span>部门/Dept.</span>
                        </td>
                        <td>
                            <span>{{ item.department }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>标题/Title</span>
                        </td>
                        <td align="center" colspan="7">
                            <span>{{ item.name }}</span>
                        </td>
                    </tr>
                    <tr class="report-title">
                        <td colspan="8" align="center">
                            <span>发动机信息/Engine Data</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span>总成号/Assembly.</span>
                        </td>
                        <td colspan="2">
                            <span>{{ item.assembly }}</span>
                        </td>
                        <td colspan="2">
                            <span>发动机号/Engine No.</span>
                        </td>
                        <td colspan="2">
                            <span>{{ item.eb_number }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span>生产阶段/Phase.</span>
                        </td>
                        <td colspan="6">
                            <span>{{ $status('assembly_status', item.status) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span>转配日期/Assembly Date</span>
                        </td>
                        <td colspan="2">
                            <span>{{ $tool.dateFormat(item.examine_at) }}</span>
                        </td>
                        <td colspan="2">
                            <span>热试日期/QC Date</span>
                        </td>
                        <td colspan="2">
                            <span>{{ $tool.dateFormat(item.qc_at) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span>接机日期/Audit Beginning</span>
                        </td>
                        <td colspan="2">
                            <span>{{ $tool.dateFormat(item.beginning_at) }}</span>
                        </td>
                        <td colspan="2">
                            <span>交机日期/Audit End</span>
                        </td>
                        <td colspan="2">
                            <span>{{ $tool.dateFormat(item.assembled_at) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span>再装配检查/ReQualification</span>
                        </td>
                        <td colspan="2">
                            <span>{{ $tool.dateFormat(item.updated_at) }}</span>
                        </td>
                        <td colspan="2">
                            <span>考核员/Auditor</span>
                        </td>
                        <td colspan="2">
                            <span>{{ item.auditor }}</span>
                        </td>
                    </tr>
                    <tr class="report-title">
                        <td colspan="8" align="center">
                            <span>缺陷判定/Finding</span>
                        </td>
                    </tr>
                    <tr v-for="(issue,index) in issues" :key="index">
                        <td>
                            <span>位置/Location</span>
                        </td>
                        <td>
                            <span>M264</span>
                        </td>
                        <td>
                            <span>缺陷等级/Defect Class</span>
                        </td>
                        <td>
                            <span>M264</span>
                        </td>
                        <td>
                            <span>描达/Description</span>
                        </td>
                        <td colspan="3">
                            <span>M264</span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="middle">
                            <span>图片/Pictures</span>
                        </td>
                        <td colspan="7">
                            <span>M264</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </Layout>
</template>
<script>

export default {
    props: {
        item: {
            type: Object,
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
        },
        question_position: {
            type: Array,
            default: []
        },
        plant: {
            type: Array,
            default: []
        },
        assembly_status: {
            type: Array,
            default: []
        },
        issue_status: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            query: {
                page: 1,
                limit: 20,
                user_id: '',
                engine: '',
                keyword: '',
                date: ['', ''],
                type: 2
            },
            DetailDialogVisit: false,
            editable: false
        }
    },
    mounted() {
        this.$nextTick(() => { })
    },
    methods: {
        goEdit() {
            this.editable = true
        },
        goList() {
            history.go(-1)
        },
        previewItem() {
            this.$goTo('stuff.preview', { id: this.item.id });
        },
        async onSearch() {
            var validate = await this.$refs.query.validate().catch(() => { })
            if (!validate) return false;
            this.$nextTick(() => {
                this.$refs.table.upData(this.query)
            })
        },
        openDetail(item) {
            this.DetailDialogVisit = true
            this.$nextTick(() => {
                this.$refs.DetailDialog.open(item)
            })
        },
        refreshData() {
            this.editable = false
            this.$refs.table.refresh()
        }
    }
}
</script>

<style scoped>
:deep(.el-button > span) {
    display: inline-block;
}

table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
    border: #CCCCCC 1px solid;
    table-layout: fixed;
    background: #ffffff;

    tr {
        height: 50px;

        &.report-title {
            td {
                background-color: #EEEEEE;
            }
        }
    }

    td {
        border-color: #CCC;
        position: relative;
        padding: 4px 8px;
        font-size: 16px;
        box-sizing: border-box;
    }
}
</style>