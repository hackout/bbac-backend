<template>
    <el-drawer :title="`${item.name} 考核项列表`" v-model="visitable" size="calc(100vw - 400px)" @closed="$emit('success')">
        <el-table border v-loading="loading" class="DataTable" :data="items">
            <el-table-column prop="sort_order" align="center" label="序号" width="100"></el-table-column>
            <el-table-column label="工作内容" prop="content_zh" min-width="180px">
                <template #default="scope">
                    <el-tooltip effect="dark" :content="scope.row.content_en" placement="bottom">
                        <el-text>{{ scope.row.content_zh }}</el-text>
                    </el-tooltip>
                </template>
            </el-table-column>
            <el-table-column prop="type" align="center" label="检查项类型" width="135">
                <template #default="scope">
                    <el-tag size="small">{{ $status('examine_item_type', scope.row.type) }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="操作" align="right" fixed="right" width="335">
                <template #default="scope">
                    <el-button link type="primary" @click="toTop(scope.$index)"
                        v-if="scope.$index > 0 && items.length > 1" icon="el-icon-caret-top"></el-button>
                    <el-button link type="primary" @click="toBottom(scope.$index)"
                        v-if="scope.$index < items.length - 1 && items.length > 1"
                        icon="el-icon-caret-bottom"></el-button>
                    <el-button link type="primary" @click="viewItem(scope.row)">查看</el-button>
                    <el-button link type="primary" @click="editItem(scope.row)">编辑</el-button>
                    <el-popconfirm title="确定删除此项?" @confirm="deleteItem(scope.row)">
                        <template #reference>
                            <el-button type="danger" link>
                                <span>删除</span>
                            </el-button>
                        </template>
                    </el-popconfirm>
                    <el-button link type="primary" @click="openOption(scope.row)">配置实际测量项</el-button>
                </template>
            </el-table-column>
        </el-table>
        <el-dialog modal-class="chooseDialog" v-model="chooseAddVisit" :show-close="false">
            <el-row :gutter="20">
                <el-col :span="24 / typeList.length" v-for="(t, i) in typeList" :key="i">
                    <span @click="openItem(t.value)" class="col-text">{{ t.name }}</span>
                </el-col>
            </el-row>
        </el-dialog>
        <ItemDetail ref="ItemDetail" v-if="viewable" @success="getItems" @closed="viewable = false"
            :other_type="other_type" :standard_type="standard_type" :gluing_type="gluing_type"
            :dynamic_type="dynamic_type" :special="special" :torque="torque" :bolt_model="bolt_model" :bolt_type="bolt_type"
            :bolt_status="bolt_status" :examine_item_type="examine_item_type"></ItemDetail>
        <ItemDialog ref="ItemDialog" v-if="editable" @success="getItems" @closed="editable = false"
            :other_type="other_type" :standard_type="standard_type" :gluing_type="gluing_type"
            :dynamic_type="dynamic_type" :special="special" :torque="torque" :bolt_model="bolt_model" :bolt_type="bolt_type"
            :bolt_status="bolt_status"></ItemDialog>
        <OptionDrawer ref="OptionDrawer" @closed="optionVisit = false" v-if="optionVisit"></OptionDrawer>
        <template #footer v-if="item.status == 0">
            <div class="drawer-footer">
                <el-button @click="visitable = false">取消</el-button>
                <el-button type="primary" @click="addItem">新增考核项</el-button>
            </div>
        </template>
    </el-drawer>
</template>

<script>
import ItemDialog from './ItemDialog.vue'
import ItemDetail from './ItemDetail.vue'
import OptionDrawer from './OptionDrawer.vue'
export default {
    components: {
        ItemDetail,
        ItemDialog,
        OptionDrawer
    },
    props: {
        special: {
            type: Array,
            default: []
        },
        bolt_model: {
            type: Array,
            default: []
        },
        bolt_type: {
            type: Array,
            default: []
        },
        bolt_status: {
            type: Array,
            default: []
        },
        other_type: {
            type: Array,
            default: []
        },
        torque: {
            type: Array,
            default: []
        },
        standard_type: {
            type: Array,
            default: []
        },
        gluing_type: {
            type: Array,
            default: []
        },
        dynamic_type: {
            type: Array,
            default: []
        },
        examine_item_type: {
            type: Array,
            default: []
        },
    },
    emits: ['success', 'closed'],
    data() {
        return {
            commits: [],
            visitable: false,
            editable: false,
            viewable: false,
            chooseAddVisit: false,
            optionVisit: false,
            loading: false,
            type: 'inline',
            item: {
                id: '',
                name: '',
                engine: '',
                commit_id: '',
                version: '',
                description: '',
                period: '',
                type: '',
                sub_type: '',
                status: 0
            },
            items: []
        }
    },
    computed: {
        typeList() {
            if (this.type == 'inline') {
                if (this.item.sub_type == 1) return this.examine_item_type.filter(n => this.standard_type.indexOf(n.value) > -1)
                if (this.item.sub_type == 2) return this.examine_item_type.filter(n => this.gluing_type.indexOf(n.value) > -1)
                if (this.item.sub_type == 3) return this.examine_item_type.filter(n => this.dynamic_type.indexOf(n.value) > -1)
            }
            return this.examine_item_type.filter(n => this.other_type.indexOf(n.value) > -1)
        }
    },
    methods: {
        async getItems() {
            this.loading = true
            let res = await this.$axios.get(this.$route('commit_item.list', { id: this.item.id }))
            this.loading = false
            if (res.code == this.$config.successCode) {
                this.items = res.data
            } else {
                this.$message.error(res.message)
                this.visitable = false
                this.$emit('closed')
            }
        },
        addItem() {
            this.chooseAddVisit = true
        },
        openItem(type) {
            this.editable = true
            this.chooseAddVisit = false
            this.$nextTick(() => {
                this.$refs.ItemDialog.open('add', type, this.type, this.item.sub_type, this.item)
            })
        },
        viewItem(item){
            this.viewable = true 
            this.$nextTick(()=>{
                this.$refs.ItemDetail.open(this.type,item,this.item)
            })
        },
        editItem(item) {
            this.editable = true
            this.$nextTick(() => {
                this.$refs.ItemDialog.open('edit', item.type, this.type, this.item.sub_type, this.item, item)
            })
        },
        openOption(item) {
            this.optionVisit = true
            this.$nextTick(() => {
                this.$refs.OptionDrawer.open(this.item, item)
            })
        },
        async toTop(index) {
            let items = this.items
            let _item = items[index];
            let _after = items[index - 1];
            items[index - 1] = _item
            items[index] = _after
            let res = await this.$axios.post(this.$route('commit_item.order', { id: this.item.id }), { list: items.map(n => n.id) })
            if (res.code == this.$config.successCode) {
                this.items = items;
            } else {
                this.$message.error(res.message)
            }
        },
        async toBottom(index) {
            let items = this.items
            let _item = items[index];
            let _after = items[index + 1];
            items[index + 1] = _item
            items[index] = _after
            let res = await this.$axios.post(this.$route('commit_item.order', { id: this.item.id }), { list: items.map(n => n.id) })
            if (res.code == this.$config.successCode) {
                this.items = items;
            } else {
                this.$message.error(res.message)
            }
        },
        async deleteItem(item) {
            let res = await this.$axios.delete(this.$route('commit_item.delete', { id: this.item.id, item_id: item.id }))
            if (res.code == this.$config.successCode) {
                this.$message.success('删除检测项成功')
                this.getItems()
            } else {
                this.$message.error(res.message)
            }
        },
        open(type, item) {
            this.type = type
            this.item = item
            this.getItems()
            this.visitable = true
        }
    }
}
</script>

<style scoped lang="scss">
.DataTable.el-table {
    --el-table-border-color: var(--el-border-light);
}

.DataTable :deep(th.el-table__cell) {
    background-color: var(--el-table-th-bg);
}

.DataTable :deep(thead) {
    color: var(--el-table-th);
}

.DataTable :deep(thead th) {
    font-weight: 200;
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

.chooseDialog .el-dialog__header {
    display: none !important;
}
</style>