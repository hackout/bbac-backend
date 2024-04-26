<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block">
            <div class="page-search">
                <div class="page-search-buttons">
                    <el-button type="primary" @click="addItem" icon="el-icon-plus">新增</el-button>
                    <el-button type="primary" :disabled="items.length != 1" @click="editItem"
                        icon="el-icon-edit">编辑</el-button>
                    <el-divider direction="vertical" />
                    <el-button type="danger" :disabled="items.length == 0" @click="deleteItem" icon="el-icon-delete"
                        :loading="loading">删除</el-button>
                    <el-divider direction="vertical" />
                    <el-button type="warning" :disabled="items.length == 0" @click="invalidItem" icon="el-icon-hide"
                        :loading="loading">禁用</el-button>
                    <el-button type="success" :disabled="items.length == 0" @click="validItem" icon="el-icon-view"
                        :loading="loading">启用</el-button>
                    <el-divider direction="vertical" />
                    <el-button type="warning" :disabled="items.length == 0" @click="lockItem" icon="el-icon-lock"
                        :loading="loading">锁定</el-button>
                    <el-button type="success" :disabled="items.length == 0" @click="unlockItem" icon="el-icon-unlock"
                        :loading="loading">解锁</el-button>
                    <el-divider direction="vertical" />
                    <el-upload :action="$route('user.import')" ref="importUpload" :on-success="importSuccess"
                        class="page-search-buttons-upload" :limit="1" :show-file-list="false" :headers="uploadHeaders">
                        <el-button icon="el-icon-upload" type="primary">导入</el-button>
                    </el-upload>
                    <el-button type="primary" @click="$goTo('user.template')" link
                        icon="el-icon-download">模板</el-button>
                </div>
            </div>
            <DataTable ref="table" :apiName="$route('user.list')" @change-page="query.page = $event"
                @change-page-size="query.limit = $event" height="550px" :params="query" stripe highlightCurrentRow
                remoteSort @selection-change="selectionChange" remoteFilter>
                <el-table-column label="选择" type="selection" align="center" width="55" />
                <el-table-column label="序号" align="center" prop="id" width="100">
                    <template #default="scope">
                        <span>{{ scope.$index + 1 < 10 ? '0' + (scope.$index + 1) : scope.$index + 1 }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="用户名" align="center" prop="username" width="200">
                    <template #default="scope">
                        <span v-if="!scope.row.username">-</span>
                        <template v-else>
                            <el-text :type="scope.row.is_valid ? '' : 'info'">{{ scope.row.username }}</el-text>
                            <el-tag type="danger" size="small" style="margin-left:3px;"
                                v-if="scope.row.is_lock">锁定</el-tag>
                            <el-tag type="danger" size="small" style="margin-left:3px;"
                                v-if="scope.row.is_super">超管</el-tag>
                        </template>
                    </template>
                </el-table-column>
                <el-table-column label="工号" align="center" prop="number" width="100">
                    <template #default="scope">
                        <span v-if="!scope.row.number">-</span>
                        <span v-else>{{ scope.row.number }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="姓名" align="center" prop="name" width="100">
                    <template #default="scope">
                        <span v-if="!scope.row.name">-</span>
                        <span v-else>{{ scope.row.name }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="性别" align="center" prop="gender" width="80">
                    <template #default="scope">
                        <span>{{ $status('gender', scope.row.gender) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="所在部门" align="center" prop="department" width="150">

                    <template #default="scope">
                        <span v-if="!scope.row.department">-</span>
                        <span v-else>{{ scope.row.department }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="分配角色" align="center" prop="roles" min-width="150">

                    <template #default="scope">
                        <template v-if="scope.row.roles.length > 0">
                            <el-tag size="small" v-for="(item, index) in scope.row.roles" :key="index">{{ item.name
                                }}</el-tag>
                        </template>
                        <span v-else>-</span>
                    </template>
                </el-table-column>
                <el-table-column label="手机号码" align="center" prop="mobile" width="150">

                    <template #default="scope">
                        <span v-if="!scope.row.mobile">-</span>
                        <span v-else>{{ scope.row.mobile }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="操作" align="center" prop="action" width="200">
                    <template #default="scope">
                        <el-button size="small" :disabled="scope.row.is_super" @click="viewItem(scope.row)" type="primary" link>查看详情</el-button>
                    </template>
                </el-table-column>
            </DataTable>
        </div>
        <SaveDialog v-if="editable" @success="refreshData" @closed="editable = false" ref="SaveDialog"
            :defaultPassword="default_password" :departments="departments" :roles="roles"></SaveDialog>
        <ViewDrawer v-if="viewable" @success="refreshData" @closed="viewable = false" ref="ViewDrawer"
            :departments="departments" :roles="roles" :gender="gender" :nation="nation" :skill_level="skill_level"
            :career_level="career_level">
        </ViewDrawer>
    </Layout>
</template>

<script>
import SaveDialog from './Addons/SaveDialog.vue'
import ViewDrawer from './Addons/ViewDrawer.vue'
export default {
    components: {
        SaveDialog,
        ViewDrawer
    },
    props: {
        default_password: {
            type: String,
            default: '123456'
        },
        departments: {
            type: Array,
            default: []
        },
        roles: {
            type: Array,
            default: []
        },
        gender: {
            type: Array,
            default: []
        },
        nation: {
            type: Array,
            default: []
        },
        skill_level: {
            type: Array,
            default: []
        },
        career_level: {
            type: Array,
            default: []
        },
    },
    data() {
        return {
            saving: false,
            query: {
                page: 1,
                limit: 20
            },
            uploadHeaders: {
                'X-XSRF-TOKEN': this.$tool.cookies.get('XSRF-TOKEN')
            },
            items: [],
            loading: false,
            editable: false,
            viewable: false
        }
    },
    mounted() {
        this.$nextTick(() => {
        })
    },
    methods: {
        selectionChange(items) {
            this.items = items
        },
        addItem() {
            this.editable = true
            this.$nextTick(() => {
                this.$refs.SaveDialog.open('add')
            })
        },
        editItem() {
            this.editable = true
            this.$nextTick(() => {
                this.$refs.SaveDialog.open('edit', this.items[0])
            })
        },
        viewItem(item) {
            this.viewable = true
            this.$nextTick(() => {
                this.$refs.ViewDrawer.open(item)
            })
        },
        deleteItem() {
            this.$confirm('确定删除所选项?', '操作提示').then(async () => {
                this.deleting = true
                var res = await this.$axios.post(this.$route('user.batch_delete'), { ids: this.items.map(n => n.id) })
                this.deleting = false
                if (res.code == this.$config.successCode) {
                    this.$message.success("批量删除成功")
                    this.refreshData()
                } else {
                    this.$message.error(res.message)
                }
            }).catch(() => { })
        },
        invalidItem() {
            this.$confirm('确定禁用所选项?', '操作提示').then(async () => {
                this.patchItem('invalid')
            }).catch(() => { })
        },
        validItem() {
            this.$confirm('确定启用所选项?', '操作提示').then(async () => {
                this.patchItem('valid')
            }).catch(() => { })
        },
        unlockItem() {
            this.$confirm('确定解锁所选项?', '操作提示').then(() => {
                this.patchItem('unlock')
            }).catch(() => { })
        },
        lockItem() {
            this.$confirm('确定锁定所选项?', '操作提示').then(() => {
                this.patchItem('lock')
            }).catch(() => { })
        },
        async patchItem(type) {
            this.loading = true
            let res = await this.$axios.post(this.$route('user.patch', { slug: type }), { ids: this.items.map(n => n.id) })
            this.loading = false
            if (res.code == this.$config.successCode) {
                this.$message.success('批量操作成功')
                this.refreshData()
            } else {
                this.$message.error(res.message)
            }
        },
        importSuccess() {
            this.$message.success(`导入员工成功,统一默认密码:${this.default_password}`)
            this.refreshData()
        },
        refreshData() {
            this.items = []
            this.$refs.importUpload.clearFiles()
            this.$refs.table.refresh()
        }
    }
}
</script>

<style scoped lang="scss"></style>