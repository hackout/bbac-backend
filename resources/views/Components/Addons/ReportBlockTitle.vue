<template>
    <div class="report-block-title">
        <span class="report-block-title-icon"></span>
        <span class="report-block-title-text">{{ blockTitle }}</span>
    </div>
</template>
<script>
export default {
    props: {
        title: {
            type: String,
            default: ''
        }
    },
    watch: {
        title(val) {
            this.blockTitle = val
        }
    },
    data() {
        return {
            blockTitle: this.title
        }
    }
}
</script>
<style scoped lang="scss">
.report-block-title {
    height: 40px;
    width: 100%;
    background-color: var(--el-report-primary-light);
    color: var(--el-color-white);
    display: flex;
    flex-direction: row;
    align-items: center;
    position: relative;
    padding: 12px 20px;

    &::before {
        width: 100%;
        height: 20px;
        content: '';
        background-color: var(--el-report-primary);
        position: absolute;
        bottom: 0;
        left: 0;
    }

    &-icon {
        width: 6px;
        height: 16px;
        border-radius: 3px;
        background-color: var(--el-report-block-icon);
        margin-right: 8px;
        position: relative;
        z-index: 2;
    }

    &-text {
        position: relative;
        font-size: 16px;
        z-index: 2;
        margin-top: -2px;
    }
}
</style>