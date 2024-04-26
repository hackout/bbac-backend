<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block">
            <div class="page-search">
                <div class="page-search-buttons">
                    <div class="page-search-buttons-item">
                        <el-button icon="el-icon-plus" type="primary" @click="addItem">新增</el-button>
                        <el-divider direction="vertical" />
                        <el-button icon="el-icon-download" link @click="exportData" type="primary">导出</el-button>
                    </div>
                </div>
                <div class="page-search-form">
                    <el-form ref="queryForm" :model="listQuery" size="large" inline @submit.native.prevent="onSearch">
                        <el-form-item label="关键词">
                            <el-input v-model="listQuery.keyword" placeholder="名称/标识"></el-input>
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
            <DataTable ref="table" :apiName="$route('dict.list')" @change-page="listQuery.page = $event"
                @change-page-size="listQuery.limit = $event" :params="listQuery" stripe highlightCurrentRow remoteSort
                remoteFilter>
                <el-table-column label="序号" align="center" prop="id" width="100"></el-table-column>
                <el-table-column label="字典名称" align="center" prop="name" width="200"></el-table-column>
                <el-table-column label="字典类型" align="center" prop="code"></el-table-column>
                <el-table-column label="备注" align="center" prop="description"></el-table-column>
                <el-table-column label="创建时间" align="center" prop="created_at">
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.created_at) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="操作" align="center" prop="action" width="200">
                    <template #default="scope">
                        <el-button type="primary" @click="editItem(scope.row)" link>
                            <span>修改</span>
                        </el-button>
                        <el-button type="primary" @click="viewItem(scope.row)" link>
                            <span>配置项</span>
                        </el-button>
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
        <DictDialog v-if="dialogVisit" ref="formDialog" @success="refreshData" @closed="dialogVisit = false">
        </DictDialog>
        <ListDrawer v-if="viewVisit" ref="viewDialog" @closed="viewVisit = false"></ListDrawer>
    </Layout>
</template>
<script>
import DictDialog from './Addons/DictDialog.vue';
import ListDrawer from './Addons/ListDrawer.vue';
export default {
    components: {
        DictDialog,
        ListDrawer
    },
    props: {
        query: {
            type: Object,
            default: () => {
                return {
                    page: 1,
                    limit: 10,
                    keyword: null
                }
            }
        }
    },
    data() {
        return {
            listQuery: this.query,
            viewVisit: false,
            dialogVisit: false,
            form: {
                name: '',
                description: '',
                code: ''
            },
            formMode: 'add',
            formRules: {
                name: [
                    { required: true, message: '字典项名称不能为空', trigger: 'blur' }
                ],
                code: [
                    { required: true, message: '字典项标识不能为空', trigger: 'blur' }
                ]
            }
        }
    },
    mounted() {
        this.$nextTick(() => {

        })
    },
    methods: {
        async onSearch() {
            var validate = await this.$refs.queryForm.validate().catch(() => { })
            if (!validate) return false;
            this.$nextTick(() => {
                this.$refs.table.upData(this.queryForm)
            })
        },
        refreshData() {
            this.$nextTick(() => {
                this.$refs.table.refresh()
            })
        },
        addItem() {
            this.dialogVisit = true
            this.$nextTick(() => {
                this.$refs.formDialog.open('add')
            })
        },
        editItem(item) {
            this.dialogVisit = true
            this.$nextTick(() => {
                this.$refs.formDialog.open('edit', item)
            })
        },
        viewItem(item) {
            this.viewVisit = true
            this.$nextTick(() => {
                this.$refs.viewDialog.open(item)
            })
        },
        async deleteItem(item) {
            var res = await this.$axios.delete(this.$route('dict.delete', { id: item.id }));
            if (res.code == this.$config.successCode) {
                this.$message.success("删除字典成功");
                this.refreshData()
            } else {
                this.$message.error(res.message)
            }
        },
        async exportData() {
            var res = await this.$axios.post(this.$route('dict.export'), this.query)
            if (res.code == this.$config.successCode) {
                this.$message.success('数据正在打包中,3秒后自动下载');
                setTimeout(() => {
                    this.$download(res.data)
                }, 3000)
            } else {
                this.$message.error(res.message)
            }
        }
    }
}
</script>

<style></style>