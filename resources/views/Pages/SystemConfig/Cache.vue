<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block">
            <el-result :icon="icon" :title="title" :sub-title="subTitle">
                <template #extra>
                    <el-button @click="clearCache" type="primary">清空缓存</el-button>
                </template>
            </el-result>
        </div>
    </Layout>
</template>
<script>
export default {
    props: {
        cacheSize: {
            type: Number,
            default: 0
        },
        cacheTotal: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
            loading: 0,
            cache: {
                size: this.cacheSize,
                total: this.cacheTotal
            }
        }
    },
    computed: {
        title() {
            return this.$tool.byteString(this.cache.size)
        },
        subTitle() {
            return `当前系统已对${this.cache.total}条信息进行了数据缓存处理`
        },
        icon() {
            if (this.loading == 1) return 'warning'
            if (this.loading == 2) return 'success'
            if (this.loading == 3) return 'error'
            return 'info'
        }
    },
    methods: {
        async clearCache() {
            this.loading = 1
            var res = await this.$axios.post(this.$route('system_config.cache_clear'))
            if (res.code == this.$config.successCode) {
                this.$message.success('缓存已清空');
                this.loading = 2
                this.cache = {
                    size: 0,
                    total: 0
                }
            } else {
                this.loading = 3
                this.$message.error('清理缓存失败')
            }
        },
    }
}
</script>

<style scoped></style>