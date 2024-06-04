<template>
    <div class="DataTable" :style="{ 'height': _height }" ref="DataTableMain" v-loading="loading">
        <div class="DataTable-table" :style="{ 'height': _table_height }">
            <el-table v-bind="$attrs" :data="tableData" :row-key="rowKey" :cell-class-name="cellClassName"
                :key="toggleIndex" ref="DataTable" :height="height == 'auto' ? null : '100%'" :size="config.size"
                :border="config.border" :stripe="config.stripe"
                :summary-method="remoteSummary ? remoteSummaryMethod : summaryMethod" :span-method="spanMethod"
                @sort-change="sortChange" @filter-change="filterChange">
                <slot></slot>
                <template #empty>
                    <el-empty :description="emptyText" :image-size="100"></el-empty>
                </template>
            </el-table>
        </div>
        <div class="DataTable-page" v-if="!hidePagination || !hideDo">
            <div class="DataTable-pagination">
                <el-pagination v-if="!hidePagination" background :small="true" :layout="paginationLayout" :total="total"
                    :page-size="dataPageSize" :page-sizes="pageSizes" :current-page="currentPage"
                    @current-change="paginationChange" @size-change="pageSizeChange"></el-pagination>
            </div>
            <div class="DataTable-do" v-if="!hideDo">
                <el-button v-if="!hideRefresh" @click="refresh" icon="el-icon-refresh" circle
                    style="margin-left:15px"></el-button>
                <el-popover v-if="!hideSetting" placement="top" title="设置" :width="400" trigger="click" :hide-after="0">
                    <template #reference>
                        <el-button icon="el-icon-setting" circle style="margin-left:15px"></el-button>
                    </template>
                    <el-form label-width="80px" label-position="left">
                        <el-form-item label="尺寸">
                            <el-radio-group v-model="config.size" size="small" @change="configSizeChange">
                                <el-radio-button label="large">大</el-radio-button>
                                <el-radio-button label="default">正常</el-radio-button>
                                <el-radio-button label="small">小</el-radio-button>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="样式">
                            <el-checkbox v-model="config.border" label="边框"></el-checkbox>
                            <el-checkbox v-model="config.stripe" label="斑马条"></el-checkbox>
                        </el-form-item>
                    </el-form>
                </el-popover>
            </div>
        </div>
    </div>
</template>

<script>
import config from "@/config/table";

export default {
    name: 'DataTable',
    props: {
        tableName: { type: String, default: "" },
        apiName: { type: String, default: () => '' },
        params: { type: Object, default: () => ({}) },
        data: { type: Object, default: () => { } },
        height: { type: [String, Number], default: "100%" },
        size: { type: String, default: "default" },
        border: { type: Boolean, default: true },
        stripe: { type: Boolean, default: true },
        pageSize: { type: Number, default: config.pageSize },
        pageSizes: { type: Array, default: config.pageSizes },
        rowKey: { type: String, default: "" },
        summaryMethod: { type: Function, default: null },
        spanMethod: { type: Function, default: null },
        cellClassName: { type: Function, default: null },
        column: { type: Object, default: () => { } },
        remoteSort: { type: Boolean, default: false },
        remoteFilter: { type: Boolean, default: false },
        remoteSummary: { type: Boolean, default: false },
        hidePagination: { type: Boolean, default: false },
        hideDo: { type: Boolean, default: false },
        hideRefresh: { type: Boolean, default: false },
        hideSetting: { type: Boolean, default: false },
        paginationLayout: { type: String, default: config.paginationLayout },
    },
    watch: {
        //监听从props里拿到值了
        data() {
            this.tableData = this.data;
            this.total = this.tableData.length;
        },
        apiName() {
            this.tableParams = this.params;
            this.refresh();
        },
        column() {
            this.userColumn = this.column;
        },
        params: {
            deep: true,
            handler(val) {
                this.tableParams = val
            }
        }
    },
    computed: {
        _height() {
            return Number(this.height) ? Number(this.height) + 'px' : this.height
        },
        _table_height() {
            return this.hidePagination && this.hideDo ? "100%" : "calc(100% - 50px)"
        }
    },
    data() {
        return {
            dataPageSize: this.params.limit ?? this.pageSize,
            isActive: true,
            emptyText: '暂无任何数据',
            toggleIndex: 0,
            tableData: [],
            total: 0,
            currentPage: this.params.page ?? 1,
            prop: null,
            order: null,
            loading: false,
            tableHeight: '100%',
            tableParams: this.params,
            customColumnShow: false,
            summary: {},
            config: {
                size: this.size,
                border: this.border,
                stripe: this.stripe
            }
        }
    },
    mounted() {
        //判断是否静态数据
        if (this.apiName) {
            this.getData();
        } else if (this.data) {
            this.tableData = this.data;
            this.total = this.tableData.length
        }
    },
    activated() {
        if (!this.isActive) {
            this.$refs.DataTable.doLayout()
        }
    },
    deactivated() {
        this.isActive = false;
    },
    methods: {
        getData() {
            var reqData = {
                [config.request.page]: this.currentPage,
                [config.request.pageSize]: this.dataPageSize,
                [config.request.prop]: this.prop,
                [config.request.order]: this.order
            }
            if (this.hidePagination) {
                delete reqData[config.request.page]
                delete reqData[config.request.pageSize]
            }
            Object.assign(reqData, this.tableParams)
            this.loading = true
            let url = this.apiName.indexOf('://') > -1 ? this.apiName : this.$route(this.apiName)
            this.$axios.get(url, reqData).then(res => {
                try {
                    var response = config.parseData(res);
                } catch (error) {
                    this.loading = false;
                    this.emptyText = '暂无任何数据';
                    return false;
                }
                if (response.code != config.successCode) {
                    this.loading = false;
                    this.emptyText = response.msg;
                } else {
                    this.emptyText = '空数据';
                    if (this.hidePagination) {
                        this.tableData = response.data || [];
                    } else {
                        this.tableData = response.rows || [];
                    }
                    this.total = response.total || 0;
                    this.summary = response.summary || {};
                    this.loading = false;
                }
                this.$nextTick(() => {
                    this.$refs.DataTable.setScrollTop(0)
                    this.$emit('dataChange', res, this.tableData)
                })
            }).catch(error => {
                var name = Object.keys(error)[0]
                this.loading = false;
                this.emptyText = page[name][0];
            })
        },
        //分页点击
        paginationChange(val) {
            this.currentPage = val
            this.$emit('changePage', this.currentPage)
            this.getData();
        },
        //条数变化
        pageSizeChange(size) {
            this.dataPageSize = size
            this.$emit('changePageSize', size)
            this.getData();
        },
        //刷新数据
        refresh() {
            this.$refs.DataTable.clearSelection();
            this.getData();
        },
        //更新数据 合并上一次params
        upData(params, page = 1) {
            this.currentPage = page;
            this.$refs.DataTable.clearSelection();
            Object.assign(this.tableParams, params || {})
            this.getData()
        },
        //重载数据 替换params
        reload(params, page = 1) {
            this.currentPage = page;
            this.tableParams = params || {}
            this.$refs.DataTable.clearSelection();
            this.$refs.DataTable.clearSort()
            this.$refs.DataTable.clearFilter()
            this.getData()
        },
        //排序事件
        sortChange(obj) {
            if (!this.remoteSort) {
                return false
            }
            if (obj.column && obj.prop) {
                this.prop = obj.prop
                this.order = obj.order
            } else {
                this.prop = null
                this.order = null
            }
            this.getData()
        },
        //本地过滤
        filterHandler(value, row, column) {
            const property = column.property;
            return row[property] === value;
        },
        //过滤事件
        filterChange(filters) {
            if (!this.remoteFilter) {
                return false
            }
            Object.keys(filters).forEach(key => {
                filters[key] = filters[key].join(',')
            })
            this.upData(filters)
        },
        //远程合计行处理
        remoteSummaryMethod(param) {
            const { columns } = param
            const sums = []
            columns.forEach((column, index) => {
                if (index === 0) {
                    sums[index] = '合计'
                    return
                }
                const values = this.summary[column.property]
                if (values) {
                    sums[index] = values
                } else {
                    sums[index] = ''
                }
            })
            return sums
        },
        configSizeChange() {
            this.$refs.DataTable.doLayout()
        },
        //插入行 unshiftRow
        unshiftRow(row) {
            this.tableData.unshift(row)
        },
        //插入行 pushRow
        pushRow(row) {
            this.tableData.push(row)
        },
        //根据key覆盖数据
        updateKey(row, rowKey = this.rowKey) {
            this.tableData.filter(item => item[rowKey] === row[rowKey]).forEach(item => {
                Object.assign(item, row)
            })
        },
        //根据index覆盖数据
        updateIndex(row, index) {
            Object.assign(this.tableData[index], row)
        },
        //根据index删除
        removeIndex(index) {
            this.tableData.splice(index, 1)
        },
        //根据index批量删除
        removeIndexes(indexes = []) {
            indexes.forEach(index => {
                this.tableData.splice(index, 1)
            })
        },
        //根据key删除
        removeKey(key, rowKey = this.rowKey) {
            this.tableData.splice(this.tableData.findIndex(item => item[rowKey] === key), 1)
        },
        //根据keys批量删除
        removeKeys(keys = [], rowKey = this.rowKey) {
            keys.forEach(key => {
                this.tableData.splice(this.tableData.findIndex(item => item[rowKey] === key), 1)
            })
        },
        //原生方法转发
        clearSelection() {
            this.$refs.DataTable.clearSelection()
        },
        toggleRowSelection(row, selected) {
            this.$refs.DataTable.toggleRowSelection(row, selected)
        },
        toggleAllSelection() {
            this.$refs.DataTable.toggleAllSelection()
        },
        toggleRowExpansion(row, expanded) {
            this.$refs.DataTable.toggleRowExpansion(row, expanded)
        },
        setCurrentRow(row) {
            this.$refs.DataTable.setCurrentRow(row)
        },
        clearSort() {
            this.$refs.DataTable.clearSort()
        },
        clearFilter(columnKey) {
            this.$refs.DataTable.clearFilter(columnKey)
        },
        doLayout() {
            this.$refs.DataTable.doLayout()
        },
        sort(prop, order) {
            this.$refs.DataTable.sort(prop, order)
        }
    }
}
</script>

<style scoped>
.DataTable-table {
    height: calc(100% - 50px);
}

.DataTable-page {
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 15px;
}

.DataTable-do {
    white-space: nowrap;
}

.DataTable :deep(.el-table) {
    --el-table-border-color: var(--el-border-light);
}

.DataTable :deep(th.el-table__cell) {
    background-color: var(--el-table-th-bg);
}

.DataTable :deep(thead) {
    color: var(--el-table-th);
}

.DataTable :deep(thead th) {
    font-weight: 400;
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
</style>
