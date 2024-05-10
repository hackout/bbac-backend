<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block">
            <div class="list">
                <div class="list-item">
                    <span>查看日报</span>
                </div>
                <div class="list-item">
                    <span>查看周报</span>
                </div>
                <div class="list-item">
                    <span>查看月报</span>
                </div>
                <div class="list-item">
                    <span>查看年报</span>
                </div>
            </div>
        </div>
    </Layout>
</template>
<script>

export default {
    props: {
    },
    data() {
        return {
        }
    },
    mounted() {
        this.$nextTick(() => { })
    },
    methods: {
    }
}
</script>

<style scoped>
.list {
    width: 100%;
    height: 640px;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
}

.list-item {
    width: 200px;
    height: 400px;
    margin: 10px 20px;
    border: #ccc 1px solid;
    border-radius: 10px;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: var(--el-box-shadow-light);
}
.list-item span{
    font-size: 22px;
    letter-spacing: 20px;
    writing-mode: vertical-rl;
}
</style>