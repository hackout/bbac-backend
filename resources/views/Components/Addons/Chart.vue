<template>
    <div ref="scEcharts" :style="{ height: height, width: width }"></div>
</template>

<script>
import * as echarts from 'echarts';
import T from '@/chart.js';
echarts.registerTheme('T', T);
const unWarp = (obj) => obj && (obj.__v_raw || obj.valueOf() || obj);

export default {
    ...echarts,
    name: "Chart",
    props: {
        height: { type: String, default: "100%" },
        width: { type: String, default: "100%" },
        noData: { type: Boolean, default: false },
        option: { type: Object, default: () => { } }
    },
    data() {
        return {
            isActivate: false,
            myChart: null
        }
    },
    watch: {
        option: {
            deep: true,
            handler(v) {
                unWarp(this.myChart).setOption(v);
            }
        }
    },
    computed: {
        myOptions: function () {
            return this.option || {};
        }
    },
    activated() {
        if (!this.isActivate) {
            this.$nextTick(() => {
                this.myChart.resize()
            })
        }
    },
    deactivated() {
        this.isActivate = false;
    },
    mounted() {
        this.isActivate = true;
        this.$nextTick(() => {
            this.draw();
        })
    },
    methods: {
        draw() {
            var myChart = echarts.init(this.$refs.scEcharts, 'T');
            myChart.setOption(this.myOptions);
            this.myChart = myChart;
            window.addEventListener('resize', () => myChart.resize());
        }
    }
}
</script>
