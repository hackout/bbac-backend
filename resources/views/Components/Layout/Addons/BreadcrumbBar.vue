<template>
    <div class="bbacUi-header-breadcrumb">
        <el-breadcrumb separator-icon="el-icon-arrow-right">
            <transition-group name="breadcrumb">
                <el-breadcrumb-item v-for="(item, index) in breadList" :to="item.end ? '' : $route(item.path)" :key="index">
                    <span>{{ item.name }}</span>
                </el-breadcrumb-item>
            </transition-group>
        </el-breadcrumb>
    </div>
</template>

<script>
export default {
    data() {
        return {
            breadList: []
        }
    },
    created() {
        this.$nextTick(() => {
            this.getBreadcrumb();
        })
    },
    methods: {
        getBreadcrumb() {
            this.breadList = this.$page.props.histories
        }
    }
}
</script>

<style scoped lang="scss">
:deep(.el-breadcrumb__inner) {
    font-size: 16px;
    color: var(--el-text-color);
    &.is-link
    {
        color: var(--el-link-color);
        font-weight: 200;
    }
}
</style>
