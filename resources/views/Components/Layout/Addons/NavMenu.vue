<template>
    <Link :href="$route(item.path)" class="bbacUi-aside-item" :class="{ active: currentName == item.path }"
        v-for="(item, index) in items" :key="index">
        <div class="bbacUi-aside-item-icon">
            <el-icon>
                <component :is="item.icon || 'el-icon-menu'"></component>
            </el-icon>
        </div>
        <div class="bbacUi-aside-item-title">
            <span>{{ item.name }}</span>
            <span>{{ item.intro }}</span>
        </div>
        <div class="bbacUi-aside-item-arrow">
            <el-icon-arrow-right />
        </div>
    </Link>
</template>

<script>
export default {
    props: {
        items: Array,
        parentCurrent: String
    },
    data() {
        return {
            currentName: this.parentCurrent ?? 'home'
        }
    },
    created(){
        this.currentName = this.$page.props.parentCurrent
    },
    watch: {
        parentCurrent(val) {
            this.currentName = val
        }
    }
}
</script>
<style lang="scss" scoped>
.bbacUi-aside-item {
    width: 100%;
    height: 58px;
    padding: 6px 20px;
    box-sizing: border-box;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    cursor:pointer;
    --item-color: var(--el-color-white);

    &.active{
        background-color:var(--el-color-white);
        --item-color: var(--el-text-color);
    }
    &-icon {
        font-size: 24px;
        width: 24px;
        height: 24px;
        margin-right: 20px;
        color: var(--item-color);
    }

    &-title {
        flex: 1;
        display: flex;
        flex-direction: column;
        span{
            font-size: 16px;
            color: var(--item-color);
            &:last-child{
                font-size: 14px;
                opacity: .5;
            }
        }
    }

    &-arrow{
        width: 19px;
        height: 19px;
        font-size: 19px;
        margin-left: 20px;
        color: var(--item-color);
    }
}
</style>