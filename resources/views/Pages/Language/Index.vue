<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block">
            <div class="page-search">
                <div class="page-search-buttons">
                    <div class="page-search-buttons-item">
                        <el-button icon="el-icon-plus" type="primary" @click="addItem">新增</el-button>
                        <el-divider direction="vertical" />
                        <el-upload :action="$route('locale_package.import')" ref="importUpload"
                            :on-success="importSuccess" class="page-search-buttons-upload" :limit="1"
                            :show-file-list="false" :headers="uploadHeaders">
                            <el-button icon="el-icon-upload" type="primary">导入</el-button>
                        </el-upload>
                        <el-button type="primary" @click="$goTo('locale_package.template', { type: 'other' })" link
                            icon="el-icon-download">模板</el-button>
                        <el-divider direction="vertical" />
                        <el-button icon="el-icon-download" link @click="exportData" type="primary">导出</el-button>
                    </div>
                </div>
                <div class="page-search-form">
                    <el-form ref="queryForm" :model="query" size="large" inline @submit.native.prevent="onSearch">
                        <el-form-item label="关键词">
                            <el-input v-model="query.keyword" placeholder="标识/中文/英文"></el-input>
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
            <DataTable ref="table" :apiName="$route('locale_package.list')" @change-page="query.page = $event"
                @change-page-size="query.limit = $event" :params="query" stripe highlightCurrentRow remoteSort
                remoteFilter>
                <el-table-column label="序号" align="center" prop="id" width="100">
                    <template #default="scope">
                        <span>{{ scope.$index + 1 }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="标识" align="center" prop="code" width="200"></el-table-column>
                <el-table-column label="中文" align="center" prop="content_zh"></el-table-column>
                <el-table-column label="英文" align="center" prop="content_en"></el-table-column>
                <el-table-column label="创建时间" align="center" prop="created_at" width="165">
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.created_at) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="操作" align="center" prop="action" width="200">
                    <template #default="scope">
                        <el-button type="primary" @click="editItem(scope.row)" link>
                            <span>修改</span>
                        </el-button>
                    </template>
                </el-table-column>
            </DataTable>
        </div>
        <SaveDialog v-if="dialogVisit" ref="formDialog" @success="refreshData" @closed="dialogVisit = false">
        </SaveDialog>
    </Layout>
</template>
<script>
import SaveDialog from './Addons/SaveDialog.vue';
export default {
    components: {
        SaveDialog
    },
    data() {
        return {
            uploadHeaders: {
                'X-XSRF-TOKEN': this.$tool.cookies.get('XSRF-TOKEN')
            },
            query: {
                limit: 20,
                page: 1,
                keyword: ''
            },
            dialogVisit: false,
        }
    },
    methods: {
        async onSearch() {
            var validate = await this.$refs.queryForm.validate().catch(() => { })
            if (!validate) return false;
            this.$nextTick(() => {
                this.$refs.table.upData(this.queryForm)
            })
        },
        importSuccess() {
            this.$message.success(`导入记录成功`)
            this.refreshData()
        },
        refreshData() {
            this.$refs.importUpload.clearFiles()
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
        async exportData() {
            var res = await this.$axios.post(this.$route('locale_package.export'), this.query)
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
