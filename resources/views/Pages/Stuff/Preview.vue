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
                            <el-button type="primary" @click="exportData" icon="el-icon-lightning">
                                <span>导出</span>
                            </el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            <table>
                <colgroup>
                    <col v-for="(col, index) in item.col" :key="index" :width="col" />
                </colgroup>
                <tbody>
                    <tr v-for="(tr, index) in item.table" :key="index" :style="{ height: tr.height + 'pt' }">
                        <td v-for="(td, index2) in tr.data" :width="td.width" :key="index2" :style="td.style"
                            :colspan="td.colspan" :rowspan="td.rowspan">
                            <span v-for="(text, index3) in td.value" :key="index3" :style="text.style"
                                v-html="text.value"></span>
                            <template v-if="td.images && td.images.length > 0">
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
            console.log(this.item)
        })
    },
    methods: {
        goList() {
            history.go(-1)
        },
        async exportData() {
            var res = await this.$axios.get(this.$route('stuff.export',{ id: this.item.id }))
            if (res.code == this.$config.successCode) {
                this.$message.success('数据正在打包中,3秒后自动下载');
                setTimeout(() => {
                    this.$download(res.data)
                }, 3000)
            } else {
                this.$message.error(res.message)
            }
        }
    }
}
</script>

<style scoped lang="scss">
:deep(.el-button > span) {
    display: inline-block;
}

table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
    border: #CCCCCC 1px solid;
    table-layout: fixed;
    background: #ffffff;

    td {
        position: relative;
    }
}
</style>