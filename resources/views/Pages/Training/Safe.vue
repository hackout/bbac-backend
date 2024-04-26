<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block">
            <div class="page-search">
                <div class="page-search-buttons">
                    <el-button type="danger" :disabled="items.length == 0" @click="deleteItem" icon="el-icon-delete"
                        :loading="deleting">删除</el-button>
                    <el-upload :action="$route('training.import', { type: 'safe' })" ref="importUpload"
                        :on-success="importSuccess" class="page-search-buttons-upload" :limit="1"
                        :show-file-list="false" :headers="uploadHeaders">
                        <el-button icon="el-icon-upload" type="primary">导入</el-button>
                    </el-upload>
                    <el-button type="primary" @click="$goTo('training.template', { type: 'safe' })" link
                        icon="el-icon-download">模板</el-button>
                    <el-button icon="el-icon-download" link @click="exportData" type="primary">导出</el-button>
                </div>
                <div class="page-search-form">
                    <el-form v-model="query" inline>
                        <el-form-item>
                            <el-tree-select v-model="query.department_id" :data="options" filterable node-key="id"
                                :props="cascaderProp" placeholder="按部门查看" clearable highlight-current default-expand-all
                                check-strictly @change="changeDepartmentId" />
                        </el-form-item>
                    </el-form>
                </div>
                <div class="page-upload-file">
                    <el-upload :action="itemUploadUrl" ref="importUpload_file" :on-success="importSuccess"
                        class="page-search-buttons-upload" :limit="9" multiple :show-file-list="false"
                        :headers="uploadHeaders">
                        <el-button link icon="el-icon-upload" id="updateFile" type="primary">上传文件</el-button>
                    </el-upload>
                </div>
            </div>
            <DataTable ref="table" :apiName="$route('training.list', { type: 'safe' })"
                @change-page="query.page = $event" @change-page-size="query.limit = $event" height="570px"
                :params="query" stripe highlightCurrentRow remoteSort @selection-change="selectionChange" remoteFilter>
                <el-table-column label="选择" type="selection" align="center" width="55" fixed="left" />
                <el-table-column label="序号" align="center" prop="id" width="80" fixed="left">
                    <template #default="scope">
                        <span>{{ scope.$index + 1 < 10 ? '0' + (scope.$index + 1) : scope.$index + 1 }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="培训内容" align="center" prop="name" width="200" show-overflow-tooltip
                    fixed="left"></el-table-column>
                <el-table-column label="培训时间" align="center" prop="started_at" width="120" fixed="left">
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.started_at, 'YYYY-MM-DD') }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="培训周期(天)" align="center" prop="period" width="120"
                    fixed="left"></el-table-column>
                <el-table-column label="状态" align="center" prop="status" width="100" fixed="left">
                    <template #default="scope">
                        <el-tag size="small" :type="tags[scope.row.status]">{{ $status('training_status',
                            scope.row.status) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="完成时间" align="center" prop="ended_at" width="120" fixed="left">
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.ended_at, 'YYYY-MM-DD') }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="附件" align="center" prop="name" width="400" fixed="left">

                    <template #default="scope">
                        <el-dropdown v-for="(item, index) in scope.row.attachments" trigger="click"
                            @command="handleDropdown(scope.row, item, $event)" :key="index">
                            <el-text type="primary" class="text-cell" size="small">
                                <div class="file-item-icon" :class="item.type">
                                    <component :is="icons[item.type]"></component>
                                </div>
                                <span>{{ item.name }}</span>
                            </el-text>
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-dropdown-item command="download">下载文件</el-dropdown-item>
                                    <el-dropdown-item command="delete" type="danger" divided>删除附件</el-dropdown-item>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                        <div class="text-cell">
                            <el-button link icon="el-icon-upload" @click="uploadFile(scope.row)"
                                type="primary">上传文件</el-button>
                        </div>
                    </template>
                </el-table-column>
                <template v-if="columns.length > 0">
                    <el-table-column v-for="(column, index) in columns" :key="index" :label="column.name" align="center"
                        prop="users" width="145">
                        <template #default="scope">
                            <el-dropdown @command="changeStatus(scope.row.id, column.value, $event)" trigger="click">
                                <el-text tag="ins" :type="status[scope.row.users[column.value]]">{{
                                    $status('training_item_status', scope.row.users[column.value]) }}</el-text>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item v-for="(ss, ii) in training_item_status" :key="ii"
                                            :command="ss.value">
                                            <el-text :type="status[ss.value]">{{ ss.name }}</el-text>
                                        </el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>

                        </template>
                    </el-table-column>
                </template>
                <el-table-column label="班组完成率" align="center" prop="rate" min-width="150">
                    <template #default="scope">
                        <span>{{ scope.row.rate }}%</span>
                    </template>
                </el-table-column>
            </DataTable>
        </div>
    </Layout>
</template>

<script>
export default {
    props: {
        departments: {
            type: Array,
            default: []
        },
        training_status: {
            type: Array,
            default: []
        },
        training_item_status: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            icons: {
                directory: 'bbac-icon-folder',
                file: 'bbac-icon-file',
                excel: 'bbac-icon-file-excel',
                image: 'bbac-icon-file-image',
                pdf: 'bbac-icon-file-pdf',
                ppt: 'bbac-icon-file-ppt',
                txt: 'bbac-icon-file-txt',
                video: 'bbac-icon-file-video',
                unknown: 'bbac-icon-file-unknown',
                word: 'bbac-icon-file-word',
                zip: 'bbac-icon-file-zip',
                music: 'bbac-icon-file-music',
                other: 'bbac-icon-file-other'
            },
            tags: {
                1: 'danger',
                2: 'primary',
                3: 'success'
            },
            status: {
                4: 'info',
                1: 'warning',
                2: 'primary',
                3: 'danger'
            },
            query: {
                page: 1,
                limit: 20,
                department_id: ""
            },
            uploadHeaders: {
                'X-XSRF-TOKEN': this.$tool.cookies.get('XSRF-TOKEN')
            },
            options: [],
            items: [],
            deleting: false,
            cascaderProp: {
                label: 'name'
            },
            columns: [],
            itemUploadUrl: ''
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.departments.forEach(n => {
                this.options.push({
                    name: n.name,
                    id: n.id,
                    children: this.makeChildren(n)
                })
            })
        })
    },
    methods: {
        uploadFile(item) {
            this.itemUploadUrl = this.$route('training.upload', { type: 'safe', id: item.id })
            document.querySelector('#updateFile').click()
        },
        handleDropdown(row, file, type) {
            if (type == 'download') {
                this.$download(file.url)
            }
            if (type == 'delete') {
                this.$confirm('确定删除所选附件?', '操作提示').then(async () => {
                    this.deleteFile(row.id, file.uuid)
                }).catch(() => { })
            }
        },
        async deleteFile(id, uuid) {
            let res = await this.$axios.delete(this.$route('training.file_delete', { type: 'safe', id: id, file: uuid }))
            if (res.code == this.$config.successCode) {
                this.refreshData()
            } else {
                this.$message.error(res.message)
            }
        },
        async exportData() {
            var res = await this.$axios.post(this.$route('training.export', { type: 'safe' }), this.query)
            if (res.code == this.$config.successCode) {
                this.$message.success('数据正在打包中,3秒后自动下载');
                setTimeout(() => {
                    this.$download(res.data)
                }, 3000)
            } else {
                this.$message.error(res.message)
            }
        },
        async changeStatus(id, user_id, val) {
            var res = await this.$axios.post(this.$route('training.patch', { type: 'safe' }), {
                id: id,
                user_id: user_id,
                status: val
            })
            if (res.code == this.$config.successCode) {
                this.refreshData()
            } else {
                this.$message.error(res.message)
            }
        },
        makeChildren(options) {
            let result = []
            if (options.children) {
                options.children.forEach(n => {
                    result.push({
                        name: n.name,
                        id: n.id,
                        children: this.makeChildren(n)
                    })
                })
            }
            return result.length > 0 ? result : null
        },
        async changeDepartmentId(value) {
            this.query.department_id = value ?? ''
            if (this.query.department_id.length > 0) {
                this.columns = []
                var res = await this.$axios.get(this.$route('user.department', { id: this.query.department_id }))
                if (res.code == this.$config.successCode) {
                    this.columns = res.data
                }
            }
            this.refreshData()
        },
        selectionChange(items) {
            this.items = items
        },
        deleteItem() {
            this.$confirm('确定删除所选项?', '操作提示').then(async () => {
                this.deleting = true
                var res = await this.$axios.post(this.$route('training.delete', { type: 'safe' }), { ids: this.items.map(n => n.id) })
                this.deleting = false
                if (res.code == this.$config.successCode) {
                    this.$message.success("批量删除成功")
                    this.refreshData()
                } else {
                    this.$message.error(res.message)
                }
            }).catch(() => { })
        },
        importSuccess() {
            this.$message.success(`导入记录成功`)
            this.refreshData()
        },
        refreshData() {
            this.itemIds = []
            this.$refs.importUpload.clearFiles()
            this.$refs.table.refresh()
        }
    }
}
</script>

<style scoped lang="scss">
.text-cell {
    @extend .flexColumn;
    flex-direction: row;
    width: 380px;
    cursor: pointer;
}

.page-upload-file {
    position: fixed;
    top: -9999rem;
}

.file-item {
    width: 30px;
    color: var(--el-color-primary);
    @extend .flexColumn;
    flex-direction: row;
    height: 30px;
    cursor: pointer;
    font-size: 14px;

    &-icon {
        width: 26px;
        height: 26px;
        font-size: 24px;


        &.file {
            svg {
                fill: #222222;
            }
        }

        &.excel {
            svg {
                fill: #107c41;
            }
        }

        &.image {
            svg {
                fill: #fe3364;
            }
        }

        &.music {
            svg {
                fill: #0e6db0;
            }
        }

        &.pdf {
            svg {
                fill: #b40b00;
            }
        }

        &.ppt {
            svg {
                fill: #c43e1c;
            }
        }

        &.unknown {
            svg {
                fill: #2d9be5;
            }
        }

        &.word {
            svg {
                fill: #185abc;
            }
        }

        &.txt {
            svg {
                fill: #adadad;
            }
        }

        &.video {
            svg {
                fill: #874cd9;
            }
        }

        &.zip {
            svg {
                fill: var(--el-color-warning);
            }
        }

        &.other {
            svg {
                fill: var(--el-color-warning);
            }
        }
    }
}
</style>