<template>
    <el-drawer :title="item.title" v-model="visitable"
        destroy-on-close size="800px" @closed="$emit('closed')">
        <div class="basic">
            <div class="basic-header">
                <div class="basic-header-list">
                    <div class="basic-header-list-item">
                        <el-icon-clock></el-icon-clock>
                        <span>{{ $tool.dateFormat(item.created_at) }}</span>
                    </div>
                    <div class="basic-header-list-item" v-if="item.from">
                        <el-icon-share></el-icon-share>
                        <span>来自:{{ item.from }}</span>
                    </div>
                    <div class="basic-header-list-item">
                        <el-icon-discount></el-icon-discount>
                        <span>{{ $status('notice_type', item.type) }}</span>
                    </div>
                    <div class="basic-header-list-item">
                        <el-icon-operation></el-icon-operation>
                        <el-text size="small" type="primary" v-if="item.is_valid">已发布</el-text>
                        <el-text size="small" type="danger" v-else>未发布</el-text>
                    </div>
                </div>
            </div>
            <div class="basic-content" v-if="item.type != 3">
                <el-scrollbar scroll-y="true">
                    <article v-html="item.content"></article>
                </el-scrollbar>
            </div>
            <div class="basic-content" v-else>
                <el-descriptions title="变更详情" direction="vertical" :column="1" :size="size" border>
                    <el-descriptions-item label="版本号">{{ item.extra.version }}</el-descriptions-item>
                    <el-descriptions-item :label="change.name" v-for="(change, index) in item.extra.change"
                        :key="index">
                        <el-text type="danger" tag="del">{{ change.before }}</el-text>
                        <el-text type="primary">{{ change.content }}</el-text>
                    </el-descriptions-item>
                </el-descriptions>
            </div>
        </div>
    </el-drawer>
</template>

<script>
export default {
    props: {
        notice_type: {
            type: Array,
            default: []
        }
    },
    emits: ['closed'],
    data() {
        return {
            visitable: false,
            loading: false,
            item: {
                id: 0,
                type: 1,
                from: '',
                title: '消息详情',
                content: '',
                extra: {
                    version: '',
                    document_id: '',
                    change: []
                },
                is_valid: true
            },
        }
    },
    methods: {
        async getData() {
            var res = await this.$axios.get(this.$route('notice.detail', { id: this.item.id }))
            if (res.code == this.$config.successCode) {

                this.item = res.data
                this.visitable = true
            } else {
                this.$message.error(res.message)
                this.$emit('closed')
            }
        },
        open(item) {
            this.item.id = item.id;
            this.getData();
        }
    }
}
</script>

<style scoped lang="scss">
.extra {

    float: right;
    color: var(--el-text-color-secondary);
    font-size: 13px;
}

.basic {
    width: 100%;
    height: 100%;
    @extend .flexColumn;

    &-header {
        width: 100%;
        height: 30px;
        border-bottom: var(--el-border-lighter) 1px solid;
        @extend .flexColumn;
        &-list {
            width: 100%;
            flex: 1;
            @extend .flexColumn;
            flex-direction: row;

            &-item {
                width: 25%;
                @extend .flexColumn;
                flex-direction: row;
                font-size: 18px;
                color: var(--el-link-color);

                span {
                    margin-left: 6px;
                    font-size: 14px;
                }
            }
        }
    }

    &-content {
        height: calc(100% - 30px);
        padding: 10px 0;
        box-sizing: border-box;
        width: calc(100% + 15px);
        margin-right: -15px;

        article {
            width: 100%;
            height: auto;
            font-size: 16px;
            padding-right: 15px;
            box-sizing: border-box;
        }
    }
}
</style>