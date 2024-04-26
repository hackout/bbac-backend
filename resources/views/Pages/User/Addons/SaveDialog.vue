<template>
    <el-dialog :title="titles[mode]" v-model="visitable" width="450px" @closed="$emit('closed')">
        <el-form :model="form" label-width="120px" ref="form" @submit.native.prevent="onSave">
            <el-form-item label="所属部门" prop="department_id">
                <el-cascader :options="options" v-model="form.department_id" style="width:100%;" :props="cascaderProp"
                    @change="changeParentId" clearable />
            </el-form-item>
            <el-form-item label="用户名" prop="username">
                <el-input v-model="form.username" :disabled="mode == 'edit'"
                    :placeholder="mode == 'edit' ? '禁止修改' : '请输入用户名'" clearable></el-input>
            </el-form-item>
            <el-form-item label="邮箱地址" prop="email">
                <el-input v-model="form.email" :disabled="mode == 'edit'"
                    :placeholder="mode == 'edit' ? '禁止修改' : '请输入邮箱地址'" clearable></el-input>
            </el-form-item>
            <el-form-item label="员工号" prop="number">
                <el-input v-model="form.number" :disabled="mode == 'edit'"
                    :placeholder="mode == 'edit' ? '禁止修改' : '请输入员工号'" clearable></el-input>
            </el-form-item>
            <el-form-item label="手机号码" prop="mobile">
                <el-input v-model="form.mobile" :disabled="mode == 'edit'"
                    :placeholder="mode == 'edit' ? '禁止修改' : '请输入手机号码'" clearable></el-input>
            </el-form-item>
            <el-form-item label="账号状态" prop="is_valid">
                <el-switch v-model="form.is_valid" inactive-text="禁用" active-text="可用"></el-switch>
            </el-form-item>
            <el-form-item label="账号角色" prop="roles">
                <el-select v-model="form.roles" multiple placeholder="请选择账号角色" style="width: 100%" clearable>
                    <el-option v-for="item in roles" :key="item.value" :label="item.name" :value="item.value" />
                </el-select>
            </el-form-item>
        </el-form>
        <el-alert v-if="mode == 'add'" :title="`默认登录密码为:${defaultPassword}`" type="error" />
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
        departments: {
            type: Array,
            default: []
        },
        roles: {
            type: Array,
            default: []
        },
        defaultPassword: {
            type: String,
            default: '123456'
        }
    },
    emits: ['success', 'closed'],
    data() {
        return {
            mode: 'add',
            titles: {
                add: '添加新的用户',
                edit: '编辑账号密码'
            },
            visitable: false,
            loading: false,
            options: [],
            cascaderProp: {
                checkStrictly: true,
                label: 'name',
                value: 'id'
            },
            form: {
                id: 0,
                is_valid: true,
                is_super: false,
                department_id: '0',
                username: '',
                email: '',
                number: '',
                mobile: '',
                roles: []
            }
        }
    },
    methods: {
        changeParentId(value) {
            this.form.department_id = value[value.length - 1]
        },
        onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    let method = this.mode == 'add' ? 'post' : 'put'
                    let url = this.mode == 'add' ? this.$route('user.create') : this.$route('user.update', { id: this.form.id })
                    this.loading = true
                    let res = await this.$axios[method](url, this.form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success(`${this.mode == 'add' ? '添加' : '修改'}用户成功`)
                        this.$emit('success')
                        this.visitable = false
                    } else {
                        this.$message.error(res.message)
                    }
                }
            })
        },
        open(type, item) {
            this.mode = type
            if (item) {
                this.form = {
                    id: item.id,
                    is_valid: item.is_valid,
                    is_super: item.is_super,
                    department_id: item.department_id,
                    username: item.username,
                    email: item.email,
                    number: item.number,
                    mobile: item.mobile,
                    roles: item.roles.map(n => n.id),
                }
            } else {
                this.form = {
                    id: 0,
                    is_valid: true,
                    is_super: false,
                    department_id: '0',
                    username: '',
                    email: '',
                    number: '',
                    mobile: '',
                    roles: []
                }
            }
            this.options = [
                {
                    name: '无',
                    id: '0',
                    disabled: false
                }
            ]
            this.departments.forEach(n => {
                this.options.push({
                    name: n.name,
                    id: n.id,
                    disabled: n.id == this.form.id,
                    children: this.makeChildren(n, n.id == this.form.id)
                })
            })
            this.visitable = true
        },
        makeChildren(options, disabled = false) {
            let result = []
            if (options.children) {
                options.children.forEach(n => {
                    result.push({
                        name: n.name,
                        id: n.id,
                        disabled: disabled || n.id == this.form.id,
                        children: this.makeChildren(n, disabled || n.id == this.form.id)
                    })
                })
            }
            return result.length > 0 ? result : null
        }
    }
}
</script>

<style scoped lang="scss"></style>