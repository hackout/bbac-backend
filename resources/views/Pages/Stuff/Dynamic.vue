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
                            <el-select style="width:120px" v-model="query.engine" placeholder="机型筛选" @change="onSearch"
                                clearable filterable>
                                <el-option v-for="(item, index) in engine_type" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-date-picker v-model="query.date" type="daterange" range-separator="至"
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
            <DataTable ref="table" :apiName="$route('stuff.list')" @change-page="query.page = $event"
                @change-page-size="query.limit = $event" height="550px" :params="query" stripe highlightCurrentRow>
                <el-table-column align="center" prop="id" width="80">
                    <template #header>
                        <span>序号</span><br /><span>No.</span>
                    </template>
                    <template #default="scope">
                        <span>{{ scope.$index + 1 }}</span>
                    </template>
                </el-table-column>
                <el-table-column align="center" prop="type" width="150">
                    <template #header>
                        <span>产品</span><br /><span>Product</span>
                    </template>
                    <template #default="scope">
                        <el-tag size="small">{{ $status('engine_type', scope.row.engine) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="center" prop="author" width="100">
                    <template #header>
                        <span>考核员</span><br /><span>Auditor.</span>
                    </template>
                </el-table-column>
                <el-table-column align="center" width="150">
                    <template #header>
                        <span>总成号</span><br /><span>Assembly No.</span>
                    </template>
                    <template #default="scope">
                        <span>{{ scope.row.assembly }}</span>
                    </template>
                </el-table-column>
                <el-table-column align="center" width="100">
                    <template #header>
                        <span>发动机号</span><br /><span>Engine No.</span>
                    </template>
                    <template #default="scope">
                        <span>{{ scope.row.eb_number }}</span>
                    </template>
                </el-table-column>
                <el-table-column align="center" prop="description" min-width="250">
                    <template #header>
                        <span>缺陷判定</span><br /><span>Finding.</span>
                    </template>
                    <template #default="scope">
                        <el-tag size="small">{{ $status('defect_category', scope.row.defect_category) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="center" prop="author" width="150">
                    <template #header>
                        <span>考核详情</span><br /><span>Audit Detail.</span>
                    </template>
                    <template #default="scope">
                        <el-text size="small" @click="openDetail(scope.row)" tag="ins" link>查看详情/Detail</el-text>
                    </template>
                </el-table-column>
                <el-table-column align="center" min-width="175">
                    <template #header>
                        <span>备注信息</span><br /><span>Remark</span>
                    </template>
                    <template #default="scope">
                        <span>{{ scope.row.remark }}</span>
                    </template>
                </el-table-column>
                <el-table-column align="center" width="175">
                    <template #header>
                        <span>考核时间</span><br /><span>Audit Date</span>
                    </template>
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.work_date) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="操作" align="center" prop="action" width="185" fixed="right">
                    <template #header>
                        <span>操作</span><br /><span>Operation</span>
                    </template>
                    <template #default="scope">
                        <el-button type="primary" size="small" @click="previewItem(scope.row)" link>
                            <span>预览</span><br />
                            <small>Preview</small>
                        </el-button>
                        <el-button type="primary" size="small" @click="viewItem(scope.row)" link>
                            <span>维护</span><br />
                            <small>Edit</small>
                        </el-button>
                        <el-popconfirm title="确定删除此项?" @confirm="deleteItem(scope.row)">
                            <template #reference>
                                <el-button type="primary" size="small" link>
                                    <span>删除</span><br />
                                    <small>Delete</small>
                                </el-button>
                            </template>
                        </el-popconfirm>
                    </template>
                </el-table-column>
            </DataTable>
        </div>
        <DynamicDialog ref="DynamicDialog" :examine_product_item_type="examine_product_item_type" v-if="DynamicDialogVisit"
            @success="refreshData" @closed="DynamicDialogVisit = false"></DynamicDialog>
    </Layout>
</template>
<script>
import DynamicDialog from './Addons/DynamicDialog.vue';
export default {
    components: {
        DynamicDialog
    },
    props: {
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
        examine_product_item_type: {
            type: Array,
            default: []
        },
        examine_type: {
            type: Array,
            default: []
        },
        status: {
            type: Array,
            default: []
        },
        plant: {
            type: Array,
            default: []
        },
        line: {
            type: Array,
            default: []
        },
        users: {
            type: Array,
            default: []
        },
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
                type: 1
            },
            DynamicDialogVisit: false
        }
    },
    mounted() {
        this.$nextTick(() => { })
    },
    methods: {
        viewItem(item) {
            this.$ajax.visit(this.$route('stuff.detail', { id: item.id }))
        },
        previewItem(item) {
            this.$ajax.visit(this.$route('stuff.preview', { id: item.id }))
        },
        async onSearch() {
            var validate = await this.$refs.query.validate().catch(() => { })
            if (!validate) return false;
            this.$nextTick(() => {
                this.$refs.table.upData(this.query)
            })
        },
        openDetail(item) {
            this.DynamicDialogVisit = true
            this.$nextTick(() => {
                this.$refs.DynamicDialog.open(item)
            })
        },
        refreshData() {
            this.editable = false
            this.$refs.table.refresh()
        },
        async deleteItem(item) {
            var res = await this.$axios.delete(this.$route('stuff.delete', { id: item.id }))
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