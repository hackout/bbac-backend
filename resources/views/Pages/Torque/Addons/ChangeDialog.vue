<template>
    <el-dialog title="变更记录" v-model="visitable" width="750px" @closed="$emit('closed')">
        <el-table class="DataTable" :data="items" border>
            <el-table-column align="center" label="变更时间" prop="updated_at" width="175px">
                <template #default="scope">
                    <span>{{ $tool.dateFormat(scope.row.updated_at) }}</span>
                </template>
            </el-table-column>
            <el-table-column align="center" label="变更人" prop="user" width="100px"></el-table-column>
            <el-table-column align="center" label="涉及IO" prop="is_io" width="100px">
                <template #default="scope">
                    <el-tag size="small">{{ scope.row.is_io ? '是' : '否' }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column align="center" label="审批领导" prop="approver" width="100px"></el-table-column>
            <el-table-column align="center" label="变更内容" prop="content" min-width="130px">
                <template #default="scope">
                    <el-tag v-for="(extra, index) in scope.row.extra" :key="index">
                        <span>{{ fields[extra.field] }}:</span>
                        <el-text tag="del" type="danger">{{ extra.before }}</el-text>
                        <el-text>{{ extra.content }}</el-text>
                    </el-tag>
                </template>
            </el-table-column>
        </el-table>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="visitable = false">关闭</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script>
export default {
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
        motorcycle_type: {
            type: Array,
            default: []
        },
        model: {
            type: Array,
            default: []
        },
        type: {
            type: Array,
            default: []
        },
        status: {
            type: Array,
            default: []
        },
        stage: {
            type: Array,
            default: []
        },
        special: {
            type: Array,
            default: []
        },
    },
    emits: ['closed'],
    data() {
        return {
            visitable: false,
            loading: false,
            id: '',
            items: [],
            statusFields: [
                'plant',
                'line',
                'engine',
                'vehicle_type',
                'assembly_id',
                'model',
                'type',
                'status',
                'stage',
                'special',
            ],
            statusFieldKey: {
                plant: 'plant',
                line: 'line',
                engine: 'engine_type',
                vehicle_type: 'motorcycle_type',
                assembly_id: 'assemblies',
                model: 'model',
                type: 'type',
                status: 'status',
                stage: 'stage',
                special: 'special'
            },
            fields: {
                plant: '工厂',
                line: '产线',
                engine: '机型',
                vehicle_type: '车型',
                assembly_id: '总成号',
                number: '螺栓编号',
                content_zh: '中文描述',
                content_en: '英文描述',
                quantity: '请输入螺栓数量',
                model: '分类1',
                type: '分类2',
                status: '开放状态',
                stage: '项目阶段',
                station: '工位',
                sub_station: '工位2',
                special: '特殊特性',
                param: '参数',
                torque_target: '目标扭矩',
                torque_lower: '扭矩下限',
                torque_upper: '扭矩上限',
                angle_target: '角度标准',
                angle_lower: '角度下限',
                angle_upper: '角度上限',
                lasted_at: '最近放行时间',
                expected_at: '预计放行时间',
                final_at: '最终放行时间',
                start_torque: '起始扭矩',
                residual_torque: '转矩角',
                pfu_test: 'PFU测试值',
                pfu_lower: 'PFU考核下限',
                pfu_upper: 'PFU考核上限',
                pfu_early_lower: 'PFU预警上限',
                pfu_early_upper: 'PFU预警下限',
                l_pfu_test: 'L-PFU测试值',
                l_pfu_lower: 'L-PFU考核下限',
                l_pfu_upper: 'L-PFU考核上限',
                l_pfu_early_lower: 'L-PFU预警上限',
                l_pfu_early_upper: 'L-PFU预警下限'
            }
        }
    },
    methods: {
        async getChangeRecord() {
            let res = await this.$axios.get(this.$route('torque_change_record.list', { id: this.id }))
            if (res.code == this.$config.successCode) {
                this.items = res.data
            }
        },
        open(item) {
            this.id = item.id
            this.getChangeRecord()
            this.visitable = true
        }
    }
}
</script>

<style scoped lang="scss">
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

.el-text {
    margin: 0 4px;
}
</style>