<template>
    <el-dialog :title="titles[mode]" v-model="visitable" width="450px" @closed="$emit('closed')">
        <el-form :model="form" ref="form" label-width="100px" :rules="rules" @submit.native.prevent="onSave">
            <el-form-item label="上级部门" prop="parent_id">
                <el-cascader :options="options" v-model="form.parent_id" style="width:100%;" :props="cascaderProp"
                    @change="changeParentId" clearable />
            </el-form-item>
            <el-form-item label="部门名称" prop="name">
                <el-input v-model="form.name" placeholder="请输入部门名称" clearable></el-input>
            </el-form-item>
            <el-form-item label="责任用户" prop="leader_id">
                <el-select placeholder="请选择负责人用户信息" style="width:100%;" v-model="form.leader_id" @change="changeUser" clearable>
                    <el-option value="" label="未录入人员"></el-option>
                    <el-option v-for="(user,index) in users" :key="index" :value="user.value" :label="`${user.number}-${user.name}(${user.roles.join('、')})`"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="负责人" prop="contact">
                <el-input v-model="form.contact" placeholder="请输入负责人" clearable></el-input>
            </el-form-item>
            <el-form-item label="联系电话" prop="mobile">
                <el-input v-model="form.mobile" placeholder="请输入联系电话" clearable></el-input>
            </el-form-item>
            <el-form-item label="联系邮箱" prop="email">
                <el-input v-model="form.email" placeholder="请输入联系邮箱" clearable></el-input>
            </el-form-item>
            <el-form-item label="部门权限" prop="department_role">
                <el-radio-group v-model="form.role">
                    <el-radio-button v-for="(role,index) in department_role" :key="index" :label="role.value">{{ role.name }}</el-radio-button>
                </el-radio-group>
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
        department_role: {
            type: Array,
            default: []
        },
        departments: {
            type: Array,
            default: []
        },
        users: {
            type: Array,
            default: []
        }
    },
    emits: ['success', 'closed'],
    data() {
        return {
            mode: 'add',
            titles: {
                add: '添加新的部门',
                edit: '编辑部门信息'
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
                id: '',
                parent_id: '',
                leader_id: '',
                name: '',
                contact: '',
                mobile: '',
                email: '',
                role: 0
            },
            rules: {
                name: [
                    { required: true, message: '请输入部门名称', trigger: 'blur' }
                ]
            }
        }
    },
    methods: {
        changeParentId(value) {
            this.form.parent_id = value[value.length - 1]
            console.log(value)
        },
        onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    let method = this.mode == 'add' ? 'post' : 'put'
                    let url = this.mode == 'add' ? this.$route('department.create') : this.$route('department.update', { id: this.form.id })
                    this.loading = true
                    let res = await this.$axios[method](url, this.form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success(`${this.mode == 'add' ? '添加' : '修改'}部门信息成功`)
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
                    parent_id: item.parent_id ? item.parent_id : '',
                    leader_id: item.leader_id ? item.leader_id : '',
                    name: item.name,
                    contact: item.contact,
                    mobile: item.mobile,
                    email: item.email,
                    role: item.role,
                }
            } else {
                this.form = {
                    id: '',
                    parent_id: '',
                    leader_id: '',
                    name: '',
                    contact: '',
                    mobile: '',
                    email: '',
                    role: 0
                }
            }
            this.options = [
                {
                    name: '无上级',
                    id: null,
                    role: 0,
                    disabled: false
                }
            ]
            this.departments.forEach(n => {
                this.options.push({
                    name: n.name,
                    id: n.id,
                    role: n.role,
                    disabled: n.id == this.form.id,
                    children: this.makeChildren(n,n.id == this.form.id)
                })
            })
            this.visitable = true
        },
        changeUser(e){
            let user = this.users.filter(n=>n.value == e)
            if(user.length > 0)
            {
                this.form.contact = user[0].name
                this.form.mobile = user[0].mobile
                this.form.email = user[0].email
            }
        },
        makeChildren(options,disabled = false) {
            let result = []
            if (options.children) {
                options.children.forEach(n => {
                    result.push({
                        name: n.name,
                        id: n.id,
                        role: n.role,
                        disabled: disabled || n.id == this.form.id,
                        children: this.makeChildren(n,disabled || n.id == this.form.id)
                    })
                })
            }
            return result.length > 0 ? result : null
        }
    }
}
</script>

<style scoped lang="scss"></style>