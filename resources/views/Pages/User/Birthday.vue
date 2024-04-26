<template>
    <Layout>
        <TopNav></TopNav>
        <div class="page-block">
            <div class="page-search">
                <div class="page-search-form">
                    <el-dropdown :hide-on-click="false" :disabled="items.length == 0" @command="setCard">
                        <el-button type="primary" :disabled="items.length == 0" icon="el-icon-edit">设置生日卡样式</el-button>
                        <template #dropdown>
                            <el-dropdown-menu style="width:135px;max-height:500px;">
                                <el-dropdown-item command="0">无样式</el-dropdown-item>
                                <el-dropdown-item v-for="(card, index) in cards" :key="index" :command="card.id">
                                    <el-image style="width: 125px; height: 100px" :src="card.example" fit="cover" />
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                    <el-button type="primary" style="margin-left: 12px;" @click="showCard"
                        icon="el-icon-plus">管理生日卡样式</el-button>
                </div>
            </div>
            <DataTable ref="table" :apiName="$route('user.birthday_list')" @change-page="query.page = $event"
                @change-page-size="query.limit = $event" height="100%" :params="query" stripe highlightCurrentRow
                remoteSort @selection-change="selectionChange" remoteFilter>
                <el-table-column label="选择" type="selection" align="center" width="55" />
                <el-table-column label="序号" align="center" prop="id" width="100">
                    <template #default="scope">
                        <span>{{ scope.$index + 1 < 10 ? '0' + (scope.$index + 1) : scope.$index + 1 }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="工号" align="center" prop="number" width="200"></el-table-column>
                <el-table-column label="姓名" align="center" prop="name" width="200"></el-table-column>
                <el-table-column label="拼音" align="center" prop="pinyin" width="200"></el-table-column>
                <el-table-column label="生日" align="center" prop="birth" width="200">
                    <template #default="scope">
                        <span>{{ $tool.dateFormat(scope.row.birth, 'YYYY年MM月DD') }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="卡片样式" align="center" prop="birthday_card_id">
                    <template #default="scope">
                        <span>{{ $status('cards', scope.row.birthday_card_id, 'id') }}</span>
                    </template>
                </el-table-column>
            </DataTable>
        </div>
        <CardDrawer v-if="editable" @success="refreshData" @closed="closeDrawer" ref="CardDrawer"></CardDrawer>
    </Layout>
</template>

<script>
import CardDrawer from './Addons/CardDrawer.vue';
import { router } from '@inertiajs/vue3'
export default {
    components: {
        CardDrawer
    },
    props: {
        cards: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            saving: false,
            query: {
                page: 1,
                limit: 20
            },
            uploadHeaders: {
                'X-XSRF-TOKEN': this.$tool.cookies.get('XSRF-TOKEN')
            },
            items: [],
            deleting: false,
            editable: false
        }
    },
    mounted() {
        this.$nextTick(() => {
        })
    },
    methods: {
        closeDrawer() {
            router.reload({
                only: ['cards'],
                onSuccess: () => {
                    this.editable = false
                    this.refreshData()
                }
            })
        },
        showCard() {
            this.editable = true
            this.$nextTick(() => {
                this.$refs.CardDrawer.open()
            })
        },
        selectionChange(items) {
            this.items = items
        },
        setCard(value) {
            this.$confirm('确定设置所选员工为改样式?', '操作提示').then(async () => {
                this.deleting = true
                var res = await this.$axios.post(this.$route('user.birthday_update',{id:value}), { ids: this.items.map(n => n.id) })
                this.deleting = false
                if (res.code == this.$config.successCode) {
                    this.$message.success("批量设置成功")
                    this.refreshData()
                } else {
                    this.$message.error(res.message)
                }
            }).catch(() => { })
        },
        refreshData() {
            this.itemIds = []
            this.$refs.table.refresh()
        }
    }
}
</script>

<style scoped lang="scss">
:deep(.el-dropdown-menu__item) {
    padding: 5px;
    text-align: center;
}
</style>