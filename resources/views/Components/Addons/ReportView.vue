<template>
    <div class="report">
        <div class="report-title">

        </div>
    </div>
</template>

<script>
import config from "@/config/table";

export default {
    name: 'ReportView',
    props: {
    },
    watch: {
    },
    data() {
        return {
            blockTitle: 'Inline Audit Daily Report'
        }
    },
    mounted() {
    },
    methods: {
    }
}
</script>

<style scoped lang="scss">
@font-face {
    font-family: "HYZSJ";
    src: url("/assets/font/hyzsj.ttf");
}

@font-face {
    font-family: "CorpoA";
    src: url("/assets/font/corpoA.ttf");
}

@font-face {
    font-family: "CorpoS";
    src: url("/assets/font/corpoS.ttf");
}

.report {
    width: 100%;
    height: auto;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;

    &-title {
        width: 100%;
        font-family: "CorpoA","HYZSJ","HarmonyOS";
        font-size: 18px;
    }
}
</style>
