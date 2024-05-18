<template>
    <Layout>
        <table>
            <tbody>
                <tr v-for="(tr, index) in item.template" :key="index" :style="{ height: tr.height + 'pt' }">
                    <td v-for="(td, index2) in tr.data" :key="index2" :style="td.style" :colspan="td.colspan"
                        :rowspan="td.rowspan">
                        <span v-for="(text, index3) in td.value" :key="index3" :style="text.style">{{ text.value
                            }}</span>
                        <div v-if="td.images.length > 0" style="width:100%;position: relative;">
                            <el-image v-for="(img, index4) in td.images" :key="index4" :style="img.style" :src="img.src"
                                :alt="img.alt"></el-image>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </Layout>
</template>
<script>

export default {
    props: {
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
        this.$nextTick(() => { })
    },
    methods: {
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

<style scoped>
.el-form-item-msg {
    color: var(--el-link-color)
}

:deep(.el-button > span) {
    display: inline-block;
}

table {
    border-collapse: collapse;
    border-spacing: 0;
}
</style>