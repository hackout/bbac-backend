<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block">
            <div class="page-search">
                <div class="page-search-buttons">
                    <el-button type="primary" @click="addItem" icon="el-icon-plus">新增</el-button>
                    <el-upload :action="$route('commit_inline.import')" ref="importUpload" :on-success="importSuccess"
                        class="page-search-buttons-upload" :limit="1" :show-file-list="false" :headers="uploadHeaders">
                        <el-button icon="el-icon-upload" type="primary">导入</el-button>
                    </el-upload>
                    <el-divider direction="vertical" />
                    <el-button type="primary" @click="$goTo('commit_inline.template')" link
                        icon="el-icon-download">模板</el-button>
                </div>
                <div class="page-search-form">
                    <el-form :model="query" ref="query" inline @submit.native.prevent="onSearch">
                        <el-form-item>
                            <el-select style="width:110px" v-model="query.type" @change="onSearch" placeholder="考核类型"
                                clearable>
                                <el-option v-for="(item, index) in examine_inline_type" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-select style="width:110px" v-model="query.engine" @change="onSearch" placeholder="考核机型"
                                clearable>
                                <el-option v-for="(item, index) in engine_type" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-select style="width:110px" v-model="query.status" @change="onSearch" placeholder="审核状态"
                                clearable>
                                <el-option v-for="(item, index) in template_status" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-input v-model="query.keyword" style="width:160px" placeholder="关键词"
                                clearable></el-input>
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
            <DataTable ref="table" :apiName="$route('commit_inline.list')" @change-page="query.page = $event"
                @change-page-size="query.limit = $event" height="570px" :params="query" stripe highlightCurrentRow>
                <el-table-column label="序号" align="center" prop="id" width="100">
                    <template #default="scope">
                        <span>{{ scope.$index + 1 < 10 ? '0' + (scope.$index + 1) : scope.$index + 1 }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="模板名称" align="center" prop="name" min-width="200"></el-table-column>
                <el-table-column label="考核类型" align="center" prop="line" width="100">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('examine_inline_type', scope.row.type) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="发动机型号" align="center" prop="engine" width="100">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('engine_type', scope.row.engine) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="版本号" align="center" prop="version" width="100"></el-table-column>
                <el-table-column label="上个版本号" align="center" prop="last_version" width="150"></el-table-column>
                <el-table-column label="版本状态" align="center" prop="status" width="150">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('template_status', scope.row.status) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="创建人" align="center" prop="author" width="150"></el-table-column>
                <el-table-column label="创建时间" align="center" prop="created_at" width="175">
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.created_at) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="最后更新人" align="center" prop="user" width="100"></el-table-column>
                <el-table-column label="最后更新时间" align="center" prop="updated_at" width="175">
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.updated_at) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="操作" align="center" prop="action" width="285" fixed="right">
                    <template #default="scope">
                        <el-button size="small" v-if="scope.row.status == 0" @click="editItem(scope.row)" type="primary"
                            link>编辑</el-button>
                        <el-button size="small" @click="viewDetail(scope.row)" type="primary" link>查看</el-button>
                        <el-button size="small" @click="viewItem(scope.row)" type="primary" link>配置考核项</el-button>
                        <el-button size="small" v-if="scope.row.can_approve" @click="approveItem(scope.row)"
                            type="primary" link>送审批</el-button>
                        <el-popconfirm title="确定删除此项?" @confirm="deleteItem(scope.row)">
                            <template #reference>
                                <el-button type="danger" link>
                                    <span>删除</span>
                                </el-button>
                            </template>
                        </el-popconfirm>
                    </template>
                </el-table-column>
            </DataTable>
        </div>
        <el-dialog modal-class="chooseDialog" v-model="chooseAddVisit" :show-close="false">
            <el-row :gutter="20">
                <el-col :span="24 / examine_inline_type.length" v-for="(t, i) in examine_inline_type" :key="i">
                    <span @click="addItemByType(t.value)" class="col-text">{{ t.name }}</span>
                </el-col>
            </el-row>
        </el-dialog>
        <DetailDialog v-if="detailVisit" @success="refreshData" @closed="detailVisit = false" ref="DetailDialog"
            :examine_inline_item_type="examine_inline_item_type" :template_status="template_status"
            :engine_type="engine_type">
        </DetailDialog>
        <SaveDialog v-if="editable" @success="refreshData" @closed="editable = false" ref="SaveDialog"
            :engine_type="engine_type">
        </SaveDialog>
        <ApproveDialog v-if="approveVisit" @success="refreshData" @closed="approveVisit = false" ref="ApproveDialog">
        </ApproveDialog>
        <ItemDrawer v-if="viewable" @success="refreshData" @closed="viewable = false" ref="ItemDrawer"
            :special="special" :bolt_status="bolt_status" :bolt_model="bolt_model" :bolt_type="bolt_type"
            :examine_inline_type="examine_inline_type" :examine_inline_item_type="examine_inline_item_type">
        </ItemDrawer>
    </Layout>
</template>
<script>
import DetailDialog from './Addons/DetailDialog.vue'
import SaveDialog from './Addons/SaveDialog.vue'
import ItemDrawer from './Addons/ItemDrawer.vue'
import ApproveDialog from './Addons/ApproveDialog.vue'
export default {
    components: {
        DetailDialog,
        SaveDialog,
        ApproveDialog,
        ItemDrawer
    },
    props: {
        special: {
            type: Array,
            default: []
        },
        bolt_status: {
            type: Array,
            default: []
        },
        bolt_model: {
            type: Array,
            default: []
        },
        bolt_type: {
            type: Array,
            default: []
        },
        examine_inline_type: {
            type: Array,
            default: []
        },
        examine_inline_item_type: {
            type: Array,
            default: []
        },
        template_status: {
            type: Array,
            default: []
        },
        engine_type: {
            type: Array,
            default: []
        },

    },
    data() {
        return {
            uploadHeaders: {
                'X-XSRF-TOKEN': ''
            },
            query: {
                page: 1,
                limit: 20,
                type: '',
                status: '',
                keyword: ''
            },
            items: [],
            editable: false,
            viewable: false,
            detailVisit: false,
            chooseAddVisit: false,
            approveVisit: false
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.uploadHeaders['X-XSRF-TOKEN'] = this.$tool.cookies.get('XSRF-TOKEN')
        })
    },
    methods: {
        addItemByType(type) {
            this.editable = true
            this.chooseAddVisit = false
            this.$nextTick(() => {
                this.$refs.SaveDialog.open('add', type)
            })
        },
        addItem() {
            this.chooseAddVisit = true
        },
        viewDetail(item) {
            this.detailVisit = true
            this.$nextTick(() => {
                this.$refs.DetailDialog.open(item)
            })
        },
        approveItem(item) {
            this.approveVisit = true
            this.$nextTick(() => {
                this.$refs.ApproveDialog.open(item)
            })
        },
        editItem(item) {
            this.editable = true
            this.chooseAddVisit = false
            this.$nextTick(() => {
                this.$refs.SaveDialog.open('edit', item.type, item)
            })
        },
        viewItem(item) {
            this.viewable = true
            this.$nextTick(() => {
                this.$refs.ItemDrawer.open(item)
            })
        },
        importSuccess() {
            this.$message.success('导入信息成功')
            this.refreshData()
        },
        async deleteItem(item) {
            const res = await this.$axios.delete(this.$route('commit_inline.delete', { id: item.id }))
            if (res.code == this.$config.successCode) {
                this.$message.success('删除考核成功')
                this.refreshData()
            } else {
                this.$message.error(res.message)
            }
        },
        async onSearch() {
            var validate = await this.$refs.query.validate().catch(() => { })
            if (!validate) return false;
            this.$nextTick(() => {
                this.$refs.table.upData(this.query)
            })
        },
        refreshData() {
            this.items = []
            this.$refs.importUpload.clearFiles()
            this.$refs.table.refresh()
        }
    }
}
</script>

<style scoped lang="scss">
.col-text {
    width: 100%;
    height: 200px;
    @extend .flexColumn;
    font-size: 18px;
    border: var(--el-border-light) 1px solid;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
}
</style>
<style>
.chooseDialog .el-dialog__header {
    display: none !important;
}
</style>