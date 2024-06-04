<template>
    <Head :title="title"></Head>
    <section class="bbacUi">
        <el-container>
            <el-aside width="260px" class="bbacUi-aside">
                <div class="bbacUi-aside-logo">
                    <img src="/assets/imgs/logo-white.png" alt="BBAC" />
                </div>
                <nav-menu :items="menus"></nav-menu>
            </el-aside>
            <el-container style="height:100vh">
                <el-header class="bbacUi-header">
                    <breadcrumb-bar></breadcrumb-bar>
                    <user-bar></user-bar>
                </el-header>
                <el-main class="bbacUi-main">
                    <el-scrollbar>
                        <div class="bbacUi-content">
                            <slot></slot>
                        </div>
                    </el-scrollbar>
                </el-main>
            </el-container>
        </el-container>
    </section>
</template>
<script>
import { Head } from '@inertiajs/vue3'
import BreadcrumbBar from './Addons/BreadcrumbBar.vue'
import UserBar from './Addons/UserBar.vue'
import NavMenu from './Addons/NavMenu.vue'
export default {
    components: {
        BreadcrumbBar,
        UserBar,
        NavMenu,
        Head
    },
    data() {
        return {
            menus: [],
            currentMenu: '/',
            nextMenu: null,
            title: ''
        }
    },
    created() {
        this.$nextTick(() => {
            this.menus = this.$page.props.routes
            this.currentMenu = this.$page.props.current
        })
    },
    watch: {
        '$page.props.flash': {
            handler() {
                this.showFlash()
            },
            deep: true
        }
    },
    methods: {
        showFlash() {
            if (this.$page.props.flash.success) {
                this.$message.success(this.$page.props.flash.success)
            } else
                if (this.$page.props.flash.error) {
                    var name = Object.keys(this.$page.props.flash.error)[0]
                    this.$message.error(this.$page.props.flash.error[name][0])
                }
            this.title = this.$page.props.title
        }
    }
}
</script>

<style scoped lang="scss">
.bbacUi {
    width: 100vw;
    height: 100vh;

    &-aside {
        width: 260px;
        height: 100vh;
        background: var(--el-color-primary-dark-2) url(/assets/imgs/aside.jpg) bottom center no-repeat;
        color: var(--el-color-white);
        font-size: var(--el-aside-size);

        &-logo {
            width: 100%;
            padding: 10px 20px;
            box-sizing: border-box;
            height: 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            img {
                width: 147px;
                height: auto;
            }
        }
    }

    &-header {
        height: 68px;
        background-color: var(--el-color-white);
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        box-shadow: var(--el-box-shadow-light);
        position: relative;
        z-index: 2;
        :deep(.el-breadcrumb__inner.is-link){
            font-weight: 400;
        }
    }

    &-main {
        padding: 0;
        width: 100%;
        height: calc(100% - 68px);
    }

    &-content{
        padding: 30px;
        box-sizing: border-box;
        width: 100%;
        height: auto;
    }
}
</style>