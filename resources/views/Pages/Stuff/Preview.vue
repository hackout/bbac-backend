<template>
    <Layout>
        <div class="printer-box">
            <div class="page-search">
                <div class="page-search-buttons">
                    <el-button @click="goList">返回</el-button>
                </div>
                <div class="page-search-form">
                    <el-form ref="query" inline>
                        <el-form-item>
                            <el-button type="primary" @click="exportPpt" icon="el-icon-lightning">
                                <span>导出</span>
                            </el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            <table>
                <colgroup>
                    <col v-for="(col, index) in item.template.col" :key="index" :width="col" />
                </colgroup>
                <tbody>
                    <template v-for="(tr, index) in item.template.table">
                        <tr v-if="tr.loop && item.issues && item.issues.length > 0" v-for="(issue, index2) in item.issues" :key="index + '-' + index2">
                            <td v-for="(td, index2) in tr.data" :width="td.width" :key="index2" :style="td.style"
                                :colspan="td.colspan" :rowspan="td.rowspan">
                                <span v-for="(text, index3) in td.value" :key="index3" :style="text.style"
                                    v-html="matchData(text.value)"></span>
                                <template v-if="td.images.length > 0">
                                    <el-image v-for="(img, index4) in td.images" :key="index4" :style="img.style"
                                        :src="img.src" :alt="img.alt"></el-image>
                                </template>
                            </td>
                        </tr>
                        <tr v-else :style="{ height: tr.height + 'pt' }" :index="index">
                            <td v-for="(td, index2) in tr.data" :width="td.width" :key="index2" :style="td.style"
                                :colspan="td.colspan" :rowspan="td.rowspan">
                                <span v-for="(text, index3) in td.value" :key="index3" :style="text.style"
                                    v-html="matchData(text.value)"></span>
                                <template v-if="td.images.length > 0">
                                    <el-image v-for="(img, index4) in td.images" :key="index4" :style="img.style"
                                        :src="img.src" :alt="img.alt"></el-image>
                                </template>
                            </td>
                        </tr>
                    </template>
                    <tr v-for="(tr, index) in item.template.table" :key="index" :style="{ height: tr.height + 'pt' }">
                        <td v-for="(td, index2) in tr.data" :width="td.width" :key="index2" :style="td.style"
                            :colspan="td.colspan" :rowspan="td.rowspan">
                            <span v-for="(text, index3) in td.value" :key="index3" :style="text.style"
                                v-html="matchData(text.value)"></span>
                            <template v-if="td.images.length > 0">
                                <el-image v-for="(img, index4) in td.images" :key="index4" :style="img.style"
                                    :src="img.src" :alt="img.alt"></el-image>
                            </template>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </Layout>
</template>
<script>

export default {
    props: {
        item: {
            type: Object,
            default: {
                template: []
            }
        },
        defect_level: {
            type: Array,
            default: []
        },
        defect_category: {
            type: Array,
            default: []
        },
        task_status: {
            type: Array,
            default: []
        },
        engine_type: {
            type: Array,
            default: []
        },
        examine_product_item_type: {
            type: Array,
            default: []
        },
        examine_type: {
            type: Array,
            default: []
        },
        status: {
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
        users: {
            type: Array,
            default: []
        },
    },
    data() {
        return {
            query: {
                page: 1,
                limit: 20,
                user_id: '',
                engine: '',
                keyword: '',
                date: ['', ''],
                type: 2
            },
            DetailDialogVisit: false
        }
    },
    mounted() {
        this.$nextTick(() => {
            console.log(this.item.template)
        })
    },
    methods: {
        goList() {
            this.$goTo('stuff.index')
        },
        matchData(item) {
            let res = item.match(/\{(.+?)\}/g)
            if (res) {
                let result = item;
                res.forEach(n => {
                    result = result.replace(n, this.getValue(n))
                })
                return result
            } else {
                return item
            }
        },
        getValue(i) {
            let x = i.replace('{', '').replace('}', '').split('#')
            return this.item[x[0]] ? this.item[x[0]] : this.$status(x[0], this.item[x[0]])
        },
        exportPpt() {

        },
        viewItem(item) {
            this.$goTo('stuff.detail', { id: item.id });
        },
        previewItem(item) {
            this.$goTo('stuff.report', { id: item.id });
        },
        async onSearch() {
            var validate = await this.$refs.query.validate().catch(() => { })
            if (!validate) return false;
            this.$nextTick(() => {
                this.$refs.table.upData(this.query)
            })
        },
        openDetail(item) {
            this.DetailDialogVisit = true
            this.$nextTick(() => {
                this.$refs.DetailDialog.open(item)
            })
        },
        refreshData() {
            this.editable = false
            this.$refs.table.refresh()
        }
    }
}
</script>

<style scoped lang="scss">
.el-form-item-msg {
    color: var(--el-link-color)
}

:deep(.el-button > span) {
    display: inline-block;
}

table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
    border: #000 1px solid;
    table-layout: fixed;
    background: #ffffff;

    td {
        position: relative;
    }
}
</style>