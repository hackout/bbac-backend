<template>
    <div class="report" :style="{height:height,width:width}">
        <ReportTitle v-if="reportTitle" :title="reportTitle" :mode="reportMode"></ReportTitle>
        <div class="report-box" :style="{flexDirection:flexDirection}">
            <slot></slot>
        </div>
        <ReportFooter v-if="reportFooter" :content="reportFooter"></ReportFooter>
    </div>
</template>
<script>
import ReportTitle from './ReportTitle.vue'
import ReportFooter from './ReportFooter.vue'
export default {
    components: {
        ReportTitle,
        ReportFooter
    },
    props:{
        modeName: {
            type: String,
            default: null
        },
        title: {
            type: String,
            default: null
        },
        footer: {
            type:String,
            default: null
        },
        width: {
            type:String,
            default: '1580px'
        },
        height: {
            type:String,
            default: '689px'
        },
        direction: {
            type: String,
            default: 'column'
        }
    },
    data(){
        return {
            reportTitle: this.title,
            reportFooter: this.footer,
            reportMode: this.modeName,
            flexDirection: this.direction
        }
    },
    watch:{
        direction(val){
            this.flexDirection = val
        },
        title(val){
            this.reportTitle = val
        },
        footer(val){
            this.reportFooter = val
        },
        modeName(val){
            this.reportMode = val
        }
    }
}
</script>
<style scoped lang="scss">
.report{
    width: 1580px;
    height: 689px;
    display: flex;
    flex-direction: column;
    background-color: var(--report-bg);
    border-color: var(--report-border);
    &-box{
        height: calc(100% - 100px);
        width: 100%;
        display: flex;
        flex-direction: column;
    }
}
</style>