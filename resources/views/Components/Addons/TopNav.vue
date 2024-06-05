<template>
    <div class="page-block top-nav" v-if="navList.length > 0">
        <Link class="top-nav-item" :class="{current:item.current}" v-for="(item,index) in navList" :key="index" :href="$route(item.path)">
            <span>{{ item.name }}</span>
        </Link>
    </div>
</template>
<script>
export default {
    data(){
        return {
            navList: []
        }
    },
    mounted(){
        this.$nextTick(()=>{
            this.navList = this.$page.props.children
        })
    }
}
</script>
<style scoped lang="scss">
.top-nav{
    height: 64px;
    padding-top: 20px;
    padding-bottom: 24px;
    margin-bottom: 20px;
    width: 100%;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: flex-start;
    &-item{
        font-size: 16px;
        line-height: 20px;
        margin-right: 30px;
        position: relative;
        color: var(--el-link-color);
        cursor: pointer;
        &.current{
            color: var(--el-text-color);
            &::after{
                content: '';
                width: 50%;
                position: absolute;
                bottom: -24px;
                height: 2px;
                background-color: var(--el-text-color);
                left: 25%;
            }
        }
    }
}
</style>