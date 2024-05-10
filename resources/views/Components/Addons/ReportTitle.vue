<template>
    <div class="report-title">
        <div class="report-title-icon">
            <img src="/assets/imgs/report_arrow.png" alt="">
        </div>
        <div class="report-title-content">
            <span>{{ reportTitle }}</span>
        </div>
        <div class="report-title-extra">
            <img src="/assets/imgs/report_icon.png" alt="">
            <div class="report-title-extra-text">
                <span>{{ titles[type].big }}</span>
                <span>{{ titles[type].title }}</span>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        title: {
            type: String,
            default: ''
        },
        mode: {
            type: String,
            default: 'vehicle'
        }
    },
    watch: {
        title(val) {
            this.reportTitle = val
        },
        mode(val) {
            this.type = val
        }
    },
    data() {
        return {
            reportTitle: this.title,
            type: this.mode,
            titles: {
                vehicle: {
                    big: 'QM-E&B',
                    title: 'I-Service'
                },
                product: {
                    big: 'QM-E&B',
                    title: 'Product Audit'
                },
                inline: {
                    big: 'QM-E&B',
                    title: 'Inline Audit'
                }
            }
        }
    }
}
</script>
<style scoped lang="scss">
.report-title {
    height: 50px;
    width: 100%;
    background-color: var(--el-report-primary);
    color: var(--el-color-white);
    display: flex;
    flex-direction: row;
    &-icon{
        width: 0.2598425in;
        height: 0.2283465in;
        margin-left: 0.1259843in ;
        margin-top: 0.1259843in;
        img{
            width: 100%;
            height: 100%;
        }
    }
    &-content{
        margin-left: 0.075in;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        color: var(--el-color-white);
        font-size: 16pt;
        flex: 1;
    }
    &-extra{
        width: 3.9062in;
        height: 50px;
        padding-right: 0.25in;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        img{
            width: 100%;
            height: 100%;
            position: absolute;
            right: 0;
            top: 0;
        }
        &-text{
            flex: 1;
            height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 2;
        span{
            color: var(--el-report-primary);
            font-size: 12pt;
            &:last-child{
                font-size: 10pt;
            }
        }
        }
    }
}
</style>