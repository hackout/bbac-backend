<template>
    <Layout>
        <el-alert title="单击单元格数字可编辑PPM Target值" type="warning" />
        <TopNav></TopNav>
        <div class="page-block">
            <DataTable :data="items" :hidePagination="true" :hideDo="true" :pageSize="30">
                <el-table-column label="年份" prop="yearly">
                </el-table-column>
                <el-table-column v-for="(type, index) in eb_type" :label="type.name" :key="index" :prop="type.value">
                    <template #default="scope">
                        <el-text style="cursor:pointer;width:100%;" size="large"
                            @click="editTarget(scope.row.yearly, type.value, scope.row[type.value])">{{
                            scope.row[type.value] }}</el-text>
                    </template>
                </el-table-column>
            </DataTable>
        </div>
        <el-dialog title="年度PPM Target" v-model="editable" @closed="editable = false">
            <el-form :model="form" ref="form" label-position="top" :rules="rules" v-loading="saving" label-width="120px"
                @submit.native.prevent="onSubmit">

                <el-form-item label="年度" prop="yearly">
                    <el-input :value="form.yearly" disabled></el-input>
                </el-form-item>
                <el-form-item label="机型" prop="yearly">
                    <el-input :value="$status('eb_type', form.eb_type)" disabled></el-input>
                </el-form-item>
                <el-form-item label="PPM Target" prop="target">
                    <el-input type="number" min="0" v-model="form.target" placeholder="请输入PPM Target"></el-input>
                </el-form-item>
            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="editable = false">取消</el-button>
                    <el-button type="primary" v-loading="saving" @click="onSubmit">确定</el-button>
                </div>
            </template>
        </el-dialog>
    </Layout>
</template>
<script>
export default {
    props: {
        eb_type: {
            type: Array,
            default: []
        },
        items: {
            type: Array,
            default: []
        },
    },
    data() {
        return {
            loading: false,
            editable: false,
            saving: false,
            form: {
                yearly: '',
                eb_type: '',
                target: ''
            }
        }
    },
    mounted() {
        this.$nextTick(() => {

        })
    },
    computed: {

    },
    methods: {
        editTarget(yearly, eb, target) {
            this.form = {
                yearly: yearly,
                eb_type: eb,
                target: target
            }
            this.editable = true
        },
        onSubmit() {
            if (!this.loading && !this.saving) {
                this.loading = true
                this.saving = true
                this.$ajax.post(this.$route('vehicle.target_update'), this.form, {
                    forceFormData: true,
                    onFinish: () => {
                        this.loading = false
                        this.saving = false
                        this.$ajax.reload();
                    },
                    onSuccess: () => {

                        this.editable = false
                    }
                })
            }
        }
    }
}
</script>

<style scoped></style>