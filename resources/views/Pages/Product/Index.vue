<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block">
            <div class="page-search">
                <div class="page-search-buttons">
                    <el-button type="primary" @click="addItem" icon="el-icon-plus">新增</el-button>
                    <el-upload :action="$route('product.import')" ref="importUpload" :on-success="importSuccess"
                        class="page-search-buttons-upload" :limit="1" :show-file-list="false" :headers="uploadHeaders">
                        <el-button icon="el-icon-upload" type="primary">导入</el-button>
                    </el-upload>
                    <el-button type="primary" @click="$goTo('product.template')" link
                        icon="el-icon-download">模板</el-button>
                </div>
                <div class="page-search-form">
                    <el-form :model="query" ref="query" inline @submit.native.prevent="onSearch">
                        <el-form-item>
                            <el-select style="width:110px" v-model="query.engine" @change="onSearch" placeholder="机型"
                                clearable filterable>
                                <el-option v-for="(item, index) in engine_type" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-select style="width:110px" v-model="query.assembly_id" @change="onSearch"
                                placeholder="总成号" clearable filterable>
                                <el-option v-for="(item, index) in assemblies" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-input v-model="query.keyword" style="width:160px" placeholder="发动机号"></el-input>
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
            <DataTable ref="table" :apiName="$route('product.list')" @change-page="query.page = $event"
                @change-page-size="query.limit = $event" height="550px" :params="query" stripe highlightCurrentRow>
                <el-table-column label="序号" align="center" prop="id" width="100">
                    <template #default="scope">
                        <span>{{ scope.$index + 1 < 10 ? '0' + (scope.$index + 1) : scope.$index + 1 }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="厂区" align="center" prop="plant" width="100">
                    <template #default="scope">
                        <span>{{ $status('plant', scope.row.plant) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="产线" align="center" prop="line" width="100">
                    <template #default="scope">
                        <span>{{ $status('line', scope.row.line) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="机型" align="center" prop="engine" width="100">
                    <template #default="scope">
                        <span>{{ $status('engine_type', scope.row.engine) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="项目阶段" align="center" prop="status" width="100">
                    <template #default="scope">
                        <span>{{ $status('status', scope.row.status) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="总成号" align="center" prop="assembly_id" width="185">
                    <template #default="scope">
                        <span>{{ $status('assemblies', scope.row.assembly_id) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="发动机号" align="center" prop="number" min-width="185"></el-table-column>
                <el-table-column label="接机时间" align="center" prop="beginning_at" width="185">
                    <template #default="scope">
                        <el-tag size="small">{{ $tool.dateFormat(scope.row.beginning_at) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="考核时间" align="center" prop="examine_at" width="185">
                    <template #default="scope">
                        <el-tag size="small">{{ $tool.dateFormat(scope.row.examine_at) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="试热时间" align="center" prop="qc_at" width="185">
                    <template #default="scope">
                        <el-tag size="small">{{ $tool.dateFormat(scope.row.qc_at) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="装配时间" align="center" prop="assembled_at" width="185">
                    <template #default="scope">
                        <el-tag size="small">{{ $tool.dateFormat(scope.row.assembled_at) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="添加时间" align="center" prop="created_at" width="185">
                    <template #default="scope">
                        <el-tag size="small">{{ $tool.dateFormat(scope.row.created_at) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="更新时间" align="center" prop="updated_at" width="185">
                    <template #default="scope">
                        <el-tag size="small">{{ $tool.dateFormat(scope.row.updated_at) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="操作" align="center" prop="action" width="165" fixed="right">
                    <template #default="scope">
                        <el-button size="small" @click="editItem(scope.row)" type="primary" link>编辑</el-button>
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
        <SaveDialog v-if="editable" @success="refreshData" @closed="editable = false" ref="SaveDialog" :plant="plant"
            :line="line" :status="status" :engine_type="engine_type">
        </SaveDialog>
    </Layout>
</template>
<script>
import SaveDialog from './Addons/SaveDialog.vue'
export default {
    components: {
        SaveDialog
    },
    props: {
        plant: {
            type: Array,
            default: []
        },
        line: {
            type: Array,
            default: []
        },
        status: {
            type: Array,
            default: []
        },
        engine_type: {
            type: Array,
            default: []
        },
        assemblies: {
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
                engine: '',
                assembly_id: '',
                keyword: ''
            },
            items: [],
            editable: false
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.uploadHeaders['X-XSRF-TOKEN'] = this.$tool.cookies.get('XSRF-TOKEN')
        })
    },
    methods: {
        addItem() {
            this.editable = true
            this.$nextTick(() => {
                this.$refs.SaveDialog.open('add')
            })
        },
        editItem(item) {
            this.editable = true
            this.$nextTick(() => {
                this.$refs.SaveDialog.open('edit', item)
            })
        },
        async deleteItem(item) {
            var res = await this.$axios.delete(this.$route('product.delete', { id: item.id }))
            this.deleting = false
            if (res.code == this.$config.successCode) {
                this.$message.success("删除成功")
                this.refreshData()
            } else {
                this.$message.error(res.message)
            }
        },
        importSuccess() {
            this.$message.success('导入信息成功')
            this.refreshData()
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

<style scoped></style>