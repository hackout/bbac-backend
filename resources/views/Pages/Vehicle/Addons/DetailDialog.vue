<template>
    <el-drawer title="考核检查详情" v-model="visitable" size="65vw" @closed="$emit('closed')">
        <DataTable :data="item.items">
            <el-table-column label="序号" align="center">
                <template #default="scope">
                    <span>{{ scope.$index + 1 }}</span>
                </template>
            </el-table-column>
            <el-table-column label="检查类型" align="center">
                <template #default="scope">
                    <span>{{ $status('examine_vehicle_item_type',scope.row.extra.type) }}</span>
                </template>
            </el-table-column>
            <el-table-column label="检查内容" align="center">
                <template #default="scope">
                    <span>{{ scope.row.extra.content }}</span>
                </template>
            </el-table-column>
            <el-table-column label="检查数量" align="center">
                <template #default="scope">
                    <span>{{ decode(scope.row.content).number }}</span>
                </template>
            </el-table-column>
            <el-table-column label="其他要求" align="center">
                <template #default="scope">
                    <span>{{ scope.row.extra.other }}</span>
                </template>
            </el-table-column>
        </DataTable>
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
        examine_vehicle_item_type: {
            type: Array,
            default: []
        }
    },
    emits: ['success', 'closed'],
    data() {
        return {
            assemblies: [],
            visitable: false,
            loading: false,
            item: {
                items: ''
            }
        }
    },
    methods: {
        open(item) {
            this.item = item
            this.visitable = true
        },
        decode(item){
            if(!item) return {
                status: false,
                number: '未提交'
            }
            return JSON.parse(item)
        }
    }
}
</script>

<style scoped lang="scss"></style>