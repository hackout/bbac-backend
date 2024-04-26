<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block">
            <div class="page-search">
                <div class="page-search-form">
                    <el-form ref="queryForm" :model="query" size="large" inline @submit.native.prevent="onSearch">
                        <el-form-item label="发生时间">
                            <el-date-picker v-model="query.date" type="daterange" range-separator="至"
                                start-placeholder="开始时间" end-placeholder="结束时间" @change="onSearch" />
                        </el-form-item>
                        <el-form-item label="关键词">
                            <el-input v-model="query.keyword" placeholder="端口/操作说明/路由"></el-input>
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
            <DataTable ref="table" :apiName="$route('user_log.list')" @change-page="query.page = $event"
                @change-page-size="query.limit = $event" height="580px" :params="query" stripe highlightCurrentRow remoteSort
                remoteFilter>
                <el-table-column type="expand">
                    <template #default="scope">
                        <el-scrollbar>
                            <div style="max-height:200px;margin:15px;">
                                <vue-json-pretty :data="scope.row.extra"></vue-json-pretty>
                            </div>
                        </el-scrollbar>
                    </template>
                </el-table-column>
                <el-table-column align="center" label="序号" prop="id" width="100">
                    <template #default="scope">
                        {{ scope.$index + 1 }}
                    </template>
                </el-table-column>
                <el-table-column align="center" label="操作人" prop="username" width="150"></el-table-column>
                <el-table-column align="center" label="端口" prop="os" width="150">
                    <template #default="scope">
                        <el-tag effect="dark" size="small">{{ scope.row.os == 'backend' ? '后台' : 'PAD' }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="center" show-overflow-tooltip label="操作说明" prop="description">
                    <template #default="scope">
                        <el-tooltip effect="dark" :content="scope.row.route" placement="top-end">
                            <span>{{ scope.row.name }}</span>
                        </el-tooltip>
                    </template>
                </el-table-column>
                <el-table-column align="center" label="请求方式" prop="method" width="100"></el-table-column>
                <el-table-column align="center" show-overflow-tooltip label="访问结果" prop="status" width="150">
                    <template #default="scope">
                        <el-tag type="success" v-if="scope.row.status" effect="dark" size="small">成功</el-tag>
                        <el-tag type="error" v-else effect="dark" size="small">失败</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="center" label="访问时间" prop="created_at" width="185" sortable='custom'>
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.created_at) }}</span>
                    </template>
                </el-table-column>
            </DataTable>
        </div>
    </Layout>
</template>
<script>
export default {
    data() {
        return {
            query: {
                page: 1,
                limit: 20,
                keyword: '',
                date: []
            },
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
        refreshData() {
            this.$nextTick(() => {
                this.$refs.table.refresh()
            })
        },
    }
}
</script>

<style></style>