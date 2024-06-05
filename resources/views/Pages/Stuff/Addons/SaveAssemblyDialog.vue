<template>
    <el-dialog title="考核记录维护/Audit Record Edit" v-model="visitable" width="400px">

        <el-form :model="form" ref="form" size="small" label-position="top" :rules="rules" v-loading="loading"
            @submit.native.prevent="onSave">
            <el-form-item label="缺陷等级/Defect Level" prop="defect_level">
                <el-select style="width:100%" v-model="form.defect_level" placeholder="请选择缺陷等级/Defect Level">
                    <el-option v-for="(item, index) in defect_level" :key="index" :value="item.value"
                        :label="item.name"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="责任人/Person In Charge" prop="ira">
                <el-input v-model="form.ira" placeholder="请输入责任人/Person In Charge" clearable></el-input>
            </el-form-item>
            <el-form-item label="责任部门/Department" prop="department">
                <el-input v-model="form.department" placeholder="请输入责任部门/Department" clearable></el-input>
            </el-form-item>
            <el-form-item label="问题描述/Problem Description" prop="defect_description">
                <el-select style="width:100%" v-model="form.defect_description"
                    placeholder="请选择问题描述/Problem Description" clearable>
                    <el-option v-for="(item, index) in defect_category" :key="index" :value="item.value"
                        :label="item.name"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="SOMA" prop="soma">
                <el-input type="textarea" v-model="form.soma" maxlength="250" show-word-limit placeholder="请输入SOMA"
                    clearable></el-input>
            </el-form-item>
            <el-form-item label="LAMA" prop="lama">
                <el-input type="textarea" v-model="form.lama" maxlength="250" show-word-limit placeholder="请输入LAMA"
                    clearable></el-input>
            </el-form-item>
            <el-form-item label="Score card" prop="score_card">
                <el-input type="number" v-model="form.score_card" min="0" placeholder="请输入Score card"
                    clearable></el-input>
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="visitable = false">取消</el-button>
                <el-button type="primary" v-loading="loading" @click="onSave">提交保存</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script>
export default {
    props: {
        defect_level: {
            type: Array,
            default: []
        },
        defect_category: {
            type: Array,
            default: []
        }
    },
    emits: ['success'],
    data() {
        return {
            visitable: false,
            loading: false,
            item: {
                id: ''
            },
            form: {
                defect_level: '',
                ira: '',
                department: '',
                defect_description: '',
                soma: '',
                lama: '',
                score_card: '',
            },
            rules: {
                defect_level: [
                    { required: true, message: '请选择缺陷等级', trigger: 'change' }
                ]
            }
        }
    },
    methods: {
        open(item) {
            this.item = item
            this.loading = false;
            this.form = {
                defect_level: item.defect_level,
                ira: item.ira,
                department: item.department,
                defect_description: item.defect_description,
                soma: item.soma,
                lama: item.lama,
                score_card: item.score_card,
            }
            this.visitable = true
        },
        async onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    this.loading = true
                    this.form.score_card = parseInt(this.form.score_card)
                    let res = await this.$axios.post(this.$route('stuff.update', { id: this.item.id }), this.form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success('维护记录成功')
                        this.$emit('success')
                        this.visitable = false
                    } else {
                        this.$message.error(res.message)
                    }
                }
            })
        }
    }
}
</script>

<style scoped lang="scss"></style>