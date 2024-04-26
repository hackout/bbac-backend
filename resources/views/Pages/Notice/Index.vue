<template>
    <Layout>
        <div class="page-block" style="height:800px;">
            <div class="page-search">
                <div class="page-search-button">
                    <el-button type="primary" @click="addItem" icon="el-icon-plus">新增</el-button>
                    <el-button type="success" :disabled="canEdit" @click="editItem" icon="el-icon-edit">编辑</el-button>
                    <el-button type="danger" :disabled="items.length == 0" @click="deleteItem" icon="el-icon-delete"
                        :loading="deleting">删除</el-button>
                    <el-button-group style="margin-left:12px;">
                        <el-button :disabled="canPush" @click="pushNotice">发布并推送</el-button>
                        <el-button :disabled="canRetract" @click="retractNotice">撤回消息</el-button>
                    </el-button-group>
                </div>
                <div class="page-search-form">
                    <el-form ref="queryForm" :model="query" size="small" inline @submit.native.prevent="onSearch">
                        <el-form-item>
                            <el-select placeholder="信息状态" style="width:120px;" clearable v-model="query.is_valid"
                                @change="onSearch">
                                <el-option value="" label="全部信息"></el-option>
                                <el-option value="0" label="只看草稿"></el-option>
                                <el-option value="1" label="只看发布"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-select placeholder="消息类型" clearable style="width:120px;" v-model="query.type"
                                @change="onSearch">
                                <template v-for="(type, index) in notice_type">
                                    <el-option :key="index" :value="type.value" :label="type.name"
                                        v-if="type.value < 4"></el-option>
                                </template>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-input v-model="query.keyword" placeholder="请输入关键词"></el-input>
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
            <DataTable ref="table" :apiName="$route('notice.list')" @change-page="query.page = $event"
                @change-page-size="query.limit = $event" height="720px" :params="query" stripe highlightCurrentRow
                remoteSort @selection-change="selectionChange" remoteFilter>
                <el-table-column label="选择" type="selection" align="center" width="55" />
                <el-table-column label="序号" align="center" prop="id" width="100">

                    <template #default="scope">
                        <span>{{ scope.$index + 1 < 10 ? '0' + (scope.$index + 1) : scope.$index + 1 }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="信息标题" align="center" prop="title" show-overflow-tooltip></el-table-column>
                <el-table-column label="消息类型" align="center" prop="type" width="100px">
                    <template #default="scope">
                        <span>{{ $status('notice_type', scope.row.type) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="状态" align="center" prop="is_valid" width="100px">
                    <template #default="scope">
                        <el-tag v-if="scope.row.is_valid">已发布</el-tag>
                        <el-tag v-else type="success">未发布</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="推送" align="center" prop="is_sent" width="100px">
                    <template #default="scope">
                        <el-tag v-if="scope.row.is_sent">已推送</el-tag>
                        <el-tag v-else type="success">未推送</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="发布者" align="center" prop="author" width="100px"></el-table-column>
                <el-table-column label="发布时间" align="center" prop="created_at" width="185px">
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.created_at) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="操作" align="center" prop="action" width="120px" fixed="right">
                    <template #default="scope">
                        <el-button type="primary" v-if="scope.row.type < 4" @click="reviewItem(scope.row)" size="small"
                            link>预览</el-button>
                    </template>
                </el-table-column>
            </DataTable>
        </div>
        <el-dialog title="选择信息类型" v-model="visitable" width="650px">
            <el-row :gutter="20">
                <template v-for="(notice, index) in notice_type">
                    <el-col :span="8" v-if="notice.value < 4" :key="index">
                        <div class="choose-item" :class="{ active: form.type == notice.value }"
                            @click="form.type = notice.value">
                            <span>{{ notice.name }}</span>
                        </div>
                    </el-col>
                </template>
            </el-row>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="visitable = false">取消</el-button>
                    <el-button type="primary" :disabled="form.type == ''" @click="goAdd">确定</el-button>
                </div>
            </template>
        </el-dialog>
        <SaveDialog ref="SaveDialog" v-if="editable" @closed="editable = false" @success="refreshData"
            :document_type="document_type">
        </SaveDialog>
        <ViewDialog ref="ViewDialog" v-if="viewable" @closed="viewable = false" :document_type="document_type">
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
        notice_type: {
            type: Array,
            default: []
        },
        document_type: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            editable: false,
            query: {
                category_id: '',
                keyword: '',
                is_valid: '',
                page: 1,
                limit: 20
            },
            form: {
                type: ''
            },
            items: [],
            deleting: false,
            visitable: false,
            viewable: true
        }
    },
    mounted() {
        this.$nextTick(() => {

        })
    },
    computed: {
        canEdit() {
            return this.items.length != 1
        },
        canPush() {
            return this.items.filter(n => !n.is_valid).length > 0
        },
        canRetract() {
            return this.items.filter(n => !n.is_sent).length > 0
        }
    },
    methods: {
        selectionChange(items) {
            this.items = items
        },
        goAdd() {
            this.editable = true
            this.visitable = false
            this.$nextTick(() => {
                this.$refs.SaveDialog.open('add', this.form)
            })
        },
        addItem() {
            this.form.slug = ''
            this.visitable = true
        },
        editItem() {
            this.editable = true
            let item = this.items[0]
            this.$nextTick(() => {
                this.$refs.SaveDialog.open('edit', item)
            })
        },
        reviewItem(item) {
            this.viewable = true
            this.$nextTick(() => {
                this.$refs.ViewDialog.open(item)
            })
        },
        retractNotice() {
            this.$confirm('确定要撤回所选消息?', '操作提示').then(async () => {
                var res = await this.$axios.post(this.$route('notice.retract'), { ids: this.items.map(n => n.id) })
                if (res.code == this.$config.successCode) {
                    this.$message.success("撤回消息成功")
                    this.refreshData()
                } else {
                    this.$message.error(res.message)
                }
            }).catch(() => { })
        },
        pushNotice() {
            this.$confirm('确定要发布所选消息?', '操作提示').then(async () => {
                var res = await this.$axios.post(this.$route('notice.push'), { ids: this.items.map(n => n.id) })
                if (res.code == this.$config.successCode) {
                    this.$message.success("批量发布成功")
                    this.refreshData()
                } else {
                    this.$message.error(res.message)
                }
            }).catch(() => { })
        },
        deleteItem() {
            this.$confirm('确定删除所选项?', '操作提示').then(async () => {
                this.deleting = true
                var res = await this.$axios.post(this.$route('notice.batch_delete'), { ids: this.items.map(n => n.id) })
                this.deleting = false
                if (res.code == this.$config.successCode) {
                    this.$message.success("批量删除成功")
                    this.refreshData()
                } else {
                    this.$message.error(res.message)
                }
            }).catch(() => { })
        },
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
        }
    }
}
</script>

<style scoped lang="scss">
.choose-item {
    width: 100%;
    height: 100px;
    @extend .flexColumn;
    border: var(--el-border-light) 1px solid;
    border-radius: 6px;
    cursor: pointer;

    span {
        font-size: 14px;
        color: var(--el-border-light);

        &:first-child {
            font-size: 18px;
            color: var(--el-text-color)
        }
    }

    &.active {
        border-color: var(--el-color-primary);
        font-weight: bold;

        span {
            color: var(--el-link-color);

            &:first-child {
                color: var(--el-color-primary)
            }
        }
    }
}
</style>