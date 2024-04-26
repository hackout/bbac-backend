<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block">
            <div class="page-search">
                <div class="page-search-buttons">
                    <el-upload :action="$route('torque.import')" ref="importUpload" :on-success="importSuccess"
                        class="page-search-buttons-upload" :limit="1" :show-file-list="false" :headers="uploadHeaders">
                        <el-button icon="el-icon-upload" type="primary">导入</el-button>
                    </el-upload>
                    <el-divider direction="vertical" />
                    <el-button type="primary" @click="$goTo('torque.template')" link
                        icon="el-icon-download">模板</el-button>
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
            <DataTable ref="table" :apiName="$route('torque.list')" @change-page="query.page = $event"
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
                <el-table-column label="总成号" align="center" prop="assembly_id" width="150">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('assemblies', scope.row.assembly_id) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="车型" align="center" prop="vehicle_type" width="100">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('motorcycle_type', scope.row.vehicle_type) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="螺栓编号" align="center" prop="number" width="150"></el-table-column>
                <el-table-column label="描述-中文" align="center" prop="content_zh" width="150"></el-table-column>
                <el-table-column label="描述-英文" align="center" prop="content_en" width="150"></el-table-column>
                <el-table-column label="螺栓数量" align="center" prop="quantity" width="100"></el-table-column>
                <el-table-column label="螺栓分类1" align="center" prop="model" width="100">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('model', scope.row.model) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="螺栓分类2" align="center" prop="type" width="100">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('type', scope.row.type) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="放行状态" align="center" prop="status" width="100">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('status', scope.row.status) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="项目阶段" align="center" prop="stage" width="100">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('stage', scope.row.stage) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="工位" align="center" prop="station" width="125"></el-table-column>
                <el-table-column label="工位2" align="center" prop="sub_station" width="125"></el-table-column>
                <el-table-column label="特殊特性" align="center" prop="special" width="125">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('special', scope.row.special) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="螺栓参数" align="center" prop="param" width="125"></el-table-column>
                <el-table-column label="目标扭矩" align="center" prop="torque_target" width="125"></el-table-column>
                <el-table-column label="扭矩下限" align="center" prop="torque_lower" width="125"></el-table-column>
                <el-table-column label="扭矩上限" align="center" prop="torque_upper" width="125"></el-table-column>
                <el-table-column label="角度标准" align="center" prop="angle_target" width="125"></el-table-column>
                <el-table-column label="角度下限" align="center" prop="angle_lower" width="125"></el-table-column>
                <el-table-column label="角度上限" align="center" prop="angle_upper" width="125"></el-table-column>
                <el-table-column label="最近放行时间" align="center" prop="lasted_at" width="175">
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.lasted_at) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="预计放行时间" align="center" prop="expected_at" width="175">
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.expected_at) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="最终放行时间" align="center" prop="final_at" width="175">
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.final_at) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="起始扭矩" align="center" prop="start_torque" width="125"></el-table-column>
                <el-table-column label="转矩角" align="center" prop="residual_torque" width="125"></el-table-column>
                <el-table-column label="PFU测试值" align="center" prop="pfu_test" width="125"></el-table-column>
                <el-table-column label="PFU考核下限" align="center" prop="pfu_lower" width="125"></el-table-column>
                <el-table-column label="PFU考核上限" align="center" prop="pfu_upper" width="125"></el-table-column>
                <el-table-column label="PFU预警上限" align="center" prop="pfu_early_lower" width="125"></el-table-column>
                <el-table-column label="PFU预警下限" align="center" prop="pfu_early_upper" width="125"></el-table-column>
                <el-table-column label="L-PFU测试值" align="center" prop="l_pfu_test" width="125"></el-table-column>
                <el-table-column label="L-PFU考核下限" align="center" prop="l_pfu_lower" width="125"></el-table-column>
                <el-table-column label="L-PFU考核上限" align="center" prop="l_pfu_upper" width="125"></el-table-column>
                <el-table-column label="L-PFU预警上限" align="center" prop="l_pfu_early_lower"
                    width="125"></el-table-column>
                <el-table-column label="L-PFU预警下限" align="center" prop="l_pfu_early_upper"
                    width="125"></el-table-column>
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
                        <el-button size="small" @click="editItem(scope.row)" type="primary" link>变更</el-button>
                        <el-button size="small" @click="viewItem(scope.row)" type="primary" link>变更记录</el-button>
                    </template>
                </el-table-column>
            </DataTable>
        </div>
        <SaveDialog v-if="editable" @success="refreshData" @closed="editable = false" ref="SaveDialog" :plant="plant"
            :line="line" :engine_type="engine_type" :motorcycle_type="motorcycle_type" :model="model" :type="type"
            :status="status" :stage="stage" :special="special">
        </SaveDialog>
        <ChangeDialog v-if="viewable" @closed="viewable = false" ref="ChangeDialog" :plant="plant"
            :line="line" :engine_type="engine_type" :motorcycle_type="motorcycle_type" :model="model" :type="type"
            :status="status" :stage="stage" :special="special" :assemblies="assemblies">
        </ChangeDialog>
    </Layout>
</template>
<script>
import SaveDialog from './Addons/SaveDialog.vue'
import ChangeDialog from './Addons/ChangeDialog.vue'
export default {
    components: {
        SaveDialog,
        ChangeDialog
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
            editable: false,
            viewable: false
        }
    },
    mounted() {
        this.$nextTick(() => { })
    },
    methods: {
        editItem(item) {
            this.editable = true
            this.$nextTick(() => {
                this.$refs.SaveDialog.open( item)
            })
        },
        viewItem(item){
            this.viewable = true
            this.$nextTick(() => {
                this.$refs.ChangeDialog.open(item)
            })
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

<style scoped>
.el-form-item-msg {
    color: var(--el-link-color)
}
</style>