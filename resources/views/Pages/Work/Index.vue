<template>
    <Layout>
        <div class="page-block assignment">
            <div class="assignment-top">
                <div class="assignment-top-left">
                    <el-tree-select style="width:100%" v-model="form.department_id" :data="options" filterable
                        node-key="id" :props="cascaderProp" placeholder="按部门查看" clearable highlight-current
                        default-expand-all check-strictly @change="changeDepartmentId" />
                </div>
                <div class="assignment-top-center">
                    <el-button link icon="el-icon-arrow-left-bold" @click="prevMonth"></el-button>
                    <el-date-picker v-model="form.month" @change="changeMonth" :clearable="false" :editable="false"
                        type="month" placeholder="选择月份" format="YYYY-MM" value-format="YYYY-MM">
                    </el-date-picker>
                    <el-button link icon="el-icon-arrow-right-bold" @click="nextMonth"></el-button>
                </div>
                <div class="assignment-top-right">
                    <el-button type="primary" @click="exportItem" style="width:100%"
                        icon="el-icon-download">导出</el-button>
                </div>
            </div>
            <div class="assignment-table">
                <el-table style="height:100%;" v-if="form.department_id != ''" v-loading="loading"
                    :header-cell-style="headerStyle" border size="small" :data="items" :highlight-current-row="false"
                    :span-method="spanMethod">
                    <el-table-column prop="name" fixed="left" width="150" align="center" label="姓名"></el-table-column>
                    <el-table-column label="记录" align="center" fixed="left" width="250">
                        <el-table-column prop="type" header-align="right" align="center" fixed="left" width="150">
                            <template #default="scope">
                                <span>{{ scope.row.type > 0 ? $status('work_type', scope.row.type) : '总时长' }}</span>
                            </template>
                        </el-table-column>
                        <el-table-column prop="mark" header-align="left" align="center" fixed="left" width="100">
                            <template #default="scope">
                                <span v-if="scope.row.mark == 'detail'">具体安排</span>
                                <span v-if="scope.row.mark == 'period'">工时数</span>
                                <span v-if="scope.row.mark == '0'">{{ scope.row.sub }}</span>
                            </template>
                        </el-table-column>
                    </el-table-column>
                    <el-table-column v-for="(date, index) in dates" :key="index" align="center"
                        :label="date.label + ''">
                        <template #default="scope">
                            <div v-if="date.disabled" class="cell-disabled">
                                <el-icon-semi-select />
                            </div>
                            <div v-else-if="scope.row.type == 0" class="cell-item">
                                {{ scope.row[date.label] }}
                            </div>
                            <div v-else class="cell-item" @dblclick="clickCell(scope.row, date.label)">
                                {{ scope.row[date.label] }}
                            </div>
                        </template>
                    </el-table-column>
                </el-table>
                <el-empty v-else description="请先选择一个部门" />
            </div>
        </div>
        <SaveDialog v-if="editable" ref="SaveDialog" :work_type="work_type" :examine_type="examine_type"
            @success="refreshData" @closed="editable = false">
        </SaveDialog>
    </Layout>
</template>
<script>
import dayjs from 'dayjs'
import SaveDialog from './Addons/SaveDialog.vue'
export default {
    components: {
        SaveDialog
    },
    props: {
        work_type: {
            type: Array,
            default: []
        },
        examine_type: {
            type: Array,
            default: []
        },
        departments: {
            type: Array,
            default: []
        },
    },
    data() {
        return {
            form: {
                department_id: '',
                month: dayjs().format('YYYY-MM')
            },
            editable: false,
            cascaderProp: {
                label: 'name'
            },
            options: [],
            items: [],
            dates: [],
            loading: false,
            spanMethod: ({ column, rowIndex, columnIndex }) => {
                if (columnIndex == 0) {
                    if (rowIndex % 13 == 0) {
                        return [13, 1]
                    } else {

                        return [0, 0]
                    }
                }
                if (columnIndex == 1) {
                    let cRowIndex = (rowIndex - (rowIndex % 13)) / 13;
                    let dRowIndex = rowIndex - (cRowIndex * 13);
                    if (dRowIndex < 10 && dRowIndex % 2 == 0) {
                        return [2, 1]
                    } else {
                        if (this.items[rowIndex][column.property] == null) {
                            return [0, 0]
                        }
                        return [1, 1]
                    }
                }
                return [1, 1]
            },
            headerStyle: ({ rowIndex }) => {
                if (rowIndex == 1) return { display: 'none' };
                return {
                    height: '60px'
                }
            }

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
            if (this.form.department_id != '') {
                this.getData()
            }
        })
    },
    methods: {
        prevMonth() {
            this.form.month = dayjs(this.form.month).subtract(1, 'month').format('YYYY-MM')
            this.refreshData()
        },
        nextMonth() {
            this.form.month = dayjs(this.form.month).add(1, 'month').format('YYYY-MM')
            this.refreshData()
        },
        changeMonth(e) {
            this.form.month = e
            this.refreshData()
        },
        clickCell(item, column) {
            if (item[column] == null) {
                this.addItem(item.sub, item.user_id, this.form.month + '-' + (column < 10 ? '0' + column : column))
            }
            if((item.type == 6 || item.type == 7) && item[column] == 0)
            {
                this.addItem(item.type, item.user_id, this.form.month + '-' + (column < 10 ? '0' + column : column))
            }
        },
        async getData() {
            this.loading = true
            var res = await this.$axios.get(this.$route('work.list'), this.form)
            this.loading = false
            if (res.code == this.$config.successCode) {
                this.dates = res.data.dates
                this.items = res.data.items
            } else {
                this.$message.error(res.message)
            }
        },
        makeChildren(options) {
            let res = new Array()
            if (options.children) {
                options.children.forEach((n) => {
                    var x = {
                        name: n.name,
                        id: n.id,
                        children: this.makeChildren(n)
                    };
                    res.push(x)
                })
            }
            return res.length > 0 ? res : null
        },
        async changeDepartmentId(value) {
            this.form.department_id = value ?? ''
            this.refreshData()
        },
        async exportItem() {
            var res = await this.$axios.post(this.$route('assignment.export'), this.form)
            if (res.code == this.$config.successCode) {
                this.$message.success('数据正在打包中,3秒后自动下载');
                setTimeout(() => {
                    this.$download(res.data)
                }, 3000)
            } else {
                this.$message.error(res.message)
            }
        },
        addItem(type, user_id, date) {
            this.editable = true
            this.$nextTick(() => {
                this.$refs.SaveDialog.open(type, date, user_id)
            })
        },
        async deleteItem(item) {
            var res = await this.$axios.delete(this.$route('task.delete', { id: item.id }),)
            this.deleting = false
            if (res.code == this.$config.successCode) {
                this.$message.success("删除任务成功")
                this.refreshData()
            } else {
                this.$message.error(res.message)
            }
        },
        async onSearch() {
            var validate = await this.$refs.form.validate().catch(() => { })
            if (!validate) return false;
            this.$nextTick(() => {
                this.$refs.table.upData(this.form)
            })
        },
        refreshData() {
            this.editable = false
            if (this.form.department_id == '') {
                this.items = []
                this.dates = []
            } else {
                this.getData()
            }
        }
    }
}
</script>

<style scoped lang="scss">
.assignment {
    width: 100%;
    height: calc(100vh - 120px);
    margin: -5px 0 -25px 0 ;
    @extend .flexColumn;
    flex-direction: column;

    &-top {
        width: 100%;
        height: 60px;
        @extend .flexColumn;
        flex-direction: row;

        &-left {
            width: 150px;
            margin-right: 15px;
            height: 60px;
            @extend .flexColumn;
        }

        &-right {
            width: 150px;
            margin-left: 15px;
            height: 60px;
            @extend .flexColumn;
        }

        &-center {
            flex: 1;
            height: 60px;
            @extend .flexColumn;
            flex-direction: row;

            :deep(.el-button) {
                font-size: 24px;
            }

            :deep(.el-input__prefix) {
                display: none;
            }

            :deep(.el-input__inner) {
                font-size: 32px;
                text-align: center;
            }

            :deep(.el-date-editor.el-input) {
                width: 120px;
                height: 40px;
                margin-top: -4px;
            }

            :deep(.el-input__wrapper) {
                width: auto;
                background: transparent !important;
                border: 0;
                border-radius: 0;
                padding: 0;
                height: 40px;

                input {
                    height: 40px;
                }

                box-shadow: none !important;
            }
        }
    }

    &-table {
        margin-top: 5px;
        width: 100%;
        height: calc(100vh - 200px);

        :deep(.el-table .cell) {
            font-weight: 200;
            color: #010101;
            padding: 0;
        }

        :deep(.el-table .el-table__cell) {
            padding: 0;
        }

        .cell-disabled {
            background-color: var(--el-fill-color-light);
            width: 100%;
            height: 36px;
            @extend .flexColumn;
            color: var(--el-table-border-color)
        }

        .cell-item {
            min-width: 36px;
            height: 36px;
            box-sizing: border-box;
            max-width: 120px;
            padding: 5px 10px;
            @extend .flexColumn;
            font-size: 11px;
        }

        :deep(.el-table .el-table__body tr.hover-row > td) {
            background-color: #ffffff !important
        }

        :deep(.el-table--enable-row-hover .el-table__body tr:hover > td) {
            background-color: #ffffff !important
        }
    }
}
</style>
