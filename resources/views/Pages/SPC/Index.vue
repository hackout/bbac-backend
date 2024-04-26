<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block">
            <div class="page-search">
                <div class="page-search-buttons">
                    <el-button type="primary" @click="exportData" link icon="el-icon-download">导出数据</el-button>
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
                            <el-input v-model="query.keyword" style="width:160px" placeholder="螺栓编号/说明"></el-input>
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
            <DataTable ref="table" :apiName="$route('torque_item.list')" @change-page="query.page = $event"
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
                <el-table-column label="机型" align="center" prop="engine" width="100">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('engine_type', scope.row.engine) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="总成号" align="center" prop="assembly_id" min-width="150">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('assemblies', scope.row.assembly_id) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="螺栓编号" align="center" prop="number" width="150"></el-table-column>
                <el-table-column :label="month.name + '月份'" align="center" v-for="(month, index) in months" :key="index"
                    width="300">
                    <el-table-column label="备注" align="center" width="130">
                        <template #default="scope">
                            <span>{{ scope.row.months[month.name].remark }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column label="CP" align="center" prop="number" width="85">
                        <template #default="scope">
                            <span>{{ scope.row.months[month.name].cp }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column label="CPK" align="center" prop="number" width="85">
                        <template #default="scope">
                            <span>{{ scope.row.months[month.name].cpk }}</span>
                        </template>
                    </el-table-column>
                </el-table-column>
            </DataTable>
        </div>
    </Layout>
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
        months: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            query: {
                page: 1,
                limit: 20,
                plant: '',
                line: '',
                keyword: ''
            },
            items: [],
            editable: false,
            viewable: false
        }
    },
    mounted() {
        this.$nextTick(() => { })
    },
    methods: {
        async exportData() {
            var res = await this.$axios.post(this.$route('torque_item.export'), this.query)
            if (res.code == this.$config.successCode) {
                this.$message.success('数据正在打包中,3秒后自动下载');
                setTimeout(() => {
                    this.$download(res.data)
                }, 3000)
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