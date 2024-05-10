<template>
    <div class="report-footer">
        <span>{{ value }}</span>
    </div>
</template>
<script>
export default {
    props: {
        content: {
            type: String,
            default: ''
        }
    },
    watch: {
        content(val) {
            this.value = val
        },
    },
    data() {
        return {
            value: this.content
        }
    }
}
</script>
<style scoped lang="scss">
.report-footer {
    height: 50px;
    width: 100%;
    background-color: var(--el-report-light);
    color: var(--el-report-main);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 10pt;
}
</style>