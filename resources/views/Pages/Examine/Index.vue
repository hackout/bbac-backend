<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block">
            <div class="page-search">
                <div class="page-search-buttons">
                    <el-button type="primary" @click="exportData" icon="el-icon-download">导出</el-button>
                </div>
                <div class="page-search-form">
                    <el-form :model="query" ref="query" inline @submit.native.prevent="onSearch">
                        <el-form-item>
                            <el-select style="width:110px" v-model="query.type" @change="onSearch" placeholder="模板类型"
                                clearable>
                                <el-option v-for="(item, index) in examine_type" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-select style="width:110px" v-model="query.sub_type" @change="onSearch"
                                placeholder="考核类型" clearable>
                                <el-option v-for="(item, index) in typeList" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-select style="width:110px" v-model="query.engine" @change="onSearch" placeholder="发动机机型"
                                clearable>
                                <el-option v-for="(item, index) in engine_type" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-input v-model="query.keyword" style="width:160px" placeholder="关键词"></el-input>
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
            <DataTable ref="table" :apiName="$route('examine.list')" @change-page="query.page = $event"
                @change-page-size="query.limit = $event" height="570px" :params="query" stripe highlightCurrentRow>
                <el-table-column label="序号" align="center" prop="id" width="100">
                    <template #default="scope">
                        <span>{{ scope.$index + 1 < 10 ? '0' + (scope.$index + 1) : scope.$index + 1 }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="模板名称" align="center" prop="name" min-width="200"></el-table-column>
                <el-table-column label="模板类型" align="center" prop="type" width="100">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('examine_type', scope.row.type) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="考核类型" align="center" prop="sub_type" width="100">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('sub_type', scope.row.sub_type) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="发动机型号" align="center" prop="engine" width="100">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('engine_type', scope.row.engine) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="版本号" align="center" prop="version" width="100"></el-table-column>
                <el-table-column label="上个版本号" align="center" prop="last_version" width="150"></el-table-column>
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
                <el-table-column label="操作" align="center" prop="action" width="135" fixed="right">
                    <template #default="scope">
                        <el-button size="small" @click="viewDetail(scope.row)" type="primary" link>查看</el-button>
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
    </Layout>
</template>
<script>
export default {
    props: {
        examine_type: {
            type: Array,
            default: []
        },
        inline_type: {
            type: Array,
            default: []
        },
        product_type: {
            type: Array,
            default: []
        },
        service_type: {
            type: Array,
            default: []
        },
        sub_type: {
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
            query: {
                page: 1,
                limit: 20,
                type: '',
                sub_type: '',
                engine: '',
                keyword: ''
            }
        }
    },
    computed: {
        typeList() {
            let array = this.inline_type.concat(this.product_type.concat(this.service_type));
            if (this.query.type == 1) array = this.inline_type
            if (this.query.type == 2) array = this.product_type
            if (this.query.type == 3) array = this.service_type
            return this.sub_type.filter(n => array.indexOf(n.value) > -1)
        }
    },
    mounted() {
        this.$nextTick(() => { })
    },
    methods: {
        addItem() {
            this.chooseAddVisit = true
        },
        editItem(item) {
            this.editable = true
            this.chooseAddVisit = false
            this.$nextTick(() => {
                this.$refs.SaveDialog.open('edit', item.sub_type, item)
            })
        },
        viewItem(item) {
            this.viewable = true
            this.$nextTick(() => {
                this.$refs.ChangeDialog.open(item)
            })
        },
        importSuccess() {
            this.$message.success('导入信息成功')
            this.refreshData()
        },
        async deleteItem(item) {
            const res = await this.$axios.delete(this.$route('examine.delete', { id: item.id }))
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