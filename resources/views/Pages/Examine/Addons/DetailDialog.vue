<template>
    <el-drawer title="考核模板详情" v-model="visitable" :size="650" @closed="$emit('closed')">
        <el-descriptions title="基础信息" v-loading="loading" :column="1" border class="detail-box">
            <el-descriptions-item label="序号"><span>{{ item.id }}</span></el-descriptions-item>
            <el-descriptions-item label="模板名称"><span>{{ item.name }}</span></el-descriptions-item>
            <el-descriptions-item label="模板类型"><span>{{ types[type] }}</span></el-descriptions-item>
            <el-descriptions-item label="考核类型"><span>{{ $status('sub_type', item.sub_type)
                    }}</span></el-descriptions-item>
            <el-descriptions-item label="发动机机型"><span>{{ $status('engine_type', item.engine)
                    }}</span></el-descriptions-item>
            <el-descriptions-item label="检查项"><span>{{ item.items_count }}</span></el-descriptions-item>
            <el-descriptions-item label="版本号"><span>{{ item.version }}</span></el-descriptions-item>
            <el-descriptions-item label="上个版本号"><span>{{ item.last_version }}</span></el-descriptions-item>
            <el-descriptions-item label="版本状态"><span>{{ $status('template_status', item.status)
                    }}</span></el-descriptions-item>
            <el-descriptions-item label="创建人"><span>{{ item.author }}</span></el-descriptions-item>
            <el-descriptions-item label="最后更新人"><span>{{ item.user }}</span></el-descriptions-item>
            <el-descriptions-item label="最后更新时间"><span>{{ $tool.dateFormat(item.updated_at)
                    }}</span></el-descriptions-item>
        </el-descriptions>
        <div class="el-descriptions__title" style="margin: 15px 0;">
            <span>修改记录</span>
        </div>
        <el-table :data="item.approves" border class="DataTable" size="small" style="width: 100%;">
            <el-table-column label="修改人" prop="user" width="120px"></el-table-column>
            <el-table-column label="审批人" prop="approver" width="120px"></el-table-column>
            <el-table-column label="修改时间" align="center" prop="created_at" width="140px">
                <template #default="scope">
                    <span>{{ $tool.dateFormat(scope.row.created_at) }}</span>
                </template>
            </el-table-column>
            <el-table-column label="修改内容" align="right" prop="content"></el-table-column>
        </el-table>

        <template #footer>
            <div class="drawer-footer">
                <el-button @click="visitable = false">关闭</el-button>
            </div>
        </template>
    </el-drawer>
</template>

<script>
export default {
    props: {
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
        }
    },
    emits: ['closed'],
    data() {
        return {
            visitable: false,
            loading: false,
            item: {
                id: '',
                approves: [],
                type: '',
                sub_type: '',
                engine: '',
                items_count: '',
                version: '',
                last_version: '',
                status: '',
                author: '',
                user: '',
                updated_at: '',
            },
            commit: {},
            type: 'inline',
            types: {
                inline: '在线考核',
                product: '产品考核',
                service: '整车服务'
            }
        }
    },
    methods: {
        open(type, item) {
            this.commit = item
            this.type = type
            this.getData()
        },
        async getData() {
            this.loading = true
            var res = await this.$axios.get(this.$route('commit.detail', { id: this.commit.id }))
            this.loading = false
            if (res.code == this.$config.successCode) {
                this.item = res.data
                this.visitable = true
            } else {
                this.$message.error(res.message)
                this.$emit('closed')
            }
        }
    }
}
</script>

<style scoped lang="scss">
:deep(.el-descriptions__label) {
    width: 160px;
    font-weight: 200 !important;
}
.DataTable.el-table {
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