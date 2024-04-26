<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block">
            <div class="page-search">
                <div class="page-search-buttons">
                    <el-button type="primary" @click="addItem" icon="el-icon-plus">新增</el-button>
                    <el-upload :action="$route('part.import')" ref="importUpload" :on-success="importSuccess"
                        class="page-search-buttons-upload" :limit="1" :show-file-list="false" :headers="uploadHeaders">
                        <el-button icon="el-icon-upload" type="primary">导入</el-button>
                    </el-upload>
                    <el-button type="primary" @click="$goTo('part.template')" link
                        icon="el-icon-download">模板</el-button>
                </div>
                <div class="page-search-form">
                    <el-form :model="query" ref="query" inline @submit.native.prevent="onSearch">
                        <el-form-item>
                            <el-select style="width:120px" v-model="query.type" placeholder="零件类型" @change="onSearch"
                                clearable>
                                <el-option value="esd" label="ESD"></el-option>
                                <el-option value="traceability" label="追踪件"></el-option>
                                <el-option value="one_time" label="一次性"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-select style="width:110px" v-model="query.engine" @change="onSearch" placeholder="机型"
                                clearable filterable>
                                <el-option v-for="(item, index) in engine_type" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-select style="width:110px" v-model="query.assembly" @change="onSearch" placeholder="总成号"
                                clearable filterable>
                                <el-option v-for="(item, index) in assemblies" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-input v-model="query.keyword" style="width:160px" placeholder="零件编号/名称/工位"></el-input>
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
            <DataTable ref="table" :apiName="$route('part.list')" @change-page="query.page = $event"
                @change-page-size="query.limit = $event" height="550px" :params="query" stripe highlightCurrentRow>
                <el-table-column type="expand">
                    <template #default="props">
                        <el-table style="width:350px;margin:10px;" border :data="props.row.assemblies">
                            <el-table-column label="机型">
                                <template #default="scope">
                                    <span>{{ $status('engine_type', scope.row.type) }}</span>
                                </template>
                            </el-table-column>
                            <el-table-column label="总成号" prop="name"></el-table-column>
                            <el-table-column label="数量" prop="num"></el-table-column>
                        </el-table>
                    </template>
                </el-table-column>
                <el-table-column label="序号" align="center" prop="id" width="100">
                    <template #default="scope">
                        <span>{{ scope.$index + 1 < 10 ? '0' + (scope.$index + 1) : scope.$index + 1 }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="工位" align="center" prop="station" width="100"></el-table-column>
                <el-table-column label="名称" align="center" prop="name" width="150" show-overflow-tooltip></el-table-column>
                <el-table-column label="英文" align="center" prop="name_en" width="150" show-overflow-tooltip></el-table-column>
                <el-table-column label="零件号" align="center" prop="number" min-width="150"></el-table-column>
                <el-table-column label="ESD" align="center" prop="is_esd" width="100">
                    <template #default="scope">
                        <el-tag size="small" v-if="scope.row.is_esd">√</el-tag>
                        <el-tag size="small" v-else type="danger">x</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="追踪件" align="center" prop="is_traceability" width="100">
                    <template #default="scope">
                        <el-tag size="small" v-if="scope.row.is_traceability">√</el-tag>
                        <el-tag size="small" v-else type="danger">x</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="一次性" align="center" prop="is_one_time" width="100">
                    <template #default="scope">
                        <el-tag size="small" v-if="scope.row.is_one_time">√</el-tag>
                        <el-tag size="small" v-else type="danger">x</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="更新时间" align="center" prop="updated_at" width="185">
                    <template #default="scope">
                        <el-tag size="small">{{ $tool.dateFormat(scope.row.updated_at) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="操作" align="center" prop="action" width="165" fixed="right">
                    <template #default="scope">
                        <el-button size="small" @click="viewItem(scope.row)" type="primary" link>列表</el-button>
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
        <SaveDialog v-if="editable" @success="refreshData" @closed="editable = false" ref="SaveDialog"
            :assemblies="assemblies">
        </SaveDialog>
        <ViewDialog v-if="viewable" @closed="viewable = false" ref="ViewDialog">
        </ViewDialog>
    </Layout>
</template>
<script>
import SaveDialog from './Addons/SaveDialog.vue'
import ViewDialog from './Addons/ViewDialog.vue'
export default {
    components: {
        SaveDialog,
        ViewDialog
    },
    props: {
        engine_type: {
            type: Array,
            default: []
        },
        assemblies: {
            type: Array,
            default: []
        },
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
                engine: '',
                assembly: '',
                keyword: ''
            },
            items: [],
            editable: false,
            viewable: false
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.uploadHeaders['X-XSRF-TOKEN'] = this.$tool.cookies.get('XSRF-TOKEN')
        })
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
        viewItem(item){
            this.viewable =  true
            this.$nextTick(()=>{
                this.$refs.ViewDialog.open(item)
            })
        },
        async deleteItem(item) {
            var res = await this.$axios.delete(this.$route('part.delete', { id: item.id }))
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
</style>