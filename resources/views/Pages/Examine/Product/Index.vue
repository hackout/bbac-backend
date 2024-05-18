<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block">
            <div class="page-search">
                <div class="page-search-buttons">
                    <el-button type="primary" @click="addItem" icon="el-icon-plus">新增</el-button>
                    <el-upload :action="$route('commit_product.import')" ref="importUpload" :on-success="importSuccess"
                        class="page-search-buttons-upload" :limit="1" :show-file-list="false" :headers="uploadHeaders">
                        <el-button icon="el-icon-upload" type="primary">导入</el-button>
                    </el-upload>
                    <el-divider direction="vertical" />
                    <el-button type="primary" @click="$goTo('commit_product.template')" link
                        icon="el-icon-download">模板</el-button>
                    <el-divider direction="vertical" />

                    <el-dropdown style="vertical-align: middle;" @command="moreCommand">
                        <el-button type="primary" link>更多模板</el-button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item command="download_assembly">下载拆检记录模板</el-dropdown-item>
                                <el-dropdown-item command="download_reassembly">下载装配记录模板</el-dropdown-item>
                                <el-dropdown-item command="download_dynamic">下载动态考核记录模板</el-dropdown-item>
                                <el-dropdown-item divided command="upload_assembly">上传拆检记录模板</el-dropdown-item>
                                <el-dropdown-item command="upload_reassembly">上传装配记录模板</el-dropdown-item>
                                <el-dropdown-item command="upload_dynamic">上传动态考核记录模板</el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </div>
                <div class="page-search-form">
                    <el-form :model="query" ref="query" inline @submit.native.prevent="onSearch">
                        <el-form-item>
                            <el-select style="width:110px" v-model="query.type" @change="onSearch" placeholder="考核类型"
                                clearable>
                                <el-option v-for="(item, index) in examine_product_type" :key="index"
                                    :value="item.value" :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-select style="width:110px" v-model="query.engine" @change="onSearch" placeholder="考核机型"
                                clearable>
                                <el-option v-for="(item, index) in engine_type" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-select style="width:110px" v-model="query.status" @change="onSearch" placeholder="审核状态"
                                clearable>
                                <el-option v-for="(item, index) in template_status" :key="index" :value="item.value"
                                    :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-input v-model="query.keyword" style="width:160px" placeholder="关键词"
                                clearable></el-input>
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
            <DataTable ref="table" :apiName="$route('commit_product.list')" @change-page="query.page = $event"
                @change-page-size="query.limit = $event" height="570px" :params="query" stripe highlightCurrentRow>
                <el-table-column label="序号" align="center" prop="id" width="100">
                    <template #default="scope">
                        <span>{{ scope.$index + 1 < 10 ? '0' + (scope.$index + 1) : scope.$index + 1 }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="模板名称" align="center" prop="name" min-width="200"></el-table-column>
                <el-table-column label="考核类型" align="center" prop="line" width="100">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('examine_product_type', scope.row.type) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="发动机型号" align="center" prop="engine" width="100">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('engine_type', scope.row.engine) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="版本号" align="center" prop="version" width="100"></el-table-column>
                <el-table-column label="上个版本号" align="center" prop="last_version" width="150"></el-table-column>
                <el-table-column label="版本状态" align="center" prop="status" width="150">
                    <template #default="scope">
                        <el-tag size="small">{{ $status('template_status', scope.row.status) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="创建人" align="center" prop="author" width="150"></el-table-column>
                <el-table-column label="创建时间" align="center" prop="created_at" width="175">
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.created_at) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="最后更新人" align="center" prop="user" width="100"></el-table-column>
                <el-table-column label="最后更新时间" align="center" prop="updated_at" width="175">
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.updated_at) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="操作" align="center" prop="action" width="285" fixed="right">
                    <template #default="scope">
                        <el-button size="small" v-if="scope.row.status == 0" @click="editItem(scope.row)" type="primary"
                            link>编辑</el-button>
                        <el-button size="small" @click="viewDetail(scope.row)" type="primary" link>查看</el-button>
                        <el-button size="small" @click="viewItem(scope.row)" type="primary" link>配置考核项</el-button>
                        <el-button size="small" v-if="scope.row.can_approve" @click="approveItem(scope.row)"
                            type="primary" link>送审批</el-button>
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
        <el-dialog modal-class="chooseDialog" v-model="chooseAddVisit" :show-close="false">
            <el-row :gutter="20">
                <el-col :span="24 / examine_product_type.length" v-for="(t, i) in examine_product_type" :key="i">
                    <span @click="addItemByType(t.value)" class="col-text">{{ t.name }}</span>
                </el-col>
            </el-row>
        </el-dialog>
        <el-dialog v-model="uploadDialogVisit" :title="titles[form.type]">
            <el-form :model="form" :rules="rules" label-width="150px" ref="form"
                @submit.native.prevent="uploadTemplate">
                <el-form-item label="发动机机型" prop="engine">
                    <el-select style="width:100%" v-model="form.engine" placeholder="发动机机型" clearable filterable>
                        <el-option v-for="(item, index) in engine_type" :key="index" :value="item.value"
                            :label="item.name"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="上传模板" prop="file">
                    <el-input type="file" id="fileTemplate" v-model="form.file" placeholder="请输入上传模板" clearable></el-input>
                </el-form-item>
            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="uploadDialogVisit = false">取消</el-button>
                    <el-button type="primary" @click="uploadTemplate">提交保存</el-button>
                </div>
            </template>
        </el-dialog>
        <DetailDialog v-if="detailVisit" @success="refreshData" @closed="detailVisit = false" ref="DetailDialog"
            :examine_product_item_type="examine_product_item_type" :template_status="template_status"
            :engine_type="engine_type">
        </DetailDialog>
        <SaveDialog v-if="editable" @success="refreshData" @closed="editable = false" ref="SaveDialog"
            :engine_type="engine_type">
        </SaveDialog>
        <ApproveDialog v-if="approveVisit" @success="refreshData" @closed="approveVisit = false" ref="ApproveDialog">
        </ApproveDialog>
        <ItemDrawer v-if="viewable" @success="refreshData" @closed="viewable = false" ref="ItemDrawer" :parts="parts"
            :examine_product_type="examine_product_type" :examine_product_item_type="examine_product_item_type">
        </ItemDrawer>
    </Layout>
</template>
<script>
import DetailDialog from './Addons/DetailDialog.vue'
import SaveDialog from './Addons/SaveDialog.vue'
import ItemDrawer from './Addons/ItemDrawer.vue'
import ApproveDialog from './Addons/ApproveDialog.vue'
export default {
    components: {
        DetailDialog,
        SaveDialog,
        ApproveDialog,
        ItemDrawer
    },
    props: {
        parts: {
            type: Array,
            default: []
        },
        examine_product_type: {
            type: Array,
            default: []
        },
        examine_product_item_type: {
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
                status: '',
                keyword: ''
            },
            items: [],
            editable: false,
            viewable: false,
            detailVisit: false,
            chooseAddVisit: false,
            approveVisit: false,
            uploadDialogVisit: false,
            form: {
                engine: '',
                type: '',
                file: null
            },
            titles: {
                dynamic: '上传动态考核记录模板',
                assembly: '上传拆检记录模板',
                reassembly: '上传装配记录模板'
            },
            rules: {
                engine: [
                    { required: true, message: '请选择发动机机型', trigger: 'change' }
                ],
                file: [
                    {
                        required: true, message: '请上传模板', trigger: 'change'
                    }
                ]
            }
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.uploadHeaders['X-XSRF-TOKEN'] = this.$tool.cookies.get('XSRF-TOKEN')
        })
    },
    methods: {
        moreCommand(val) {
            switch (val) {
                case 'download_assembly':
                    window.location.href = '/ProductAssemblyTemplate.xlsx'
                    break;
                case 'download_reassembly':
                    window.location.href = '/ProductReassemblyTemplate.xlsx'
                    break;
                case 'download_dynamic':
                    window.location.href = '/ProductDynamicTemplate.xlsx'
                    break;
                case 'upload_assembly':
                    this.form = {
                        engine: '',
                        type: 'assembly',
                        file: null
                    }
                    this.uploadDialogVisit = true
                    break;
                case 'upload_dynamic':
                    this.form = {
                        engine: '',
                        type: 'dynamic',
                        file: null
                    }
                    this.uploadDialogVisit = true
                    break;
                case 'upload_reassembly':
                    this.form = {
                        engine: '',
                        type: 'reassembly',
                        file: null
                    }
                    this.uploadDialogVisit = true
                    break;
            }
        },
        uploadTemplate() {
            this.$refs.form.validate().then(async valid => {
                if (valid) {
                    let form = new FormData()
                    form.append('engine', this.form.engine)
                    form.append('type', this.form.type)
                    const fileInput = document.querySelector('#fileTemplate');
                    const file = fileInput.files[0];
                    form.append('file', file)
                    let res = await this.$axios.post(this.$route('commit_product.upload'), form, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                    if (res.code == this.$config.successCode) {
                        this.$message.success('上传模板成功')
                        this.uploadDialogVisit = false
                    } else {
                        this.$message.error(res.message)
                    }
                }
            }).catch(() => { })
        },
        addItemByType(type) {
            this.editable = true
            this.chooseAddVisit = false
            this.$nextTick(() => {
                this.$refs.SaveDialog.open('add', type)
            })
        },
        addItem() {
            this.chooseAddVisit = true
        },
        viewDetail(item) {
            this.detailVisit = true
            this.$nextTick(() => {
                this.$refs.DetailDialog.open(item)
            })
        },
        approveItem(item) {
            this.approveVisit = true
            this.$nextTick(() => {
                this.$refs.ApproveDialog.open(item)
            })
        },
        editItem(item) {
            this.editable = true
            this.chooseAddVisit = false
            this.$nextTick(() => {
                this.$refs.SaveDialog.open('edit', item.type, item)
            })
        },
        viewItem(item) {
            this.viewable = true
            this.$nextTick(() => {
                this.$refs.ItemDrawer.open(item)
            })
        },
        importSuccess() {
            this.$message.success('导入信息成功')
            this.refreshData()
        },
        async deleteItem(item) {
            const res = await this.$axios.delete(this.$route('commit_product.delete', { id: item.id }))
            if (res.code == this.$config.successCode) {
                this.$message.success('删除考核成功')
                this.refreshData()
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
            this.items = []
            this.$refs.importUpload.clearFiles()
            this.$refs.table.refresh()
        }
    }
}
</script>

<style scoped lang="scss">
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
<style>
.chooseDialog .el-dialog__header {
    display: none !important;
}
</style>