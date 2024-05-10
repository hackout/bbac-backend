<template>
    <div class="report-block" :style="customStyle">
        <ReportBlockTitle v-if="blockTitle" :title="blockTitle"></ReportBlockTitle>
        <div class="report-block-box">
            <slot></slot>
        </div>
    </div>
</template>
<script>
import ReportBlockTitle from './ReportBlockTitle.vue'
export default {
    components: {
        ReportBlockTitle
    },
    props: {
        blockStyle: {
            type: Object,
            default: {}
        },
        title: {
            type: String,
            default: null
        }
    },
    data() {
        return {
            blockTitle: this.title,
            customStyle: this.blockStyle
        }
    },
    watch: {
        title(val) {
            this.blockTitle = val
        },
        blockStyle: {
            handler(val) {
                this.customStyle = val
            },
            deep: true
        }
    }
}
</script>
<style scoped lang="scss">
.report-block {
    margin-top: 9px;
    margin-bottom: 10px;
    height: 570px;
    display: flex;
    flex-direction: column;

    &-box {
        margin-top: 10px;
        height: calc(100% - 50px);
        width: 100%;
    }
}
</style>