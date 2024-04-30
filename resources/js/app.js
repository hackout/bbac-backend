import { createApp, h } from 'vue'
import { createInertiaApp, Head, Link, router } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import ElementPlus from 'element-plus'
import zhCn from 'element-plus/dist/locale/zh-cn.mjs'
import axios from '@/utils/request'
import config from '@/config'
import ZiggyJs from '../../vendor/tightenco/ziggy/src/js'
import { Ziggy } from '@/ziggy'
import tool from '@/utils/tool'
import 'element-plus/dist/index.css'
import 'element-plus/theme-chalk/display.css'
import '@css/app.css'
import * as elIcons from '@element-plus/icons-vue'
import * as bbacIcons from '@/icons/index.js'
import Layout from '@view/Components/Layout/Index.vue';
import NavMenu from '@view/Components/Layout/Addons/NavMenu.vue';
import TopNav from '@view/Components/Addons/TopNav.vue';
import Chart from '@view/Components/Addons/Chart.vue';
import Editor from '@view/Components/Addons/Editor.vue';
import DataTable from '@view/Components/Addons/DataTable.vue';
import VueJsonPretty from 'vue-json-pretty';
import 'vue-json-pretty/lib/styles.css';
import mixinJs from '@/utils/mixin';
import VueDraggable from 'vue-draggable'; 
import tableParse from '@/utils/tableParse';


const ZiggyFunc = (name, params, absolute, config = Ziggy) => ZiggyJs(name, params, absolute, config);
createInertiaApp({
    resolve: (name) => resolvePageComponent(`../views/Pages/${name}.vue`, import.meta.glob('../views/Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(VueDraggable)
            .use(ElementPlus, {
                locale: zhCn
            });

        for (let icon in elIcons) {
            app.component(`ElIcon${icon}`, elIcons[icon])
        }

        for (let icon in bbacIcons) {
            app.component(`BbacIcon${icon}`, bbacIcons[icon])
        }
        app.component('Layout', Layout)
        app.component('Head', Head)
        app.component('Link', Link)
        app.component('NavMenu', NavMenu)
        app.component('TopNav', TopNav)
        app.component('Editor', Editor)
        app.component('Chart', Chart)
        app.component('DataTable', DataTable)
        app.component('VueJsonPretty', VueJsonPretty)
        app.provide('route', ZiggyFunc);
        app.config.globalProperties.$route = ZiggyFunc
        app.config.globalProperties.$ajax = router
        app.config.globalProperties.$tool = tool
        app.config.globalProperties.$axios = axios
        app.config.globalProperties.$config = config
        app.config.globalProperties.$tableParse = tableParse
        window.document.getElementById('pageLoading').remove();
        app.mixin(mixinJs)
        tool.initDpi()
        return app.mount(el)
    },
});