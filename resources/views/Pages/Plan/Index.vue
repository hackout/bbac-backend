<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block">
            <div class="page-search">
                <div class="page-search-buttons">
                    <el-button type="primary" @click="addItem" icon="el-icon-plus">新增</el-button>
                    <el-divider direction="vertical" />
                    <el-button type="primary" @click="exportData" link icon="el-icon-download">导出</el-button>
                </div>
                <div class="page-search-form">
                    <el-form :model="query" ref="query" inline @submit.native.prevent="onSearch">
                        <el-form-item>
                            <el-select style="width:110px" v-model="query.plant" @change="onSearch" placeholder="工厂"
                                clearable>
                                <el-option v-for="(item, index) in plant" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-select style="width:120px" v-model="query.line" placeholder="产线" @change="onSearch"
                                clearable>
                                <el-option v-for="(item, index) in line" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-input v-model="query.keyword" style="width:160px" placeholder="备注信息"></el-input>
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
            <DataTable ref="table" :apiName="$route('plan.list')" @change-page="query.page = $event"
                @change-page-size="query.limit = $event" height="570px" :params="query" stripe highlightCurrentRow>
                <el-table-column label="序号" align="center" prop="id" width="100">
                    <template #default="scope">
                        <span>{{ scope.$index + 1 < 10 ? '0' + (scope.$index + 1) : scope.$index + 1 }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="工厂" align="center" prop="plant" width="100">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('plant', scope.row.plant) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="产线" align="center" prop="line" width="100">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('line', scope.row.line) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="机型" align="center" prop="type" width="100">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('engine_type', scope.row.type) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="总成号" align="center" prop="assembly_id" width="150">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('assemblies', scope.row.assembly_id) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="计划产量" align="center" prop="quantity" width="150"></el-table-column>
                <el-table-column label="计划时间" align="center" prop="plan_at" width="175">
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.plan_at) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="备注信息" align="center" prop="remark" min-width="150"></el-table-column>
                <el-table-column label="创建时间" align="center" prop="created_at" width="175">
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.created_at) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="更新时间" align="center" prop="updated_at" width="175">
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.updated_at) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="操作" align="center" prop="action" width="135" fixed="right">
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
            :line="line" :engine_type="engine_type">
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
        assemblies: {
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
        engine_type: {
            type: Array,
            default: []
        },
    },
    data() {
        return {
            uploadHeaders: {
                'X-XSRF-TOKEN': this.$tool.cookies.get('XSRF-TOKEN')
            },
            query: {
                page: 1,
                limit: 20,
                plant: '',
                line: '',
                keyword: ''
            },
            items: [],
            editable: false
        }
    },
    mounted() {
        this.$nextTick(() => { })
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
        async onSearch() {
            var validate = await this.$refs.query.validate().catch(() => { })
            if (!validate) return false;
            this.$nextTick(() => {
                this.$refs.table.upData(this.query)
            })
        },
        async exportData() {
            var res = await this.$axios.post(this.$route('plan.export'), this.query)
            if (res.code == this.$config.successCode) {
                this.$message.success('数据正在打包中,3秒后自动下载');
                setTimeout(() => {
                    this.$download(res.data)
                }, 3000)
            } else {
                this.$message.error(res.message)
            }
        },
        async deleteItem(item) {
            var res = await this.$axios.delete(this.$route('plan.delete', { id: item.id }))
            this.deleting = false
            if (res.code == this.$config.successCode) {
                this.$message.success("删除成功")
                this.refreshData()
            } else {
                this.$message.error(res.message)
            }
        },
        refreshData() {
            this.$refs.table.refresh()
        }
    }
}
</script>

<style scoped>
.el-form-item-msg {
    color: var(--el-link-color)
}
</style>