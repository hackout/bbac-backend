<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block">
            <div class="page-search">
                <div class="page-search-form" style="width: 100%;">
                    <el-form :model="query" ref="query" inline @submit.native.prevent="onSearch">
                        <el-form-item>
                            <el-select style="width:120px" v-model="query.user_id" placeholder="考核员" @change="onSearch"
                                clearable filterable>
                                <el-option v-for="(item, index) in users" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-select style="width:120px" v-model="query.status" placeholder="问题状态" @change="onSearch"
                                clearable filterable>
                                <el-option v-for="(item, index) in issue_status" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-date-picker v-model="query.date" type="daterange" range-separator="至" @change="onSearch"
                                start-placeholder="开始日期" end-placeholder="结束日期" clearable />
                        </el-form-item>
                        <el-form-item>
                            <el-input v-model="query.keyword" style="width:200px" placeholder="发动机号/关键词"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" native-type="submit">
                                <span>查询</span>
                            </el-button>
                            <el-button native-type="reset">
                                <span>重置</span>
                            </el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            <DataTable ref="table" :apiName="$route('stuff.issue_list')" @change-page="query.page = $event"
                @change-page-size="query.limit = $event" height="550px" :params="query" stripe highlightCurrentRow>
                <el-table-column align="center" prop="id" width="80" label="序号">
                    <template #default="scope">
                        <span>{{ scope.$index + 1 }}</span>
                    </template>
                </el-table-column>
                <el-table-column align="center" prop="eb_number" min-width="225" label="发动机号"></el-table-column>
                <el-table-column align="center" prop="type" width="150" label="问题描述">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('defect_category', scope.row.defect_description) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="center" prop="type" width="150" label="问题零件">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('problem_parts', scope.row.defect_part) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="center" prop="author" width="100" label="考核员">
                </el-table-column>
                <el-table-column align="center" prop="soma" width="150" label="短期措施"></el-table-column>
                <el-table-column align="center" prop="lama" width="150" label="长期措施"></el-table-column>
                <el-table-column align="center" prop="eight_disciplines" width="150" label="8D NO."></el-table-column>
                <el-table-column align="center" prop="status" width="150" label="追踪状态">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('issue_status', scope.row.status) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="center" width="175" label="发生时间">
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.created_at) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="操作" align="center" prop="action" width="85" fixed="right">
                    <template #default="scope">
                        <el-button type="primary" size="small" @click="detailItem(scope.row)" link>
                            <span>详情</span>
                        </el-button>
                    </template>
                </el-table-column>
            </DataTable>
        </div>
        <SaveIssueDialog ref="SaveIssueDialog" :issue_status="issue_status" :defect_level="defect_level"
            :defect_category="defect_category" :problem_parts="problem_parts" :question_position="question_position"
            :exactly_1="exactly_1" :exactly_2="exactly_2" :exactly_3="exactly_3" :exactly_4="exactly_4"
            :exactly_5="exactly_5" :exactly_6="exactly_6" :exactly_7="exactly_7" :exactly_8="exactly_8"
            :exactly_9="exactly_9" :exactly_10="exactly_10" :exactly_11="exactly_11" :exactly_12="exactly_12"
            :exactly_13="exactly_13" :exactly_14="exactly_14" :exactly_15="exactly_15" :exactly_16="exactly_16"
            :exactly_17="exactly_17" :exactly_18="exactly_18" :exactly_19="exactly_19" :exactly_20="exactly_20"
            :exactly_21="exactly_21" :exactly_22="exactly_22" v-if="SaveIssueDialogVisit" @success="refreshData"
            @closed="SaveIssueDialogVisit = false"></SaveIssueDialog>
    </Layout>
</template>
<script>
import SaveIssueDialog from './Addons/SaveIssueDialog.vue';
export default {
    components: {
        SaveIssueDialog
    },
    props: {
        issue_status: {
            type: Array,
            default: []
        },
        users: {
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
        problem_parts: {
            type: Array,
            default: []
        },
        question_position: {
            type: Array,
            default: []
        },
        exactly_1: {
            type: Array,
            default: []
        },
        exactly_2: {
            type: Array,
            default: []
        },
        exactly_3: {
            type: Array,
            default: []
        },
        exactly_4: {
            type: Array,
            default: []
        },
        exactly_5: {
            type: Array,
            default: []
        },
        exactly_6: {
            type: Array,
            default: []
        },
        exactly_7: {
            type: Array,
            default: []
        },
        exactly_8: {
            type: Array,
            default: []
        },
        exactly_9: {
            type: Array,
            default: []
        },
        exactly_10: {
            type: Array,
            default: []
        },
        exactly_11: {
            type: Array,
            default: []
        },
        exactly_12: {
            type: Array,
            default: []
        },
        exactly_13: {
            type: Array,
            default: []
        },
        exactly_14: {
            type: Array,
            default: []
        },
        exactly_15: {
            type: Array,
            default: []
        },
        exactly_16: {
            type: Array,
            default: []
        },
        exactly_17: {
            type: Array,
            default: []
        },
        exactly_18: {
            type: Array,
            default: []
        },
        exactly_19: {
            type: Array,
            default: []
        },
        exactly_20: {
            type: Array,
            default: []
        },
        exactly_21: {
            type: Array,
            default: []
        },
        exactly_22: {
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
                status: '',
                keyword: '',
                date: ['', '']
            },
            SaveIssueDialogVisit: false
        }
    },
    mounted() {
        this.$nextTick(() => { })
    },
    methods: {
        async onSearch() {
            var validate = await this.$refs.query.validate().catch(() => { })
            if (!validate) return false;
            this.$nextTick(() => {
                this.$refs.table.upData(this.query)
            })
        },
        detailItem(item) {
            this.SaveIssueDialogVisit = true
            this.$nextTick(() => {
                this.$refs.SaveIssueDialog.open(item)
            })
        },
        refreshData() {
            this.editable = false
            this.$refs.table.refresh()
        },
        async deleteItem(item) {
            var res = await this.$axios.delete(this.$route('stuff_issue.delete', { id: item.id }))
            this.deleting = false
            if (res.code == this.$config.successCode) {
                this.$message.success("删除成功")
                this.refreshData()
            } else {
                this.$message.error(res.message)
            }
        }
    }
}
</script>

<style scoped>
.el-form-item-msg {
    color: var(--el-link-color)
}

:deep(.el-button > span) {
    display: inline-block;
}
</style>