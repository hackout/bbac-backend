<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block DataTable">
            <el-table :data="items" border v-loading="loading" height="600px" stripe highlightCurrentRow>
                <el-table-column label="序号" align="center" prop="value" width="65">
                    <template #default="scope">
                        <span>{{ scope.$index + 1 }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="发动机机型" align="center" prop="name" width="100">
                    <template #default="scope">
                        <span>{{ $status('engine_type', scope.row.engine) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="文件名称" align="center">
                    <template #default="scope">
                        <el-link :href="scope.row.url" type="primary" size="small" target="_blank" v-if="scope.row.url">{{ scope.row.name
                            }}</el-link>
                        <el-upload v-else :action="$route('document.overhaul_update', { engine: scope.row.engine })"
                            :ref="`importUploadLink_${scope.row.engine}`" :on-success="importSuccess"
                            class="page-search-buttons-upload" :limit="1" :show-file-list="false"
                            :headers="uploadHeaders">
                            <el-button icon="el-icon-upload" link size="small" type="primary">导入文件</el-button>
                        </el-upload>
                    </template>
                </el-table-column>
                <el-table-column label="文件大小" prop="size" align="center" width="150">
                    <template #default="scope">
                        <span>{{ $tool.byteString(scope.row.size) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="最新操作者" align="center" width="150" prop="user"></el-table-column>
                <el-table-column label="最近操作时间" align="center" width="185">
                    <template #default="scope">
                        <div>
                            {{ $tool.dateFormat(scope.row.updated_at) }}
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="操作" align="center" prop="action" width="185" fixed="right">
                    <template #default="scope">
                        <el-upload :action="$route('document.overhaul_update', { engine: scope.row.engine })"
                            :ref="`importUpload_${scope.row.engine}`" :on-success="importSuccess"
                            class="page-search-buttons-upload" :limit="1" :show-file-list="false"
                            :headers="uploadHeaders">
                            <el-button icon="el-icon-upload" link v-if="!scope.row.url" type="primary">导入文件</el-button>
                            <el-button icon="el-icon-upload" link v-else type="primary">更新文件</el-button>
                        </el-upload>
                        <el-popconfirm title="确定删除此项?" v-if="scope.row.id.length > 0" @confirm="deleteItem(scope.row)">
                            <template #reference>
                                <el-button type="danger" link>
                                    <span>删除</span>
                                </el-button>
                            </template>
                        </el-popconfirm>
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </Layout>
</template>
<script>
export default {
    props: {
        engine_type: {
            type: Array,
            default: []
        }
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
            loading: false
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.uploadHeaders['X-XSRF-TOKEN'] = this.$tool.cookies.get('XSRF-TOKEN')
            this.getData()
        })
    },
    methods: {
        async getData() {
            this.loading = true
            var res = await this.$axios.get(this.$route('document.list', { type: 1 }));
            this.loading = false
            if (res.code == this.$config.successCode) {
                this.items = res.data
            }
        },
        async deleteItem(item) {
            var res = await this.$axios.delete(this.$route('document.delete', { id: item.id }))
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
        refreshData() {
            this.engine_type.forEach(n => {
                if (this.$refs[`importUpload_${n.value}`]) this.$refs[`importUpload_${n.value}`].clearFiles();
                if (this.$refs[`importUploadLink_${n.value}`]) this.$refs[`importUploadLink_${n.value}`].clearFiles();
            })
            this.getData()
        }
    }
}
</script>

<style scoped>
.DataTable :deep(.el-table) {
    --el-table-border-color: var(--el-border-light);
}

.DataTable :deep(th.el-table__cell) {
    background-color: var(--el-table-th-bg);
}

.DataTable :deep(thead) {
    color: var(--el-table-th);
}

.DataTable :deep(thead th) {
    font-weight: 200;
}

.DataTable :deep(.el-table__footer) .cell {
    font-weight: bold;
}

.DataTable :deep(.el-table__body-wrapper) .el-scrollbar__bar.is-horizontal {
    height: 12px;
    border-radius: 12px;
}

.DataTable :deep(.el-table__body-wrapper) .el-scrollbar__bar.is-vertical {
    width: 12px;
    border-radius: 12px;
}
</style>